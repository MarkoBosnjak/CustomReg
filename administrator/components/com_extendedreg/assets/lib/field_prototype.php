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

class erField extends JObject {
	
	protected $_fld;
	protected $_model;
	protected $_options;
	protected $_params;
	protected $_user;
	
	function __construct($record) {
		$this->_model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$this->_fld = $record;
		$this->_options = array();
		$this->_params = null;
		$this->_user = null;
		
		if ($this->hasOptions()) {
			$conf = $this->_model->getConfObj();
			$custom_sql = '';
			if ((int)$conf->use_opts_sql && isset($this->_fld->custom_sql)) {
				$custom_sql = $this->_fld->custom_sql;
			}
			$this->_options = $this->_model->getFieldOpts((int)$this->_fld->id, 0, $custom_sql);
		}
		
		if ($this->hasParams()) {
			$name = mb_strtolower($this->_fld->type);
			$paramPath = JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . JFile::makeSafe($name) . '.xml';
			if (is_file($paramPath)) {
				$paramsData = isset($this->_fld->params) ? trim($this->_fld->params) : '';
				$this->_params = new JvitalsParameter($paramsData, $paramPath);
			} else {
				JError::raiseError(83008, JText::sprintf('COM_EXTENDEDREG_FIELDPROTOTYPE_FILENOTFOUND', $paramPath));
				jexit();
			}
		}
	}
	
	public function setUser($user) {
		$this->_user = $user;
	}
	
	public function getUser() {
		return $this->_user;
	}
	
	public function getField() {
		return $this->_fld;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function renderParams() {
		$result = '';
		if ($this->_params) {
			$result = $this->_params->render();
		}
		return $result;
	}
	
	public function getOptions() {
		return $this->_options;
	}
	
	public function hideTitle() {
		return false;
	}
	
	public function hasFormField() {
		return true;
	}
	
	public function getJavascptValidation() {
		$validations = erHelperAddons::loadAddons('validation');
		if ($validations) {
			$params = JvitalsHelper::params2object($this->_fld->params);
			$selval = $params->get('validations');
			if (!is_array($selval) && (int)$selval) {
				$selval = array((int)$selval);
			}
			foreach ($validations as $lib) {
				if ($selval && is_array($selval) && in_array((int)$lib->id, $selval)) {
					$obj = erHelperAddons::getFieldValidation($lib, $this->_fld);
					$obj->loadFieldJavascript();
				}
			}
		}
	}
	
	public function serversideValidation(&$post) {
		return true;
	}
	
	public function getResultHtml($value) {
		$displayvalue = trim($value);
		if ($displayvalue) {
			if ($this->isMultiselect()) {
				$temp = explode('#!#', $value);
				if (count($temp) > 1) {
					$displayvalue = '<ul><li>' . implode('</li><li>', $temp) . '</li></ul>';
				}
			}
		}
		return $displayvalue;
	}
	
	public function getSearchHtml($value, $name = '') {
		if (!mb_strlen(trim($value))) $value = '';
		if (!trim($name)) $name = $this->_fld->name;
		return '<input class="inputbox" type="text" name="' . $name . '" value="' . $value . '" />';
	}
	
	public function getNoeditHtml($value) {
		if (!mb_strlen(trim($value))) $value = '';
		$displayvalue = $value;
		if ($this->isMultiselect()) {
			$temp = explode('#!#', $value);
			if (count($temp) > 1) {
				$displayvalue = '<ul><li>' . implode('</li><li>', $temp) . '</li></ul>';
			}
		}
		return '<span class="er-noedit-value">' . $displayvalue . '</span>
		<input type="hidden" name="' . $this->_fld->name . '" value="' . $value . '" />';
	}
	
	public function isExportable() {
		return true;
	}
	
	public function hasSpecialObject() {
		return false;
	}
	
	public function getSpecialObject($defval, $options, &$view, $fieldIDAttr) {
		return null;
	}
	
}