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

require_once ('default.php');

class ExtendedregModelUsers extends ExtendedregModelDefault {
	
	function __construct() {
		parent::__construct();

		// Load the Joomla! RAD layer
		if (version_compare(JvitalsDefines::joomlaVersion(), '3.2.0', 'ge') && !defined('FOF_INCLUDED')) {
			include_once JPATH_LIBRARIES . '/fof/include.php';
		}
	}
	
	function syncUsers() {
		$query = "SELECT " . $this->dbo->quoteName('id') . " FROM #__extendedreg_forms WHERE " . $this->dbo->quoteName('isdefault') . " = " . $this->dbo->Quote('1') . " LIMIT 1";
		$this->dbo->setQuery($query);
		$form_id = (int)$this->dbo->loadResult();
		
		$query = "INSERT INTO #__extendedreg_users ( " . $this->dbo->quoteName('user_id') . ",  " . $this->dbo->quoteName('acceptedterms') . ",  " . $this->dbo->quoteName('overage') . ",  " . $this->dbo->quoteName('approve') . ",  " . $this->dbo->quoteName('form_id') . ")
			SELECT u." . $this->dbo->quoteName('id') . ", '0', '0', (case WHEN u." . $this->dbo->quoteName('block') . " = '0' THEN '1' ELSE '0' end), (case WHEN " . (int)$form_id . " > 0 THEN " . (int)$form_id . " ELSE NULL end)
			FROM #__users AS u
				LEFT JOIN #__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id') . " 
			WHERE er." . $this->dbo->quoteName('user_id') . " IS NULL";
		$this->dbo->setQuery($query);
		$result = $this->dbo->execute();
		
		if ($result) {
			$query = "DELETE #__extendedreg_users 
				FROM #__extendedreg_users LEFT JOIN #__users 
					ON #__extendedreg_users." . $this->dbo->quoteName('user_id') . " = #__users." . $this->dbo->quoteName('id') . " 
				WHERE #__users." . $this->dbo->quoteName('id') . " IS NULL";
			$this->dbo->setQuery($query);
			$result = $this->dbo->execute();
		}
		
		return $result;
	}
	
	function getUserGroups() {
		$result = UsersHelper::getGroups();
		if (!$result) $result = array();
		return $result;
	}
	
	function getStateOptions() {
		$result = UsersHelper::getStateOptions();
		if (!$result) $result = array();
		return $result;
	}
	
	function getActiveOptions() {
		$result = UsersHelper::getActiveOptions();
		if (!$result) $result = array();
		return $result;
	}
	
	function getApprovedOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_APPROVED'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_UNAPPROVED'));
		return $result;
	}
	
	function getTermsOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_ACCEPTEDTERMS'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_NOT_ACCEPTEDTERMS'));
		return $result;
	}
	
	function getAgeOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_OVERAGE'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_NOT_OVERAGE'));
		return $result;
	}
	
	function getUsersList() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$loggeduser = JFactory::getUser();
		
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		$listOrder = $app->getUserStateFromRequest($option . '.list.ordering', 'filter_order', 'u.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest($option . '.list.direction', 'filter_order_Dir', 'ASC', 'word');
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('u.name', 'u.username', 'u.block', 'u.email', 'u.lastvisitDate', 'u.activation', 'u.registerDate', 'u.id', 'er.approve', 'er.acceptedterms', 'er.overage')) && !preg_match('~^er\.cf_.+$~smi', $listOrder)) {
			$listOrder = 'u.name';
			$app->setUserState($option . '.list.ordering', $listOrder);
		}

		if (!in_array(mb_strtoupper($listDirn), array('ASC', 'DESC'))) {
			$listDirn = 'ASC';
			$app->setUserState($option . '.list.direction', $listDirn);
		}
		
		$filter_search = $app->getUserStateFromRequest($option . '.filter.users_search', 'filter_search');
		$filter_search = trim(strip_tags($filter_search));
		if ($filter_search) {
			$filter_search = mb_strtolower($filter_search);
			$filter_search = preg_replace('~[^\w|\s|\d]+~i', ' ', $filter_search);
			$filter_search = preg_replace('~\s+~i', '%', trim($filter_search));
		}
		
		$filter_state = $app->getUserStateFromRequest($option . '.filter.users_state', 'filter_state');
		$filter_active = $app->getUserStateFromRequest($option . '.filter.users_active', 'filter_active');
		$filter_approved = $app->getUserStateFromRequest($option . '.filter.users_approved', 'filter_approved');
		$filter_terms = $app->getUserStateFromRequest($option . '.filter.users_terms', 'filter_terms');
		$filter_age = $app->getUserStateFromRequest($option . '.filter.users_age', 'filter_age');

		$query = "SELECT SQL_CALC_FOUND_ROWS u.*, er.*, '' as group_names
			FROM #__users as u 
			JOIN #__extendedreg_users as er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id') . " ";
		
		$where = array();
		if (!is_null($filter_state) && is_numeric($filter_state)) {
			$where[] = "u." . $this->dbo->quoteName('block') . " = " . (int)((int)$filter_state == 0 ? '0' : '1');
		}
		
		if (!is_null($filter_active) && is_numeric($filter_active)) {
			$where[] = "u." . $this->dbo->quoteName('activation') . " " . ((int)$filter_active ? '' : '!') . "= " . $this->dbo->Quote('');
		}
		
		if (!is_null($filter_terms) && is_numeric($filter_terms)) {
			$where[] = "er." . $this->dbo->quoteName('acceptedterms') . " = " . $this->dbo->Quote((int)$filter_terms == 0 ? '0' : '1');
		}
		
		if (!is_null($filter_approved) && is_numeric($filter_approved)) {
			$where[] = "er." . $this->dbo->quoteName('approve') . " = " . $this->dbo->Quote((int)$filter_approved == 0 ? '0' : '1');
		}
		
		if (!is_null($filter_age) && is_numeric($filter_age)) {
			$where[] = "er." . $this->dbo->quoteName('overage') . " = " . $this->dbo->Quote((int)$filter_age == 0 ? '0' : '1');
		}
		
		if ($filter_search) {
			$searchEscaped = $this->dbo->Quote('%' . $filter_search . '%', false);
			$where[] = "(
				er." . $this->dbo->quoteName('ip_addr') . " LIKE " . $searchEscaped . " OR 
				u." . $this->dbo->quoteName('username') . " LIKE " . $searchEscaped . " OR 
				u." . $this->dbo->quoteName('email') . " LIKE " . $searchEscaped . " OR 
				u." . $this->dbo->quoteName('name') . " LIKE " . $searchEscaped . "
			)";
		}
		
		if (count($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " ORDER BY " . $listOrder . " " . $listDirn;
		
		$this->dbo->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$result->items = $this->dbo->loadObjectList('user_id');
		if (!$result->items) $result->items = array();
		
		$this->dbo->setQuery('SELECT FOUND_ROWS();');
		$result->total = (int)$this->dbo->loadResult();
		
		return $result;
	}
	
	function getGroupsForUserList(&$items) {
		if (!count($items)) {
			return false;
		}
		
		$userIDs = array_keys($items);
		$userIDs = array_unique($userIDs);
		JArrayHelper::toInteger($userIDs);
		
		$query = "SELECT map." . $this->dbo->quoteName('user_id') . ", GROUP_CONCAT(g." . $this->dbo->quoteName('title') . " SEPARATOR " . $this->dbo->Quote(";") . ") AS group_names
			FROM #__usergroups g 
			JOIN #__user_usergroup_map map ON g." . $this->dbo->quoteName('id') . " = map." . $this->dbo->quoteName('group_id') . " 
				AND map." . $this->dbo->quoteName('user_id') . " IN (" . implode(',', $userIDs) . ") 
			GROUP BY map." . $this->dbo->quoteName('user_id');

		$this->dbo->setQuery($query);
		$result = $this->dbo->loadObjectList();
		if (!$result) $result = array();
		
		foreach ($result as $user) {
			if (isset($items[$user->user_id])) {
				$items[$user->user_id]->group_names = $user->group_names;
			}
		}
		
		return true;
	}
	
	function getUsersForExport() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$loggeduser = JFactory::getUser();
		
		$listOrder = $app->getUserStateFromRequest($option . '.list.ordering', 'filter_order', 'u.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest($option . '.list.direction', 'filter_order_Dir', 'ASC', 'word');
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('u.name', 'u.username', 'u.block', 'u.email', 'u.lastvisitDate', 'u.activation', 'u.registerDate', 'u.id', 'er.approve', 'er.acceptedterms', 'er.overage')) && !preg_match('~^er\.cf_.+$~smi', $listOrder)) {
			$listOrder = 'u.name';
			$app->setUserState($option . '.list.ordering', $listOrder);
		}

		if (!in_array(mb_strtoupper($listDirn), array('ASC', 'DESC'))) {
			$listDirn = 'ASC';
			$app->setUserState($option . '.list.direction', $listDirn);
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS u.*, er.*";
		$query .= ", GROUP_CONCAT(g." . $this->dbo->quoteName('title') . " SEPARATOR " . $this->dbo->Quote(";") . ") AS group_names ";
		
		$query .= " FROM #__users as u 
			JOIN #__extendedreg_users as er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id') . " ";
		
		$query .= "JOIN #__user_usergroup_map AS map ON map." . $this->dbo->quoteName('user_id') . " = u." . $this->dbo->quoteName('id') . " 
			JOIN #__usergroups AS g ON g." . $this->dbo->quoteName('id') . " = map." . $this->dbo->quoteName('group_id') . " ";

		
		$cid = JRequest::getVar('cid');
		if (!is_array($cid)) $cid = array((int)$cid);
		$cid = array_unique($cid);
		JArrayHelper::toInteger($cid);
		
		if (is_array($cid) && count($cid)) {
			$query .= " WHERE u." . $this->dbo->quoteName('id') . " IN (" . implode(',', $cid) . ")";
		} else {
			$query .= " WHERE false";
		}
		$query .= " GROUP BY u." . $this->dbo->quoteName('id') . " ORDER BY " . $listOrder . " " . $listDirn;
		
		$this->dbo->setQuery($query);
		$result = $this->dbo->loadObjectList();
		if (!$result) $result = array();
		return $result;
	}
	
	function set_block(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		foreach ($ids as $i => $user_id) {
			$canChange = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
			$user = JFactory::getUser($user_id);
			
			if (!$iAmSuperAdmin) {
				// If this group is super admin and this user is not super admin, $canChange is false
				if (JAccess::check($user->id, 'core.admin')) {
					$canChange = false;
				}

			}
			
			if (!$canChange) {
				unset($ids[$i]);
				continue;
			}
		}
		
		if (count($ids)) {
			$this->dbo->setQuery("UPDATE #__users SET " . $this->dbo->quoteName('block') . " = " . ((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
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
	
	function activate(&$ids) {
		$app = JFactory::getApplication();
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		if ($app->isAdmin()) {
			$loggeduser = JFactory::getUser();
			
			if (in_array($loggeduser->id, $ids)) {
				$this->setError(JText::_('COM_EXTENDEDREG_ERROR_CANNOT_DEACTIVATE_SELF'));
				return false;
			}
			
			$iAmSuperAdmin = $loggeduser->authorise('core.admin');
			foreach ($ids as $i => $user_id) {
				$canChange = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
				$user = JFactory::getUser($user_id);
				
				if (!$iAmSuperAdmin) {
					// If this group is super admin and this user is not super admin, $canChange is false
					if (JAccess::check($user->id, 'core.admin')) {
						$canChange = false;
					}

				}
				
				if (!$canChange) {
					unset($ids[$i]);
					continue;
				}
			}
			
			$block = 0;
		} else {
			$conf = $this->getConfObj();
			if ((int)$conf->enable_admin_approval) {
				$block = 1;
			} else {
				$block = 0;
			}
		}
		
		if (count($ids)) {
			$this->dbo->setQuery("UPDATE #__users SET " . $this->dbo->quoteName('block') . " = " . $this->dbo->Quote($block) . ", " . $this->dbo->quoteName('activation') . " = '' WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
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
	
	function set_approve(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		foreach ($ids as $i => $user_id) {
			$canChange = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
			$user = JFactory::getUser($user_id);
			
			if (!$iAmSuperAdmin) {
				// If this group is super admin and this user is not super admin, $canChange is false
				if (JAccess::check($user->id, 'core.admin')) {
					$canChange = false;
				}

			}
			
			if (!$canChange) {
				unset($ids[$i]);
				continue;
			}
		}
		
		if (count($ids)) {
			$this->dbo->setQuery("UPDATE #__extendedreg_users SET 
				" . $this->dbo->quoteName('approve') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . ", 
				" . $this->dbo->quoteName('approve_hash') . " = " . $this->dbo->Quote((int)$value ? '' : md5(time() . uniqid())) . " 
				WHERE " . $this->dbo->quoteName('user_id') . " IN (" . implode(',', $ids). ")");
			if (!$this->dbo->execute()) {
				$this->setError($this->dbo->getErrorMsg());
				return false;
			}
			
			$this->dbo->setQuery("UPDATE #__users SET " . $this->dbo->quoteName('block') . " = " . $this->dbo->Quote((int)$value ? '0' : '1') . ", " . $this->dbo->quoteName('activation') . " = '' WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
			if (!$this->dbo->execute()) {
				$this->setError($this->dbo->getErrorMsg());
				return false;
			}
		} else {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		if ((int)$value == 1) {
			// Send emails
			$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
			foreach ($ids as $i => $user_id) {
				$approvedUser = $this->loadUserById((int)$user_id);
				$form = $formsModel->loadForm((int)$approvedUser->form_id);
				$userEmailSent = erHelperMail::sendUserApproveDoneMail($approvedUser, (int)$form->id);
			}
		}
		
		return true;
	}
	
	function set_approve_front($user_id) {
		if (!(int)$user_id) {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		$this->dbo->setQuery("UPDATE #__extendedreg_users SET 
			" . $this->dbo->quoteName('approve') . " = " . $this->dbo->Quote('1') . ", 
			" . $this->dbo->quoteName('approve_hash') . " = " . $this->dbo->Quote('') . " 
			WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id);
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		$this->dbo->setQuery("UPDATE #__users SET " . $this->dbo->quoteName('block') . " = " . $this->dbo->Quote('0') . ", " . $this->dbo->quoteName('activation') . " = '' WHERE " . $this->dbo->quoteName('id') . " = " . (int)$user_id);
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$approvedUser = $this->loadUserById((int)$user_id);
		$form = $formsModel->loadForm((int)$approvedUser->form_id);
		$userEmailSent = erHelperMail::sendUserApproveDoneMail($approvedUser, (int)$form->id);
		
		return true;
	}
	
	function set_terms(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		foreach ($ids as $i => $user_id) {
			$canChange = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
			$user = JFactory::getUser($user_id);
			
			if (!$iAmSuperAdmin) {
				// If this group is super admin and this user is not super admin, $canChange is false
				if (JAccess::check($user->id, 'core.admin')) {
					$canChange = false;
				}

			}
			
			if (!$canChange) {
				unset($ids[$i]);
				continue;
			}
		}
		
		if (count($ids)) {
			$this->dbo->setQuery("UPDATE #__extendedreg_users SET " . $this->dbo->quoteName('acceptedterms') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('user_id') . " IN (" . implode(',', $ids). ")");
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
	
	function set_overage(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		foreach ($ids as $i => $user_id) {
			$canChange = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
			$user = JFactory::getUser($user_id);
			
			if (!$iAmSuperAdmin) {
				// If this group is super admin and this user is not super admin, $canChange is false
				if (JAccess::check($user->id, 'core.admin')) {
					$canChange = false;
				}

			}
			
			if (!$canChange) {
				unset($ids[$i]);
				continue;
			}
		}
		
		if (count($ids)) {
			$this->dbo->setQuery("UPDATE #__extendedreg_users SET " . $this->dbo->quoteName('overage') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('user_id') . " IN (" . implode(',', $ids). ")");
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
	
	function delete(&$ids) {
		$loggeduser = JFactory::getUser();
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		
		if (in_array($loggeduser->id, $ids)) {
			$this->setError(JText::_('COM_EXTENDEDREG_ERROR_CANNOT_DELETE_SELF'));
			return false;
		}
		
		foreach ($ids as $i => $user_id) {
			$canDelete = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
			$user = JFactory::getUser($user_id);
			
			if (!$iAmSuperAdmin) {
				// If this group is super admin and this user is not super admin, $canDelete is false
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
	
	function loadUser() {
		$cid = JRequest::getVar('cid');
		if (!$cid) {
			$cid = JRequest::getVar('id');
		}
		if (is_array($cid)) $cid = $cid[0];
		$cid = (int)$cid;
		
		$result = $this->loadUserById($cid);
		return $result;
	}
	
	function loadUserById($id) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("u.*, er.*")
			->from("#__users AS u")
			->join("INNER", "#__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id'))
			->where("u." . $this->dbo->quoteName('id') . " = " . (int)$id);
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObject();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) {
			$result = new stdClass;
			$result->id = 0;
			$result->form_id = 0;
			$result->user_id = 0;
			$result->notes = '';
			$result->ip_addr = '';
			$result->registerDate = '';
			$result->lastvisitDate = '';
			$result->params = '{}';
		}
		return $result;
	}
	
	function loadUserByEmail($email) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("u.*, er.*")
			->from("#__users AS u")
			->join("INNER", "#__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id'))
			->where("LOWER(u." . $this->dbo->quoteName('email') . ") = " . $this->dbo->quote(mb_strtolower($email)));
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObject();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) $result = new stdClass;
		return $result;
	}
	
	function loadUserByUsername($username) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("u.*, er.*")
			->from("#__users AS u")
			->join("INNER", "#__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id'))
			->where("u." . $this->dbo->quoteName('username') . " = " . $this->dbo->quote($username));
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObject();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) $result = new stdClass;
		return $result;
	}
	
	function loadUserByActivation($activation, $block = 1) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("u.*, er.*")
			->from("#__users AS u")
			->join("INNER", "#__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id'))
			->where("u." . $this->dbo->quoteName('activation') . " = " . $this->dbo->quote($activation))
			->where("u." . $this->dbo->quoteName('block') . " = " . $this->dbo->quote($block));
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObject();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) $result = new stdClass;
		return $result;
	}
	
	function loadUserByApproveHash($hash, $user_id) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("u.*, er.*")
			->from("#__users AS u")
			->join("INNER", "#__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id'))
			->where("er." . $this->dbo->quoteName('approve_hash') . " = " . $this->dbo->quote($hash))
			->where("u." . $this->dbo->quoteName('id') . " = " . (int)$user_id);
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObject();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) $result = new stdClass;
		return $result;
	}
	
	function loadUsersByIdArray($id) {
		JArrayHelper::toInteger($id, array(0));
		
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("u.*, er.*")
			->from("#__users AS u")
			->join("INNER", "#__extendedreg_users AS er ON u." . $this->dbo->quoteName('id') . " = er." . $this->dbo->quoteName('user_id'))
			->where("u." . $this->dbo->quoteName('id') . " IN (" . implode(',', $id) . ")");
		
		// Setup the query
		$this->dbo->setQuery($query);
		
		try {
			$result = $this->dbo->loadObjectList();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		if (!$result) $result = array();
		return $result;
	}
	
	function getAssignedGroups($id) {
		$result = array();
		if (!(int)$id) {
			$config = JComponentHelper::getParams('com_users');
			if ($groupId = $config->get('new_usertype')) {
				$result[] = $groupId;
			}
		} else {
			jimport('joomla.user.helper');
			$result = JUserHelper::getUserGroups($id);
		}
		return $result;
	}
	
	function setUserForm() {
		$id = (int)JRequest::getVar('id');
		if (!$id) return false;
		$form_id = (int)JRequest::getVar('form_id');
		$query = "UPDATE #__extendedreg_users SET " . $this->dbo->quoteName('form_id') . " = " .  ((int)$form_id ? (int)$form_id : 'null') ." WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$id;
		$this->dbo->setQuery($query);
		return (boolean)$this->dbo->execute();
	}
	
	private function changeDataIfNeeded(&$post, &$conf) {
		if (isset($post['verify-password'])) {
			$post['password2'] = $post['verify-password'];
			unset($post['verify-password']);
		}
		
		if (trim($conf->replace_name_with)) {
			$post['name'] = trim($conf->replace_name_with);
			if (preg_match_all('~\{(.+?)\}~', $post['name'], $m)) {
				$replace = array();
				foreach ($m[1] as $fldname) {
					$replace[] = trim($post[$fldname]);
				}
				$post['name'] = trim(str_replace($m[0], $replace, $post['name']));
			}
		}
		if (trim($conf->replace_email_with)) {
			$post['email'] = trim($conf->replace_email_with);
			if (preg_match_all('~\{(.+?)\}~', $post['email'], $m)) {
				$replace = array();
				foreach ($m[1] as $fldname) {
					$replace[] = trim($post[$fldname]);
				}
				$post['email'] = trim(str_replace($m[0], $replace, $post['email']));
			}
		}
		if ((int)$conf->email_for_username) {
			$post['username'] = $post['email'];
		} else {
			if (trim($conf->replace_username_with)) {
				$post['username'] = trim($conf->replace_username_with);
				if (preg_match_all('~\{(.+?)\}~', $post['username'], $m)) {
					$replace = array();
					foreach ($m[1] as $fldname) {
						$replace[] = trim($post[$fldname]);
					}
					$post['username'] = trim(str_replace($m[0], $replace, $post['username']));
				}
			}
		}
		if (trim($conf->replace_pass_with)) {
			$post['password'] = trim($conf->replace_pass_with);
			if (preg_match_all('~\{(.+?)\}~', $post['password'], $m)) {
				$replace = array();
				foreach ($m[1] as $fldname) {
					$replace[] = trim(JRequest::getVar($fldname, '', 'post', 'string', JREQUEST_ALLOWRAW));
				}
				$post['password'] = trim(str_replace($m[0], $replace, $post['password']));
			}
		}
		if (trim($conf->replace_confirmpass_with)) {
			$post['password2'] = trim($conf->replace_confirmpass_with);
			if (preg_match_all('~\{(.+?)\}~', $post['password2'], $m)) {
				$replace = array();
				foreach ($m[1] as $fldname) {
					$replace[] = trim(JRequest::getVar($fldname, '', 'post', 'string', JREQUEST_ALLOWRAW));
				}
				$post['password2'] = trim(str_replace($m[0], $replace, $post['password2']));
			}
		}
	}
	
	function saveUser() {
		$app = JFactory::getApplication();
		$task = JRequest::getVar('task');
		$id = (int)JRequest::getVar('id');
		$fid = (int)JRequest::getVar('fid');
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		$conf = $this->getConfObj();
		$saveuser = JUser::getInstance($id);
		$loggeduser = JFactory::getUser();
		
		if ($app->isAdmin()) {
			$url = 'index.php?option=com_extendedreg&task=users.' . ($id ? 'edit&cid=' . $id : 'add') . ($fid ? '&fid=' . $fid : '');
		} else {
			$url = 'index.php?option=com_extendedreg&task=users.profile';
		}
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		
		if ($app->isAdmin()) {
			if ($id && $id == (int)$loggeduser->id && (int)$post['block']) {
				$app->enqueueMessage(JText::_('COM_EXTENDEDREG_ERROR_CANNOT_BLOCK_SELF'), 'error');
				$app->redirect(JRoute::_($url, false));
				jexit();
			}
			
			if ($iAmSuperAdmin && $id && $id == (int)$loggeduser->id) {
				// Check that at least one of our new groups is Super Admin
				$stillSuperAdmin = false;
				$myNewGroups = $post['groups'];
				foreach ($myNewGroups as $group) {
					$stillSuperAdmin = ($stillSuperAdmin) ? ($stillSuperAdmin) : JAccess::checkGroup($group, 'core.admin');
				}

				if (!$stillSuperAdmin) {
					$app->enqueueMessage(JText::_('COM_EXTENDEDREG_ERROR_CANNOT_DEMOTE_SELF'), 'error');
					$app->redirect(JRoute::_($url, false));
					jexit();
				}
			}
		} else {
			if ((int)$id != (int)$loggeduser->id) {
				JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
				jexit();
			}
			
			if (!(int)$conf->allow_uname_change) {
				$post['username'] = $saveuser->username;
			}
		}
		
		// Handle the two factor authentication setup
		
		if (isset($post['jform']) && isset($post['jform']['twofactor'])) {
			$twoFactorMethod = $post['jform']['twofactor']['method'];

			// Get the current One Time Password (two factor auth) configuration
			$otpConfig = $this->getOtpConfig($id);

			if ($twoFactorMethod != 'none') {
				// Run the plugins
				FOFPlatform::getInstance()->importPlugin('twofactorauth');
				$otpConfigReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorApplyConfiguration', array($twoFactorMethod));

				// Look for a valid reply
				foreach ($otpConfigReplies as $reply) {
					if (!is_object($reply) || empty($reply->method) || ($reply->method != $twoFactorMethod)) {
						continue;
					}

					$otpConfig->method = $reply->method;
					$otpConfig->config = $reply->config;

					break;
				}

				// Save OTP configuration.
				$this->setOtpConfig($id, $otpConfig);

				// Generate one time emergency passwords if required (depleted or not set)
				if (empty($otpConfig->otep)) {
					$oteps = $this->generateOteps($id);
				}
			} else {
				$otpConfig->method = 'none';
				$otpConfig->config = array();
				$this->setOtpConfig($id, $otpConfig);
			}

			// Unset the raw data
			unset($post['jform']);

			// Reload the user record with the updated OTP configuration
			$saveuser->load($id);
		}
		
		// Change data if needed
		$this->changeDataIfNeeded($post, $conf);
		
		$errorArray = array();
		
		// Start checks and validations
		
		// Validate name
		if (!mb_strlen(trim($post['name']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_NAME'));
		}
		
		// Validate username
		if (!mb_strlen(trim($post['username']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_USERNAME'));
		} elseif ((boolean)$this->checkUsernameExists(trim($post['username']), (int)$id)) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_USERNAME_EXISTS_ERROR');
		} else {
			$blUsernames = array();
			if (trim($conf->blacklist_usernames)) $blUsernames = explode("\n", trim($conf->blacklist_usernames));
			
			$found = false;
			if (!$iAmSuperAdmin) {
				if (count($blUsernames)) {
					foreach ($blUsernames as $test) {
						$test = str_replace('*', '.*?', trim($test));
						if (preg_match('~^' . addslashes($test) . '$~smi', trim($post['username']))) {
							$found = true;
							break;
						}
					}
				}
			}
			
			if ($found) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_USERNAME_BLACKLISTED_ERROR');
			}
		}
		
		// Validate email
		if (!mb_strlen(trim($post['email']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_EMAIL'));
		} elseif (!JvitalsHelper::validateEmail(trim($post['email']))) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_EMAIL_NOTVALID_ERROR');
		} elseif ((boolean)$this->checkEmailExists(trim($post['email']), (int)$id)) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_EMAIL_EXISTS_ERROR');
		} else {
			$blEmails = array();
			if (trim($conf->blacklist_emails)) $blEmails = explode("\n", trim($conf->blacklist_emails));
			
			$found = false;
			if (!$iAmSuperAdmin) {
				if (count($blEmails)) {
					foreach ($blEmails as $test) {
						$test = str_replace('*', '.*?', trim($test));
						if (preg_match('~^' . addslashes($test) . '$~smi', trim($post['email']))) {
							$found = true;
							break;
						}
					}
				}
			}
			
			if ($found) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_EMAIL_BLACKLISTED_ERROR');
			}
		}
		
		$password_updated = false;
		if ($app->isAdmin() && !(int)$id) {
			if (!mb_strlen(trim($post['password']))) {
				$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_PASSWORD'));
			} elseif (!mb_strlen(trim($post['password2']))) {
				$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD'));
			} else {
				if (trim($post['password']) != trim($post['password2'])) {
					$errorArray[] = JText::_('COM_EXTENDEDREG_PASSWORDS_DO_NOT_MATCH');
				} else {
					if (!erHelperPassword::validate($post['password'])) {
						$errorArray[] = erHelperPassword::errorString();
					} else {
						$password_updated = true;
					}
				}
			}
		} else {
			if (isset($post['password']) && trim($post['password'])) {
				if (!erHelperPassword::validate($post['password'])) {
					$app->enqueueMessage(erHelperPassword::errorString(), 'error');
					$app->redirect(JRoute::_($url, false));
					jexit();
				} else {
					$password_updated = true;
				}
			}
		}
		
		// Custom fields
		$extrafields = "";
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$ef = $formsModel->getExtraFieldsInfo();
		
		$session = JFactory::getSession();
		$fh1 = $session->get('erFieldsHash' . (int)$fid, null, 'extendedreg');
		$fh2 = JRequest::getVar('fh');
		if ($fh1 != $fh2) {
			JError::raiseError(83005, JText::_('COM_EXTENDEDREG_INVALID_FORM'));
			jexit();
		}
		
		$formFields = unserialize(base64_decode($fh1));
		if (count($formFields) <= 1) {
			JError::raiseError(83005, JText::_('COM_EXTENDEDREG_INVALID_FORM'));
			jexit();
		}
		unset($formFields['time']);
		unset($formFields['secure']);
		$formFields = array_flip($formFields);
		
		if (count($ef)) {
			foreach ($ef as $fld) {
				if (!(isset($formFields['custom_' . $fld->id]) || isset($formFields['group_' . $fld->grpid]))) {
					continue;
				} else {
					$fldObject = erHelperAddons::getFieldType($fld);
					if (!$fldObject->hasFormField()) {
						continue;
					}
					if (!$fldObject->serversideValidation($post)) {
						$errorArray[] = $fldObject->getError();
					} else {
						if ($app->isAdmin() || (int)$fld->editable) {
							$value = $post[$fld->name];
							if (method_exists($fldObject, "prepareDataForSave")) {
								$value = $fldObject->prepareDataForSave($value);
								$post[$fld->name] = $value;
							}
							if (method_exists($fldObject, "manipulatePostData")) {
								$fldObject->manipulatePostData($post);
							}
							if (is_array($value)) $value = implode('#!#', $value);
							$extrafields .= "," . $this->dbo->quoteName($fld->name) . " = " . $this->dbo->Quote($value) . " " . "\n";
						}
					}
				}
			}
		}
		
		if (count($errorArray)) {
			$app->enqueueMessage(implode('<br/>', $errorArray), 'error');
			$app->redirect(JRoute::_($url, false));
			jexit();
		}
		
		// Bind the data.
		if (!$saveuser->bind($post)) {
			$app->enqueueMessage($saveuser->getError(), 'error');
			$app->redirect(JRoute::_($url, false));
			jexit();
		}
		
		// Store the data.
		if (!$saveuser->save()) {
			$app->enqueueMessage($saveuser->getError(), 'error');
			$app->redirect(JRoute::_($url, false));
			jexit();
		}
		
		$defaultfields = " " . $this->dbo->quoteName('user_id') . " = " . (int)$saveuser->id . " ";
		
		if ($app->isAdmin()) {
			$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_users WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$saveuser->id . " LIMIT 1");
			$exists = (int)$this->dbo->LoadResult();
			if (!$exists) {
				$this->dbo->setQuery("INSERT INTO #__extendedreg_users (" . $this->dbo->quoteName('user_id') . ") VALUES (" . (int)$saveuser->id . ")");
				$this->dbo->execute();
			}
			
			$defaultfields = " " . $this->dbo->quoteName('acceptedterms') . " = " . $this->dbo->Quote((int)$post['acceptedterms']) . ", 
			" . $this->dbo->quoteName('form_id') . " = " . $this->dbo->Quote((int)$fid) . ", 
			" . $this->dbo->quoteName('notes') . " = " . $this->dbo->Quote($post['notes']) . ", 
			" . $this->dbo->quoteName('overage') . " = " . $this->dbo->Quote((int)$post['overage']) . ", 
			" . $this->dbo->quoteName('approve') . " = " . $this->dbo->Quote((int)$post['approve']) . " ";
		}
		
		if ($password_updated) {
			$defaultfields .= ", " . $this->dbo->quoteName('last_pass_change') . " = " . $this->dbo->Quote(JvitalsTime::getUtc()->toSql()) . " ";
		}
		
		$this->dbo->setQuery("UPDATE #__extendedreg_users SET 
			" . $defaultfields . "
			" . $extrafields . "
		WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$saveuser->id);
		if (!$this->dbo->execute()) {
			$app->enqueueMessage($this->dbo->getErrorMsg(), 'error');
			$app->redirect(JRoute::_($url, false));
			jexit();
		}
		
		// Load plugins from jvPlugins
		$jvPlugins = JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'jvPlugins';
		if (is_dir($jvPlugins)) {
			JPluginHelper::importPlugin('jvPlugins');
		}
		
		// Trigger event for pluigns
		$extendedreg = $this->loadUserById((int)$saveuser->id);
		$saveuser->set('extreg', $extendedreg);
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onExtregUserProfile', array((int)$fid, $saveuser, $extendedreg));
		
		if ($app->isAdmin()) {
			if ($task == 'apply') {
				$url = 'index.php?option=com_extendedreg&task=users.edit&cid=' . (int)$saveuser->id;
			} elseif ($task == 'savenew') {
				$url = 'index.php?option=com_extendedreg&task=users.add';
			} else {
				$url = 'index.php?option=com_extendedreg&task=users.manage';
			}
		} else {
			$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
			$proxy = (int)JvitalsHelper::checkForProxy();
			$this->setLastKnownIP((int)$saveuser->id, $ip_addr);
			$this->addStats($ip_addr, (int)$saveuser->id, 'profile_edit', $proxy, ($_SERVER['REMOTE_PORT'] ? $_SERVER['REMOTE_PORT'] : ''));
			
			// Register the needed session variables
			$session->set('user', $saveuser);
		}
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_USER_SAVED'));
		$app->redirect(JRoute::_($url, false));
		jexit();
	}
	
	public function saveTfasetup() {
		$app = JFactory::getApplication();
		$post = JRequest::get('post');
		$loggeduser = JFactory::getUser();
		$id = (int)$loggeduser->id;
		$saveuser = JUser::getInstance($id);
		
		// Handle the two factor authentication setup
		
		if (isset($post['jform']) && isset($post['jform']['twofactor'])) {
			$twoFactorMethod = $post['jform']['twofactor']['method'];

			// Get the current One Time Password (two factor auth) configuration
			$otpConfig = $this->getOtpConfig($id);

			if ($twoFactorMethod != 'none') {
				// Run the plugins
				FOFPlatform::getInstance()->importPlugin('twofactorauth');
				$otpConfigReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorApplyConfiguration', array($twoFactorMethod));

				// Look for a valid reply
				foreach ($otpConfigReplies as $reply) {
					if (!is_object($reply) || empty($reply->method) || ($reply->method != $twoFactorMethod)) {
						continue;
					}

					$otpConfig->method = $reply->method;
					$otpConfig->config = $reply->config;

					break;
				}

				// Save OTP configuration.
				$this->setOtpConfig($id, $otpConfig);

				// Generate one time emergency passwords if required (depleted or not set)
				if (empty($otpConfig->otep)) {
					$oteps = $this->generateOteps($id);
				}
			} else {
				$otpConfig->method = 'none';
				$otpConfig->config = array();
				$this->setOtpConfig($id, $otpConfig);
			}

			// Unset the raw data
			unset($post['jform']);

			// Reload the user record with the updated OTP configuration
			// $saveuser->load($id);
		}
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_USER_TFASETUP_SAVED'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.profile&layout=tfasetup', false));
		jexit();
	}
	
	function registerUser() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			$app->redirect(JURI::base(true) . '/');
			jexit();
		}
		
		$conf = $this->getConfObj();
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		$real_IP_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		
		if ((int)$conf->disable_ip_track) {
			$post['ip_addr'] = '0.0.0.0';
		} else {
			$post['ip_addr'] = $real_IP_addr;
		}
		
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$form = $formsModel->loadForm((int)$post['fid']);
		$errorURL = JRoute::_('index.php?option=com_extendedreg&task=users.register&fid='.(int)$form->id, false);
		
		if (!(int)$conf->allow_user_registration) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$session = JFactory::getSession();
		$fh1 = $session->get('erFieldsHash' . (int)$form->id, null, 'extendedreg');
		$fh2 = JRequest::getVar('fh');
		if ($fh1 != $fh2) {
			JError::raiseError(83005, JText::_('COM_EXTENDEDREG_INVALID_FORM'));
			jexit();
		}
		
		$formFields = unserialize(base64_decode($fh1));
		if (count($formFields) <= 1) {
			JError::raiseError(83005, JText::_('COM_EXTENDEDREG_INVALID_FORM'));
			jexit();
		}
		unset($formFields['time']);
		unset($formFields['secure']);
		$formFields = array_flip($formFields);
		
		// Change data if needed
		$this->changeDataIfNeeded($post, $conf);
		
		// Generate Random Username
		if ((int)$conf->generate_random_uname) {
			$unameLength = mt_rand(5, 20);
			$post['username'] = JUserHelper::genRandomPassword($unameLength);
			$post['username'] = preg_replace('~[^\w|\d]+~', '', $post['username']);
			$cnt = 1;
			while ((boolean)$this->checkUsernameExists(trim($post['username']))) {
				$cnt ++;
			}
			if ($cnt) {
				$post['username'] = $post['username'] . $cnt;
			}
		}
		
		// Generate Random Password
		if ((int)$conf->generate_random_pass) {
			$passLength1 = mt_rand(5, 10);
			$passLength2 = mt_rand(1, 5);
			$randomPassword = JUserHelper::genRandomPassword($passLength1) . JUserHelper::genRandomPassword($passLength2);
			$post['password'] = $randomPassword;
			$post['password2'] = $randomPassword;
			$post['verify-password'] = $randomPassword;
		}
		
		$session = JFactory::getSession();
		$fldsession = $session->get('erFldSession', null, 'extendedreg');
		if (trim($fldsession)) {
			$fldsession = unserialize(base64_decode($fldsession));
		}
		if (!is_array($fldsession)) $fldsession = array();
		
		$errorArray = array();
		
		// Start checks and validations
		if ($real_IP_addr != '0.0.0.0') {
			$blIPaddresses = array();
			if (trim($conf->blacklist_ips)) $blIPaddresses = explode("\n", trim($conf->blacklist_ips));
			
			$foundIP = false;
			if (count($blIPaddresses)) {
				foreach ($blIPaddresses as $testIP) {
					$testIP = str_replace('*', '##all##', trim($testIP));
					$testIP = preg_quote($testIP);
					$testIP = str_replace('##all##', '.*?', $testIP);
					if (preg_match('~^' . $testIP . '$~smi', trim($real_IP_addr))) {
						$foundIP = true;
						break;
					}
				}
			}
			
			if ($foundIP) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_IPADDR_BLACKLISTED_ERROR');
			}
		}
		
		$proxy = (int)JvitalsHelper::checkForProxy();
		if ((int)$conf->forbid_proxies && $proxy) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_USING_PROXY_FORBIDDEN_ERROR');
		}
		
		// Validate name
		if (!mb_strlen(trim($post['name']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_NAME'));
		} else {
			$fldsession['extreg_fld_name'] = $post['name'];
		}
		
		// Validate username
		if (!mb_strlen(trim($post['username']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_USERNAME'));
		} elseif ((boolean)$this->checkUsernameExists(trim($post['username']))) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_USERNAME_EXISTS_ERROR');
		} else {
			$blUsernames = array();
			if (trim($conf->blacklist_usernames)) $blUsernames = explode("\n", trim($conf->blacklist_usernames));
			
			$found = false;
			if (count($blUsernames)) {
				foreach ($blUsernames as $test) {
					$test = str_replace('*', '.*?', trim($test));
					if (preg_match('~^' . addslashes($test) . '$~smi', trim($post['username']))) {
						$found = true;
						break;
					}
				}
			}
			
			if ($found) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_USERNAME_BLACKLISTED_ERROR');
			} else {
				$fldsession['extreg_fld_username'] = $post['username'];
			}
		}
		
		// Validate email
		if (!mb_strlen(trim($post['email']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_EMAIL'));
		} elseif (!JvitalsHelper::validateEmail(trim($post['email']))) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_EMAIL_NOTVALID_ERROR');
		} elseif ((boolean)$this->checkEmailExists(trim($post['email']))) {
			$errorArray[] = JText::_('COM_EXTENDEDREG_EMAIL_EXISTS_ERROR');
		} else {
			$blEmails = array();
			if (trim($conf->blacklist_emails)) $blEmails = explode("\n", trim($conf->blacklist_emails));
			
			$found = false;
			if (count($blEmails)) {
				foreach ($blEmails as $test) {
					$test = str_replace('*', '.*?', trim($test));
					if (preg_match('~^' . addslashes($test) . '$~smi', trim($post['email']))) {
						$found = true;
						break;
					}
				}
			}
			
			if ($found) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_EMAIL_BLACKLISTED_ERROR');
			} else {
				$fldsession['extreg_fld_email'] = $post['email'];
			}
		}
		
		// Validate password
		if (!mb_strlen(trim($post['password']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_PASSWORD'));
		} elseif (!mb_strlen(trim($post['password2']))) {
			$errorArray[] = JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD'));
		} else {
			if (trim($post['password']) != trim($post['password2'])) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_PASSWORDS_DO_NOT_MATCH');
			} else {
				if (!erHelperPassword::validate($post['password'])) {
					$errorArray[] = erHelperPassword::errorString();
				}
			}
		}
		
		// Validate captcha
		if (isset($formFields['captcha_fld']) && trim($conf->use_captcha)) {
			$captchaLib = erHelperAddons::getCaptchaLib();
			if ($captchaLib) {
				if (!$captchaLib->validate($post)) {
					$errorArray[] = JText::_('COM_EXTENDEDREG_CAPTCHA_INCORRECT_ERROR');
				}
			}
		}
		
		// Prepare sql for extendedreg fields
		$extrafields = "";
		
		// Validate terms and conditions
		if ((int)$form->show_terms && isset($formFields['terms_fld'])) {
			if (!(int)$post['acceptedterms']) {
				$errorArray[] = JText::_('COM_EXTENDEDREG_TERMS_ERROR');
			} else {
				$fldsession['extreg_fld_terms'] = 1;
				$extrafields .= "," . $this->dbo->quoteName('acceptedterms') . " = " . $this->dbo->Quote('1') . " " . "\n";
			}
		}
		
		// Validate age
		if ((int)$form->show_age && isset($formFields['age_fld'])) {
			if (!(int)$post['overage']) {
				$errorArray[] = JText::sprintf('COM_EXTENDEDREG_AGE_ERROR', $form->age_value);
			} else {
				$fldsession['extreg_fld_age'] = 1;
				$extrafields .= "," . $this->dbo->quoteName('overage') . " = " . $this->dbo->Quote('1') . " " . "\n";
			}
		}
		
		// Validate custom fields
		$ef = $formsModel->getExtraFieldsInfo();
		if (count($ef)) {
			foreach ($ef as $fld) {
				if (!(isset($formFields['custom_' . $fld->id]) || isset($formFields['group_' . $fld->grpid]))) {
					continue;
				} else {
					$fldObject = erHelperAddons::getFieldType($fld);
					if (!$fldObject->hasFormField()) {
						continue;
					}
					
					if (!$fldObject->serversideValidation($post)) {
						$errorArray[] = $fldObject->getError();
					} else {
						$value = $post[$fld->name];
						if (method_exists($fldObject, "prepareDataForSave")) {
							$value = $fldObject->prepareDataForSave($value);
							$post[$fld->name] = $value;
						}
						if (method_exists($fldObject, "manipulatePostData")) {
							$fldObject->manipulatePostData($post);
						}
						if (is_array($value)) $value = implode('#!#', $value);
						$fldsession['extreg_fld_'.$fld->name] = $value;
						$extrafields .= "," . $this->dbo->quoteName($fld->name) . " = " . $this->dbo->Quote($value) . " " . "\n";
					}
				}
			}
		}
		
		$fldsession = base64_encode(serialize($fldsession));
		$session->set('erFldSession', $fldsession, 'extendedreg');
		if (isset($post['password']) && trim($post['password'])) {
			$session->set('erClearPassword', $post['password'], 'extendedreg');
		}
		
		if (count($errorArray)) {
			$app->enqueueMessage(implode('<br/>', $errorArray), 'error');
			$app->redirect(JRoute::_($errorURL, false));
			jexit();
		}
		
		// Save the data
		$saveuser = new JUser;
		
		// Bind the data.
		if (!$saveuser->bind($post)) {
			$app->enqueueMessage($saveuser->getError(), 'error');
			$app->redirect(JRoute::_($errorURL, false));
			jexit();
		}
		
		// If user activation is turned on, we need to set the activation information
		if ((int)$conf->enable_user_activation) {
			$saveuser->set('activation', JApplication::getHash(JUserHelper::genRandomPassword()));
			$saveuser->set('block', '1');
		}
		if ((int)$conf->enable_admin_approval) {
			$saveuser->set('block', '1');
		}
		
		$formNewuserGroups = trim($form->groups);
		
		// Set some initial user values
		$params = JComponentHelper::getParams('com_users');
		if (trim($formNewuserGroups)) {
			$formNewuserGroups = explode(',', $formNewuserGroups);
			JArrayHelper::toInteger($formNewuserGroups, array());
		} else {
			$formNewuserGroups = array();
		}
		
		$groups = array();
		if (count($formNewuserGroups)) {
			$groups = $formNewuserGroups;
		} else {
			$groups[] = $params->get('new_usertype', 2);
		}
		$saveuser->set('groups', $groups);
		
		// Store the data.
		if (!$saveuser->save()) {
			$app->enqueueMessage($saveuser->getError(), 'error');
			$app->redirect(JRoute::_($errorURL, false));
			jexit();
		}
		
		$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_users WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$saveuser->id . " LIMIT 1");
		$exists = (int)$this->dbo->LoadResult();
		if (!$exists) {
			$this->dbo->setQuery("INSERT INTO #__extendedreg_users (" . $this->dbo->quoteName('user_id') . ") VALUES (" . (int)$saveuser->id . ")");
			$this->dbo->execute();		
		}
		
		$approve = 1;
		$approve_hash = '';
		if ((int)$conf->enable_admin_approval) {
			$approve = 0;
			$approve_hash = md5(time() . uniqid());
		}
		
		$this->dbo->setQuery("UPDATE #__extendedreg_users SET 
			" . $this->dbo->quoteName('form_id') . " = " . (int)$form->id . ",
			" . $this->dbo->quoteName('ip_addr') . " = " . $this->dbo->Quote($post['ip_addr']) . ",
			" . $this->dbo->quoteName('approve') . " = " . $this->dbo->Quote((int)$approve) . ",
			" . $this->dbo->quoteName('approve_hash') . " = " . $this->dbo->Quote($approve_hash) . ",
			" . $this->dbo->quoteName('last_pass_change') . " = " . $this->dbo->Quote(JvitalsTime::getUtc()->toSql()) . " 
			" . $extrafields . "
		WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$saveuser->id);
		
		if (!$this->dbo->execute()) {
			$app->enqueueMessage($this->dbo->getErrorMsg(), 'error');
			$app->redirect(JRoute::_($errorURL, false));
			jexit();
		}
		
		$userEmailSent = false;
		if ((int)$conf->enable_user_activation){
			$userEmailSent = erHelperMail::sendUserActivationMail($saveuser, (int)$form->id);
			$returnUrl = erHelperRouter::getUrl($conf->redir_url_reg_need_activation, $conf->redir_url_reg_need_activation_other, $conf->redir_url_default);
			$warningMessage = JText::_('COM_EXTENDEDREG_REG_COMPLETE_ACTIVATE_WARNING');
			$successMessage = JText::_('COM_EXTENDEDREG_REG_COMPLETE_ACTIVATE');
		} elseif ((int)$conf->enable_admin_approval) {
			$userEmailSent = erHelperMail::sendUserNeedApprovalMail($saveuser, (int)$form->id);
			$returnUrl = erHelperRouter::getUrl($conf->redir_url_reg_need_approval, $conf->redir_url_reg_need_approval_other, $conf->redir_url_default);
			$warningMessage = JText::_('COM_EXTENDEDREG_REG_COMPLETE_APPROVAL_WARNING');
			$successMessage = JText::_('COM_EXTENDEDREG_REG_COMPLETE_APPROVAL');
		} else {
			$userEmailSent = erHelperMail::sendUserRegistrationInfoMail($saveuser, (int)$form->id);
			$returnUrl = erHelperRouter::getUrl($conf->redir_url_register, $conf->redir_url_register_other, $conf->redir_url_default);
			$warningMessage = JText::_('COM_EXTENDEDREG_REG_COMPLETE_WARNING');
			$successMessage = JText::_('COM_EXTENDEDREG_REG_COMPLETE');
		}
		
		$lret = JRequest::getVar('lret', '', 'method', 'base64');
		if (trim($lret)) {
			$returnUrl = base64_decode($lret);
		}
		
		$session->clear('erFldSession', 'extendedreg');
		$session->clear('erTmpFormValues', 'extendedreg');
		$session->clear('erFieldsHash' . (int)$form->id, 'extendedreg');
		$session->clear('erStepsCount', 'extendedreg');
		$session->clear('erTmpFormID', 'extendedreg');
		
		// Load plugins from jvPlugins
		$jvPlugins = JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'jvPlugins';
		if (is_dir($jvPlugins)) {
			JPluginHelper::importPlugin('jvPlugins');
		}
		
		// Trigger event for pluigns
		$extendedreg = $this->loadUserById((int)$saveuser->id);
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onExtregUserRegister', array((int)$form->id, $saveuser, $extendedreg));
		
		if (!$userEmailSent) {
			$app->enqueueMessage($warningMessage, 'warning');
			$app->redirect($returnUrl);
			jexit();
		}
		
		if (!(int)$conf->enable_user_activation) {
			if ((int)$conf->enable_admin_approval) {
				erHelperMail::sendAdminNeedApprovalMail($saveuser, (int)$form->id);
			} else {
				erHelperMail::sendAdminRegistrationInfoMail($saveuser, (int)$form->id);
			}
		}
		
		$this->setLastKnownIP((int)$saveuser->id, $post['ip_addr']);
		$this->addStats($post['ip_addr'], (int)$saveuser->id, 'user_register', $proxy, ($_SERVER['REMOTE_PORT'] ? $_SERVER['REMOTE_PORT'] : ''));
		
		$app->enqueueMessage($successMessage);
		$app->redirect(JRoute::_($returnUrl, false));
		jexit();
	}
	
	function checkEmailExists($email, $user_id = 0) {
		$this->dbo->setQuery("SELECT count(*) FROM #__users WHERE LOWER(" . $this->dbo->quoteName('email') . ") = " . $this->dbo->Quote(mb_strtolower($email)) . " AND id != " . (int)$user_id);
		return $this->dbo->loadResult();
	}
	
	function checkUsernameExists($username, $user_id = 0) {
		$this->dbo->setQuery("SELECT count(*) FROM #__users WHERE LOWER(" . $this->dbo->quoteName('username') . ") = " . $this->dbo->Quote(mb_strtolower($username)) . " AND id != " . (int)$user_id);
		return $this->dbo->loadResult();
	}
	
	function getUsernameByEmail($email) {
		$user = $this->loadUserByEmail($email);
		return trim($user->username);
	}
	
	function testForApprove($credentials) {
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('id') . " FROM #__users WHERE " . $this->dbo->quoteName('username') . " = " . $this->dbo->Quote($credentials['username']));
		$user_id = (int)$this->dbo->loadResult();
		if (!$user_id) return -1;
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('approve') . " FROM #__extendedreg_users WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id);
		return (int)$this->dbo->loadResult();
	}
	
	function setLastKnownIP($user_id, $ip_addr) {
		$this->dbo->setQuery("UPDATE #__extendedreg_users SET " . $this->dbo->quoteName('ip_addr') . " = " . $this->dbo->Quote($ip_addr) . " WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id);
		return (boolean)$this->dbo->execute();
	}
	
	function setActivationString($user_id, $string) {
		$this->dbo->setQuery("UPDATE #__users SET " . $this->dbo->quoteName('activation') . " = " . $this->dbo->Quote($string) . " WHERE " . $this->dbo->quoteName('id') . " = " . (int)$user_id);
		return (boolean)$this->dbo->execute();
	}
	
	function remindUsername($email) {
		$user = $this->loadUserByEmail($email);
		if (!$user) return false;
		return erHelperMail::sendRemindMail($user);
	}
	
	function resetPassword($email) {
		$user = $this->loadUserByEmail($email);
		if (!$user) return false;
		return erHelperMail::sendResetMail($user);
	}
	
	function confirmReset($token, $username) {
		if (mb_strlen($token) != 32) {
			$this->setError(JText::_('COM_EXTENDEDREG_INVALID_TOKEN'));
			return false;
		}
		
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('id') . ", " . $this->dbo->quoteName('activation') . " FROM #__users 
			WHERE " . $this->dbo->quoteName('block') . " = 0 AND " . $this->dbo->quoteName('username') . " = " . $this->dbo->Quote($username));
		$row = $this->dbo->loadObject();
		
		if (!$row) {
			$this->setError(JText::_('COM_EXTENDEDREG_INVALID_TOKEN'));
			return false;
		}
		
		$parts = explode(':', $row->activation);
		$crypt = $parts[0];
		if (!isset($parts[1])) {
			$this->setError(JText::_('COM_EXTENDEDREG_INVALID_TOKEN'));
			return false;
		}
		$salt	= $parts[1];
		//~ $testcrypt = JUserHelper::getCryptedPassword($token, $salt);
		$testcrypt = md5($token . $salt);

		// Verify the token
		if ($crypt != $testcrypt) {
			$this->setError(JText::_('COM_EXTENDEDREG_INVALID_TOKEN'));
			return false;
		}
		
		// Push the token and user id into the session
		$app = JFactory::getApplication();
		$app->setUserState('com_extendedreg.reset.token', $crypt . ':' . $salt);
		$app->setUserState('com_extendedreg.reset.id', $row->id);
		return true;
	}
	
	function completeReset($password1, $password2) {
		// Make sure that we have a pasword
		if (!trim($password1)) {
			$this->setError(JText::_('COM_EXTENDEDREG_MUST_SUPPLY_PASSWORD'));
			return false;
		}

		// Verify that the passwords match
		if ($password1 != $password2) {
			$this->setError(JText::_('COM_EXTENDEDREG_PASSWORDS_DO_NOT_MATCH'));
			return false;
		}
		
		$password_updated = false;
		if (!erHelperPassword::validate($password1)) {
			$this->setError(erHelperPassword::errorString());
			return false;
		} else {
			$password_updated = true;
		}
		
		// Get the necessary variables
		$app = JFactory::getApplication();
		$token = $app->getUserState('com_extendedreg.reset.token');
		$user_id = $app->getUserState('com_extendedreg.reset.id');
		
		$salt = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($password1, $salt);
		$password = $crypt.':'.$salt;
		
		$this->dbo->setQuery("UPDATE #__users SET 
			" . $this->dbo->quoteName('password') . " = " . $this->dbo->Quote($password) . ",
			" . $this->dbo->quoteName('activation') . " = " . $this->dbo->Quote('') . " 
		WHERE " . $this->dbo->quoteName('id') . " = " . (int)$user_id . "
			AND " . $this->dbo->quoteName('activation') . " = " . $this->dbo->Quote($token) . " 
			AND " . $this->dbo->quoteName('block') . " = 0");
		
		if (!$this->dbo->execute()) {
			$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
			return false;
		}
		
		$this->dbo->setQuery("UPDATE #__extendedreg_users SET 
				" . $this->dbo->quoteName('last_pass_change') . " = " . $this->dbo->Quote(JvitalsTime::getUtc()->toSql()) . " 
			WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id);
		if (!$this->dbo->execute()) {
			$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
			return false;
		}
		
		$app->setUserState('com_extendedreg.reset.token', null);
		$app->setUserState('com_extendedreg.reset.id', null);
		return true;
	}
	
	function requestActivationEmail($email) {
		$user = $this->loadUserByEmail($email);
		if (!$user) return false;
		if ((int)$user->block && trim($user->activation)) {
			$conf = $this->getConfObj();
			$time = JvitalsTime::getUtc()->format('U');
			$lastrequest = strtotime($user->last_activation_request);
			if ($time > ($lastrequest + ((int)$conf->request_activation_timeout * 60 * 60))) {
				$this->dbo->setQuery("UPDATE #__extendedreg_users SET " . $this->dbo->quoteName('last_activation_request') . " = " . $this->dbo->Quote(JvitalsTime::getUtc()->toSql()) . " WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user->id);
				if (!$this->dbo->execute()) {
					$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
					return false;
				}
				return erHelperMail::sendUserActivationMail($user, (int)$user->form_id);
			}
		}
		return false;
	}
	
	function sendTerminate($password1, $password2) {
		$JVersion = new JVersion();
		$version = $JVersion->getShortVersion();
		$version = preg_replace('~[^\d|\.]~', '', $version);
		
		// Get the necessary variables
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$user_id = (int)$user->id;
		$username = $user->username;
		
		// Make sure that we have a pasword
		if (!trim($password1)) {
			$this->setError(JText::_('COM_EXTENDEDREG_MUST_SUPPLY_PASSWORD'));
			return false;
		}

		// Verify that the passwords match
		if ($password1 != $password2) {
			$this->setError(JText::_('COM_EXTENDEDREG_PASSWORDS_DO_NOT_MATCH'));
			return false;
		}
		
		// Check if the password is valid
		
		// get user id and pass
		$this->dbo->setQuery('SELECT ' . $this->dbo->quoteName('id') . ', ' . $this->dbo->quoteName('password') . ' 
			FROM ' . $this->dbo->quoteName('#__users') . '  
			WHERE ' . $this->dbo->quoteName('username') . ' = ' . $this->dbo->Quote($username));
		$result = $this->dbo->loadObject();
		
		// we want to cover all possible cases (Joomla! versions)
		if (version_compare($version, '3.1.6', 'le')) {
		
			$parts = explode(':', $result->password);
			$crypt = $parts[0];
			$salt = @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($password1, $salt);
			$passMatch = (boolean)($crypt == $testcrypt);
			
		} elseif (version_compare($version, '3.2.0', 'eq')) {
		
			if (substr($result->password, 0, 4) == '$2y$') {
				// BCrypt passwords are always 60 characters, but it is possible that salt is appended although non standard.
				$password60 = substr($result->password, 0, 60);
				if (JCrypt::hasStrongPasswordSupport()) {
					$passMatch = password_verify($password1, $password60);
				}
			} elseif (substr($result->password, 0, 8) == '{SHA256}') {
				// Check the password
				$parts = explode(':', $result->password);
				$crypt = $parts[0];
				$salt = @$parts[1];
				$testcrypt = JUserHelper::getCryptedPassword($password1, $salt, 'sha256', false);
				$passMatch = (boolean)($result->password == $testcrypt);
			} else {
				// Check the password
				$parts = explode(':', $result->password);
				$crypt = $parts[0];
				$salt = @$parts[1];
				$testcrypt = JUserHelper::getCryptedPassword($password1, $salt, 'md5-hex', false);
				$passMatch = (boolean)($crypt == $testcrypt);
			}
		
		} elseif (version_compare($version, '3.2.1', 'ge')) {
		
			$passMatch = JUserHelper::verifyPassword($password1, $result->password, $result->id);
			
		}

		if (!$passMatch) {
			return false;
		}		
		
		// if everything is alright - generate the termination hash
		$hash = md5(time() . uniqid());
		
		$this->dbo->setQuery("UPDATE #__extendedreg_users SET " . $this->dbo->quoteName('terminate_hash') . " = " . $this->dbo->Quote($hash) . "
								WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id);
		if (!$this->dbo->execute()) {
			$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
			return false;
		}
		$user->set('terminate_hash', $hash);
		
		// send the mail with the termination link
		return erHelperMail::sendTerminateMail($user);
	}
	
	function terminateAccount($hash) {
		
		// Get the necessary variables
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$user_id = (int)$user->id;
		$username = $user->username;
		$conf = $this->getConfObj();
		
		// Check if the hash is valid
		$this->dbo->setQuery("SELECT COUNT(*) as cnt FROM #__extendedreg_users  
							 WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id . "
								AND " . $this->dbo->quoteName('terminate_hash') . " = " . $this->dbo->Quote($hash));
		$result = $this->dbo->loadResult();
		if (!(int)$result) {
			$this->setError(JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_INVALIDHASH'));
			return false;
		}	
		
		$terminated = false;
		if ((int)$conf->terminate_type == 1) {
			// Delete account
			
			// Delete from main table
			$user1 = JFactory::getUser($user_id);
			if (!$user1->delete()) {
				$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
			} else {
				// Delete from ER
				$this->dbo->setQuery("DELETE FROM #__extendedreg_users WHERE " . $this->dbo->quoteName('user_id') . " = " . (int)$user_id);
				if (!$this->dbo->execute()) {
					$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
				} else {
					$terminated = true;
				}
			}
		} elseif ((int)$conf->terminate_type == 2) {
			// De-activate account
			$this->dbo->setQuery("UPDATE #__users SET " . $this->dbo->quoteName('block') . " = 1 WHERE " . $this->dbo->quoteName('id') . " = " . (int)$user_id);
			if (!$this->dbo->execute()) {
				$this->setError(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'));
			} else {
				$terminated = true;
			}
		}
		
		// if we terminated the user, we log the user out
		if ($terminated) {
			$app->logout($user_id, array('clientid' => 0));
		}
		
		// send admins and e-mail that the user has terminated their account
		return $terminated ? erHelperMail::sendAdminTerminateMail($user) : false;
		
	}
	
	function addLoginAttempts($ip_addr, $username) {
		if (($ip_addr == '0.0.0.0') || empty($username)) {
			return true;
		}
		$this->dbo->setQuery("INSERT INTO #__extendedreg_login_attempts (" . $this->dbo->quoteName('ip_addr') . ", " . $this->dbo->quoteName('username') . ", " . $this->dbo->quoteName('tstamp') . ") 
			VALUES (" . $this->dbo->Quote($ip_addr) . ", " . $this->dbo->Quote($username) . ", " . $this->dbo->Quote(JvitalsTime::getUtc()->toSql()) . ") ");
		return $this->dbo->execute();
	}
	
	function getBlockTstamp($ip_addr, $username) {
		$block_tstamp = 0;
		$this->dbo->setQuery("SELECT * FROM #__extendedreg_blocks WHERE " . $this->dbo->quoteName('block_item') . " IN (" . $this->dbo->Quote($ip_addr) . ", " . $this->dbo->Quote($username) . ")");
		$res = $this->dbo->loadObjectList();
		if (!$res) $res = array();
		
		foreach ($res as $row) {
			if ((int)$block_tstamp < (int)$row->blocked_until) {
				$block_tstamp = (int)$row->blocked_until;
			}
		}
		return $block_tstamp;
	}
	
	function setBlockTstamp($ip_addr, $username, $blocked_until, $blockIP) {
		$this->dbo->setQuery("INSERT INTO #__extendedreg_blocks (" . $this->dbo->quoteName('block_item') . ", " . $this->dbo->quoteName('blocked_until') . ") VALUES (" . $this->dbo->Quote($username) . ", " . $this->dbo->Quote($blocked_until) . ")");
		$this->dbo->execute();
		
		if ($blockIP) {
			$this->dbo->setQuery("INSERT INTO #__extendedreg_blocks (" . $this->dbo->quoteName('block_item') . ", " . $this->dbo->quoteName('blocked_until') . ") VALUES (" . $this->dbo->Quote($ip_addr) . ", " . $this->dbo->Quote($blocked_until) . ")");
			$this->dbo->execute();
		}
		
		return true;
	}
	
	function getFailedAttempts($attempt_tstamp, $ip_addr, $username) {
		if ($ip_addr == '0.0.0.0') {
			return 0;
		}
		$this->dbo->setQuery("SELECT COUNT(*) FROM #__extendedreg_login_attempts WHERE UNIX_TIMESTAMP(" . $this->dbo->quoteName('tstamp') . ") > " . $this->dbo->Quote($attempt_tstamp) . "
			AND (" . $this->dbo->quoteName('ip_addr') . " = " . $this->dbo->Quote($ip_addr) . " OR " . $this->dbo->quoteName('username') . " = " . $this->dbo->Quote($username) . ")");
		return (int)$this->dbo->loadResult();
	}
	
	function checkLoginAttempts($ip_addr, $username, $is_admin) {
		if (($ip_addr == '0.0.0.0') || empty($username)) {
			return true;
		}
		
		$conf = $this->getConfObj();
		
		$user_id = JUserHelper::getUserId($username);
		$user = JUser::getInstance($user_id);
		if (!($user && (int)$user->id)) {
			return true;
		}
		
		// multipliers to get how much seconds are there in one minute, hour and day
		$units = array(0 => 60, 1 => 60*60, 2 => 24*60*60);
		$block_tstamp = 0;
		$current_tstamp = JvitalsTime::getUtc()->format('U');
		
		$blockCookie = JvitalsHelper::getCookie('ER_BLOCK_TSTAMP');
		if ($blockCookie === false) {
			// if no cookie is set, get the data from the database and set it in the the cookie
			$block_tstamp = (int)$this->getBlockTstamp($ip_addr, $username);
			$cookie_arr = array($block_tstamp);
			JvitalsHelper::setCookie('ER_BLOCK_TSTAMP', $cookie_arr);
		} else {
			$block_tstamp = (int)$blockCookie[0];
		}
		
		// user OR ip is still blocked
		if ((int)$block_tstamp > $current_tstamp) {
			return false;
		}
		
		$to_be_blocked = false;
		if ($is_admin) {
			$attempt_tstamp = (int)$current_tstamp - ((int)$conf->max_login_attempt_time_back * $units[(int)$conf->max_login_attempt_units_back]);
			$max_login_user_allowed = (int)$conf->max_login_user_back;
			$block_time = ((int)$conf->max_login_block_time_back * $units[(int)$conf->max_login_block_units_back]);
			$block_time_msg = (int)$conf->max_login_block_time_back . ' ' . JText::_('COM_EXTENDEDREG_MAX_LOGIN_UNIT_' . (int)$conf->max_login_block_units_back);
			$blockIP  = (int)$conf->blockip_max_login_back;
		} else {
			$attempt_tstamp = (int)$current_tstamp - ((int)$conf->max_login_attempt_time_front * $units[(int)$conf->max_login_attempt_units_front]);
			$max_login_user_allowed = (int)$conf->max_login_user_front;
			$block_time = ((int)$conf->max_login_block_time_front * $units[(int)$conf->max_login_block_units_front]);
			$block_time_msg = (int)$conf->max_login_block_time_front . ' ' . JText::_('COM_EXTENDEDREG_MAX_LOGIN_UNIT_' . (int)$conf->max_login_block_units_front);
			$blockIP  = (int)$conf->blockip_max_login_front;
		}
		$failed_attempts_user = $this->getFailedAttempts($attempt_tstamp, $ip_addr, $username);
		
		if ((int)$failed_attempts_user >= $max_login_user_allowed) {
			$to_be_blocked = true;
		}
		
		if ($to_be_blocked) {
			$blocked_until = $current_tstamp + $block_time;
			$this->setBlockTstamp($ip_addr, $username, $blocked_until, $blockIP);
			
			// send mail to the user whose account has been blocked and to the admins
			erHelperMail::sendFailedLoginsMail($user, $block_time_msg);
			erHelperMail::sendAdminFailedLoginsMail($user, $block_time_msg);
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the one time password (OTP) – a.k.a. two factor authentication –
	 * configuration for a particular user.
	 *
	 * @param   integer  $user_id  The numeric ID of the user
	 *
	 * @return  stdClass  An object holding the OTP configuration for this user
	 *
	 * @since   3.2
	 */
	public function getOtpConfig($user_id) {

		// Initialise
		$otpConfig = (object) array(
			'method' => 'none',
			'config' => array(),
			'otep' => array()
		);

		/**
		 * Get the raw data, without going through JUser (required in order to
		 * be able to modify the user record before logging in the user).
		 */
		$query = $this->dbo->getQuery(true)
			->select('*')
			->from($this->dbo->quoteName('#__users'))
			->where($this->dbo->quoteName('id') . ' = ' . $this->dbo->quote($user_id));
		$this->dbo->setQuery($query);
		$item = $this->dbo->loadObject();

		// Make sure this user does have OTP enabled
		if (empty($item->otpKey)) {
			return $otpConfig;
		}

		// Get the encrypted data
		list($method, $encryptedConfig) = explode(':', $item->otpKey, 2);
		$encryptedOtep = $item->otep;

		// Create an encryptor class
		$key = $this->getOtpConfigEncryptionKey();
		$aes = new FOFEncryptAes($key, 256);

		// Decrypt the data
		$decryptedConfig = $aes->decryptString($encryptedConfig);
		$decryptedOtep = $aes->decryptString($encryptedOtep);

		// Remove the null padding added during encryption
		$decryptedConfig = rtrim($decryptedConfig, "\0");
		$decryptedOtep = rtrim($decryptedOtep, "\0");

		// Update the configuration object
		$otpConfig->method = $method;
		$otpConfig->config = @json_decode($decryptedConfig);
		$otpConfig->otep = @json_decode($decryptedOtep);

		/*
		 * If the decryption failed for any reason we essentially disable the
		 * two-factor authentication. This prevents impossible to log in sites
		 * if the site admin changes the site secret for any reason.
		 */
		if (is_null($otpConfig->config)) {
			$otpConfig->config = array();
		}

		if (is_object($otpConfig->config)) {
			$otpConfig->config = (array) $otpConfig->config;
		}

		if (is_null($otpConfig->otep)) {
			$otpConfig->otep = array();
		}

		if (is_object($otpConfig->otep)) {
			$otpConfig->otep = (array) $otpConfig->otep;
		}

		// Return the configuration object
		return $otpConfig;
	}
	
	/**
	 * Sets the one time password (OTP) – a.k.a. two factor authentication –
	 * configuration for a particular user. The $otpConfig object is the same as
	 * the one returned by the getOtpConfig method.
	 *
	 * @param   integer   $user_id    The numeric ID of the user
	 * @param   stdClass  $otpConfig  The OTP configuration object
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.2
	 */
	public function setOtpConfig($user_id, $otpConfig) {
	
		$updates = (object) array(
			'id' => $user_id,
			'otpKey' => '',
			'otep' => ''
		);

		// Create an encryptor class
		$key = $this->getOtpConfigEncryptionKey();
		$aes = new FOFEncryptAes($key, 256);

		// Create the encrypted option strings
		if (!empty($otpConfig->method) && ($otpConfig->method != 'none')) {
			$decryptedConfig = json_encode($otpConfig->config);
			$decryptedOtep = json_encode($otpConfig->otep);
			$updates->otpKey = $otpConfig->method . ':' . $aes->encryptString($decryptedConfig);
			$updates->otep = $aes->encryptString($decryptedOtep);
		}

		$result = $this->dbo->updateObject('#__users', $updates, 'id');

		return $result;
	}
	
	/**
	 * Generates a new set of One Time Emergency Passwords (OTEPs) for a given user.
	 *
	 * @param   integer  $user_id  The user ID
	 * @param   integer  $count    How many OTEPs to generate? Default: 10
	 *
	 * @return  array  The generated OTEPs
	 *
	 * @since   3.2
	 */
	public function generateOteps($user_id, $count = 10) {

		// Initialise
		$oteps = array();

		// Get the OTP configuration for the user
		$otpConfig = $this->getOtpConfig($user_id);

		// If two factor authentication is not enabled, abort
		if (empty($otpConfig->method) || ($otpConfig->method == 'none')) {
			return $oteps;
		}

		$salt = "0123456789";
		$base = strlen($salt);
		$length = 16;

		for ($i = 0; $i < $count; $i++) {
			$makepass = '';
			$random = JCrypt::genRandomBytes($length + 1);
			$shift = ord($random[0]);

			for ($j = 1; $j <= $length; ++$j) {
				$makepass .= $salt[($shift + ord($random[$j])) % $base];
				$shift += ord($random[$j]);
			}

			$oteps[] = $makepass;
		}

		$otpConfig->otep = $oteps;

		// Save the now modified OTP configuration
		$this->setOtpConfig($user_id, $otpConfig);

		return $oteps;
	}

	/**
	 * Gets the symmetric encryption key for the OTP configuration data. It
	 * currently returns the site's secret.
	 *
	 * @return  string  The encryption key
	 *
	 * @since   3.2
	 */
	public function getOtpConfigEncryptionKey() {
		return JFactory::getConfig()->get('secret');
	}

	/**
	 * Gets the configuration forms for all two-factor authentication methods
	 * in an array.
	 *
	 * @param   integer  $user_id  The user ID to load the forms for (optional)
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function getTwofactorform($user_id) {
	
		$otpConfig = $this->getOtpConfig($user_id);

		FOFPlatform::getInstance()->importPlugin('twofactorauth');

		return FOFPlatform::getInstance()->runPlugins('onUserTwofactorShowConfiguration', array($otpConfig, $user_id));
	}

	/**
	 * Creates a list of two factor authentication methods used in com_users
	 * on user view
	 *
	 * @return  array
	 *
	 * @since   3.2.0
	 */
	public static function getTwoFactorMethods() {

		FOFPlatform::getInstance()->importPlugin('twofactorauth');
		$identities = FOFPlatform::getInstance()->runPlugins('onUserTwofactorIdentify', array());

		$options = array(
			JHtml::_('select.option', 'none', JText::_('COM_USERS_OPTION_OTPMETHOD_NONE'), 'value', 'text'),
		);

		if (!empty($identities)) {
			foreach ($identities as $identity) {
				if (!is_object($identity)) {
					continue;
				}
				$options[] = JHtml::_('select.option', $identity->method, $identity->title, 'value', 'text');
			}
		}

		return $options;
	}

}