<?php 
/**
 * @package		ExtendedReg
 * @version		2.03
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erFieldInformation extends erField implements erFieldInterface {

	function __construct($record) {
		parent::__construct($record);
	}
	
	public function getSqlType() {
		return  "";
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
	
	public function hasFormField() {
		return false;
	}
	
	public function getJavascptValidation() {
		return false;
	}
	
	public function hideTitle() {
		return true;
	}
	
	public function getSearchHtml($value, $name = '') {
		return '';
	}
	
	public function isExportable() {
		return false;
	}
	
	protected function getInformation() {
		$result = trim($this->_params->get('contents', ''));
		if (!preg_match('~\s+~', $result)) {
			return JText::_($result);
		}
		return $result;
	}
	
	public function getHtml($value, $id = null) {
		return $this->getInformation();
	}
	
	public function getResultHtml($value) {
		return $this->getInformation();
	}
	
	public function getNoeditHtml($value) {
		return $this->getInformation();
	}
}