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

class erHelperLanguage {
	
	public static function install($base, $tag, $override = false) {
		$client = ($base == JPATH_ADMINISTRATOR ? 'admin' : 'site');
		$_destPath = $base . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $tag;
		if (!is_dir($_destPath)) {
			return false;
		}
		$_destFileArray = array();
		$_destFileArray[] = $_destPath . DIRECTORY_SEPARATOR . $tag . '.com_extendedreg.ini';
		$_destFileArray[] = $_destPath . DIRECTORY_SEPARATOR . $tag . '.com_extendedreg.sys.ini';
		
		foreach ($_destFileArray as $dest_file) {
			if (is_file($dest_file) && !$override) {
				continue;
			}
			$source_file = JvitalsDefines::comBackPath('com_extendedreg') . 'language' . DIRECTORY_SEPARATOR . $client . DIRECTORY_SEPARATOR . basename($dest_file);
			if (is_file($source_file)) {
				if (is_file($dest_file) && $override) {
					JFile::delete($dest_file);
				}
				JFile::copy($source_file, $dest_file);
			}
		}
		return true;
	}
	
	public static function installAllAvailable($override = false) {
		// initialize the language class
		$lang = JFactory::getLanguage(JPATH_ADMINISTRATOR);
		
		// check the administrator languages
		$languages = $lang->getKnownLanguages();
		foreach($languages as $lg) {
			erHelperLanguage::install(JPATH_ADMINISTRATOR, $lg['tag'], $override);
		}
		
		// check the site languages
		$languages = $lang->getKnownLanguages(JPATH_SITE);
		foreach($languages as $lg) {
			erHelperLanguage::install(JPATH_SITE, $lg['tag'], $override);
		}
	}
	
	public static function getLangsForJavascript() {
		static $strings;
		if (!$strings) {
			$strings = array();
			$lang = JFactory::getLanguage();
			$path = JLanguage::getLanguagePath(JPATH_BASE, $lang->getTag());
			
			$langsToLoad = array('com_extendedreg');
			
			foreach ($langsToLoad as $extension) {
				$filename = ( $extension == 'joomla' ) ?  $lang->getTag() : $lang->getTag() . '.' . $extension ;
				$filename = $path . DIRECTORY_SEPARATOR . $filename . '.ini';
				
				if ($content = @file_get_contents($filename)) {
					//Take off BOM if present in the ini file
					if ( $content[0] == "\xEF" && $content[1] == "\xBB" && $content[2] == "\xBF" ) {
						$content = substr($content, 3);
					}
					$registry = new JRegistry();
					$registry->loadString($content, 'INI');
					
					$newStrings = $registry->toArray();
					if (is_array($newStrings)) {
						$strings = array_merge($strings, $newStrings);
					}
				}
			}
			if (!$strings) $strings = array();
			$strings = array_map("strip_tags", $strings);
		}
		
		return $strings;
	}
	
	public static function load() {
		static $loaded;
		
		if (!$loaded) {
			$lang = JFactory::getLanguage();
			$lang->load('com_users');
			$lang->load('com_extendedreg');
			$loaded = true;
		}
	}

}