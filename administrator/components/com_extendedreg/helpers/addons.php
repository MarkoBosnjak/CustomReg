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

class erHelperAddons {
	
	public static function loadAddons($type, $published_only = true) {
		static $addons;
		if (!$addons) $addons = array();
		if (!isset($addons[$type.(int)$published_only])) {
			$dbo = JFactory::getDBO();
			$dbo->setQuery("SELECT * FROM #__extendedreg_addons 
				WHERE " . $dbo->quoteName('type') . " = " . $dbo->Quote($type) . "
				" . ($published_only ? " AND " . $dbo->quoteName('published') . " = " . $dbo->Quote('1') : ""));
			$res = $dbo->loadObjectList();
			if (!$res) $res = array();
			$addons[$type.(int)$published_only] = $res;
		}
		return $addons[$type.(int)$published_only];
	}
	
	public static function getFieldType($record, $stopOnError = true) {
		static $fieldsLoaded;
		if (!$fieldsLoaded) $fieldsLoaded = array();
		if (!isset($record->id)) $record->id = 0;
		
		$name = trim(mb_strtolower($record->type));
		$includePath = JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . JFile::makeSafe($name) . '.php';
		if (is_file($includePath)) {
			require_once ($includePath);
		} else {
			if ($stopOnError) {
				JError::raiseError(83006, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_FILENOTFOUND', $includePath));
				jexit();
			} else {
				return null;
			}
		}
		if (!isset($fieldsLoaded[$record->id . $name])) {
			$classname = 'erField' . ucfirst($name);
			if (class_exists($classname)) {
				$tmp = new $classname($record);
				$fieldsLoaded[$record->id . $name] = $tmp;
			} else {
				if ($stopOnError) {
					JError::raiseError(83007, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_CLASSNOTFOUND', $classname));
					jexit();
				} else {
					return null;
				}
			}
		}
		return $fieldsLoaded[$record->id . $name];
	}
	
	public static function getCaptchaLib($stopOnError = true) {
		static $library;
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$name = trim(mb_strtolower($conf->use_captcha));
		$includePath = JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . JFile::makeSafe($name) . '.php';
		if (is_file($includePath)) {
			require_once ($includePath);
		} else {
			if ($stopOnError) {
				JError::raiseError(83006, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_FILENOTFOUND', $includePath));
				jexit();
			} else {
				return null;
			}
		}
		if (!$library) {
			$classname = 'erCaptcha' . ucfirst($name);
			if (class_exists($classname)) {
				$library = new $classname($conf);
			} else {
				if ($stopOnError) {
					JError::raiseError(83007, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_CLASSNOTFOUND', $classname));
					jexit();
				} else {
					return null;
				}
			}
		}
		return $library;
	}
	
	public static function getFieldValidation($lib, $field = null, $stopOnError = true) {
		static $validationsLoaded;
		if (!$validationsLoaded) $validationsLoaded = array();
		
		$key = (int)$lib->id;
		if ($field) $key .= '_' . $field->id;
		$name = trim(mb_strtolower($lib->file_name));
		$includePath = JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'validations' . DIRECTORY_SEPARATOR . JFile::makeSafe($name) . '.php';
		
		if (is_file($includePath)) {
			require_once ($includePath);
		} else {
			if ($stopOnError) {
				JError::raiseError(83006, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_FILENOTFOUND', $includePath));
				jexit();
			} else {
				return null;
			}
		}
		
		if (!isset($validationsLoaded[$key]) || !$validationsLoaded[$key]) {
			$classname = 'erValidation' . ucfirst($name);
			if (class_exists($classname)) {
				$tmp = new $classname($lib, ($field ? $field : null));
				$validationsLoaded[$key] = $tmp;
			} else {
				if ($stopOnError) {
					JError::raiseError(83007, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_CLASSNOTFOUND', $classname));
					jexit();
				} else {
					return null;
				}
			}
		}
		
		return $validationsLoaded[$key];
	}
	
	public static function getIntegration($record, $stopOnError = true) {
		static $integrationsLoaded;
		if (!$integrationsLoaded) $integrationsLoaded = array();
		
		$name = trim(mb_strtolower($record->file_name));
		$includePath = JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . JFile::makeSafe($name) . '.php';
		
		if (is_file($includePath)) {
			require_once ($includePath);
		} else {
			if ($stopOnError) {
				JError::raiseError(83006, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_FILENOTFOUND', $includePath));
				jexit();
			} else {
				return null;
			}
		}
		
		if (!isset($integrationsLoaded[$name])) {
			$classname = 'erIntegration' . ucfirst($name);
			if (class_exists($classname)) {
				$tmp = new $classname($record);
				$integrationsLoaded[$name] = $tmp;
			} else {
				if ($stopOnError) {
					JError::raiseError(83007, JText::sprintf('COM_EXTENDEDREG_ADDONSHELPER_CLASSNOTFOUND', $classname));
					jexit();
				} else {
					return null;
				}
			}
		}
		
		return $integrationsLoaded[$name];
	}
}