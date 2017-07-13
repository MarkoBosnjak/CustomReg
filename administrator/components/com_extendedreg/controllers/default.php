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
	
	function display($cachable = false, $urlparams = false) {
		$view = $this->getView('default', 'html', '');
		$view->setLayout('default');
		$view->display();
	}
	
	function about() {
		$view = $this->getView('about', 'html', '');
		$view->setLayout('default');
		$view->display();
	}
	
	function settings() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('settings', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function save_settings() {
		$app = JFactory::getApplication();
		$config_group = $app->input->getCmd('group', 'default');
		
		if (JvitalsHelper::canDo('core.admin', 'com_extendedreg')) {
			$model = JvitalsHelper::loadModel('extendedreg', 'Default');
			$model->configSave();
			$this->setMessage(JText::_('COM_EXTENDEDREG_SETTINGS_SAVED_MSG'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=default.settings&group=' . $config_group, false));
	}
	
	function stats() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('stats', 'html', '');
		$view->setLayout(JRequest::getVar('layout', 'default'));
		$view->setModel($model, true);
		$view->display();
	}
	
	function checkemail() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$email = trim(JRequest::getVar('s'));
		
		$result = JvitalsHelper::validateEmail($email);
		if (!$result) {
			echo '1 ';
			exit;
		}
		
		$blacklist = array();
		$conf = $model->getConfObj();
		if ($conf->blacklist_emails) {
			$blacklist = explode("\n", $conf->blacklist_emails);
		}
		
		foreach ($blacklist as $test) {
			$test = str_replace('*', '.*?', trim($test));
			if (preg_match('~^' . addslashes($test) . '$~smi', $search)) {
				$result = false;
				break;
			}
		}
		if (!$result) {
			echo '1 ';
			exit;
		}
		
		echo '0 ';
		exit;
	}
	
	function export_activity() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('export_activity', 'raw', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function export_inactive() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('export_inactive', 'raw', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function export_ipaddr() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('export_ipaddr', 'raw', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function clear_stats() {
		$view = $this->getView('clear_stats', 'html', '');
		$view->setLayout('default');
		$view->display();
		exit;
	}
	
	function doClearStats() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$model->clearStats();
	}
	
	function purgeusers() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			exit;
		}
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = JvitalsHelper::loadModel('extendedreg', 'Users');
			// Change the state of the records.
			if (!$model->delete($ids)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_USER_DELETED'));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=stats&layout=' . JRequest::getVar('layout'), false));
	}
	
	function purgeusers2() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			exit;
		}
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = JvitalsHelper::loadModel('extendedreg', 'Default');
			// Change the state of the records.
			if (!$model->deleteUsersByStats($ids)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_USER_DELETED'));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=stats&layout=' . JRequest::getVar('layout'), false));
	}
	
	function checkunique() {
		$session = JFactory::getSession();
		$user_id = (int)$session->get('erAdminLoadedUser', 0, 'extendedreg');
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		$value = trim(JRequest::getVar('s'));
		$id = (int)JRequest::getVar('id');
		
		if (!strlen($value)) {
			echo '0 ';
			exit;
		}
		
		$dbo = JFactory::getDBO();
		if ($id > 0) {
			
			$field = $model->loadField($id);
			if (!(int)$field->id) {
				echo '0 ';
				exit;
			}
			
			$dbo->setQuery("SELECT count(*) FROM #__extendedreg_users WHERE " . $dbo->quoteName($field->name) . " = " . $dbo->Quote($value) . " AND " . $dbo->quoteName('user_id') . " != " . (int)$user_id);
			echo (int)$dbo->loadResult() . ' ';
			exit;
			
		} elseif ($id == -1 && (int)$conf->validate_joomla_username) {
			
			$dbo->setQuery("SELECT count(*) FROM #__users WHERE " . $dbo->quoteName('username') . " = " . $dbo->Quote($value) . " AND " . $dbo->quoteName('id') . " != " . (int)$user_id);
			echo (int)$dbo->loadResult() . ' ';
			exit;
			
		} elseif ($id == -2 && (int)$conf->validate_joomla_email) {
			
			$dbo->setQuery("SELECT count(*) FROM #__users WHERE " . $dbo->quoteName('email') . " = " . $dbo->Quote($value) . " AND " . $dbo->quoteName('id') . " != " . (int)$user_id);
			echo (int)$dbo->loadResult() . ' ';
			exit;
			
		}
		
		echo '0 ';
		exit;
	}
	
	function clearadminmenus() {
		$dbo = JFactory::getDBO();
		$dbo->setQuery("DELETE FROM `#__menu` WHERE `link` like '%option=com_extendedreg%' AND client_id = 1");
		$dbo->execute();

		$session = JFactory::getSession();
		$session->clear('application.queue');
		
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_PLEASE_REINSTALL_WARNING'), 'warning');
		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=install', false));
	}

	function update() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		
		if (function_exists("curl_init") && trim($conf->liveupdate_license) && trim($conf->liveupdate_email)) {
			if (jvGetLiveupdateFile('com_extendedreg.zip', 'extendedreg', $conf->liveupdate_license, $conf->liveupdate_email)) {
				$config = JFactory::getConfig();
				$tmp_dest = $config->get('tmp_path') . DIRECTORY_SEPARATOR . 'liveupdate' . DIRECTORY_SEPARATOR . 'com_extendedreg.zip';
				$package = JInstallerHelper::unpack($tmp_dest);
				
				$installer = JInstaller::getInstance();
				$installer->install($package['dir']);

				JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
				
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_EXTENDEDREG_LIVEUPDATE_SUCCESS'), 'notice');
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=default.dashboard', false));
	}
}
