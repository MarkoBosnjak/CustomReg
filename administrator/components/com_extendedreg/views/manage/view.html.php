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

class ExtendedregViewManage extends JViewLegacy {

	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_USER_MANAGER');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'erusers');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);

		$model = $this->getModel();
		
		// We load the users
		$users = $model->getUsersList();
		// We load the pagination
		$pagination = $model->getPagination($users->total);
		// We load the groups for those users
		$model->getGroupsForUserList($users->items);
		
		$this->assignRef('items', $users->items);
		$this->assignRef('pagination', $pagination);
		
		$userStateOptions = $model->getStateOptions();
		$this->assignRef('userStateOptions', $userStateOptions);
		
		$userActiveOptions = $model->getActiveOptions();
		$this->assignRef('userActiveOptions', $userActiveOptions);
		
		$userApprovedOptions = $model->getApprovedOptions();
		$this->assignRef('userApprovedOptions', $userApprovedOptions);
		
		$userTermsOptions = $model->getTermsOptions();
		$this->assignRef('userTermsOptions', $userTermsOptions);
		
		$userAgeOptions = $model->getAgeOptions();
		$this->assignRef('userAgeOptions', $userAgeOptions);
		
		$dateFormat = 'Y-m-d H:i:s';
		$this->assignRef('dateFormat', $dateFormat);
		
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$ef = $formsModel->getExtraFieldsInfo();
		$this->assignRef('ef', $ef);
		
		if (JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JToolBarHelper::custom('users.add', 'new.png', 'new.png', 'COM_EXTENDEDREG_NEW', false);
			JToolBarHelper::custom('users.edit', 'edit.png', 'edit.png', 'COM_EXTENDEDREG_EDIT', true);
			JToolBarHelper::deleteList('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG', 'users.delete', 'COM_EXTENDEDREG_DELETE');
			JToolBarHelper::divider();
			JToolBarHelper::custom('users.activate', 'publish.png', 'publish.png', 'COM_EXTENDEDREG_BTN_ACTIVATE', true);
			JToolBarHelper::custom('users.block', 'unpublish.png', 'unpublish.png', 'COM_EXTENDEDREG_BTN_BLOCK', true);
			JToolBarHelper::custom('users.unblock', 'unblock.png', 'unblock.png', 'COM_EXTENDEDREG_BTN_UNBLOCK', true);
			JToolBarHelper::custom('users.approve', 'approve.png', 'approve.png', 'COM_EXTENDEDREG_BTN_APPROVE', true);
			JToolBarHelper::custom('users.unapprove', 'unapprove.png', 'unapprove.png', 'COM_EXTENDEDREG_BTN_UNAPPROVE', true);
			JToolBarHelper::custom('users.accept_terms', 'accept-terms.png', 'accept-terms.png', 'COM_EXTENDEDREG_BTN_ACCEPT_TERMS', true);
			JToolBarHelper::custom('users.decline_terms', 'decline-terms.png', 'decline-terms.png', 'COM_EXTENDEDREG_BTN_DECLINE_TERMS', true);
			JToolBarHelper::divider();
			JToolBarHelper::custom('users.export', 'export.png', 'export.png', 'COM_EXTENDEDREG_BTN_EXPORT', true);
			JToolBarHelper::divider();
		}
		
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
	
	protected function getSortFields() {
		return array(
			'u.name' => JText::_('COM_EXTENDEDREG_NAME'),
			'u.username' => JText::_('COM_EXTENDEDREG_USERNAME'),
			'u.email' => JText::_('COM_EXTENDEDREG_EMAIL'),
			'u.block' => JText::_('COM_EXTENDEDREG_ENABLED'),
			'u.activation' => JText::_('COM_EXTENDEDREG_ACTIVATED'),
			'er.approve' => JText::_('COM_EXTENDEDREG_APPROVED'),
			'er.acceptedterms' => JText::_('COM_EXTENDEDREG_TERMS_HEADER'),
			'er.overage' => JText::_('COM_EXTENDEDREG_OVERAGE_HEADER'),
			'u.registerDate' => JText::_('COM_EXTENDEDREG_REGISTER_DATE'),
			'u.id' => 'ID',
		);
	}
	
}
