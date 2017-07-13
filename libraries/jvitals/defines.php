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

class JvitalsDefines {

	public static function joomlaVersion() {
		$JVersion = new JVersion();
		$version = $JVersion->getShortVersion();
		$version = preg_replace('~[^\d|\.]~', '', $version);
		return $version;
	}
	
	public static function compatibleMode() {
		$mode = '15';
		$version = self::joomlaVersion();
		if (version_compare($version, '4.0.0', 'ge')) {
			$mode = '40>';
		} elseif (version_compare($version, '3.0.0', 'ge')) {
			$mode = '30>';
		} elseif (version_compare($version, '2.5.0', 'ge')) {
			$mode = '25>';
		}
		return $mode;
	}
	
	public static function vendorPath($live = false) {
		if ($live) return JURI::root(true) . '/libraries/jvitals/vendor/';
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
	}
	
	public static function comFrontPath($com, $live = false) {
		if ($live) return JURI::root(true) . '/components/' . $com . '/';
		return JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $com . DIRECTORY_SEPARATOR;
	}
	
	public static function comBackPath($com, $live = false) {
		if ($live) return JURI::root(true) . '/administrator/components/' . $com . '/';
		return JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $com . DIRECTORY_SEPARATOR;
	}
	
	public static function componentVersion($com) {
		$path1 = self::comBackPath($com) . str_replace('com_', '', $com) . '.xml';
		$path2 = self::comBackPath($com) . 'manifest.xml';
		$path3 = self::comBackPath($com) . $com . '.xml';
		
		if (is_file($path1)) {
			$realPath = $path1;
		} elseif (is_file($path2)) {
			$realPath = $path2;
		} elseif (is_file($path3)) {
			$realPath = $path3;
		}
		
		$xml = simplexml_load_file($realPath);
		return (string)$xml->version;
	}
	
}
