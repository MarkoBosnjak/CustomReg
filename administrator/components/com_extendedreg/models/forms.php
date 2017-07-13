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

class ExtendedregModelForms extends ExtendedregModelDefault {
	
	function loadForm($fid, $loaddef = true) {
		$app = JFactory::getApplication();
		
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("*")
			->from("#__extendedreg_forms")
			->where($this->dbo->quoteName('id') . " = " . (int)$fid);
			
		if (!$app->isAdmin()) {
			$query->where($this->dbo->quoteName('published') . " = " . $this->dbo->quote('1'));
		}
		
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
		if (!$result && $loaddef) {
			// Clear and construct the query
			$query->clear()
				->select("*")
				->from("#__extendedreg_forms")
				->where($this->dbo->quoteName('isdefault') . " = " . $this->dbo->quote('1'));
				
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
		}
		
		if (!$result) $result = new stdClass;
		return $result;
	}
	
	function getAllForms($published = true) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("*")
			->from("#__extendedreg_forms");
			
		if ($published) {
			$query->where($this->dbo->quoteName('published') . " = " . $this->dbo->quote('1'));
		}
		
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
	
	function saveForm() {
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id', 0);
		$isDefault = $app->input->getInt('isdefault', 0);
		
		$name = trim($app->input->getString('name'));
		if (!$name) {
			$this->setError(JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_FORMS_NAME')));
			return false;
		}
		
		$termsSwitcher = $app->input->getInt('terms_switcher', 0);
		if ($termsSwitcher) {
			$termsArticleID = $app->input->getInt('terms_article_id', 0);
			$termsValue = '';
		} else {
			$termsArticleID = 0;
			if (JvitalsDefines::compatibleMode() == '25>') {
				$termsValue = JRequest::getVar('terms_value', '', 'post', 'string', JREQUEST_ALLOWRAW);
			} else {
				$termsValue = $app->input->get('terms_value', '', 'raw');
			}
			$termsValue = trim(JvitalsHelper::sanitize($termsValue));
		}
		
		$groups = $app->input->get('groups', array(), 'array');
		if (!is_array($groups) && (int)$groups) {
			$newuserGroups = array((int)$groups);
		} else {
			$newuserGroups = $groups;
		}
		JArrayHelper::toInteger($newuserGroups);
		
		$groups = '';
		if (count($newuserGroups)) {
			$groups = implode(',', $newuserGroups);
		}
		
		// then save in our table
		$query = $this->dbo->getQuery(true);
		if ((int)$id) {
			$query->update("#__extendedreg_forms")
				->set(array(
					$this->dbo->quoteName('name') . " = " . $this->dbo->quote($name), 
					$this->dbo->quoteName('description') . " = " . $this->dbo->quote(trim($app->input->getString('description'))), 
					$this->dbo->quoteName('layout') . " = " . $this->dbo->quote(trim($app->input->getString('layout'))), 
					$this->dbo->quoteName('show_terms') . " = " . $this->dbo->quote($app->input->getInt('show_terms', 0)), 
					$this->dbo->quoteName('terms_value') . " = " . $this->dbo->quote($termsValue),
					$this->dbo->quoteName('show_age') . " = " . $this->dbo->quote($app->input->getInt('show_age', 0)),
					$this->dbo->quoteName('age_value') . " = " . $this->dbo->quote($app->input->getInt('age_value', 0)),
					$this->dbo->quoteName('isdefault') . " = " . $this->dbo->quote($isDefault),
					$this->dbo->quoteName('published') . " = " . $this->dbo->quote($app->input->getInt('published', 0)),
					$this->dbo->quoteName('groups') . " = " . $this->dbo->quote($groups),
					$this->dbo->quoteName('mailfrom') . " = " . $this->dbo->quote(trim($app->input->getString('mailfrom'))),
					$this->dbo->quoteName('admin_mails') . " = " . $this->dbo->quote(trim($app->input->getString('admin_mails'))),
					$this->dbo->quoteName('terms_switcher') . " = " . $this->dbo->quote($termsSwitcher),
					$this->dbo->quoteName('terms_article_id') . " = " . (int)$termsArticleID,
					$this->dbo->quoteName('form_style_width') . " = " . $this->dbo->quote(trim($app->input->getString('form_style_width'))),
					$this->dbo->quoteName('form_style_align') . " = " . $this->dbo->quote(trim($app->input->getString('form_style_align'))),
				))
				->where($this->dbo->quoteName('id') . " = " . (int)$id);
		} else {
			$query->insert("#__extendedreg_forms")
				->columns(array(
					$this->dbo->quoteName('name'), 
					$this->dbo->quoteName('description'), 
					$this->dbo->quoteName('layout'), 
					$this->dbo->quoteName('show_terms'), 
					$this->dbo->quoteName('terms_value'),
					$this->dbo->quoteName('show_age'),
					$this->dbo->quoteName('age_value'),
					$this->dbo->quoteName('isdefault'),
					$this->dbo->quoteName('published'),
					$this->dbo->quoteName('groups'),
					$this->dbo->quoteName('mailfrom'),
					$this->dbo->quoteName('admin_mails'),
					$this->dbo->quoteName('terms_switcher'),
					$this->dbo->quoteName('terms_article_id'),
					$this->dbo->quoteName('form_style_width'),
					$this->dbo->quoteName('form_style_align'),
				))
				->values(array(
					$this->dbo->quote($name) . ", " .
					$this->dbo->quote(trim($app->input->getString('description'))) . ", " .
					$this->dbo->quote(trim($app->input->getString('layout'))) . ", " .
					$this->dbo->quote($app->input->getInt('show_terms', 0)) . ", " .
					$this->dbo->quote($termsValue) . ", " .
					$this->dbo->quote($app->input->getInt('show_age', 0)) . ", " .
					$this->dbo->quote($app->input->getInt('age_value', 0)) . ", " .
					$this->dbo->quote($isDefault) . ", " .
					$this->dbo->quote($app->input->getInt('published', 0)) . ", " .
					$this->dbo->quote($groups) . ", " .
					$this->dbo->quote(trim($app->input->getString('mailfrom'))) . ", " .
					$this->dbo->quote(trim($app->input->getString('admin_mails'))) . ", " .
					$this->dbo->quote($termsSwitcher) . ", " .
					(int)$termsArticleID . ", " .
					$this->dbo->quote(trim($app->input->getString('form_style_width'))) . ", " .
					$this->dbo->quote(trim($app->input->getString('form_style_align')))
				));
		}

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
		// Get the ID of the inserted row
		if (!(int)$id) $id = (int)$this->dbo->insertid();
		
		if ($isDefault) {
			$query->clear()
				->update("#__extendedreg_forms")
				->set(array($this->dbo->quoteName('isdefault') . " = " . $this->dbo->quote('0')))
				->where($this->dbo->quoteName('id') . " != " . (int)$id);
			
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
	
	
	function getFormsList($listOrder = 'f.name', $listDirn = 'asc', $limitstart = false, $limit = false) {
		
		$app = JFactory::getApplication();
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		// pagination
		if ($limitstart === false) $limitstart = $this->getState('limitstart');
		if ($limit === false) $limit = $this->getState('limit');
		
		// ordering
		$listOrder = $app->getUserStateFromRequest('com_extendedreg.list.forms.ordering', 'filter_order', 'f.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest('com_extendedreg.list.forms.direction', 'filter_order_Dir', 'asc', 'word');		
		
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('f.name', 'f.isdefault', 'f.published', 'f.id'))) {
			$listOrder = 'f.name';
		}
		$app->setUserState('com_extendedreg.list.forms.ordering', $listOrder);

		if (!in_array(strtolower($listDirn), array('asc', 'desc'))) {
			$listDirn = 'asc';
		}
		$app->setUserState('com_extendedreg.list.forms.direction', $listDirn);
		
		// search filter
		$filter_search = $app->getUserStateFromRequest('com_extendedreg.filter.forms.filter_search', 'filter_search');
		$filter_search = trim(strip_tags($filter_search));
		$filter_search_string = '';
		if ($filter_search) {
			$filter_search = mb_strtolower($filter_search);
			$filter_search = preg_replace('~[^\w|\s|\d]+~i', ' ', $filter_search);
			$filter_search = preg_replace('~\s+~i', '%', trim($filter_search));
			
			$searchEscaped = $this->dbo->Quote('%' . $filter_search . '%', false);
			
			$filter_search_string = "LOWER(f." . $this->dbo->quoteName('name') . ") LIKE " . $searchEscaped;
		}
		
		// state filter
		$filter_state = $app->getUserStateFromRequest('com_extendedreg.filter.forms.filter_state', 'filter_state');
		
		// Clear and construct the query
		$query = $this->dbo->getQuery(true)
			->select("*")
			->from("#__extendedreg_forms AS f");
		
		if (!is_null($filter_state) && is_numeric($filter_state)) {
			$query->where("f." . $this->dbo->quoteName('published') . " = " . $this->dbo->quote((int)$filter_state == 0 ? '0' : '1'));
		}
		
		if ($filter_search_string) {
			$query->where($filter_search_string);
		}
		
		$query->order($this->dbo->escape($listOrder) . ' ' . $this->dbo->escape($listDirn));
		
		// get the total count
		$this->dbo->setQuery($query);
		try {
			if (!$this->dbo->execute()) {
				$this->setError($this->dbo->getErrorMsg());
			}
			$result->total = (int)$this->dbo->getNumRows();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		
		// get the paginated result
		$this->dbo->setQuery($query, $limitstart, $limit);
		try {
			$result->items = $this->dbo->loadObjectList();
			if ($this->dbo->getErrorMsg()) {
				$this->setError($this->dbo->getErrorMsg());
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
		}
		if (!$result->items) $result->items = array();
		
		return $result;
	}
	
	function getFieldsList() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$result = new stdClass;
		$result->total = 0;
		$result->items = array();
		
		$listOrder = $app->getUserStateFromRequest($option . '.list.ordering', 'filter_order', 'f.name', 'cmd');
		$listDirn = $app->getUserStateFromRequest($option . '.list.direction', 'filter_order_Dir', 'ASC', 'word');
		// ensure listOrder has a valid value.
		if (!in_array($listOrder, array('f.name', 'f.title', 'f.type', 'f.published', 'f.editable', 'f.required', 'f.id', 'f.ord'))) {
			$listOrder = 'f.ord';
			$app->setUserState($option . '.list.ordering', $listOrder);
		}

		if (!in_array(mb_strtoupper($listDirn), array('ASC', 'DESC'))) {
			$listDirn = 'ASC';
			$app->setUserState($option . '.list.direction', $listDirn);
		}
		
		$filter_search = $app->getUserStateFromRequest($option . '.filter.fld_search', 'filter_search');
		$filter_search = trim(strip_tags($filter_search));
		if ($filter_search) {
			$filter_search = mb_strtolower($filter_search);
			$filter_search = preg_replace('~[^\w|\s|\d]+~i', ' ', $filter_search);
			$filter_search = preg_replace('~\s+~i', '%', trim($filter_search));
		}
		
		$filter_state = $app->getUserStateFromRequest($option . '.filter.fld_state', 'filter_state');
		$filter_required = $app->getUserStateFromRequest($option . '.filter.fld_required', 'filter_required');
		$filter_group = $app->getUserStateFromRequest($option . '.filter.fld_group', 'filter_group');
		$filter_type = $app->getUserStateFromRequest($option . '.filter.fld_type', 'filter_type');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS f.*, g." . $this->dbo->quoteName('name') . " as grpname 
		FROM #__extendedreg_fields as f 
		JOIN #__extendedreg_fields_groups as g ON g." . $this->dbo->quoteName('grpid') . " = f." . $this->dbo->quoteName('grpid') . "";
		
		$where = array();
		if (!is_null($filter_state) && is_numeric($filter_state)) {
			$where[] = "f." . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote((int)$filter_state == 0 ? '0' : '1');
		}
		
		if (!is_null($filter_required) && is_numeric($filter_required)) {
			$where[] = "f." . $this->dbo->quoteName('required') . " = " . $this->dbo->Quote((int)$filter_required == 0 ? '0' : '1');
		}
		
		if (!is_null($filter_type) && $filter_type != '*') {
			$where[] = "f." . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote($filter_type);
		}
		
		if (!is_null($filter_group) && $filter_group != '*') {
			$where[] = "f." . $this->dbo->quoteName('grpid') . " = " . $this->dbo->Quote($filter_group);
		}
		
		if ($filter_search) {
			$searchEscaped = $this->dbo->Quote('%' . $filter_search . '%', false);
			$where[] = "(
				f." . $this->dbo->quoteName('name') . " LIKE " . $searchEscaped . " OR
				f." . $this->dbo->quoteName('title') . " LIKE " . $searchEscaped . " OR
				g." . $this->dbo->quoteName('name') . " LIKE " . $searchEscaped . "
			)";
		}
		
		if (count($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " ORDER BY " . $this->dbo->quoteName('grpname') . ", g." . $this->dbo->quoteName('grpid') . ", " . $listOrder . " " . $listDirn;
		
		$this->dbo->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$result->items = $this->dbo->loadObjectList();
		if (!$result->items) $result->items = array();
		
		$this->dbo->setQuery('SELECT FOUND_ROWS();');
		$result->total = (int)$this->dbo->loadResult();
		
		return $result;
	}
	
	function loadField($fid, $type = '') {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select("*")
			->from("#__extendedreg_fields")
			->where($this->dbo->quoteName('id') . " = " . (int)$fid);
		
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
			$result->grpid = 0;
			$result->ord = 0;
			$result->custom_sql = '';
			$result->title = '';
			$result->name = '';
			$result->type = '';
			$result->description = '';
			$result->required = 0;
			$result->published = 1;
			$result->editable = 1;
			$result->exportable = 1;
			$result->params = '{}';
		}
		if (trim($type)) {
			$result->type = $type;
		}
		return $result;
	}
	
	function getStateOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_STATE_PUBLISHED'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_STATE_UNPUBLISHED'));
		return $result;
	}
	
	function getFieldRequiredOptions() {
		$result = array();
		$result[] = JHtml::_('select.option', 1, JText::_('COM_EXTENDEDREG_FIELDS_REQUIRED'));
		$result[] = JHtml::_('select.option', 0, JText::_('COM_EXTENDEDREG_FIELDS_NOT_REQUIRED'));
		return $result;
	}
	
	function getFieldTypeOptions() {
		$types = erHelperAddons::loadAddons('field', false);
		
		$result = array();
		if (count($types)) {
			foreach ($types as $type) {
				$result[] = JHtml::_('select.option', $type->file_name, JText::_($type->name));
			}
		}
		return $result;
	}
	
	function loadFieldGroups($id = 0) {
		$this->dbo->setQuery("SELECT * FROM #__extendedreg_fields_groups " . ((int)$id ? " WHERE " . $this->dbo->quoteName('grpid') . " = " . (int)$id : " ORDER BY " . $this->dbo->quoteName('name') . ", " . $this->dbo->quoteName('grpid')));
		if ((int)$id) {
			return $this->dbo->loadObject();
		}
		$groups = $this->dbo->loadObjectList();
		if (!$groups) $groups = array();
		return $groups;
	}
	
	function getFieldGroupOptions() {
		$groups = $this->loadFieldGroups();
		$result = array();
		foreach ($groups as $group) {
			$result[] = JHtml::_('select.option', $group->grpid, JText::_($group->name));
		}
		return $result;
	}
	
	function getFieldOpts($field_id, $id = 0, $custom_sql = '') {
		static $results;
		if (!$results) $results = array();
		if (!isset($results[$field_id . $id])) {
			if (trim($custom_sql)) {
				$query = $custom_sql;
			} else {
				$query = "SELECT * FROM #__extendedreg_fields_values 
				WHERE " . $this->dbo->quoteName('field_id') . " = " . (int)$field_id . ((int)$id ? " AND " . $this->dbo->quoteName('id') . " = " . (int)$id . " LIMIT 1" : " ORDER BY " . $this->dbo->quoteName('ord'));
			}
			$this->dbo->setQuery($query);
			if ((int)$id) {
				$results[$field_id . $id] = $this->dbo->loadObject();
			} else {
				$results[$field_id . $id] = $this->dbo->loadObjectList();
				if (!$results[$field_id . $id]) $results[$field_id . $id] = array();
			}
		}

		return $results[$field_id . $id];
	}
	
	function setFldPublished(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		$canChange = JvitalsHelper::canDo('fields.manage', 'com_extendedreg');
		if (!$canChange) {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$this->dbo->setQuery("UPDATE #__extendedreg_fields SET " . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function setFldRequired(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		$canChange = JvitalsHelper::canDo('fields.manage', 'com_extendedreg');
		if (!$canChange) {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$this->dbo->setQuery("UPDATE #__extendedreg_fields SET " . $this->dbo->quoteName('required') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function setFldEditable(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		$canChange = JvitalsHelper::canDo('fields.manage', 'com_extendedreg');
		if (!$canChange) {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		$this->dbo->setQuery("UPDATE #__extendedreg_fields SET " . $this->dbo->quoteName('editable') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function saveCustomField() {
		$app = JFactory::getApplication();
		$task = JRequest::getVar('task');
		
		$newParams = '';
		$params = JRequest::getVar('params', '', 'post', 'array', JREQUEST_ALLOWRAW);
		if ($params && is_array($params)) {
			foreach ($params as $k => $v) {
				if ($k == 'maxrows' && $v > 50) $v = 50;
				if (is_array($v)) {
					$v = array_map("erCleanParam", $v);
					$v = implode('|', $v);
				} else {
					$v = erCleanParam($v);
				}
				$params[$k] = $v;
			}
			$newParams = json_encode($params);
		}
		$id = (int)JRequest::getVar('id');
		
		$errormsg = '';
		$name = mb_strtolower(trim(JRequest::getVar('name')));
		if (!$id && !$name) {
			$errormsg .= (trim($errormsg) ? '<br/>' : '') . JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_FIELDS_NAME'));
		} else {
			if (!$id && !preg_match('/^[a-z0-9\_\-]+$/', $name)) {
				$errormsg .= (trim($errormsg) ? '<br/>' : '') . JText::_('COM_EXTENDEDREG_FIELDS_NAME_ERROR');
			}
			if (!preg_match('/^cf\_(.*)$/', $name)) $name = 'cf_' . $name;
			
			$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_fields 
				WHERE " . $this->dbo->quoteName('name') . " = " . $this->dbo->Quote($name) . ($id ? " AND " . $this->dbo->quoteName('id') . " != " . $id : "") . " LIMIT 1");
			if ((int)$this->dbo->loadResult()) {
				$errormsg .= (trim($errormsg) ? '<br/>' : '') . JText::_('COM_EXTENDEDREG_FIELDS_NAME_UNIQUE');
			}
		}
		
		$title = trim(JRequest::getVar('title'));
		if (!$title) {
			$errormsg .= (trim($errormsg) ? '<br/>' : '') . JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_FIELDS_TITLE'));
		}
		
		$grpid = (int)JRequest::getVar('grpid');
		if (!$grpid) {
			$errormsg .= (trim($errormsg) ? '<br/>' : '') . JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_FIELDS_GROUP'));
		}
		
		$type = mb_strtolower(trim(JRequest::getVar('type')));
		if (!$type) {
			$errormsg .= (trim($errormsg) ? '<br/>' : '') . JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_('COM_EXTENDEDREG_FIELDS_TYPE'));
		}
		
		if (trim($errormsg)) {
			$url = 'index.php?option=com_extendedreg&task=forms.fld_' . ($id ? 'edit&cid=' . $id : 'new');
			$app->enqueueMessage($errormsg, 'error');
			$app->redirect(JRoute::_($url, false));
			jexit();
		}
		
		if ($id) {
			$oldfield = $this->loadField((int)$id);
			$oldfld_class = erHelperAddons::getFieldType($oldfield);
			
			$newfield = clone($oldfield);
			$newfield->type = $type;
			$newfld_class = erHelperAddons::getFieldType($newfield);
		} else {
			$newfield = $this->loadField(0, $type);
			$newfld_class = erHelperAddons::getFieldType($newfield);
		}
		
		if ($id) {
			$oldsqltype = trim($oldfld_class->getSqlType());
			$newsqltype = trim($newfld_class->getSqlType());
			
			if ($oldsqltype != $newsqltype) {
				if ($oldsqltype) {
					$this->dbo->setQuery("ALTER TABLE #__extendedreg_users DROP " . $this->dbo->quoteName($oldfield->name));
					$this->dbo->execute();
				}
				
				if ($newsqltype) {
					$this->dbo->setQuery("ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName($newfield->name) . " " . $newsqltype);
					$this->dbo->execute();
				}
			}
			
			$query = "
				UPDATE #__extendedreg_fields SET
					" . $this->dbo->quoteName('title') . " = " . $this->dbo->Quote($title) . ",
					" . $this->dbo->quoteName('description') . " = " . $this->dbo->Quote(JRequest::getVar('description')) . ",
					" . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote($type) . ",
					" . $this->dbo->quoteName('required') . " = '" . (int)JRequest::getVar('required', 0) . "',
					" . $this->dbo->quoteName('published') . " = '" . (int)JRequest::getVar('published', 0) . "',
					" . $this->dbo->quoteName('editable') . " = '" . (int)JRequest::getVar('editable', 1) . "',
					" . $this->dbo->quoteName('exportable') . " = '" . ($newfld_class->isExportable() ? 1 : 0) . "',
					" . $this->dbo->quoteName('grpid') . " = " . (int)JRequest::getVar('grpid', 1) . ",
					" . $this->dbo->quoteName('params') . " = " . $this->dbo->Quote($newParams) . "
				WHERE " . $this->dbo->quoteName('id') . " = " . (int)$id;
		} else {
			$sqltype = trim($newfld_class->getSqlType());
			if ($sqltype) {
				$this->dbo->setQuery("ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName($name) . " " . $sqltype);
				$this->dbo->execute();
			}
			$query = "
				INSERT INTO #__extendedreg_fields (" . $this->dbo->quoteName('title') . ", " . $this->dbo->quoteName('name') . ", 
					" . $this->dbo->quoteName('type') . ", " . $this->dbo->quoteName('required') . ", " . $this->dbo->quoteName('description') . ", " . $this->dbo->quoteName('published') . ", 
					" . $this->dbo->quoteName('params') . ", " . $this->dbo->quoteName('grpid') . ", " . $this->dbo->quoteName('editable') . ", " . $this->dbo->quoteName('exportable') . ")
				VALUES (" . $this->dbo->Quote($title) . ", " . $this->dbo->Quote($name) . ", 
				" . $this->dbo->Quote($type) . ", '" . (int)JRequest::getVar('required', 0) . "', " . $this->dbo->Quote(JRequest::getVar('description')) . ", '" . (int)JRequest::getVar('published', 0) . "', 
				" . $this->dbo->Quote($newParams) . ", " . (int)JRequest::getVar('grpid', 1) . ", '" . (int)JRequest::getVar('editable', 1) . "', '" . ($newfld_class->isExportable() ? 1 : 0) . "')";
		}
		$this->dbo->setQuery($query);
		$this->dbo->execute();
		if (!$id) $id = $this->dbo->insertid();
		
		if ($task == 'fld_apply') {
			$url = 'index.php?option=com_extendedreg&task=forms.fld_edit&cid=' . $id;
		} elseif ($task == 'fld_savenew') {
			$url = 'index.php?option=com_extendedreg&task=forms.fld_new';
		} else {
			$url = 'index.php?option=com_extendedreg&task=forms.fields';
		}
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_FIELD_SAVED'));
		$app->redirect(JRoute::_($url, false));
		jexit();
	}
	
	function saveFieldOption() {
		$field_id = (int)JRequest::getVar('field_id', 0);
		if (!$field_id) return;
		$id = (int)JRequest::getVar('id', 0);
		$val = JRequest::getVar('val', '');
		$ord = (int)JRequest::getVar('ord', 0);
		
		if (!(int)$id) {
			$query = "INSERT INTO #__extendedreg_fields_values (" . $this->dbo->quoteName('field_id') . ", " . $this->dbo->quoteName('val') . ", " . $this->dbo->quoteName('ord') . ")  
				VALUES (" . (int)$field_id . ", " . $this->dbo->Quote($val) . ", " . (int)$ord . ")";
		} else {
			$query = "UPDATE #__extendedreg_fields_values SET 
					" . $this->dbo->quoteName('val') . " = " . $this->dbo->Quote($val) . ", 
					" . $this->dbo->quoteName('ord') . " = " . (int)$ord . "
				WHERE " . $this->dbo->quoteName('field_id') . " = " . (int)$field_id . " 
					AND " . $this->dbo->quoteName('id') . " = " . (int)$id;
		}
		$this->dbo->setQuery($query);
		$this->dbo->execute();
	}
	
	function deleteFieldOption() {
		$field_id = (int)JRequest::getVar('field_id', 0);
		$id = (int)JRequest::getVar('id', 0);
		if (!($field_id && $id)) return;
		$this->dbo->setQuery("DELETE FROM #__extendedreg_fields_values 
			WHERE " . $this->dbo->quoteName('field_id') . " = " . (int)$field_id . " 
				AND " . $this->dbo->quoteName('id') . " = " . (int)$id
		);
		$this->dbo->execute();
	}
	
	function fieldOptionsFromTxt() {
		$app = JFactory::getApplication();
		$field_id = (int)JRequest::getVar('field_id');
		
		if (!$field_id) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
			jexit();
		}
		
		// Get the uploaded file information
		$userfile = JRequest::getVar('from_txt', null, 'files', 'array');
		
		$errormsg = '';
		// Make sure that file uploads are enabled in php
		if (!$errormsg && !(bool) ini_get('file_uploads')) {
			$errormsg = JText::_('COM_EXTENDEDREG_FIELDOPT_UPLOAD_DISABLED');
		}
		
		// If there is no uploaded file, we have a problem...
		if (!$errormsg && (!is_array($userfile) || $userfile['error'] == 4) ) {
			$errormsg = JText::_('COM_EXTENDEDREG_FIELDOPT_UPLOAD_NOFILE');
		}
		
		// Check if there was a problem uploading the file.
		if (!$errormsg && ($userfile['error'] || $userfile['size'] < 1)) {
			$errormsg = JText::sprintf('COM_EXTENDEDREG_FIELDOPT_UPLOAD_ERROR', $userfile['error']);
		}
		
		// Check if it is a text file
		if (!$errormsg && ($userfile['type'] != 'text/plain')) {
			$errormsg = JText::_('COM_EXTENDEDREG_FIELDOPT_UPLOAD_NOTXT');
		}
		
		if ($errormsg) {
			$app->enqueueMessage($errormsg, 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid=' . (int)$field_id, false));
			jexit();
		}
		
		$contents = JvitalsHelper::removeBOM(trim(file_get_contents($userfile['tmp_name'])));
		if (!$contents) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDOPT_UPLOAD_FILE_EMPTY'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid=' . (int)$field_id, false));
			jexit();
		}
		
		if ((int)JRequest::getVar('fldopt_override', 0)) {
			$this->dbo->setQuery('DELETE FROM #__extendedreg_fields_values WHERE ' . $this->dbo->quoteName('field_id') . ' = ' . (int)$field_id);
			$this->dbo->execute();
		}
		
		$query = "INSERT INTO #__extendedreg_fields_values (" . $this->dbo->quoteName('field_id') . ", " . $this->dbo->quoteName('val') . ", " . $this->dbo->quoteName('ord') . ")  VALUES ";
		$val_arr = array();
		$fields = explode("\n", $contents);
		foreach ($fields as $i => $field) {
			$val_arr[] = '(' . (int)$field_id . ', ' . $this->dbo->Quote(trim($field)) . ', ' . (int)($i+1) . ')';
		}
		$query .= implode(',', $val_arr);
		
		$this->dbo->setQuery($query);
		if (!$this->dbo->execute()) {
			$app->enqueueMessage($this->dbo->getErrorMsg(), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid=' . (int)$field_id, false));
			jexit();
		}
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDOPT_UPLOAD_FILE_SAVED'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid=' . (int)$field_id, false));
		jexit();
	}
	
	function saveFieldGroup() {
		$app = JFactory::getApplication();
		$task = JRequest::getVar('task');
		$grpid = (int)JRequest::getVar('cid');
		
		$name = trim(JRequest::getVar('name'));
		if (!$name) {
			$url = 'index.php?option=com_extendedreg&task=forms.fldgrp_' . ($grpid ? 'edit&grpid=' . $grpid : 'new');
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDS_NAME'), 'error');
			$app->redirect(JRoute::_($url, false));
			jexit();
		}
		
		if ($grpid) {
			$query = "
				UPDATE #__extendedreg_fields_groups SET
					" . $this->dbo->quoteName('name') . " = " . $this->dbo->Quote($name) . ",
					" . $this->dbo->quoteName('description') . " = " . $this->dbo->Quote(JRequest::getVar('description')) . "
				WHERE " . $this->dbo->quoteName('grpid') . " = " . (int)$grpid;
		} else {
			$query = "
				INSERT INTO #__extendedreg_fields_groups (" . $this->dbo->quoteName('name') . ", " . $this->dbo->quoteName('description') . ")
				VALUES (" . $this->dbo->Quote($name) . ", " . $this->dbo->Quote(JRequest::getVar('description')) . ")";
		}
		$this->dbo->setQuery($query);
		$this->dbo->execute();
		if (!$grpid) $grpid = $this->dbo->insertid();
		
		if ($task == 'fldgrp_apply') {
			$url = 'index.php?option=com_extendedreg&task=forms.fldgrp_edit&grpid=' . $grpid;
		} elseif ($task == 'fldgrp_savenew') {
			$url = 'index.php?option=com_extendedreg&task=forms.fldgrp_new';
		} else {
			$url = 'index.php?option=com_extendedreg&task=forms.fields';
		}
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_SAVED_MSG'));
		$app->redirect(JRoute::_($url, false));
		jexit();
	}
	
	function getEmptyFieldGroups() {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$filter_group = $app->getUserState($option . '.filter.fld_group', '*');

		$where = "";
		if (!is_null($filter_group) && $filter_group != '*') {
			$where = " AND " . $this->dbo->quoteName('grpid') . " = " . $this->dbo->Quote($filter_group);
		}
		
		$this->dbo->setQuery("SELECT * FROM #__extendedreg_fields_groups 
			WHERE " . $this->dbo->quoteName('grpid') . " NOT IN (SELECT DISTINCT grpid FROM #__extendedreg_fields) " . $where . "
			ORDER BY " . $this->dbo->quoteName('name') . ", " . $this->dbo->quoteName('grpid'));
		$groups = $this->dbo->loadObjectList();
		if (!$groups) $groups = array();
		return $groups;
	}
	
	function setDefaultForm() {
		$app = JFactory::getApplication();
		
		$cid = JRequest::getVar('cid');
		if (is_array($cid)) $cid = $cid[0];
		$cid = (int)$cid;
		
		if ((int)$cid) {
			$query = "UPDATE #__extendedreg_forms SET " . $this->dbo->quoteName('isdefault') . " = 
				(case WHEN " . $this->dbo->quoteName('id') . " = " . (int)$cid . " THEN '1' ELSE '0' end),
				" . $this->dbo->quoteName('published') . " = (case WHEN " . $this->dbo->quoteName('id') . " = " . (int)$cid . " THEN '1' ELSE " . $this->dbo->quoteName('published') . " end)";
			$this->dbo->setQuery($query);
			$this->dbo->execute();
		}
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_FORMS_DEFAULT_SET'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.browse', false));
		jexit();
	}
	
	function setFormPublished(&$ids, $value = 1) {
		$loggeduser = JFactory::getUser();
		
		$canChange = JvitalsHelper::canDo('forms.manage', 'com_extendedreg');
		if (!$canChange) {
			$this->setError(JText::_('COM_EXTENDEDREG_NOTHING_TODO'));
			return false;
		}
		
		// Sanitize user ids.
		$ids = (array)$ids;
		$ids = array_unique($ids);
		JArrayHelper::toInteger($ids);
		
		if (!(int)$value) {
			$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_forms 
				WHERE " . $this->dbo->quoteName('isdefault') . " = " . $this->dbo->Quote('1') . "
					AND " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
			$count = (int)$this->dbo->loadResult();
			if ($count) {
				$this->setError(JText::_('COM_EXTENDEDREG_FORMS_CANT_UNPUBLISH_DEFAULT'));
				return false;
			}
		}
		
		$this->dbo->setQuery("UPDATE #__extendedreg_forms SET " . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote((int)$value ? '1' : '0') . " WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $ids). ")");
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function deleteForm() {
		$app = JFactory::getApplication();
		
		$cid = JRequest::getVar('cid');
		if (!is_array($cid)) $cid = array($cid);
		JArrayHelper::toInteger($cid, array(0));
		
		$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_forms 
			WHERE " . $this->dbo->quoteName('isdefault') . " = " . $this->dbo->Quote('1') . "
				AND " . $this->dbo->quoteName('id') . " IN (" . implode(',', $cid). ")");
		$count = (int)$this->dbo->loadResult();
		if ($count) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FORMS_CANT_DELETE_DEFAULT'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.browse', false));
			jexit();
		}
		
		$this->dbo->setQuery("DELETE FROM #__extendedreg_forms WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $cid) . ")");
		$this->dbo->execute();
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_FORMS_DELETED'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.browse', false));
		jexit();
	}
	
	function deleteCustomField() {
		$app = JFactory::getApplication();
		
		$cid = JRequest::getVar('cid');
		if (!is_array($cid)) $cid = array($cid);
		JArrayHelper::toInteger($cid, array(0));
		
		// Check for forms that use it
		$check = array();
		foreach ($cid as $fld_id) {
			$check[(int)$fld_id] = $this->dbo->quoteName('layout') . " LIKE '%#custom_" . (int)$fld_id . "#%'";
		}
		$this->dbo->setQuery("SELECT COUNT(*) FROM #__extendedreg_forms WHERE " . implode(' OR ', $check));
		$formCount = (int)$this->dbo->loadResult();
		
		if ($formCount) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_STILL_USED'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
			jexit();
		}
		
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('name') . " FROM #__extendedreg_fields WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $cid) . ")");
		$names = $this->dbo->loadObjectList();
		
		if ($names && is_array($names)) {
			foreach ($names as $obj) {
				$this->dbo->setQuery("ALTER TABLE #__extendedreg_users DROP " . $this->dbo->quoteName($obj->name));
				@$this->dbo->execute();
			}
		}
		
		$this->dbo->setQuery("DELETE FROM #__extendedreg_fields WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(',', $cid) . ")");
		$this->dbo->execute();
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_DELETED'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
		jexit();
	}
	
	function deleteFieldGroup() {
		$app = JFactory::getApplication();
		
		$cid = JRequest::getVar('grpid');
		if (!is_array($cid)) $cid = array($cid);
		JArrayHelper::toInteger($cid, array(0));
		
		if (in_array(1, $cid)) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_CANT_DELETE_DEFAULT'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
			jexit();
		}
		
		$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_fields WHERE " . $this->dbo->quoteName('grpid') . " IN (" . implode(',', $cid). ")");
		$count = (int)$this->dbo->loadResult();
		if ($count) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_CANT_DELETE'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
			jexit();
		}
		
		$this->dbo->setQuery("DELETE FROM #__extendedreg_fields_groups WHERE " . $this->dbo->quoteName('grpid') . " IN (" . implode(',', $cid) . ")");
		$this->dbo->execute();
		
		$app->enqueueMessage(JText::_('COM_EXTENDEDREG_FIELDS_GROUPS_DELETED_MSG'));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
		jexit();
	}
	
	function getExtraFieldsInfo($onlypublished = true, $onlyexportable = false) {
		static $EFinfo;
		if (!$EFinfo) $EFinfo = array();
		$key = 'key' . (int)$onlypublished . '_' . (int)$onlyexportable;
		
		if (!isset($EFinfo[$key])) {
			$where = array();
			if ($onlypublished) {
				$where[] = "f." . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote('1');
			}
			if ($onlyexportable) {
				$where[] = "f." . $this->dbo->quoteName('exportable') . " = " . $this->dbo->Quote('1');
			}
			$whereStr = (count($where) ? " WHERE " . implode(" AND ", $where) : "");
			
			$EFinfo[$key] = array();
			$this->dbo->setQuery("SELECT * FROM #__extendedreg_fields f " . $whereStr . " ORDER BY f." . $this->dbo->quoteName('ord'));
			$_EFinfo = $this->dbo->loadObjectList();
			if ($_EFinfo) {
				foreach ($_EFinfo as $EF) {
					$EFinfo[$key][(int)$EF->id] = $EF;
				}
			}
		}
		return $EFinfo[$key];
	}
	
	function getExtraFieldsInfoByGroup($grpid) {
		static $EFinfo;
		if (!$EFinfo) $EFinfo = array();
		if (!isset($EFinfo[$grpid])) {
			$EFinfo[$grpid] = array();
			$this->dbo->setQuery("SELECT * FROM #__extendedreg_fields f 
				WHERE f." . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote('1') . " 
					AND f." . $this->dbo->quoteName('grpid') . " = " . $this->dbo->Quote($grpid) . "
				ORDER BY f." . $this->dbo->quoteName('ord'));
			$_EFinfo = $this->dbo->loadObjectList();
			if ($_EFinfo) {
				foreach ($_EFinfo as $EF) {
					$EFinfo[$grpid][(int)$EF->id] = $EF;
				}
			}
		}
		return $EFinfo[$grpid];
	}
	
	function getExtraFieldsValues() {
		static $EFvalues;
		if (!$EFvalues) {
			$this->dbo->setQuery("SELECT * FROM #__extendedreg_fields_values fv 
				WHERE fv." . $this->dbo->quoteName('field_id') . " IN (
					SELECT " . $this->dbo->quoteName('id') . " FROM #__extendedreg_fields f WHERE f." . $this->dbo->quoteName('published') . " = " . $this->dbo->Quote('1') . "
				) ORDER BY " . $this->dbo->quoteName('ord') . ", " . $this->dbo->quoteName('val')
			);
			$res = $this->dbo->loadObjectList();
			$EFvalues = array();
			if (count($res)) {
				foreach ($res as $obj) {
					$EFvalues[$obj->field_id][] = $obj->val;
				}
			}
		}
		return $EFvalues;
	}
	
	function fld_saveorder($pks, $order) {
		$query = "UPDATE #__extendedreg_fields SET " . $this->dbo->quoteName('ord') . " = (case ";
		foreach ($pks as $k => $id) {
			if (isset($order[$id])) {
				$query .= " WHEN " . $this->dbo->quoteName('id') . " = " . (int)$id . " THEN " . (int)$order[$id] . " ";
			}
		}
		$query .= " ELSE 1 end) 
		WHERE " . $this->dbo->quoteName('id') . " IN (" . implode(', ', $pks) . ")";
		
		$this->dbo->setQuery($query);
		if (!$this->dbo->execute()) {
			$this->setError($this->dbo->getErrorMsg());
			return false;
		}
		return true;
	}
	
	function fld_reorder($dir) {
		$ids	= JRequest::getVar('cid', null, 'post', 'array');
		$order = JRequest::getVar('order', null, 'post', 'array');
		if (!empty($ids)) {
			$ids = (int)$ids[0];
		}
		if ((int)$ids) {
			$ord = (int)$order[$ids];
			
			if ($dir > 0) {
				$query = "UPDATE #__extendedreg_fields SET
					" . $this->dbo->quoteName('ord') . " = (case 
						WHEN " . $this->dbo->quoteName('id') . " = " . (int)$ids . " 
						THEN " . ((int)$ord + 1) . " 
						ELSE " . (int)$ord . "
					end)
				WHERE " . $this->dbo->quoteName('ord') . " IN (" . (int)$ord . ", " . ((int)$ord + 1) . ")";
			} elseif ($dir < 0) {
				$query = "UPDATE #__extendedreg_fields SET
					" . $this->dbo->quoteName('ord') . " = (case 
						WHEN " . $this->dbo->quoteName('id') . " = " . (int)$ids . " 
						THEN " . ((int)$ord - 1) . " 
						ELSE " . (int)$ord . "
					end)
				WHERE " . $this->dbo->quoteName('ord') . " IN (" . (int)$ord . ", " . ((int)$ord - 1) . ")";
			}
			
			$this->dbo->setQuery($query);
			if (!$this->dbo->execute()) {
				$this->setError($this->dbo->getErrorMsg());
				return false;
			}
			
			return true;
		}
		
		$this->setError(JText::_('COM_EXTENDEDREG_ERROR'));
		return false;
	}
	
	function saveFieldCustomSQL() {
		$app = JFactory::getApplication();
		$field_id = (int)JRequest::getVar('field_id');
		$custom_sql = JRequest::getVar('custom_sql');
		
		$query = "UPDATE #__extendedreg_fields SET " . $this->dbo->quoteName('custom_sql') . " = " . $this->dbo->Quote($custom_sql) . " WHERE " . $this->dbo->quoteName('id') . " = " . (int)$field_id;
		$this->dbo->setQuery($query);
		if (!$this->dbo->execute()) {
			$msg = 'COM_EXTENDEDREG_FIELDOPT_CUSTOM_SQL_SAVEFAILED';
		} else {
			$msg = 'COM_EXTENDEDREG_FIELDOPT_CUSTOM_SQL_SAVEOK';
		}
		
		$app->enqueueMessage(JText::_($msg));
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid=' . (int)$field_id, false));
		jexit();
	}
	
	function loadTermsArticle($termsArticleID) {
		// Construct the query
		$query = $this->dbo->getQuery(true)
			->select($this->dbo->quoteName('title') . ", " . $this->dbo->quoteName('introtext') . ", " . $this->dbo->quoteName('fulltext'))
			->from("#__content")
			->where($this->dbo->quoteName('id') . " = " . (int)$termsArticleID);
		
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
	
}