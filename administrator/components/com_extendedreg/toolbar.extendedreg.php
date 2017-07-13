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

if (JvitalsDefines::compatibleMode() != '30>') {
	JHtml::_('behavior.switcher');
} else {
	$SubmenuHTML = '';
}
$task = JRequest::getVar('task', 'dashboard');
$controller = JRequest::getVar('controller', 'default');

$dbo = JFactory::getDBO();
$dbo->setQuery("SELECT DISTINCT " . $dbo->quoteName('link') . ", " . $dbo->quoteName('title') . " FROM #__menu WHERE " . $dbo->quoteName('type') . " = " . $dbo->Quote('component') . " 
	AND " . $dbo->quoteName('client_id') . " = " . $dbo->Quote('1') . " AND " . $dbo->quoteName('level') . " = " . $dbo->Quote('2') . " 
	AND " . $dbo->quoteName('link') . " LIKE '%option=com_extendedreg%' ORDER BY " . $dbo->quoteName('id'));
$items = $dbo->loadObjectList();

if ($items && count($items)) {
	if (JvitalsDefines::compatibleMode() == '30>') {
		$SubmenuHTML .= '<ul class="nav nav-list" id="submenu">';
	}
	
	foreach ($items as $item) {
		if ($controller == 'users' || $controller == 'addons') {
			$active = (boolean)(preg_match('~^.*?(task=' . $controller . '\.).*?$~', $item->link) || preg_match('~^.*?(task=' . $task . ').*?$~', $item->link));
		} elseif ($controller == 'forms' && ($task == 'fields' || $task == 'fldgrp_new' || $task == 'fldgrp_edit' || $task == 'fld_new' || $task == 'fld_edit')) {
			$active = (boolean)(
				preg_match('~^.*?(task=forms\.fields).*?$~', $item->link) || 
				preg_match('~^.*?(task=forms\.fldgrp_new).*?$~', $item->link) || 
				preg_match('~^.*?(task=forms\.fldgrp_edit).*?$~', $item->link) || 
				preg_match('~^.*?(task=forms\.fld_new).*?$~', $item->link) || 
				preg_match('~^.*?(task=forms\.fld_edit).*?$~', $item->link)
			);
		} elseif ($controller == 'forms' && ($task == 'browse' || $task == 'new' || $task == 'edit')) {
			$active = (boolean)(
				preg_match('~^.*?(task=forms\.browse).*?$~', $item->link) || 
				preg_match('~^.*?(task=forms\.new).*?$~', $item->link) || 
				preg_match('~^.*?(task=forms\.edit).*?$~', $item->link)
			);
		} else {
			$active = (boolean)(preg_match('~^.*?(task=' . $controller . '\.' . $task . ').*?$~', $item->link) || preg_match('~^.*?(task=' . $task . ').*?$~', $item->link));
		}
		if (JvitalsDefines::compatibleMode() != '30>') {
			JSubMenuHelper::addEntry(JText::_($item->title), $item->link, $active);
		} else {
			$SubmenuHTML .= '<li' . ($active ? '  class="active"' : '') . '><a href="' . $item->link . '">' . JText::_($item->title) . '</a></li>';
		}
	}
	
	if (JvitalsDefines::compatibleMode() == '30>') {
		$SubmenuHTML .= '</ul>';
	}
}

if (JvitalsDefines::compatibleMode() == '30>') {
	echo $SubmenuHTML;
}
