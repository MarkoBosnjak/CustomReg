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

function erCheckJoomlaSettings() {
	$dbo = JFactory::getDBO();
	$app = JFactory::getApplication();
	$session = JFactory::getSession();
	$sessionQueue = $session->get('application.queue');
	
	$usersConfig = JComponentHelper::getParams('com_users');
	if ((int)$usersConfig->get('allowUserRegistration')) {
		$msg = JText::_('COM_EXTENDEDREG_WARNING_ALLOWUSERREGISTRATION');
		if (!is_array($sessionQueue) || !in_array(array('message' => $msg, 'type' => 'notice'), $sessionQueue)) {
			$app->enqueueMessage($msg, 'notice');
		}
	}
	
	$query = 'SELECT * FROM #__extensions  WHERE ' . $dbo->quoteName('type') . ' = ' . $dbo->Quote('plugin') . ' 
		AND (
			(' . $dbo->quoteName('element') . ' = ' . $dbo->Quote('joomla') . ' AND ' . $dbo->quoteName('folder') . ' = ' . $dbo->Quote('authentication') . ') OR 
			(' . $dbo->quoteName('element') . ' = ' . $dbo->Quote('joomla') . ' AND ' . $dbo->quoteName('folder') . ' = ' . $dbo->Quote('user') . ') OR 
			(' . $dbo->quoteName('element') . ' = ' . $dbo->Quote('extendedregauth') . ' AND ' . $dbo->quoteName('folder') . ' = ' . $dbo->Quote('authentication') . ') OR 
			(' . $dbo->quoteName('element') . ' = ' . $dbo->Quote('extendedregsystem') . ' AND ' . $dbo->quoteName('folder') . ' = ' . $dbo->Quote('system') . ') OR 
			(' . $dbo->quoteName('element') . ' = ' . $dbo->Quote('extendedreguser') . ' AND ' . $dbo->quoteName('folder') . ' = ' . $dbo->Quote('user') . ')
		)';

	$dbo->setQuery($query);
	
	$plugins = $dbo->loadObjectList();
	foreach ($plugins as $plugin) {
		if ($plugin->element == 'joomla' && $plugin->folder == 'user'&& (int)$plugin->enabled) {
			$msg = JText::_('COM_EXTENDEDREG_WARNING_JOOMLA_USER_PLUGIN');
			if (!is_array($sessionQueue) || !in_array(array('message' => $msg, 'type' => 'notice'), $sessionQueue)) {
				$app->enqueueMessage($msg, 'notice');
			}
		} elseif ($plugin->element == 'joomla' && $plugin->folder == 'authentication'&& (int)$plugin->enabled) {
			$msg = JText::_('COM_EXTENDEDREG_WARNING_JOOMLA_AUTHENTICATION_PLUGIN');
			if (!is_array($sessionQueue) || !in_array(array('message' => $msg, 'type' => 'notice'), $sessionQueue)) {
				$app->enqueueMessage($msg, 'notice');
			}
		} elseif ($plugin->element == 'extendedreguser' && $plugin->folder == 'user'&& !(int)$plugin->enabled) {
			$msg = JText::_('COM_EXTENDEDREG_WARNING_EXTENDEDREG_USER_PLUGIN');
			if (!is_array($sessionQueue) || !in_array(array('message' => $msg, 'type' => 'notice'), $sessionQueue)) {
				$app->enqueueMessage($msg, 'notice');
			}
		} elseif ($plugin->element == 'extendedregsystem' && $plugin->folder == 'system'&& !(int)$plugin->enabled) {
			$msg = JText::_('COM_EXTENDEDREG_WARNING_EXTENDEDREG_SYSTEM_PLUGIN');
			if (!is_array($sessionQueue) || !in_array(array('message' => $msg, 'type' => 'notice'), $sessionQueue)) {
				$app->enqueueMessage($msg, 'notice');
			}
		} elseif ($plugin->element == 'extendedregauth' && $plugin->folder == 'authentication'&& !(int)$plugin->enabled) {
			$msg = JText::_('COM_EXTENDEDREG_WARNING_EXTENDEDREG_AUTHENTICATION_PLUGIN');
			if (!is_array($sessionQueue) || !in_array(array('message' => $msg, 'type' => 'notice'), $sessionQueue)) {
				$app->enqueueMessage($msg, 'notice');
			}
		}
	}
}

if (!function_exists("erCleanParam")) {
	function erCleanParam($param) {
		$clean = JvitalsHelper::sanitize($param);
		$clean = str_replace(array("\t", "\r"), array(' ', ''), $clean);
		$clean = str_replace("\n", '<!--NL-->', $clean);
		$clean = preg_replace('~\s+~smi', ' ', $clean);
		$clean = trim($clean);
		return $clean;
	}
}
