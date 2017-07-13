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

if (!JvitalsHelper::canDo('addons.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewAddonsinstall extends JViewLegacy {
	
	function display($tpl = null) {
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_ADDON_INSTALL_HEADER');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'addons');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$model = $this->getModel();
		
		$joomlaconf = JFactory::getConfig();
		$tmp_path = $joomlaconf->get('tmp_path') . DIRECTORY_SEPARATOR;
		$this->assignRef('tmp_path', $tmp_path);
		
		
		if (JvitalsDefines::compatibleMode() == '30>') {
			JToolBarHelper::custom('addons.browse', 'cancel.png', 'cancel.png', JText::_('COM_EXTENDEDREG_CANCEL'), false, false);
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
	
}