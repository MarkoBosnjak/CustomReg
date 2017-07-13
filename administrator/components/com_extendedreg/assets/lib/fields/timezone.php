<?php 
/**
 * @package		ExtendedReg
 * @version		1.01
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erFieldTimezone extends erField implements erFieldInterface {

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
		if (!$result) {
			$this->setError($errmsg);
			return false;
		}
		return true;
	}
	
	public function manipulatePostData(&$post) {
		$post['params']['timezone'] = trim($post[$this->_fld->name]);
	}
	
	public function getHtml($value, $id = null) {
		jimport('joomla.form.form');
		
		$conf = $this->_model->getConfObj();
		$class = $this->_params->get('input_size', trim($conf->css_default_input_class));
		if ((int)$this->_fld->required) $class .= ' required';
		if (!mb_strlen(trim($value))) $value = '';
		$this->getJavascptValidation();
		
		$result = '';
		$paramsfile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'form.xml';
		$form = JForm::getInstance($this->_fld->name . $this->_fld->id . 'Form', $paramsfile);
		$form->setValue('timezone', 'custom_fields', $value);
		
		foreach ($form->getFieldset('settings') as $field) {
			$result = $field->input;
		}
		
		$result = str_replace(array('custom_fields[timezone]', '#class#'), array($this->_fld->name, $class), $result);
		
		return $result;
	}
}