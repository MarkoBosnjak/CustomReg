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

class ExtendedregViewForms extends JViewLegacy {
	
	function display($tpl = null) {
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_FORMS');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'forms');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$model = $this->getModel();
		
		$forms = $model->getFormsList();
		$pagination = $model->getPagination($forms->total);
		$this->assignRef('items', $forms->items);
		$this->assignRef('pagination', $pagination);
		
		$formStates = $model->getStateOptions();
		$this->assignRef('formStates', $formStates);
		
		if (JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
			JToolBarHelper::custom('forms.new', 'new.png', 'new.png', 'COM_EXTENDEDREG_NEW', false);
			JToolBarHelper::custom('forms.edit', 'edit.png', 'edit.png', 'COM_EXTENDEDREG_EDIT', true);
			JToolBarHelper::deleteList('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG', 'forms.delete', 'COM_EXTENDEDREG_DELETE');
			JToolBarHelper::divider();
			JToolBarHelper::custom('forms.publish', 'publish.png', 'publish.png', 'COM_EXTENDEDREG_PUBLISH', true);
			JToolBarHelper::custom('forms.unpublish', 'unpublish.png', 'unpublish.png', 'COM_EXTENDEDREG_UNPUBLISH', true);
			JToolBarHelper::makeDefault('forms.set_default');
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
			'f.name' => JText::_('COM_EXTENDEDREG_FORMS_NAME'),
			'f.isdefault' => JText::_('COM_EXTENDEDREG_FORMS_ISDEFAULT'),
			'f.published' => JText::_('COM_EXTENDEDREG_STATE'),
			'f.id' => 'ID',
		);
	}
	
}