<?php
/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com
*/
 
// no direct access
defined('_JEXEC') or die('Restricted access');

class JvitalsImage {

	protected $library = null;
	protected static $instance = null;
	
	function __construct($library = 'gd') {
		$this->library = $library;
		JLog::addLogger(array('text_file' => 'jvitalslib.errors.php'), JLog::ALL, 'jvitalslib');
	}
	
	public function setImageLibrary($library) {
		$this->library = $library;
	}
	
	public static function getInstance($library = 'gd') {
		if (is_null(self::$instance)) {
			try {
				$instance = new JvitalsImage($library);
			} catch (RuntimeException $e) {
				throw new RuntimeException(sprintf('JvitalsImage::getInstance: Cannot instantiate class. %s', $e->getMessage()));
			}
			self::$instance = $instance;
		}
		return self::$instance;
	}
	
	public function resize ($source, $dest, $width_new, $height_new = 0) {
		$lib = $this->getLib();
		if (!$lib) {
			JLog::add('JvitalsImage::resize: no valid image manipulation library found', JLog::WARNING, 'jvitalslib');
			return;
		}
		switch($lib) {
			case 'imagemagick':
				return $this->resizeImageMagick($source, $dest, $width_new, $height_new);
				break;
			case 'imagick':
				return $this->resizeImagick($source, $dest, $width_new, $height_new);
				break;
			case 'gd':
			default: 
				return $this->resizeGD($source, $dest, $width_new, $height_new);
		}
	}
	
	public function resizeGD($source, $dest, $width_new, $height_new) {
		
		// get image properties
		list($width_orig, $height_orig, $img_type, $attr) = getimagesize($source);
		
		// determine if the image is landscape or portrait and if we are making a square image
		$landscape = ($width_orig > $height_orig);
		$portrait = ($width_orig < $height_orig);
		$square = ($width_new == $height_new);
		
		// offsets - only needed for square images
		$x = 0;
		$y = 0;
		
		// we have to make different calculations for a square image
		if ($square) {
			// if the original image is not square we have to cut from it :
			// from left and right if landscape image and from top and bottom if portrait image
			if ($landscape) {
				$x = round(($width_orig - $height_orig)/2);
				$width_orig = $height_orig;
			} elseif($portrait) {
				$y = round(($height_orig - $width_orig)/2);
				$height_orig = $width_orig;
			}
		} else {
			$height_new = round(($width_new/$width_orig)*$height_orig);			
		}
		
		// if the original image is smaller than or equal to the dimensions we are resizing to, then we don't resize
		if (($landscape && ($width_new >= $width_orig)) || ($portrait && ($height_new >= $height_orig)) || ($square && ($width_new >= $width_orig))) {
			return -2;
		}
		
		// create source image resource
		switch ($img_type) {
			case IMAGETYPE_GIF: $source_img = imagecreatefromgif($source); break;
			case IMAGETYPE_JPEG: $source_img = imagecreatefromjpeg($source); break;
			case IMAGETYPE_PNG: $source_img = imagecreatefrompng($source); break;
			default: return false;
		}

		// create destination image resource		
		if ($img_type == IMAGETYPE_GIF) {
			$dest_img = imagecreate($width_new, $height_new);
		} else {
			$dest_img = imagecreatetruecolor($width_new, $height_new);
		}
		
		// handle transparency for GIFs and PNGs
		if (($img_type == IMAGETYPE_GIF) || ($img_type == IMAGETYPE_PNG) ) {
			$transparent_idx = imagecolortransparent($source_img);
			if ($transparent_idx >= 0) {
				$transparent_color = imagecolorsforindex($source_img, $transparent_idx);
				$transparent_idx = imagecolorallocate($dest_img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagefill($dest_img, 0, 0, $transparent_idx);
				imagecolortransparent($dest_img, $transparent_idx);
			} elseif ($img_type == IMAGETYPE_PNG) {
				imagealphablending($dest_img, false);
				$color = imagecolorallocatealpha($dest_img, 0, 0, 0, 127);
				imagefill($dest_img, 0, 0, $color);
				imagesavealpha($dest_img, true);
			}
		}
		
		// resize from source image to destination image
		if(!imagecopyresampled($dest_img, $source_img, 0, 0, $x, $y, $width_new, $height_new, $width_orig, $height_orig)) {
			return false;
		}
		
		// write the image
		switch ($img_type) {
			case IMAGETYPE_GIF: imagegif($dest_img, $dest); break;
			// third parameter is quality: 0 (worst quality, smaller file) to 100 (best quality, biggest file), default is 75
			case IMAGETYPE_JPEG: imagejpeg($dest_img, $dest, 90); break;
			// third parameter is compression level: from 0 (no compression) to 9
			case IMAGETYPE_PNG: imagepng($dest_img, $dest, 4); break;
			default: return false;
		}
		
		imagedestroy($source_img);
		imagedestroy($dest_img);
		
		return true;
	}
	
	public function resizeImageMagick($source, $dest, $width_new, $height_new) {
		if (!JvitalsHelper::execAvailable()) {
			JLog::add('JvitalsImage::resizeImageMagick: exec function is disabled, needed for ImageMagick', JLog::WARNING, 'jvitalslib');
			return false;
		}
		$cmd = 'convert';
		@exec('which convert', $res, $ret);
		if (!$ret) $cmd = $res[0];
		
		$img_info = getimagesize($source);
		list($width_orig, $height_orig, $img_type, $attr) = $img_info;
		
		// determine if the image is landscape or portrait and if we are making a square image
		$landscape = ($width_orig > $height_orig);
		$portrait = ($width_orig < $height_orig);
		$square = ($width_new == $height_new);
		
		// offsets - only needed for square images
		$x = 0;
		$y = 0;
		
		// we have to make different calculations and commands for a square image
		if ($square) {
		
			// if the original image is not square we have to cut from it :
			// from left and right if landscape image and from top and bottom if portrait image
			if($width_orig > $height_orig) {
				$x = round(($width_orig - $height_orig)/2);
				$width_orig = $height_orig;
			} elseif($height_orig > $width_orig) {
				$y = round(($height_orig - $width_orig)/2);
				$height_orig = $width_orig;
			}
			$cmdline = $cmd . '  -crop ' . escapeshellarg($width_orig . 'x' . $height_orig . '+' . $x . '+' . $y) . ' -thumbnail ' . escapeshellarg($width_new . 'x>') . ' ' . escapeshellarg($source) . ' ' . escapeshellarg($dest) . ' ';
			
		} else {
			// rectangular image
			$height_new = round(($width_new/$width_orig)*$height_orig);
			$cmdline = $cmd . ' -thumbnail ' . escapeshellarg($width_new . 'x>') . ' ' . escapeshellarg($source) . ' ' . escapeshellarg($dest) . ' ';
		}
		
		// if the original image is smaller than or equal to the dimensions we are resizing to, then we don't resize
		if (($landscape && ($width_new >= $width_orig)) || ($portrait && ($height_new >= $height_orig)) || ($square && ($width_new >= $width_orig))) {
			return -2;
		}
		
		exec($cmdline, $results, $return);
		
		if( $return > 0 ) {
			return false;
		} else { 
			return true;
		}

	}
	
	public function resizeImagick($source, $dest, $width_new, $height_new) {
		$image = new Imagick();
		$image->readImage($source);
		
		// get image properties
		$width_orig = $image->getImageWidth();
		$height_orig = $image->getImageHeight();
		
		// determine if the image is landscape or portrait and if we are making a square image
		$landscape = ($width_orig > $height_orig);
		$portrait = ($width_orig < $height_orig);
		$square = ($width_new == $height_new);;
		
		if (!$square) {
			$height_new = round(($width_new/$width_orig)*$height_orig);			
		}
		
		// if the original image is smaller than or equal to the dimensions we are resizing to, then we don't resize
		if (($landscape && ($width_new >= $width_orig)) || ($portrait && ($height_new >= $height_orig)) || ($square && ($width_new >= $width_orig))) {
			return -2;
		}
		
		if ($square) {
			// we use a different method for making square images
			$ret = $image->cropThumbnailImage($width_new, $height_new);			
		} else {
			$ret = $image->resizeImage($width_new, $height_new, null, 1);
		}
		
		$image->writeImage($dest);
		$image->clear();
		$image->destroy();
		if($ret) {
			return true;
		} else { 
			return false;
		}

	}

	public function getLib() {
		if (!$this->library || ($this->detectLib($this->library) === false)) {
			//~ foreach (array('imagick', 'imagemagick', 'gd') as $lib) {
			foreach (array('gd', 'imagemagick', 'imagick') as $lib) {
				if ($this->detectLib($lib) !== false) {
					$this->library = $lib;
					break;
				}
			}
		}
		return $this->library;
	}

	public function detectLib($lib, $detect_version = false) {
	
		if ($lib == 'gd') {
			$funcs = get_extension_funcs('gd');
			if (extension_loaded('gd') && is_array($funcs) && in_array('imagegd2', $funcs)) {
				if ($detect_version) {
					$version = '';				
					ob_start();
					@phpinfo(INFO_MODULES);
					$output = ob_get_contents();
					ob_end_clean();				
					if(preg_match("/GD Version[ \t]*(<[^>]+>[ \t]*)+([^<>]+)/s",$output,$matches)) {
						$version = $matches[2];
					}
					return $version;
				} else {
					return true;
				}
			} else {
				JLog::add('JvitalsImage::detectLib: GD Library not detected', JLog::WARNING, 'jvitalslib');
				return false;
			}
		} elseif ($lib == 'imagemagick') {
			if (!JvitalsHelper::execAvailable()) {
				JLog::add('JvitalsImage::detectLib: exec function is disabled, needed for ImageMagick detection', JLog::WARNING, 'jvitalslib');
				return false;
			}
			$cmd = 'convert';
			@exec('which convert', $res, $ret);
			if (!$ret) $cmd = $res[0];
			@exec($cmd . ' -version',  $result, $return);
			if(!$return && preg_match("/imagemagick[ \t]+([0-9\.]+)/i", $result[0], $matches)) {
				if ($detect_version) {
					return $matches[0];
				} else {
					return true;
				}
			} else {
				JLog::add('JvitalsImage::detectLib: ImageMagick Library not detected', JLog::WARNING, 'jvitalslib');
				return false;
			}
		} elseif ($lib == 'imagick') {
			if (extension_loaded('imagick') && class_exists('Imagick')) {
				if ($detect_version) {
					$version = '';				
					ob_start();
					@phpinfo(INFO_MODULES);
					$output = ob_get_contents();
					ob_end_clean();				
					if(preg_match("/imagick module version [ \t]*(<[^>]+>[ \t]*)+([^<>]+)/s",$output,$matches)){
						$version = $matches[2];
					}
					return $version;
				} else {
					return true;
				}
			} else {
				JLog::add('JvitalsImage::detectLib: Imagick PHP Extension not detected', JLog::WARNING, 'jvitalslib');
				return false;
			}
		}
		
		JLog::add('JvitalsImage::detectLib: wrong or empty image library', JLog::WARNING, 'jvitalslib');
		return false;
	}	
}
