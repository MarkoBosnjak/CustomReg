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

class ExtendedregViewFields extends JViewLegacy {
	
	function display($tpl = null) {
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_FIELDS');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'fields');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$model = $this->getModel();
		
		$fields = $model->getFieldsList();
		$this->assignRef('items', $fields->items);
		
		$pagination = $model->getPagination($fields->total);
		$this->assignRef('pagination', $pagination);
		
		// Preprocess the list of items to find ordering divisions.
		$ordering = array();
		foreach ($fields->items as $item) {
			$ordering[$item->grpid][] = $item->id;
		}
		$this->assignRef('ordering', $ordering);
		
		$fieldStates = $model->getStateOptions();
		$this->assignRef('fieldStates', $fieldStates);
		
		$fieldRequiredOptions = $model->getFieldRequiredOptions();
		$this->assignRef('fieldRequiredOptions', $fieldRequiredOptions);
		
		$fieldTypes = $model->getFieldTypeOptions();
		$this->assignRef('fieldTypes', $fieldTypes);
		
		$fieldGroups = $model->getFieldGroupOptions();
		$this->assignRef('fieldGroups', $fieldGroups);
		
		$emptyGroups = $model->getEmptyFieldGroups();
		$this->assignRef('emptyGroups', $emptyGroups);
		
		$fld_types = erHelperAddons::loadAddons('field');
		$checktypes = array();
		foreach ($fld_types as $type) $checktypes[] = $type->file_name;
		$this->assignRef('checktypes', $checktypes);
		
		$bar = JToolBar::getInstance('toolbar');
		
		if (JvitalsHelper::canDo('fields.groups', 'com_extendedreg')) {
			if (JvitalsDefines::compatibleMode() == '30>') {
				$bar->appendButton('Custom', '<button class="btn btn-success" onclick="javascript:submitbutton(\'forms.fldgrp_new\');" href="#"><i class="icon-new"></i>' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_NEW') . '</button>', 'forms.fldgrp_new');
				$bar->appendButton('Custom', '<button class="btn btn-small" onclick="javascript:if(document.adminForm.grpchecked.value==0){alert(\'' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_EDIT_WARNING') . '\');}else{submitbutton(\'forms.fldgrp_edit\')}" href="#"><i class="icon-edit"></i>' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_EDIT_BUTTON') . '</button>', 'forms.fldgrp_edit');
				$bar->appendButton('Custom', '<button class="btn btn-small" onclick="javascript:if(document.adminForm.grpchecked.value==0){alert(\'' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_DELETE_WARNING') . '\');}else{if(confirm(\'' . JText::_('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG') . '\')){submitbutton(\'forms.fldgrp_delete\');}}" href="#"><i class="icon-delete"></i>' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_DELETE') . '</button>', 'forms.fldgrp_delete');				
			} else {
				$bar->appendButton('Custom', '<a class="toolbar" onclick="javascript:submitbutton(\'forms.fldgrp_new\');" href="#"><span title="' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_NEW') . '" class="icon-32-new"></span>' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_NEW') . '</a>', 'forms.fldgrp_new');
				$bar->appendButton('Custom', '<a class="toolbar" onclick="javascript:if(document.adminForm.grpchecked.value==0){alert(\'' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_EDIT_WARNING') . '\');}else{submitbutton(\'forms.fldgrp_edit\')}" href="#"><span title="' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_EDIT_BUTTON') . '" class="icon-32-edit"></span>' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_EDIT_BUTTON') . '</a>', 'forms.fldgrp_edit');
				$bar->appendButton('Custom', '<a class="toolbar" onclick="javascript:if(document.adminForm.grpchecked.value==0){alert(\'' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_DELETE_WARNING') . '\');}else{if(confirm(\'' . JText::_('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG') . '\')){submitbutton(\'forms.fldgrp_delete\');}}" href="#"><span title="' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_DELETE') . '" class="icon-32-delete"></span>' . JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_DELETE') . '</a>', 'forms.fldgrp_delete');
			}
			JToolBarHelper::divider();
		}
		
		if (JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JToolBarHelper::custom('forms.fld_new', 'new.png', 'new.png', 'COM_EXTENDEDREG_FIELDS_BUTTON_NEW', false);
			JToolBarHelper::custom('forms.fld_edit', 'edit.png', 'edit.png', 'COM_EXTENDEDREG_EDIT', true);
			JToolBarHelper::deleteList('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG', 'forms.fld_delete', 'COM_EXTENDEDREG_DELETE');
			JToolBarHelper::divider();
			JToolBarHelper::custom('forms.fld_publish', 'publish.png', 'publish.png', 'COM_EXTENDEDREG_PUBLISH', true);
			JToolBarHelper::custom('forms.fld_unpublish', 'unpublish.png', 'unpublish.png', 'COM_EXTENDEDREG_UNPUBLISH', true);
			JToolBarHelper::divider();
		}

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
	
	protected function getSortFields() {
		return array(
			'f.title' => JText::_('COM_EXTENDEDREG_FIELDS_TITLE'),
			'f.name' => JText::_('COM_EXTENDEDREG_FIELDS_NAME'),
			'f.type' => JText::_('COM_EXTENDEDREG_FIELDS_TYPE'),
			'f.required' => JText::_('COM_EXTENDEDREG_FIELDS_REQUIRED'),
			'f.editable' => JText::_('COM_EXTENDEDREG_FIELDS_EDITABLE'),
			'f.published' => JText::_('COM_EXTENDEDREG_STATE'),
			'f.ord' => JText::_('COM_EXTENDEDREG_FIELDS_ORDER'),
			'f.id' => 'ID',
		);
	}
	
}