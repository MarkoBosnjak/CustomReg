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

if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewFldedit extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel();
		
		$cid = JRequest::getVar('cid');
		if (is_array($cid)) $cid = $cid[0];
		$cid = (int)$cid;

		$fld_types = erHelperAddons::loadAddons('field');
		$this->assignRef('fld_types', $fld_types);
		
		if (!count($fld_types)) {
			JError::raiseError(83004, JText::_('COM_EXTENDEDREG_FIELDS_TYPE_DOES_NOT_EXIST'));
			jexit();
		}
		
		if (!$cid) {
			$item = $model->loadField($cid, $fld_types[0]->file_name);
		} else {
			$item = $model->loadField($cid);
		}
		$this->assignRef('item', $item);
		
		$fld_class = erHelperAddons::getFieldType($item);
		$this->assignRef('fld_class', $fld_class);
		
		// Icons and titles in Toolbar
		if ((int)$item->id) {
			$_title = JText::sprintf('COM_EXTENDEDREG_FIELDS_EDIT', $item->title);
		} else {
			$_title = JText::_('COM_EXTENDEDREG_FIELDS_ADD');
		}
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'fields');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$grouplist = $model->loadFieldGroups();
		$this->assignRef('grouplist', $grouplist);
		
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::custom('forms.fld_apply', 'apply.png', 'apply.png', JText::_('COM_EXTENDEDREG_APPLY'), false, false);
		JToolBarHelper::custom('forms.fld_save', 'save.png', 'save.png', JText::_('COM_EXTENDEDREG_SAVE'), false, false);
		JToolBarHelper::custom('forms.fld_savenew', 'save-new.png', 'save-new.png', JText::_('COM_EXTENDEDREG_SAVENEW'), false, false);
		if ((int)$item->id) {
			JToolBarHelper::custom('forms.fld_delete', 'trash.png', 'trash.png', JText::_('COM_EXTENDEDREG_DELETE'), false, false);
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
