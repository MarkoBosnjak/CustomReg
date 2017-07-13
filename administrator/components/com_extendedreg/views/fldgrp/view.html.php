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

if (!JvitalsHelper::canDo('fields.groups', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewFldgrp extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel();
		
		$grpid = (int)JRequest::getVar('grpid');
		$item = $model->loadFieldGroups($grpid);
		if (!isset($item) || !is_object($item)) {
			$item = new stdClass;
			$item->grpid = 0;
			$item->name = '';
			$item->description = '';
		}
		$this->assignRef('item', $item);
		
		// Icons and titles in Toolbar
		if ((int)$item->grpid) {
			$_title = JText::sprintf('COM_EXTENDEDREG_FIELDS_GROUPS_EDIT', $item->name);
		} else {
			$_title = JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_ADD');
		}
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'fields');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::custom('forms.fldgrp_apply', 'apply.png', 'apply.png', JText::_('COM_EXTENDEDREG_APPLY'), false, false);
		JToolBarHelper::custom('forms.fldgrp_save', 'save.png', 'save.png', JText::_('COM_EXTENDEDREG_SAVE'), false, false);
		JToolBarHelper::custom('forms.fldgrp_savenew', 'save-new.png', 'save-new.png', JText::_('COM_EXTENDEDREG_SAVENEW'), false, false);
		if ((int)$item->grpid) {
			JToolBarHelper::custom('forms.fldgrp_delete', 'trash.png', 'trash.png', JText::_('COM_EXTENDEDREG_DELETE'), false, false);
		}
		JToolBarHelper::custom('forms.fields', 'cancel.png', 'cancel.png', JText::_('COM_EXTENDEDREG_CANCEL'), false, false);
		JToolBarHelper::divider();
		
		// Add help button to toolbar
		if (JvitalsHelper::canDo('core.admin', 'com_extendedreg')) {
			JToolBarHelper::preferences('com_extendedreg');
			JToolBarHelper::divider();
		}
		$bar->appendButton('Linkblank', 'help', JText::_('COM_EXTENDEDREG_HELP'), erHelperRouter::getHelpUrl());
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
	
}
