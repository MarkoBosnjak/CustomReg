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
		$app = JFactory::getApplication();
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$app->redirect(erHelperRouter::getUrl($conf->redir_url_default));
		exit;
	}
	
	function terms() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$form = $model->loadForm((int)JRequest::getVar('fid'));
		echo JText::_($form->terms_value);
		exit;
	}
	
	function captcha() {
		$lib = erHelperAddons::getCaptchaLib();
		if ($lib) {
			$lib->output();
		}
		exit;
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
	
	function checkunique() {
		$user = JFactory::getUser();
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
			
			$dbo->setQuery("SELECT count(*) FROM #__extendedreg_users WHERE " . $dbo->quoteName($field->name) . " = " . $dbo->Quote($value) . " AND " . $dbo->quoteName('user_id') . " != " . (int)$user->id);
			echo (int)$dbo->loadResult() . ' ';
			exit;
			
		} elseif ($id == -1 && (int)$conf->validate_joomla_username) {
			
			$dbo->setQuery("SELECT count(*) FROM #__users WHERE " . $dbo->quoteName('username') . " = " . $dbo->Quote($value) . " AND " . $dbo->quoteName('id') . " != " . (int)$user->id);
			echo (int)$dbo->loadResult() . ' ';
			exit;
			
		} elseif ($id == -2 && (int)$conf->validate_joomla_email) {
			
			$dbo->setQuery("SELECT count(*) FROM #__users WHERE " . $dbo->quoteName('email') . " = " . $dbo->Quote($value) . " AND " . $dbo->quoteName('id') . " != " . (int)$user->id);
			echo (int)$dbo->loadResult() . ' ';
			exit;
			
		}
		
		echo '0 ';
		exit;
	}
	
}
