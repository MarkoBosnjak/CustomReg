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

class ExtendedregViewProfile extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$is_32 = version_compare(JvitalsDefines::joomlaVersion(), '3.2.0', 'ge');
		JFactory::getLanguage()->load('com_users', JApplicationHelper::getClientInfo(1)->path);
		
		$model = $this->getModel();
		
		$conf = $model->getConfObj();
		$this->assignRef('conf', $conf);
		
		$user = JFactory::getUser();
		$userobj = $model->loadUserById((int)$user->id);
		$this->assignRef('user', $userobj);
		
		$formModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$form = $formModel->loadForm((int)$userobj->form_id);

		if ((int)$user->id && $is_32) {
			$form->tfaform = $model->getTwofactorform((int)$user->id);
			$form->otpConfig = $model->getOtpConfig((int)$user->id);
			$form->tfamethods = $model->getTwoFactorMethods();
		}
		$form->is_32 = $is_32;
		
		$this->assignRef('form', $form);
		
		$formHTML = erHelperHTML::parseForm($form, $userobj);
		$this->assignRef('formHTML', $formHTML);
		
		$params = $app->getParams();
		$this->assignRef('params', $params);
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('profile');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
	
}