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

class erHelperRouter {
	public static function route() {
		$app = JFactory::getApplication();
		$task = $app->input->getCmd('task');
		$view = $app->input->getCmd('view');
		$controller = 'default';
		
		if ($app->isSite() && !trim($task) && trim($view)) {
			switch ($view) {
				case 'default' : 
					$task = 'users.login_and_register';
					break;
				default : 
					$task = 'users.' . $view;
					break;
			}
		}
		
		if (!$task) {
			if ($app->isAdmin()) {
				$task = 'default.dashboard';
			} else {
				$task = 'users.register';
			}
		}
		
		if (strpos($task, '.')) {
			$arr = explode('.', $task);
			$controller = trim($arr[0]);
			$task = trim($arr[1]);
		}
		
		$app->input->set('task', $task);
		$app->input->set('controller', $controller);
		$app->input->set('view', null);
		
		return;
	}
	
	public static function getItemid($url) {
		static $itemidList;
		static $homepage;
		
		$dbo = JFactory::getDBO();
		$lang = JFactory::getLanguage();
		
		if (!$homepage) {
			// Home page
			$dbo->setQuery("SELECT " . $dbo->quoteName('id') . " FROM #__menu WHERE " . $dbo->quoteName('published') . " = " . $dbo->Quote('1') . " AND 
				" . $dbo->quoteName('type') . " = " . $dbo->Quote('component') . " AND " . $dbo->quoteName('home') . " = " . $dbo->Quote('1') . " LIMIT 1");
			$homepage = $dbo->loadResult();
		}
		
		if (!$itemidList) {
			$itemidList = array();
		}
		
		if (!isset($itemidList[$url])) {
			$u = JURI::getInstance($url);
			$query = $u->getQuery(true);
			
			if (!count($query)) {
				$itemidList[$url] = (int)$homepage;
			} else {
				$Itemid = (isset($query['Itemid']) ? (int)$query['Itemid'] : 0);
				if (!$Itemid) {
					$sql = "";
					foreach ($query as $key => $value) {
						if ($sql) $sql .= " AND ";
						if ($key == 'task') {
							$value2 = $value;
							if ($value == 'users.login_and_register') {
								$value2 = 'default';
							} elseif (strpos($value, '.')) {
								$arr = explode('.', $value);
								$value2 = trim($arr[1]);
							}
							$sql .= "(" . $dbo->quoteName('link') . " LIKE '%task=" . $dbo->escape($value) . "%' OR " . $dbo->quoteName('link') . " LIKE '%view=" . $dbo->escape($value2) . "%')";
						} else {
							$sql .= $dbo->quoteName('link') . " LIKE '%" . $dbo->escape($key) . "=" . $dbo->escape($value) . "%'";
						}
					}
					if ($sql) {
						$sql = "SELECT " . $dbo->quoteName('id') . " FROM #__menu WHERE " . $dbo->quoteName('published') . " = " . $dbo->Quote('1') . " AND 
							" . $dbo->quoteName('type') . " = " . $dbo->Quote('component') . " AND " . $sql  . " ORDER BY (case WHEN " . $dbo->quoteName('language') . " = '" . $lang->getTag() . "' THEN 1 ELSE 0 end) DESC LIMIT 1";
						$dbo->setQuery($sql);
						$Itemid = $dbo->loadResult();
					}
				}
				$itemidList[$url] = (int)$Itemid;
			}
		}
		return (int)$itemidList[$url];
	}
	
	public static function getUrl($url, $other = '', $default = '') {
		$result = '';
		switch ($url) {
			case 'er_home':
				$result = JURI::base(true) . '/';
				break;
			case 'er_login_register':
				$myurl = 'index.php?option=com_extendedreg&task=users.login_and_register';
				$Itemid = self::getItemid($myurl);
				if ($Itemid > 0) {
					$myurl .= '&Itemid=' . $Itemid;
				}
				$result = JRoute::_($myurl, false);
				break;
			case 'er_login':
				$myurl = 'index.php?option=com_extendedreg&task=users.login';
				$Itemid = self::getItemid($myurl);
				if ($Itemid > 0) {
					$myurl .= '&Itemid=' . $Itemid;
				}
				$result = JRoute::_($myurl, false);
				break;
			case 'er_register':
				$myurl = 'index.php?option=com_extendedreg&task=users.register';
				$Itemid = self::getItemid($myurl);
				if ($Itemid > 0) {
					$myurl .= '&Itemid=' . $Itemid;
				}
				$result = JRoute::_($myurl, false);
				break;
			case 'er_other':
				$config = JFactory::getConfig();
				$sef_conf = $config->get('sef');
				$sef_rewrite_conf = $config->get('sef_rewrite');
				if (!preg_match('~^index\.php.*?$~', $other) || ((int)$sef_conf == 1 && (int)$sef_rewrite_conf == 0 && preg_match('~^index\.php/.+?$~', $other))) {
					$result = JURI::base(true) . '/' . $other;
				} else {
					if ($other && JURI::isInternal($other)) {
						if (preg_match('~index\.php\/.+~', $other)) {
							$result = $other;
						} else {
							$Itemid = self::getItemid($other);
							if ($Itemid > 0 && !preg_match('~Itemid=~', $other)) {
								$u = JURI::getInstance($other);
								$other .= (trim($u->getQuery()) ? '&' : '?') . 'Itemid=' . $Itemid;
							}
							$result = JRoute::_($other, false);
						}
					} else {
						$result = JURI::base(true) . '/';
					}
				}
				break;
			case 'er_default':
			default: 
				if (!$default) {
					$result = JURI::base(true) . '/';
				} else {
					$result = self::getUrl($default);
				}
				break;
		}
		return $result;
	}
	
	public static function getHelpUrl($key = '') {
		return 'http://www.jvitals.com/support/tutorials/8-extendedreg-help.html';
	}
}