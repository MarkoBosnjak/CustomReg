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

class erFieldTextarea extends erField implements erFieldInterface {

	function __construct($record) {
		parent::__construct($record);
	}
	
	public function getSqlType() {
		return  "text";
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
		$post[$this->_fld->name] = JvitalsHelper::sanitize(trim($post[$this->_fld->name]));
		
		if ((int)$this->_fld->required && !mb_strlen($post[$this->_fld->name])) {
			$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
			$result = false;
		}
		if (mb_strlen($post[$this->_fld->name])) {
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
		$conf = $this->_model->getConfObj();
		$class = $this->_params->get('input_size', trim($conf->css_default_input_class));
		if ((int)$this->_fld->required) $class .= ' required';
		if (!mb_strlen(trim($value))) $value = '';
		$this->getJavascptValidation();
		return '<textarea' . (trim($class) ? ' class="' . $class . '"' : '') . (trim($id) ? ' id="' . $id . '"' : '') . ' cols="' . (int)$this->_params->get('cols', 40) . '" rows="' . (int)$this->_params->get('rows', 15) . '" name="' . $this->_fld->name . '">' . $value . '</textarea>';
	}
}