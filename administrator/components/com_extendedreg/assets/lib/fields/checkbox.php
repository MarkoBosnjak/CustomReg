<?php 
/**
 * @package		ExtendedReg
 * @version		2.02
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erFieldCheckbox extends erField implements erFieldInterface {
	
	function __construct($record) {
		parent::__construct($record);
	}
	
	public function getSqlType() {
		return  "enum('0','1')";
	}
	
	public function hasParams() {
		return true;
	}
	
	public function hasOptions() {
		return false;
	}
	
	public function isMultiselect() {
		return false;
	}
	
	public function serversideValidation(&$post) {
		$result = true;
		$errmsg = '';
		
		$post[$this->_fld->name] = (int)$post[$this->_fld->name];
		if (!in_array($post[$this->_fld->name], array(1,0))) $post[$this->_fld->name] = 0;
		if ((int)$this->_fld->required && !$post[$this->_fld->name]) {
			$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
			$result = false;
		}
		if ($post[$this->_fld->name]) {
			$validations = erHelperAddons::loadAddons('validation');
			if ($validations) {
				$selval = (array)$this->_params->get('validations');
				foreach ($validations as $lib) {
					if ($selval && is_array($selval) && in_array((int)$lib->id, $selval)) {
						$obj = erHelperAddons::getFieldValidation($lib, $this->_fld);
						if (!$obj->validate($post[$this->_fld->name], $post)) {
							$errmsg .= $obj->getError() . "\n";
							$result = false;
						}
					}
				}
			}
		}
		if (!$result) {
			$this->setError($errmsg);
			return false;
		}
		return true;
	}
	
	public function getHtml($value, $id = null) {
		$class = 'checkbox';
		if ((int)$this->_fld->required) $class .= ' required';
		if (!mb_strlen(trim($value))) $value = '';
		$this->getJavascptValidation();
		return '<input' . (trim($class) ? ' class="' . $class . '"' : '') . (trim($id) ? ' id="' . $id . '"' : '') . ' type="checkbox" name="' . $this->_fld->name . '" value="1"' . ((int)$value ? ' checked' : '') . ' />';
	}
	
	public function getSearchHtml($value, $name = '') {
		if (!mb_strlen(trim($value))) $value = '';
		if (!trim($name)) $name = $this->_fld->name;
		return '<input type="checkbox" name="' . $name . '" value="1"' . ((int)$value ? ' checked' : '') . ' />';
	}
}