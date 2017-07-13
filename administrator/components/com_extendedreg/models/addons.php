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

require_once ('default.php');

class ExtendedregModelAddons extends ExtendedregModelDefault {
	
	function getStateOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_STATE_PUBLISHED'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_STATE_UNPUBLISHED'));
		return $result;
	}
	
	function getTypeOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 'field', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_FIELD'));
		$result[] = JHtml::_('select.option', 'captcha', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_CAPTCHA'));
		$result[] = JHtml::_('select.option', 'validation', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_VALIDATION'));
		$result[] = JHtml::_('select.option', 'integration', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_INTEGRATION'));
		$result[] = JHtml::_('select.option', 'feature', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_FEATURE'));
		return $result;
	}
	
	function getAddonsList() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		$listOrder = $app->getUserStateFromRequest($option . '.list.ordering', 'filter_order', 'a.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest($option . '.list.direction', 'filter_order_Dir', 'ASC', 'word');
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('a.name', 'a.file_name', 'a.type', 'a.published', 'a.author', 'a.author_email', 'a.author_url', 'a.id'))) {
			$listOrder = 'a.name';
			$app->setUserState($option . '.list.ordering', $listOrder);
		}

		if (!in_array(mb_strtoupper($listDirn), array('ASC', 'DESC'))) {
			$listDirn = 'ASC';
			$app->setUserState($option . '.list.direction', $listDirn);
		}
		
		$filter_search = $app->getUserStateFromRequest($option . '.filter.addon_search', 'filter_search');
		$filter_search = trim(strip_tags($filter_search));
		if ($filter_search) {
			$filter_search = mb_strtolower($filter_search);
			$filter_search = preg_replace('~[^\w|\s|\d]+~i', ' ', $filter_search);
			$filter_search = preg_replace('~\s+~i', '%', trim($filter_search));
		}
		
		$filter_state = $app->getUserStateFromRequest($option . '.filter.addon_state', 'filter_state');
		$filter_type = $app->getUserStateFromRequest($option . '.filter.addon_type', 'filter_type');
		// ensure filter_type has a valid value.
		if (!in_array($filter_type, array('field','captcha','validation','integration','feature','*'))) {
			$filter_type = '*';
			$app->setUserState($option . '.filter.addon_type', $filter_type);
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS a.* FROM #__extendedreg_addons as a ";
		
		$where = array();
		if (!is_null($filter_state) && is_numeric($filter_state)) {
			$where[] = "a." . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote((int)$filter_state == 0 ? '0' : '1');
		}
		
		if (!is_null($filter_type) && $filter_type != '*') {
			$where[] = "a." . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote($filter_type);
		}
		
		if ($filter_search) {
			$searchEscaped = $this->dbo->Quote('%' . $filter_search . '%', false);
			$where[] = "(a." . $this->dbo->quoteName('name') . " LIKE " . $searchEscaped . " OR a." . $this->dbo->quoteName('file_name') . " LIKE " . $searchEscaped . ")";
		}
		
		if (count($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " ORDER BY " . $listOrder . " " . $listDirn;
		
		$this->dbo->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$result->items = $this->dbo->loadObjectList();
		if (!$result->items) $result->items = array();
		
		$this->dbo->setQuery('SELECT FOUND_ROWS();');
		$result->total = (int)$this->dbo->loadResult();
		
		return $result;
	}
	
	function installAddon() {
		$app = JFactory::getApplication();
		
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		
		$installtype = JRequest::getWord('installtype');
		if ($installtype == 'folder') {
			$this->_installAddonFromFolder();
		} elseif ($installtype == 'upload') {
			$this->_installAddonFromUpload();
		}
		
		// If we end up here we have error
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.install', false), JText::_('COM_EXTENDEDREG_ADDON_INSTALL_ERROR'), 'error');
		exit;
	}
	
	private function _installAddonFromFolder() {
		$app = JFactory::getApplication();
		
		// Get the path to the package to install
		$p_dir = JRequest::getString('install_directory');
		$p_dir = JPath::clean($p_dir);
		$errormsg = '';
		
		// Did you give us a valid directory?
		if (!$errormsg && !is_dir($p_dir)) {
			$errormsg = JText::_('COM_EXTENDEDREG_INSTALL_DIR_ERROR');
		}
		
		if (!$errormsg) {
			// Detect the package type
			$type = JInstallerHelper::detectType($p_dir);
			// Did you give us a valid package?
			if (!$type) {
				$errormsg = JText::sprintf('COM_EXTENDEDREG_NOPACK_IN_DIR_ERROR', $p_dir);
			}
		}
		
		if ($errormsg) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.install', false), $errormsg, 'error');
			exit;
		}
		
		$package['packagefile'] = null;
		$package['extractdir'] = null;
		$package['dir'] = $p_dir;
		$package['type'] = $type;
		$this->_doAcctualInstall($package);
	}
	

	private function _installAddonFromUpload() {
		$app = JFactory::getApplication();
		
		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');
		$errormsg = '';
		// Make sure that file uploads are enabled in php
		if (!$errormsg && !(bool) ini_get('file_uploads')) {
			$errormsg = JText::_('COM_EXTENDEDREG_WARNINSTALLFILE');
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!$errormsg && !extension_loaded('zlib')) {
			$errormsg = JText::_('COM_EXTENDEDREG_WARNINSTALLZLIB');
		}
		
		// If there is no uploaded file, we have a problem...
		if (!$errormsg && !is_array($userfile) ) {
			$errormsg = JText::_('COM_EXTENDEDREG_WARNINSTALLUPLOADERROR');
		}
		
		// Check if there was a problem uploading the file.
		if (!$errormsg && ($userfile['error'] || $userfile['size'] < 1)) {
			$errormsg = JText::_('COM_EXTENDEDREG_WARNINSTALLUPLOADERROR');
		}
		
		if ($errormsg) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.install', false), $errormsg, 'error');
			exit;
		}
		
		// Build the appropriate paths
		$config = JFactory::getConfig();
		$tmp_path = $config->get('tmp_path');
		$tmp_dest = $tmp_path . DIRECTORY_SEPARATOR . $userfile['name'];
		$tmp_src = $userfile['tmp_name'];

		// Move uploaded file
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest);
		$this->_doAcctualInstall($package);
	}
	
	private function _doAcctualInstall($package) {
		$app = JFactory::getApplication();
		
		// Was the package unpacked?
		if (!$package) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.install', false), JText::_('COM_EXTENDEDREG_NO_PACKAGE_FOUND_ERROR'), 'error');
			exit;
		}
		
		// Get an installer instance
		$installer = JInstaller::getInstance();
		
		// Try to load the adapter object
		require_once(JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'adapters' . DIRECTORY_SEPARATOR . mb_strtolower($package['type']) . '.php');
		$class = 'erInstaller' . ucfirst(mb_strtolower($package['type']));
		if (!class_exists($class)) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.install', false), JText::sprintf('COM_EXTENDEDREG_INSTALL_ADAPTER_ERROR', $package['type']), 'error');
			exit;
		}
		$adapter = new $class();
		$adapter->parent =& $installer;
		$adapter->returnUrl = 'index.php?option=com_extendedreg&task=addons.install';
		$installer->setAdapter($package['type'], $adapter);
		
		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = JText::sprintf('COM_EXTENDEDREG_ADDON_INSTALLEXT', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_' . mb_strtoupper($package['type'])), JText::_('COM_EXTENDEDREG_ERROR'));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('COM_EXTENDEDREG_ADDON_INSTALLEXT', JText::_('COM_EXTENDEDREG_ADDONS_TYPE_' . mb_strtoupper($package['type'])), JText::_('COM_EXTENDEDREG_SUCCESS'));
			$result = true;
		}

		// Cleanup the install files
		if (!is_file($package['packagefile'])) {
			$config = JFactory::getConfig();
			$tmp_path = $config->get('tmp_path');
			$package['packagefile'] = $tmp_path . DIRECTORY_SEPARATOR . $package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.install', false), $msg, ($result ? 'notice' : 'error'));
		exit;
	}
	
	function loadAddon($id) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("*")
			->from("#__extendedreg_addons")
			->where($this->dbo->quoteName('id') . " = " . (int)$id);
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObject();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) $result = new stdClass;
		return $result;
	}
	
	function setAddonParams($id, $params) {
		$this->dbo->setQuery("UPDATE #__extendedreg_addons SET " . $this->dbo->quoteName('params') . " = " . $this->dbo->Quote($params) . " WHERE " . $this->dbo->quoteName('id') . " = " . (int)$id);
		return $this->dbo->execute();
	}
	
	function uninstallAddon() {
		static $adapters;
		
		$app = JFactory::getApplication();
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		if (count($ids)) {
			jimport('joomla.installer.installer');
			jimport('joomla.installer.helper');
			
			// Get an installer instance
			$installer = JInstaller::getInstance();
			
			/*
			$this->dbo->setQuery("SELECT * FROM #__extendedreg_addons WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids) . ") ORDER BY " . $this->dbo->quoteName('type'));
			$addons = $this->dbo->loadObjectList();
			
			if ($addons) {
				foreach ($addons as $obj) {
					if (!isset($adapters[$obj->type])) {
						// Try to load the adapter object
						require_once(JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'adapters' . DIRECTORY_SEPARATOR . mb_strtolower($obj->type) . '.php');
						$class = 'erInstaller' . ucfirst(mb_strtolower($obj->type));
						if (!class_exists($class)) {
							$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.browse', false), JText::sprintf('COM_EXTENDEDREG_INSTALL_ADAPTER_ERROR', $obj->type), 'error');
							exit;
						}
						$adapters[$obj->type] = new $class();
						$adapters[$obj->type]->parent =& $installer;
						$adapters[$obj->type]->returnUrl = 'index.php?option=com_extendedreg&task=addons.browse';
					}
					$adapters[$obj->type]->uninstall($obj);
				}
				
				$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.browse', false), JText::sprintf('COM_EXTENDEDREG_ADDON_UNINSTALLEXT', $obj->type, JText::_('COM_EXTENDEDREG_SUCCESS')), 'notice');
				exit;
			}
			*/
			
			foreach ($ids as $id) {
				// Joomla installer invokes the adapter language files only upon install/update but not uninstall...
				JFactory::getLanguage()->load('files_adapter_eraddon', JApplicationHelper::getClientInfo(0)->path);
				$installer->uninstall('eraddon', $id);
			}
			$app->enqueueMessage(JText::sprintf('COM_EXTENDEDREG_ADDON_UNINSTALLEXT', '', JText::_('COM_EXTENDEDREG_SUCCESS')), 'notice');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.browse', false));
			exit;
		}
		
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=addons.browse', false));
		exit;
	}
	
	function setAddonPublished(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		$canChange = JvitalsHelper::canDo('addons.manage', 'com_extendedreg');
		if (!$canChange) {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$this->dbo->setQuery("UPDATE #__extendedreg_addons SET " . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
}