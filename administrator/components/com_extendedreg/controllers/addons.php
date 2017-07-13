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

class ExtendedregController extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();

		$this->registerTask('unpublish', 'publish');
		$this->registerTask('apply', 'save');
	}
	
	function display($cachable = false, $urlparams = false) {
		$view = $this->getView('default', 'html', '');
		$view->setLayout('default');
		$view->display();
	}
	
	function browse() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
		$view = $this->getView('addons', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function install() {
		if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$this->setMessage(JText::_('COM_EXTENDEDREG_ADDONS_INSTALL_REDIRECT'));
		$this->setRedirect(JRoute::_('index.php?option=com_installer', false));
		/*
		$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
		$view = $this->getView('addonsinstall', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
		*/
	}
	
	function doInstall() {
		if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
		$model->installAddon();
	}
	
	function doUninstall() {
		if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
		$model->uninstallAddon();
	}
	
	function settings() {
		if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}		
		$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
		$view = $this->getView('addonedit', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function save() {
		if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$cid = (int)JRequest::getVar('cid');
		if (!$cid) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
		$addon = $model->loadAddon($cid);
		
		if (!$addon || !(int)$addon->id || $addon->type != 'integration') {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$lib = erHelperAddons::getIntegration($addon);
		if ($model->setAddonParams($cid, $lib->prepareParams())) {
			$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_ADDON_SAVED'));
		} else {
			$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_ADDON_NOTSAVED'));
		}
		$task = JRequest::getVar('task');
		if ($task == 'apply') {
			$url = 'index.php?option=com_extendedreg&task=addons.settings&cid=' . $cid;
		} else {
			$url = 'index.php?option=com_extendedreg&task=addons.browse';
		}
		$this->setRedirect($url);
	}
	
	
	function publish() {
		if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('publish' => 1, 'unpublish' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = JvitalsHelper::loadModel('extendedreg', 'Addons');
			// Change the state of the records.
			if (!$model->setAddonPublished($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_ADDON_PUBLISHED'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_ADDON_UNPUBLISHED'));
				}
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=addons.browse', false));
	}

}
