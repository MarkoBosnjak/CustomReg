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

function extendedregBuildRoute(&$query) {
	$segments = array();
	$db = JFactory::getDBO();
	$task = '';
	$aliases = erGetAlias();
	
	if(isset($query['task'])) {
		$task = str_replace(array('users.', 'default.'), '', $query['task']);
		$segments[] = (is_array($aliases) && count($aliases) && isset($aliases[$task])) ? $aliases[$task] : $task;
		unset($query['task']);
	}
	
	unset($query['view']);
	return $segments;
}

function extendedregParseRoute($segments) {
	$vars = array();
	$task = '';
	$controller = '';
	$aliases = erGetAlias();
	$aliases = array_flip($aliases);
	$db = JFactory::getDBO();
	
	if (isset($segments[0])) {
		$task = (is_array($aliases) && count($aliases) && isset($aliases[$segments[0]])) ? $aliases[$segments[0]] : $segments[0];
	}
	if ($task) {
		$controller = erGetController($task);
		$vars['task'] = $controller . '.' . $task;
	}

	return $vars;
}

function erGetAlias() {
	static $aliases;
	if (is_null($aliases) || !is_array($aliases) || !count($aliases)) {
		$aliases = array();
		$db = JFactory::getDBO();
		$db->setQuery('SELECT optname, value from #__extendedreg_settings WHERE optname LIKE \'%sef_%\'');
		$results = $db->loadAssocList();
		foreach($results as $result) {
			if (isset($result['value']) && $result['value']) {
				$aliases[str_replace('sef_', '', $result['optname'])] = erStringURLSafe($result['value']);
			}
		}
	}
	return $aliases;
}

function erGetController($task) {
	if (in_array($task, array('terms', 'captcha', 'checkemail', 'checkunique', 'scriptoutput'))) return 'default';
	if (in_array($task, array(
		'login_and_register', 'register', 'login', 'remind', 'reset', 'request_activation_mail', 'confirm_reset', 
		'complete_reset', 'profile', 'terminate', 'do_login', 'do_remind', 'do_reset', 'logout', 'do_register', 
		'do_save', 'activate', 'approve', 'do_request_activation', 'send_terminate', 'do_terminate'))
	) {
		return 'users';
	}
	return 'default';
}

function erStringURLSafe($string) {
	
	// Replace double byte whitespaces by single byte (East Asian languages)
	$str = preg_replace('/\xE3\x80\x80/', ' ', $string);

	// Remove any '-' from the string as they will be used as concatenator.
	// Would be great to let the spaces in but only Firefox is friendly with this

	$str = str_replace('-', ' ', $str);

	// Replace forbidden characters by whitespaces
	$str = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $str);

	// Delete all '?'
	$str = str_replace('?', '', $str);

	// Trim white spaces at beginning and end of alias and make lowercase
	$str = trim(JString::strtolower($str));

	// Remove any duplicate whitespace and replace whitespaces by hyphens
	$str = preg_replace('#\x20+#', '-', $str);

	return $str;
}
