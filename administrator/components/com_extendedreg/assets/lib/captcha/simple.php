<?php
/**
 * @package		ExtendedReg
 * @version		2.11
 * @date		2014-03-29
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erCaptchaSimple extends JObject implements erCaptchaInterface {
	private $conf;
	
	/** Width of the image */
	private $width = 200;

	/** Height of the image */
	private $height = 70;

	/** Dictionary word file (empty for randnom text) */
	private $wordsFile = null;

	/** Min word length (for non-dictionary random text generation) */
	private $minWordLength = 5;

	/**
	* Max word length (for non-dictionary random text generation)
	* 
	* Used for dictionary words indicating the word-length
	* for font-size modification purposes
	*/
	private $maxWordLength = 8;

	/** Sessionname to store the original text */
	private $session_private = 'extreg_captcha_code';

	/** Background color in RGB-array */
	private $backgroundColor = array(255, 255, 255);
	/** Background transparent */
	private $backgroundTransparent = false;

	/** Foreground colors in RGB-array */
	private $colors = array(
		array(27,78,181), // blue
		array(22,163,35), // green
		array(214,36,7), // red
	);

	/** Shadow color in RGB-array or false */
	private $shadowColor = false; //array(0, 0, 0);

	/**
	* Font configuration
	*
	* - font: TTF file
	* - spacing: relative pixel space between character
	* - minSize: min font size
	* - maxSize: max font size
	*/
	private $fonts = null;

	/** Wave configuracion in X and Y axes */
	private $Yperiod = 12;
	private $Yamplitude = 14;
	private $Xperiod = 11;
	private $Xamplitude = 5;

	/** letter rotation clockwise */
	private $maxRotation = 8;

	/**
	* Internal image size factor (for better image quality)
	* 1: low, 2: medium, 3: high
	*/
	private $scale = 2;

	/** 
	* Blur effect for better image quality (but slower image processing).
	* Better image results with scale=3
	*/
	private $blur = false;

	/** Image format: jpeg or png */
	private $imageFormat = 'png';

	/** align CAPTCHA code 0 - none, 1 - left, 2 - center, 3 - right */
	private $align_captcha = 0;

	/** GD image */
	private $im;
	
	function __construct($conf) {
		$this->conf = $conf;

		$folder_captcha_class = dirname(__file__) . DIRECTORY_SEPARATOR . 'simple';

		if ((int)$this->conf->simple_captcha_width && (int)$this->conf->simple_captcha_width > 0) $this->width = (int)$this->conf->simple_captcha_width;
		if ((int)$this->conf->simple_captcha_height && (int)$this->conf->simple_captcha_height > 0) $this->height = (int)$this->conf->simple_captcha_height;

		if (isset($config['wordsFile']) && is_file($config['wordsFile'])) {
			$this->wordsFile = $config['wordsFile'];
		}
		
		$wordsFile = $folder_captcha_class . DIRECTORY_SEPARATOR . 'words' . DIRECTORY_SEPARATOR . $this->conf->simple_captcha_word_file;
		if ((int)$this->conf->simple_captcha_use_random || !is_file($wordsFile)) {
			if ((int)$this->conf->simple_captcha_min_length && (int)$this->conf->simple_captcha_min_length > 0) $this->minWordLength = (int)$this->conf->simple_captcha_min_length;
			if ((int)$this->conf->simple_captcha_max_length && (int)$this->conf->simple_captcha_max_length > 0) $this->maxWordLength = (int)$this->conf->simple_captcha_max_length;
			if ((int)$this->maxWordLength <= (int)$this->minWordLength) $this->maxWordLength = (int)$this->minWordLength + 1;
			$this->wordsFile = null;
		} else {
			$this->wordsFile = $wordsFile;
		}
		
		$this->backgroundTransparent = (boolean)((int)$this->conf->simple_captcha_bg_transparent ? true : false);
		if (!(int)$this->conf->simple_captcha_bg_transparent && trim($this->conf->simple_captcha_bgcolor)) {
			$this->backgroundColor = $this->color_hex_to_rgb($this->conf->simple_captcha_bgcolor);
		}
		
		$captcha_colors = array('#FF0000','#00FF00','#0000FF');
		if (trim($this->conf->simple_captcha_colors)) {
			$captcha_colors = explode(';', $this->conf->simple_captcha_colors);
		}
		if (is_array($captcha_colors) && count($captcha_colors)) {
			$this->colors = array();
			foreach ($captcha_colors as $color) {
				$this->colors[] = $this->color_hex_to_rgb($color);
			}
		}
		if (is_null($this->fonts)) {
			$this->fonts = array(
				'Antykwa' => array('spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'AntykwaBold.ttf'),
				'Candice' => array('spacing' =>-1.5,'minSize' => 28, 'maxSize' => 31, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'Candice.ttf'),
				'DingDong' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'Ding-DongDaddyO.ttf'),
				'Duality' => array('spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'Duality.ttf'),
				'Jura' => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'Jura.ttf'),
				'Times' => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'TimesNewRomanBold.ttf'),
				'VeraSans' => array('spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => $folder_captcha_class.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'VeraSansBold.ttf')
			);
		}
	}
	
	public function validate($post) {
		$session = JFactory::getSession();
		$captcha_code = $session->get($this->session_private);
		if (array_key_exists('captcha-code', $post) && ($captcha_code != trim($post['captcha-code']) || mb_strlen($post['captcha-code']) == 0 )) {
			return false;
		}
		return true;
	}
	
	public function write() {
		ob_start();
		?>
		<script language="JavaScript">
			function erChangeCaptcha() {
				var scdiv = document.getElementById('div_captcha_img');
				if (scdiv) {
					var set_rand = Math.floor(Math.random()*10000000001);
					var imgurl = '<?php echo JURI::base(true); ?>/index.php?option=com_extendedreg&task=captcha&rand=' + set_rand;
					var scimg = document.getElementById('captcha_img');
					scimg.src = imgurl;
				}
			}
		</script>
		<div id="div_captcha">
			<div id="div_captcha_info"><?php echo JText::_('COM_EXTENDEDREG_CAPTCHA_INFO'); ?></div>
			<div id="div_captcha_img"><img src="<?php echo JRoute::_('index.php?option=com_extendedreg&task=captcha&rand=' . time(), false); ?>" alt="" id="captcha_img" style="max-width: 95%;" /></div>
			<div id="div_captcha_new">
				<a href="javascript:void(0);" onclick="erChangeCaptcha();" id="change-image"><?php echo JText::_('COM_EXTENDEDREG_CAPTCHA_NOT_READABLE'); ?></a>
			</div>
			<div style="margin-top:5px;" id="div_captcha_code"><input type="text" name="captcha-code" id="captcha-code" class="inputbox" /></div>
		</div>
		<?php
		return ob_get_clean();
	}
		
	public function output() {
		$this->CreateImage();
	}
	
	private function CreateImage() {
		$ini = microtime(true);

		/** Initialization */
		$this->ImageAllocate();

		/** Text insertion */
		$text = $this->GetCaptchaText();
		$fontcfg = $this->fonts[array_rand($this->fonts)];
		$this->WriteText($text, $fontcfg);

		$session = JFactory::getSession();
		$session->set($this->session_private, $text);

		/** Transformations */
		$this->WaveImage();
		if ($this->blur) {
			imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
		}
		$this->ReduceImage();

		/** Set the background transparent if it is set that way */
		$this->TransparentBackground();

		/** Output */
		$this->WriteImage();
		$this->Cleanup();
	}

	/**
	* Creates the image resources
	*/
	private function ImageAllocate() {
		// Cleanup
		if (!empty($this->im)) {
			imagedestroy($this->im);
		}
		$this->im = imagecreatetruecolor(($this->width * $this->scale), ($this->height * $this->scale));

		// Background color
		$this->GdBgColor = imagecolorallocate($this->im, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
		imagefilledrectangle($this->im, 0, 0, ($this->width * $this->scale), ($this->height * $this->scale), $this->GdBgColor);

		// Foreground color
		$color = $this->colors[mt_rand(0, sizeof($this->colors) - 1)];
		$this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

		// Shadow color
		if (!empty($this->shadowColor)) {
			$this->GdShadowColor = imagecolorallocate($this->im, $this->shadowColor[0], $this->shadowColor[1], $this->shadowColor[2]);
		}
	}

	/**
	* Text generation
	*
	* @return string Text
	*/
	private function GetCaptchaText() {
		$text = $this->GetDictionaryCaptchaText();
		if (!$text) {
			$text = $this->GetRandomCaptchaText();
		}
		return $text;
	}

	/**
	* Random text generation
	*
	* @return string Text
	*/
	private function GetRandomCaptchaText($length = null) {
		if (empty($length)) {
			$length = rand($this->minWordLength, $this->maxWordLength);
		}

		$words = "abcdefghijlmnopqrstvwyz";
		$vocals = "aeiou";

		$text = "";
		$vocal = rand(0, 1);
		for ($i=0; $i<$length; $i++) {
			if ($vocal) {
				$text .= substr($vocals, mt_rand(0, 4), 1);
			} else {
				$text .= substr($words, mt_rand(0, 22), 1);
			}
			$vocal = !$vocal;
		}
		return $text;
	}

	/**
	* Random dictionary word generation
	*
	* @param boolean $extended Add extended "fake" words
	* @return string Word
	*/
	private function GetDictionaryCaptchaText($extended = false) {
		if (empty($this->wordsFile)) {
			return false;
		}
		// open the words file
		$fp = fopen($this->wordsFile, "r");
		// get the length of the words file
		$length = filesize($this->wordsFile);
		if (!$length) {
			return false;
		}
		// move the pointer to a random position
		$line = rand(0,$length-1);
		if (fseek($fp, $line) == -1) {
			return false;
		}
		// move the pointer to the ned of the current line
		$text = fgets($fp);
		// if the end of the files is reached move the pointer to the first line
		if (feof($fp)) {
			rewind($fp);
		}
		// read the text to use as the captcha code
		$text = fgets($fp);
		// remove the spaces
		$text = trim($text);
		fclose($fp);

		/** Change ramdom volcals */
		if ($extended) {
			$text = str_split($text, 1);
			$vocals = array('a', 'e', 'i', 'o', 'u');
			foreach ($text as $i => $char) {
				if (mt_rand(0, 1) && in_array($char, $vocals)) {
					$text[$i] = $vocals[mt_rand(0, 4)];
				}
			}
			$text = implode('', $text);
		}

		return $text;
	}

	/**
	* Text insertion
	*/
	private function WriteText($text, $fontcfg = array()) {
		if (empty($fontcfg)) {
			// Select the font configuration
			$fontcfg = $this->fonts[array_rand($this->fonts)];
		}
		$fontfile = $fontcfg['font'];

		/** Change font-size for shortest/longest words: 10% for each glyp */
		$lettersMissing = $this->maxWordLength - strlen($text);
		$fontSizefactor = 1 + ($lettersMissing * 0.1);

		// Text generation (char by char)
		// get the image box
		$length = strlen($text);

		// letters to write
		$letters = array();
		for ($i=0; $i<$length; $i++) {
			$current_letter = array();
			$current_letter['letter'] = substr($text, $i, 1);
			$current_letter['degree'] = rand($this->maxRotation*-1, $this->maxRotation);
			$current_letter['fontsize'] = rand($fontcfg['minSize'], $fontcfg['maxSize']) * $this->scale * $fontSizefactor;
			$current_letter['fontfile'] = $fontfile;
			$letter_box = imagettfbbox($current_letter['fontsize'], $current_letter['degree'], $current_letter['fontfile'], $current_letter['letter']);
			$current_letter['width'] = max($letter_box[2] - $letter_box[0], $letter_box[4] - $letter_box[6]);
			$current_letter['height'] = max($letter_box[1] - $letter_box[7], $letter_box[3] - $letter_box[5]);
			$current_letter['top'] = min($letter_box[5], $letter_box[7]);
			$current_letter['left'] = min($letter_box[0], $letter_box[7]);
			$letters[] = $current_letter;
		}
		
		switch ($this->align_captcha) {
			case 1:
				// left aligned
				$letters = $this->checkFontSize($letters);
				$dimm = $this->getCodeDimmensions($letters);
				$x = ($letters[0]['left'] < 0 ? $letters[0]['left'] * -1 : 0);
				$y = $this->height * $this->scale - floor(($this->height * $this->scale - $dimm['height']) / 2);
				break;
			case 2:
				// center aligned
				$letters = $this->checkFontSize($letters);
				$dimm = $this->getCodeDimmensions($letters);
				$x = floor(($this->width * $this->scale - $dimm['width']) / 2);
				$y = $this->height * $this->scale - floor(($this->height * $this->scale - $dimm['height']) / 2);
				break;
			case 3:
				// right aligned
				$letters = $this->checkFontSize($letters);
				$dimm = $this->getCodeDimmensions($letters);
				$x = floor($this->width * $this->scale - $dimm['width']);
				$y = $this->height * $this->scale - floor(($this->height * $this->scale - $dimm['height']) / 2);
				break;
			case 0:
			default:
				// not aligned
				$x = 20 * $this->scale;
				$y = round(($this->height * 27 / 40) * $this->scale);
				break;
		}
		foreach($letters as $letter) {
			if ($this->shadowColor) {
				imagettftext($this->im, $letter['fontsize'], $letter['degree'], $x + $this->scale, $y + $this->scale, $this->GdShadowColor, $letter['fontfile'], $letter['letter']);
			}
			imagettftext($this->im, $letter['fontsize'], $letter['degree'], $x, $y, $this->GdFgColor, $letter['fontfile'], $letter['letter']);
			$x += $letter['width'] + 1;
		}
	}

	private function getCodeDimmensions($letters = array()) {
		$dimm = array();
		$dimm['width'] = $letters[0]['left'] < 0 ? $letters[0]['left'] * -1 : 0;
		$dimm['height'] = 0;
		foreach($letters as $letter) {
			$dimm['width'] += $letter['width'] + 1;
			$dimm['height'] = max($dimm['height'], $letter['height']);
		}
		return $dimm;
	}

	private function checkFontSize($letters = array()) {
		$dimm = $this->getCodeDimmensions($letters);
		if ($dimm['width'] <= $this->width && $dimm['height'] <= $this->height) {
			// found the correct dimensions
		} else {
			$letters = $this->changeFontSize($letters, -1);
			$letters = $this->checkFontSize($letters);
		}
		return $letters;
	}

	private function changeFontSize($letters = array(), $val = 0) {
		foreach($letters as $key=>$letter) {
			$letters[$key]['fontsize'] = $letters[$key]['fontsize'] + $val;
			$letter_box = imagettfbbox($letters[$key]['fontsize'], $letters[$key]['degree'], $letters[$key]['fontfile'], $letters[$key]['letter']);
			$letters[$key]['width'] = max($letter_box[2] - $letter_box[0], $letter_box[4] - $letter_box[6]);
			$letters[$key]['height'] = max($letter_box[1] - $letter_box[7], $letter_box[3] - $letter_box[5]);
		}
		return $letters;
	}

	/**
	* Wave filter
	*/
	private function WaveImage() {
		// X-axis wave generation
		$xp = $this->scale*$this->Xperiod*rand(1,3);
		$k = rand(0, 100);
		for ($i = 0; $i < ($this->width*$this->scale); $i++) {
			imagecopy($this->im, $this->im, $i - 1, sin($k + $i / $xp) * ($this->scale * $this->Xamplitude), $i, 0, 1, $this->height * $this->scale);
		}

		// Y-axis wave generation
		$k = rand(0, 100);
		$yp = $this->scale * $this->Yperiod * rand(1,2);
		for ($i = 0; $i < ($this->height * $this->scale); $i++) {
			imagecopy($this->im, $this->im, sin($k + $i / $yp) * ($this->scale * $this->Yamplitude), $i - 1, 0, $i, $this->width * $this->scale, 1);
		}
	}

	/**
	* Reduce the image to the final size
	*/
	private function ReduceImage() {
		$imResampled = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($imResampled, $this->im, 0, 0, 0, 0, $this->width, $this->height, $this->width * $this->scale, $this->height * $this->scale);
		imagedestroy($this->im);
		$this->im = $imResampled;
	}

	/**
	* Set the background transparent if it is set that way
	*/
	private function TransparentBackground() {
		if ($this->backgroundTransparent) {
			imagecolortransparent($this->im, $this->GdBgColor);
		}
	}

	/**
	* File generation
	*/
	private function WriteImage() {
		if ($this->imageFormat == 'png') {
			header("Content-type: image/png");
			imagepng($this->im);
		} else {
			header("Content-type: image/jpeg");
			imagejpeg($this->im, null, 80);
		}
	}

	/**
	* Cleanup
	*/
	private function Cleanup() {
		imagedestroy($this->im);
	}
	
	private function color_hex_to_rgb( $hex_code = '') {
		if ( substr(trim($hex_code),0,1) == '#' ) {
			$hex_code = substr(trim($hex_code),1);
		}
		$rgb = array(
			base_convert(substr($hex_code,0,2), 16, 10),
			base_convert(substr($hex_code,2,2), 16, 10),
			base_convert(substr($hex_code,4,2), 16, 10)
		);
		return $rgb;
	}
}