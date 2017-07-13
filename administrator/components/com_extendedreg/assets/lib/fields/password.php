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

class erFieldPassword extends erField implements erFieldInterface {

	function __construct($record) {
		parent::__construct($record);
	}
	
	public function getSqlType() {
		return  "varchar(255)";
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
		$post[$this->_fld->name] = trim($post[$this->_fld->name]);
		
		$filter = new JFilterInput();
		$post[$this->_fld->name] = $filter->clean($post[$this->_fld->name]);
		
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
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		
		$class = $this->_params->get('input_size', trim($conf->css_default_input_class));
		if ((int)$this->_fld->required) $class .= ' required';
		if ((int)$conf->pass_strength_enable && (int)$this->_params->get('show_pass_strength', 0)) $class .= ' validate-password';
		if (!mb_strlen(trim($value))) $value = '';
		$this->getJavascptValidation();
		return '<input' . (trim($class) ? ' class="' . $class . '"' : '') . (trim($id) ? ' id="' . $id . '"' : '') . ' type="password" autocomplete="off" name="' . $this->_fld->name . '" value="" />';
	}
	
	public function getSearchHtml($value, $name = '') {
		return '';
	}
	
	public function isExportable() {
		return false;
	}
}