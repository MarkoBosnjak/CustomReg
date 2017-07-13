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

class ExtendedregViewDefault extends JViewLegacy {
	function display($tpl = null) {
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_DASHBOARD');
		$_toolbarTitle = JText::_('COM_EXTENDEDREG');
		JToolBarHelper::title($_toolbarTitle, 'dashboard');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		
		$currVersion = JvitalsDefines::componentVersion('com_extendedreg');
		$this->assignRef('currVersion', $currVersion);

		$dashboard = array (
			'DASHBOARD' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=default.dashboard', false),
				'icon' => 'dashboard.png',
				'label' => JText::_('COM_EXTENDEDREG_DASHBOARD')
			),
			'USERS' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=users.manage', false),
				'icon' => 'erusers.png',
				'label' => JText::_('COM_EXTENDEDREG_USER_MANAGER')
			),
			'FIELDS' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false),
				'icon' => 'fields.png',
				'label' => JText::_('COM_EXTENDEDREG_FIELDS')
			),
			'FORMS' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=forms.browse', false),
				'icon' => 'forms.png',
				'label' => JText::_('COM_EXTENDEDREG_FORMS')
			),
			'STATS' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=default.stats', false),
				'icon' => 'erstats.png',
				'label' => JText::_('COM_EXTENDEDREG_STATS')
			),
			'ADDONS' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=addons.browse', false),
				'icon' => 'addons.png',
				'label' => JText::_('COM_EXTENDEDREG_ADDONS')
			),
			'SETTINGS' => array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=default.settings', false),
				'icon' => 'ersettings.png',
				'label' => JText::_('COM_EXTENDEDREG_SETTINGS')
			),
			'SUPPORT' => array(
				'link' => 'http://www.jvitals.com/support/support-forum/default.board.html',
				'icon' => 'support.png',
				'label' => JText::_('COM_EXTENDEDREG_FORUM_SUPPORT'),
				'target' => '_blank'
			),
			'HELP' => array(
				'link' => erHelperRouter::getHelpUrl('extendedreg'),
				'icon' => 'help.png',
				'label' => JText::_('COM_EXTENDEDREG_HELP'),
				'target' => '_blank'
			),
		);
		
		if (function_exists("curl_init") && trim($conf->liveupdate_license) && trim($conf->liveupdate_email)) {
			$dashboard['LIVEUPDATE'] = array(
				'link' => JRoute::_('index.php?option=com_extendedreg&task=default.update', false),
				'icon' => 'updates.png',
				'label' => JText::_(JvitalsHelper::versionCompare('er-version-compare.txt', 'extendedreg', $currVersion) ? 'COM_EXTENDEDREG_LIVEUPDATE_UPTODATE' : 'COM_EXTENDEDREG_LIVEUPDATE_NEWVERSION'),
			);
		}
		
		$this->assignRef('dashboard', $dashboard);
		
		// Add help button to toolbar
		if (JvitalsHelper::canDo('core.admin', 'com_extendedreg')) {
			JToolBarHelper::preferences('com_extendedreg');
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