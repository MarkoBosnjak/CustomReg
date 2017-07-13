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

if (!JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewFormedit extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$app->input->set('hidemainmenu', true);
		
		$model = $this->getModel();
		
		$cid = JRequest::getVar('cid');
		if (!$cid) {
			$cid = JRequest::getVar('id');
		}
		if (is_array($cid)) $cid = $cid[0];
		$cid = (int)$cid;
		
		if (!$cid) {
			$default_form = '[{"type":"row","contents":[{"type":"col","contents":[{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_NAME","contents":"#name_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_USERNAME","contents":"#username_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_EMAIL","contents":"#email_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_PASSWORD","contents":"#passwd_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD","contents":"#passwd2_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_CAPTCHA","contents":"#captcha_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_ACCEPT_TERMS","contents":"#terms_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_OVER_AGE","contents":"#age_fld#"}]}]}]';
			$form = new stdClass();
			$form->id = 0;
			$form->name = '';
			$form->description = '';
			$form->isdefault = 0;
			$form->published = 0;
			$form->show_terms = 0;
			$form->terms_switcher = 0;
			$form->terms_article_id = 0;
			$form->show_age = 0;
			$form->terms_value = '';
			$form->age_value = '';
			$form->mailfrom = '';
			$form->admin_mails = '';
			$form->groups = '';
			$form->layout = $default_form;
			$form->form_style_width = '100%';
			$form->form_style_align = 'align_left';
		} else {
			$form = $model->loadForm($cid, false);
		}
		$this->assignRef('form', $form);
		
		// Icons and titles in Toolbar
		if ((int)$form->id) {
			$_title = JText::sprintf('COM_EXTENDEDREG_FORMS_EDIT', $form->name);
		} else {
			$_title = JText::_('COM_EXTENDEDREG_FORMS_ADD');
		}
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'forms');
		
		$custom_fields = $model->getExtraFieldsInfo();
		$this->assignRef('custom_fields', $custom_fields);
		
		$field_groups = $model->loadFieldGroups();
		$this->assignRef('field_groups', $field_groups);
		
		$conf = $model->getConfObj();
		$this->assignRef('conf', $conf);
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$bar = JToolBar::getInstance('toolbar');
		if (JvitalsDefines::compatibleMode() == '30>') {
			$bar->appendButton('Custom', '<button class="btn btn-success" onclick="performAllActions(\'forms.apply\'); return false;" href="#"><i class="icon-apply"></i>' . JText::_('COM_EXTENDEDREG_APPLY') . '</button>', 'forms.apply');
			$bar->appendButton('Custom', '<button class="btn btn-small" onclick="performAllActions(\'forms.save\'); return false;" href="#"><i class="icon-save"></i>' . JText::_('COM_EXTENDEDREG_SAVE') . '</button>', 'forms.save');
			$bar->appendButton('Custom', '<button class="btn btn-small" onclick="performAllActions(\'forms.savenew\'); return false;" href="#"><i class="icon-save-new"></i>' . JText::_('COM_EXTENDEDREG_SAVENEW') . '</button>', 'forms.savenew');
			$bar->appendButton('Custom', '<button class="btn btn-small" onclick="javascript:if(confirm(\'' . JText::_('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG') . '\')){submitbutton(\'forms.delete\');}" href="#"><i class="icon-trash"></i>' . JText::_('COM_EXTENDEDREG_DELETE') . '</button>', 'forms.delete');
		} else {
			$bar->appendButton('Custom', '<a class="toolbar" onclick="performAllActions(\'forms.apply\'); return false;" href="#"><span title="' . JText::_('COM_EXTENDEDREG_APPLY') . '" class="icon-32-apply"></span>' . JText::_('COM_EXTENDEDREG_APPLY') . '</a>', 'forms.apply');
			$bar->appendButton('Custom', '<a class="toolbar" onclick="performAllActions(\'forms.save\'); return false;" href="#"><span title="' . JText::_('COM_EXTENDEDREG_SAVE') . '" class="icon-32-save"></span>' . JText::_('COM_EXTENDEDREG_SAVE') . '</a>', 'forms.save');
			$bar->appendButton('Custom', '<a class="toolbar" onclick="performAllActions(\'forms.savenew\'); return false;" href="#"><span title="' . JText::_('COM_EXTENDEDREG_SAVENEW') . '" class="icon-32-save-new"></span>' . JText::_('COM_EXTENDEDREG_SAVENEW') . '</a>', 'forms.savenew');
			$bar->appendButton('Custom', '<a class="toolbar" onclick="javascript:if(confirm(\'' . JText::_('COM_EXTENDEDREG_DELETE_AREYOUSURE_MSG') . '\')){submitbutton(\'forms.delete\');}" href="#"><span title="' . JText::_('COM_EXTENDEDREG_DELETE') . '" class="icon-32-delete"></span>' . JText::_('COM_EXTENDEDREG_DELETE') . '</a>', 'forms.delete');
		}
		JToolBarHelper::custom('forms.browse', 'cancel.png', 'cancel.png', JText::_('COM_EXTENDEDREG_CANCEL'), false, false);
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