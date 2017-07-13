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

class ExtendedregModelDefault extends JvitalsModel {
	
	public function getConf() {
		static $conf;
		
		if (!$conf) {
			// Construct the query
			$query = $this->dbo->getQuery(true)
				->select("*")
				->from("#__extendedreg_settings");
			$query->order("(case WHEN " . $this->dbo->quoteName('group') . " = " . $this->dbo->quote('default') . " THEN 0 ELSE 1 end) ASC, " . $this->dbo->quoteName('group') . " ASC, " . $this->dbo->quoteName('ord') . " ASC");
			
			$this->dbo->setQuery($query);
			try {
				if (!($conf = $this->dbo->loadAssocList())) {
					$this->setError($this->dbo->getErrorMsg());
				}
			} catch (RuntimeException $e) {
				$this->setError($e->getMessage());
			}
			if (!$conf) $conf = array();
		}
		return $conf;
	}
	
	public function getConfObj() {
		static $conf;
		
		if (!$conf) {
			// Construct the query
			$query = $this->dbo->getQuery(true)
				->select("*")
				->from("#__extendedreg_settings");
			$query->order("(case WHEN " . $this->dbo->quoteName('group') . " = " . $this->dbo->quote('default') . " THEN 0 ELSE 1 end) ASC, " . $this->dbo->quoteName('group') . " ASC, " . $this->dbo->quoteName('ord') . " ASC");
			
			$this->dbo->setQuery($query);
			try {
				if (!($res = $this->dbo->loadObjectList())) {
					$this->setError($this->dbo->getErrorMsg());
				}
			} catch (RuntimeException $e) {
				$this->setError($e->getMessage());
			}
			if (!$res) $res = array();
			
			$obj = new stdClass();
			foreach ($res as $row) {
				$optname = $row->optname;
				$obj->$optname = $row->value;
			}
			$conf = $obj;
		}
		
		return $conf;
	}
	
	public function configSave() {
		$app = JFactory::getApplication();
		$config_group = $app->input->getCmd('group', 'default');
		
		$confdb = $this->getConf();
		$conf = array();
		foreach ($confdb as $obj) {
			if (($obj['group'] == $config_group) && ($obj['optname'] != 'er_version')) {
				$conf[$obj['optname']] = '';
				if (is_array($_POST[$obj['optname']])) {
					$confvalue = $app->input->get($obj['optname'], array(), 'array');
				} else {
					if (JvitalsDefines::compatibleMode() == '25>') {
						$confvalue = trim(JRequest::getVar($obj['optname'], '', 'post', 'string', JREQUEST_ALLOWRAW));
					} else {
						$confvalue = trim($app->input->get($obj['optname'], '', 'raw'));
					}
				}
				if (!is_array($confvalue)) {
					if ((bool)get_magic_quotes_gpc()) $confvalue = stripslashes($confvalue);
					$confvalue = trim(JvitalsHelper::sanitize($confvalue));
					
					if (strlen($confvalue)) {
						$conf[$obj['optname']] = $confvalue;
					}
				} else {
					$conf[$obj['optname']] = $confvalue;
				}
				if (in_array($obj['optname'], array('pass_expected_chars', 'pass_allowed_chars'))) {
					$newvalue = 0;
					if (is_array($confvalue)) {
						foreach ($confvalue as $val) $newvalue += (int)$val;
					}
					$conf[$obj['optname']] = $newvalue;
				} elseif (strlen(trim($confvalue)) || in_array($obj['optname'], array('css_theme', 'admin_mails', 'default_mailfrom'))) {
					$conf[$obj['optname']] = trim($confvalue);
				}
				if ($conf[$obj['optname']] == 'none') $conf[$obj['optname']] = '';
			}
		}
		
		// Construct the query
		$query = $this->dbo->getQuery(true);
		foreach ($conf as $optname => $value) {
			$query->clear()
				->update("#__extendedreg_settings")
				->set($this->dbo->quoteName('value') . " = " . $this->dbo->quote($value))
				->where($this->dbo->quoteName('optname') . " = " . $this->dbo->quote($optname));
			
			// Setup the query
			$this->dbo->setQuery($query);
			try {
				if (!$this->dbo->execute()) {
					$this->setError($this->dbo->getErrorMsg());
					return false;
				}
			} catch (RuntimeException $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		return true;
	}
	
	function getStats($export = false) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$loggeduser = JFactory::getUser();
		
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		$listOrder = $app->getUserStateFromRequest($option . '.list.ordering', 'filter_order', 'u.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest($option . '.list.direction', 'filter_order_Dir', 'ASC', 'word');
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('u.name', 'u.username', 's.ip_addr', 'u.email', 's.action', 's.proxy', 's.port', 's.tstamp', 's.id'))) {
			$listOrder = 'u.name';
			$app->setUserState($option . '.list.ordering', $listOrder);
		}

		if (!in_array(mb_strtoupper($listDirn), array('ASC', 'DESC'))) {
			$listDirn = 'ASC';
			$app->setUserState($option . '.list.direction', $listDirn);
		}
		
		$stats_search = $app->getUserStateFromRequest($option . '.filter.stats_search', 'stats_search');
		$stats_search = trim(strip_tags($stats_search));
		if ($stats_search) {
			$stats_search = mb_strtolower($stats_search);
			$stats_search = preg_replace('~[^\w|\s|\d]+~i', ' ', $stats_search);
			$stats_search = preg_replace('~\s+~i', '%', trim($stats_search));
		}
		
		$filter_action = $app->getUserStateFromRequest($option . '.filter.stats_action', 'filter_action');
		if (!in_array($filter_action, array('*', 'login', 'logout', 'user_register', 'profile_edit'))) {
			$filter_action = '*';
			$app->setUserState($option . '.filter.stats_action', $filter_action);
		}
		$filter_proxy = $app->getUserStateFromRequest($option . '.filter.stats_proxy', 'filter_proxy');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS s.*, u.username, u.name, u.email";
		$query .= " FROM #__extendedreg_stats as s 
			JOIN #__users as u ON u." . $this->dbo->quoteName('id') . " = s." . $this->dbo->quoteName('user_id') . " ";
		
		$where = array();
		
		if ($export) {
			$cid = JRequest::getVar('cid');
			if (!is_array($cid)) $cid = array((int)$cid);
			$cid = array_unique($cid);
			JArrayHelper::toInteger($cid);
			$where[] = "s." . $this->dbo->quoteName('id') . " IN (" .  implode(',', $cid) . ")";
		} else {
			if ($stats_search) {
				$searchEscaped = $this->dbo->Quote('%' . $stats_search . '%', false);
				$where[] = "(
					u." . $this->dbo->quoteName('name') . " LIKE " . $searchEscaped . " OR 
					u." . $this->dbo->quoteName('username') . " LIKE " . $searchEscaped . " OR 
					u." . $this->dbo->quoteName('email') . " LIKE " . $searchEscaped . " OR 
					s." . $this->dbo->quoteName('ip_addr') . " LIKE " . $searchEscaped . " OR 
					s." . $this->dbo->quoteName('port') . " LIKE " . $searchEscaped . "
				)";
			}
			
			if ($filter_action != '*') {
				$where[] = "s." . $this->dbo->quoteName('action') . " = " . $this->dbo->Quote($filter_action);
			}
			
			if (!is_null($filter_proxy) && is_numeric($filter_proxy)) {
				$where[] = "s." . $this->dbo->quoteName('proxy') . " = " . $this->dbo->Quote((int)$filter_proxy == 0 ? '0' : '1');
			}
		}
		
		if (count($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " ORDER BY " . $listOrder . " " . $listDirn;
		
		$this->dbo->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$result->items = $this->dbo->loadObjectList();
		if (!$result->items) $result->items = array();
		
		$this->dbo->setQuery('SELECT FOUND_ROWS();');
		$result->total = (int)$this->dbo->loadResult();
		
		return $result;
	}
	
	function clearStats() {
		$app = JFactory::getApplication();
		$fromdate = JRequest::getVar('fromdate');
		$todate = JRequest::getVar('todate');
		if (!($fromdate && $todate)) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_STATS_NOPERIOD'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=default.stats', false));
			jexit();
		}
		
		$this->dbo->setQuery('DELETE FROM #__extendedreg_stats WHERE ' . $this->dbo->quoteName('tstamp') . ' BETWEEN ' . $this->dbo->Quote($fromdate) . ' AND ' . $this->dbo->Quote($todate));
		$this->dbo->execute();
		
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_STATS_CLEAR_SUCCESS'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=default.stats', false));
		jexit();
	}
	
	function getInactiveUsers($export = false) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$loggeduser = JFactory::getUser();
		
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		$listOrder = $app->getUserStateFromRequest($option . '.list.ordering', 'filter_order', 'u.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest($option . '.list.direction', 'filter_order_Dir', 'ASC', 'word');
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('a.name', 'a.username', 'a.registerDate', 'a.email', 'a.last_activity'))) {
			$listOrder = 'a.name';
			$app->setUserState($option . '.list.ordering', $listOrder);
		}

		if (!in_array(mb_strtoupper($listDirn), array('ASC', 'DESC'))) {
			$listDirn = 'ASC';
			$app->setUserState($option . '.list.direction', $listDirn);
		}
		
		$stats_fromdate = $app->getUserStateFromRequest($option . '.filter.stats_fromdate', 'fromdate');
		$stats_fromdate = trim(strip_tags($stats_fromdate));
		if ($stats_fromdate) {
			$stats_fromdate = mb_strtolower($stats_fromdate);
			$stats_fromdate = preg_replace('~[^\d|\-]+~i', '', $stats_fromdate);
		} else {
			$lastyear = time() - (6 * 30 * 24 * 3600);
			$stats_fromdate = date('Y-m-d', $lastyear);
			$app->setUserState($option . '.filter.stats_fromdate', $stats_fromdate);
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS a.* ";
		$query .= " FROM (
			SELECT s.*, u.username, u.name, u.email, u.registerDate, MAX(s.tstamp) as last_activity
			FROM #__extendedreg_stats as s 
			JOIN #__users as u ON u." . $this->dbo->quoteName('id') . " = s." . $this->dbo->quoteName('user_id') . "
			GROUP BY u." . $this->dbo->quoteName('id') . " ORDER BY u." . $this->dbo->quoteName('id') . "
		) as a ";
		
		$where = array();
		
		if ($export) {
			$cid = JRequest::getVar('cid');
			if (!is_array($cid)) $cid = array((int)$cid);
			$cid = array_unique($cid);
			JArrayHelper::toInteger($cid);
			$where[] = "a." . $this->dbo->quoteName('id') . " IN (" .  implode(',', $cid) . ")";
		} else {
			if ($stats_fromdate) {
				$where[] = "a." . $this->dbo->quoteName('last_activity') . " <= " .  $this->dbo->Quote($stats_fromdate . ' 00:00:00');
			}
		}
		
		if (count($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " ORDER BY " . $listOrder . " " . $listDirn;
		
		$this->dbo->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$result->items = $this->dbo->loadObjectList();
		if (!$result->items) $result->items = array();
		
		$this->dbo->setQuery('SELECT FOUND_ROWS();');
		$result->total = (int)$this->dbo->loadResult();
		
		return $result;
	}
	
	function getUserIPaddresses() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$loggeduser = JFactory::getUser();
		
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		$query = "SELECT SQL_CALC_FOUND_ROWS u.id, s.ip_addr, GROUP_CONCAT(DISTINCT u.id) as user_id_list, COUNT(DISTINCT u.id) as users_count";
		$query .= " FROM #__extendedreg_stats as s 
			JOIN #__users as u ON u." . $this->dbo->quoteName('id') . " = s." . $this->dbo->quoteName('user_id') . " ";
		$query .= " GROUP BY s." . $this->dbo->quoteName('ip_addr') . " ORDER BY users_count DESC, ip_addr";
		
		$this->dbo->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$result->items = $this->dbo->loadObjectList();
		if (!$result->items) $result->items = array();
		
		$this->dbo->setQuery('SELECT FOUND_ROWS();');
		$result->total = (int)$this->dbo->loadResult();
		
		return $result;
	}
	
	function addStats($ip_addr, $user_id, $action, $proxy, $port) {
		$this->dbo->setQuery("INSERT INTO #__extendedreg_stats (" . $this->dbo->quoteName('ip_addr') . ", " . $this->dbo->quoteName('user_id') . ", " . $this->dbo->quoteName('action') . ", " . $this->dbo->quoteName('proxy') . ", " . $this->dbo->quoteName('port') . ", " . $this->dbo->quoteName('tstamp') . ") 
			VALUES (" . $this->dbo->Quote($ip_addr) . ", " . $this->dbo->Quote($user_id) . ", " . $this->dbo->Quote($action) . ", " . $this->dbo->Quote($proxy) . ", " . $this->dbo->Quote($port) . ", " . $this->dbo->Quote(JvitalsTime::getUtc()->toSql()) . ") ");
		return $this->dbo->execute();
	}
	
	function getUsersAndIPaddresses() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS u.id as user_id, u.username, u.name, u.email, s.ip_addr";
		$query .= " FROM #__extendedreg_stats as s 
			JOIN #__users as u ON u." . $this->dbo->quoteName('id') . " = s." . $this->dbo->quoteName('user_id') . " ";
		
		$where = array();
		
		$cid = JRequest::getVar('cid');
		if (!is_array($cid)) $cid = array((int)$cid);
		$cid = array_unique($cid);
		JArrayHelper::toInteger($cid);
		if (count($cid)) {
			$where[] = "u." . $this->dbo->quoteName('id') . " IN (" .  implode(',', $cid) . ")";
		}
		
		if (count($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " GROUP BY u.id ORDER BY u.id, s.ip_addr";
		
		$this->dbo->setQuery($query);
		$result = $this->dbo->loadObjectList();
		if (!$result) $result = array();
		return $result;
	}
	
	function getStatsActionOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 'login', 'login');
		$result[] = JHtml::_('select.option', 'logout', 'logout');
		$result[] = JHtml::_('select.option', 'user_register', 'user_register');
		$result[] = JHtml::_('select.option', 'profile_edit', 'profile_edit');
		return $result;
	}
	
	function getStatsProxyOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_YES'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_NO'));
		return $result;
	}
	
	function deleteUsersByStats(&$statIDS) {
		$loggeduser = JFactory::getUser();
		
		// Sanitize ids.
		$statIDS = (array)$statIDS;
		$statIDS = array_unique($statIDS);
		JArrayHelper::toInteger($statIDS);
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		
		$this->dbo->setQuery("SELECT DISTINCT " . $this->dbo->quoteName('user_id') . " FROM #__extendedreg_stats WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $statIDS) . ")");
		$ids = $this->dbo->loadColumn();
		JArrayHelper::toInteger($ids);
		
		if (in_array($loggeduser->id, $ids)) {
			$this->setError(JText::_('COM_EXTENDEDREG_ERROR_CANNOT_DELETE_SELF'));
			return false;
		}
		
		foreach ($ids as $i => $user_id) {
			$canDelete = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
			$user = JFactory::getUser($user_id);
			
			if (!$iAmSuperAdmin) {
				if (JAccess::check($user->id, 'core.admin')) {
					$canDelete = false;
				}
			}
			
			if (!$canDelete) {
				unset($ids[$i]);
				continue;
			}
			
			// Delete from main table
			if (!$user->delete()) {
				$this->setError($user->getError());
				return false;
			}
		}
		
		if (count($ids)) {
			$this->dbo->setQuery("DELETE FROM #__extendedreg_users WHERE " . $this->dbo->quoteName('user_id') . " IN (" . implode(',', $ids). ")");
			if (!$this->dbo->execute()) {
				$this->setError($this->dbo->getErrorMsg());
				return false;
			}
		} else {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		return true;
	}
	
}