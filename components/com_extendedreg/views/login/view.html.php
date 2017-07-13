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

class ExtendedregViewLogin extends JViewLegacy {
	function display($tpl = null) {
		$this->is_32 = version_compare(JvitalsDefines::joomlaVersion(), '3.2.0', 'ge');
		
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel();
		
		$conf = $model->getConfObj();
		$this->assignRef('conf', $conf);
		
		$params = $app->getParams();
		$this->assignRef('params', $params);
		
		$lret = JRequest::getVar('lret', '', 'method', 'base64');
		$this->assignRef('lret', $lret);
		
		$twofactormethods = array();
		if ($this->is_32) {
			require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
			$twofactormethods = UsersHelper::getTwoFactorMethods();
		}
		$this->assignRef('twofactormethods', $twofactormethods);
		
		erHelperHooks::loadHooks('html');
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
}