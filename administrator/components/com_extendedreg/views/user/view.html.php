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

if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewUser extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$is_32 = version_compare(JvitalsDefines::joomlaVersion(), '3.2.0', 'ge');
		JFactory::getLanguage()->load('com_users', JApplicationHelper::getClientInfo(1)->path);
		
		$model = $this->getModel();
		$user = $model->loadUser();
		
		// Icons and titles in Toolbar
		if ((int)$user->id) {
			$_title = JText::sprintf('COM_EXTENDEDREG_USER_MANAGER_EDIT', $user->username);
		} else {
			$_title = JText::_('COM_EXTENDEDREG_USER_MANAGER_ADD');
		}
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'erusers');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$this->assignRef('user', $user);
		
		$formModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$fid = (int)JRequest::getVar('fid', (int)$user->form_id);
		$form = $formModel->loadForm($fid);

		if ((int)$user->id && $is_32) {
			$form->tfaform = $model->getTwofactorform((int)$user->id);
			$form->otpConfig = $model->getOtpConfig((int)$user->id);
			$form->tfamethods = $model->getTwoFactorMethods();
		}
		$form->is_32 = $is_32;

		$this->assignRef('form', $form);
		
		$allforms = $formModel->getAllForms();
		$this->assignRef('allforms', $allforms);
		
		$formHTML = erHelperHTML::parseForm($form, $user);
		$this->assignRef('formHTML', $formHTML);
		
		$dateFormat = 'Y-m-d H:i:s';
		$this->assignRef('dateFormat', $dateFormat);
		
		JToolBarHelper::custom('users.apply', 'apply.png', 'apply.png', JText::_('COM_EXTENDEDREG_APPLY'), false, false);
		JToolBarHelper::custom('users.save', 'save.png', 'save.png', JText::_('COM_EXTENDEDREG_SAVE'), false, false);
		JToolBarHelper::custom('users.savenew', 'save-new.png', 'save-new.png', JText::_('COM_EXTENDEDREG_SAVENEW'), false, false);
		JToolBarHelper::custom('users.manage', 'cancel.png', 'cancel.png', JText::_('COM_EXTENDEDREG_CANCEL'), false, false);
		JToolBarHelper::divider();
		
		// Add help button to toolbar
		if (JvitalsHelper::canDo('core.admin', 'com_extendedreg')) {
			JToolBarHelper::preferences('com_extendedreg');
			JToolBarHelper::divider();
		}
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Linkblank', 'help', JText::_('COM_EXTENDEDREG_HELP'), erHelperRouter::getHelpUrl());
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
}
