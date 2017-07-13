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

class ExtendedregViewAddons extends JViewLegacy {
	
	function display($tpl = null) {
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_ADDONS');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'addons');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$model = $this->getModel();
		
		$addons = $model->getAddonsList();
		$this->assignRef('items', $addons->items);
		
		$pagination = $model->getPagination($addons->total);
		$this->assignRef('pagination', $pagination);
		
		$addonStates = $model->getStateOptions();
		$this->assignRef('addonStates', $addonStates);
		
		$addonTypes = $model->getTypeOptions();
		$this->assignRef('addonTypes', $addonTypes);
		
		if (JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
			JToolBarHelper::custom('addons.install', 'upload.png', 'upload.png', 'COM_EXTENDEDREG_ADDON_INSTALL', false);
			JToolBarHelper::custom('addons.doUninstall', 'delete.png', 'delete.png', 'COM_EXTENDEDREG_ADDON_UNINSTALL', false);
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
			'a.name' => JText::_('COM_EXTENDEDREG_ADDON_NAME'),
			'a.file_name' => JText::_('COM_EXTENDEDREG_ADDON_FILENAME'),
			'a.type' => JText::_('COM_EXTENDEDREG_ADDON_TYPE'),
			'a.published' => JText::_('COM_EXTENDEDREG_STATE'),
			'a.author' => JText::_('COM_EXTENDEDREG_ADDON_AUTHOR'),
			'a.author_email' => JText::_('COM_EXTENDEDREG_ADDON_AUTHOR_EMAIL'),
			'a.author_url' => JText::_('COM_EXTENDEDREG_ADDON_AUTHOR_URL'),
			'a.id' => 'ID',
		);
	}
	
}