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

class ExtendedregViewStats extends JViewLegacy {

	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		// Icons and titles in Toolbar
		$_title = JText::_('COM_EXTENDEDREG_STATS');
		$_toolbarTitle = JText::sprintf('COM_EXTENDEDREG2', $_title);
		JToolBarHelper::title($_toolbarTitle, 'erstats');
		
		$_pageTitle = strip_tags(JText::sprintf('COM_EXTENDEDREG3', $_title));
		$doc = JFactory::getDocument();
		$doc->setTitle($_pageTitle);

		$model = $this->getModel();
		
		$layout = JRequest::getVar('layout', 'default');
		$task = JRequest::getVar('task', 'stats');
		$this->assignRef('task', $task);
		
		$dateFormat = 'Y-m-d H:i:s';
		$this->assignRef('dateFormat', $dateFormat);
		
		$statsActionOptions = $model->getStatsActionOptions();
		$this->assignRef('statsActionOptions', $statsActionOptions);
		
		$statsProxyOptions = $model->getStatsProxyOptions();
		$this->assignRef('statsProxyOptions', $statsProxyOptions);
		
		if ($layout == 'default') {
		
			if (JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
				JToolBarHelper::deleteList(JText::_('COM_EXTENDEDREG_STATS_PURGE_USERS_MSG'), 'default.purgeusers2', JText::_('COM_EXTENDEDREG_STATS_PURGE_USERS'));
			}
			$exportTask = 'export_activity';
			$stats = $model->getStats();
			
		} elseif ($layout == 'inactive') {
			
			if (JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
				JToolBarHelper::deleteList(JText::_('COM_EXTENDEDREG_STATS_PURGE_USERS_MSG'), 'default.purgeusers', JText::_('COM_EXTENDEDREG_STATS_PURGE_INACTIVE'));
			}
			$exportTask = 'export_inactive';
			$stats = $model->getInactiveUsers();
		
		} elseif ($layout == 'ipaddr') {
			
			if (JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
				JToolBarHelper::deleteList(JText::_('COM_EXTENDEDREG_STATS_PURGE_USERS_MSG'), 'default.purgeusers', JText::_('COM_EXTENDEDREG_STATS_PURGE_USERS'));
			}
			$exportTask = 'export_ipaddr';
			$stats = $model->getUserIPaddresses();
			
		}
		$this->assignRef('items', $stats->items);
		$pagination = $model->getPagination($stats->total);
		$this->assignRef('pagination', $pagination);
		
		$bar = JToolBar::getInstance('toolbar');
		
		if (JvitalsHelper::canDo('stats.manage', 'com_extendedreg')) {
			//~ $bar->appendButton('Linkjs', 'delete', JText::_('COM_EXTENDEDREG_STATS_CLEAR'),  JRoute::_('index.php?option=com_extendedreg&task=default.clear_stats', false));
			
			JToolBarHelper::custom('default.' . $exportTask, 'export.png', 'export.png', 'COM_EXTENDEDREG_BTN_EXPORT', true);
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
		$layout = JRequest::getVar('layout', 'default');
		
		if ($layout == 'default') {
			$fields = array(
				'u.name' => JText::_('COM_EXTENDEDREG_NAME'),
				'u.username' => JText::_('COM_EXTENDEDREG_USERNAME'),
				'u.email' => JText::_('COM_EXTENDEDREG_EMAIL'),
				's.ip_addr' => JText::_('COM_EXTENDEDREG_STATS_IPADDR'),
				's.port' => JText::_('COM_EXTENDEDREG_STATS_PORT'),
				's.proxy' => JText::_('COM_EXTENDEDREG_STATS_PROXY'),
				's.action' => JText::_('COM_EXTENDEDREG_STATS_ACTION'),
				's.tstamp' => JText::_('COM_EXTENDEDREG_TIME'),
				's.id' => 'ID',
			);
		} elseif ($layout == 'inactive') {
			$fields = array(
				'a.name' => JText::_('COM_EXTENDEDREG_NAME'),
				'a.username' => JText::_('COM_EXTENDEDREG_USERNAME'),
				'a.email' => JText::_('COM_EXTENDEDREG_EMAIL'),
				'a.registerDate' => JText::_('COM_EXTENDEDREG_REGISTER_DATE'),
				'a.last_activity' => JText::_('COM_EXTENDEDREG_STATS_LAST_ACTIVITY'),
			);
		} elseif ($layout == 'ipaddr') {
			$fields = array();
		}
		
		return $fields;
	}
	
}
