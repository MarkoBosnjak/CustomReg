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

class ExtendedregViewSettings extends JViewLegacy {

	function display($tpl = null) {
		jimport('joomla.filesystem.folder');
		
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_SETTINGS');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'ersettings');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);
		
		$model = $this->getModel();
		
		$task = JRequest::getVar('task', 'settings');
		$this->assignRef('task', $task);
		
		$grp = (int)JRequest::getVar('grp', 0);
		$this->assignRef('grp', $grp);
		
		$group = JRequest::getVar('group', 'default');
		$this->assignRef('group', $group);
		
		$confobj = $model->getConfObj();
		$this->assignRef('confobj', $confobj);
		
		$confdb = $model->getConf();
		$conf = array();
		$conf_grp = array();
		
		foreach ($confdb as $obj) {
			$conf_grp[$obj['group']] = $obj['group'];
			if ($obj['group'] == $group) {
				if (!isset($conf[$obj['group']])) $conf[$obj['group']] = array();
				$conf[$obj['group']][] = array(
					'optname' => $obj['optname'],
					'value' => $obj['value'],
					'description' => $obj['description'],
				);
			}
		}
		$this->assignRef('conf', $conf);
		$this->assignRef('conf_grp', $conf_grp);
		
		if (!(int)$confobj->use_editor) {
			$editor = JFactory::getEditor();
			$this->assignRef('editor', $editor);
		}
		$captcha_libs = erHelperAddons::loadAddons('captcha');
		$this->assignRef('captcha_libs', $captcha_libs);
		
		$themesPath = JvitalsDefines::comFrontPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'themes';
		$css_themes = JFolder::files($themesPath, '.css');
		if (!$css_themes) $css_themes = array();
		$this->assignRef('css_themes', $css_themes);
		
		$bar = JToolBar::getInstance('toolbar');
		if (JvitalsHelper::canDo('core.admin', 'com_extendedreg')) {
			if (JvitalsDefines::compatibleMode() == '30>') {
				$bar->appendButton('Custom', '<button class="btn btn-small btn-success" onclick="performAllActions(\'default.save_settings\')" href="#"><i class="icon-apply icon-white"> </i> ' . JText::_('COM_EXTENDEDREG_APPLY') . '</button>', 'settings');
			} else {
				$bar->appendButton('Custom', '<a class="toolbar" onclick="performAllActions(\'default.save_settings\'); return false;" href="#"><span title="" class="icon-32-save"></span>' . JText::_('COM_EXTENDEDREG_APPLY') . '</a>', 'settings');
			}
			JToolBarHelper::divider();
		}
		
		if (JvitalsDefines::compatibleMode() == '30>') {
			JToolBarHelper::custom('default.dashboard', 'home.png', 'home.png', JText::_('COM_EXTENDEDREG_BACK_TO_DASHBOARD'), false, false);
			JToolBarHelper::divider();
		} else {
			JToolBarHelper::custom('default.dashboard', 'back.png', 'back.png', JText::_('COM_EXTENDEDREG_BACK_TO_DASHBOARD'), false, false);
			JToolBarHelper::divider();
		}
		
		// Add help button to toolbar
		if (JvitalsHelper::canDo('core.admin', 'com_extendedreg')) {
			JToolBarHelper::preferences('com_extendedreg');
			JToolBarHelper::divider();
		}
		$bar->appendButton('Linkblank', 'help', JText::_('COM_EXTENDEDREG_HELP'), erHelperRouter::getHelpUrl());
		
		erHelperHooks::loadHooks('admin');
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
}
