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

class ExtendedregViewAbout extends JViewLegacy {
	function display($tpl = null) {
		
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_MENU_ABOUT');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'about');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		if (JvitalsDefines::compatibleMode() == '30>') {
			JToolBarHelper::custom('default.dashboard', 'home.png', 'home.png', JText::_('COM_EXTENDEDREG_BACK_TO_DASHBOARD'), false, false);
			JToolBarHelper::divider();
		} else {
			JToolBarHelper::custom('default.dashboard', 'back.png', 'back.png', JText::_('COM_EXTENDEDREG_BACK_TO_DASHBOARD'), false, false);
			JToolBarHelper::divider();
		}
		
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Linkblank', 'help', JText::_('COM_EXTENDEDREG_HELP'), erHelperRouter::getHelpUrl('extendedreg'));
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
}