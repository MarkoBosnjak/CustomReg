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

class erFieldDate extends erField implements erFieldInterface {
	
	function __construct($record) {
		parent::__construct($record);
	}
	
	public function getSqlType() {
		return  "date";
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
	
	public function prepareDataForSave($value) {
		$date = '0000-00-00';
		if (is_array($value) && count($value) == 3) {
			$year = '0000';
			if ((int)$value[2] && mb_strlen(trim($value[2])) == 4) {
				$year = trim($value[2]);
			}
			$month = '00';
			if ((int)$value[1] >= 1 && (int)$value[1] <= 12) {
				$month = (int)$value[1];
				if ((int)$month < 10) {
					$month = '0' . (int)$month;
				}
			}
			$day = '00';
			if ((int)$value[0] >= 1 && (int)$value[0] <= 31) {
				$day = (int)$value[0];
				if ((int)$day < 10) {
					$day = '0' . (int)$day;
				}
			}
			if ($year == '0000' || $month == '00' || $day == '00') return '0000-00-00';
			$date = $year . '-' . $month . '-' . $day;
		}
		return $date;
	}
	
	public function serversideValidation(&$post) {
		$result = true;
		$errmsg = '';
		
		$value= $post[$this->_fld->name];
		if (trim($value) && !is_array($value)) {
			if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $value, $m)) {
				$value = array(2 => $m[1], 1 => $m[2], 0 => $m[3]);
			}
		}
		if ((int)$this->_fld->required && !is_array($value) && count($value) != 3) {
			$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
			$result = false;
		}
		if ((int)$this->_fld->required && !(mb_strlen(trim($value[0])) && mb_strlen(trim($value[2])))) {
			$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
			$result = false;
		}
		$value = $this->prepareDataForSave($value);
		
		if ($result && $value != '0000-00-00') {
			if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $value, $m)) {
				$value = array('year' => $m[1], 'month' => $m[2], 'day' => $m[3]);
				if (!(int)$value['year']) $value['year'] = '';
				if (!(int)$value['month']) $value['month'] = 1;
				if (!(int)$value['day']) $value['day'] = '';
			}
			
			$validations = erHelperAddons::loadAddons('validation');
			if ($validations) {
				$selval = (array)$this->_params->get('validations');
				foreach ($validations as $lib) {
					if ($selval && is_array($selval) && in_array((int)$lib->id, $selval)) {
						$obj = erHelperAddons::getFieldValidation($lib, $this->_fld);
						if (!$obj->validate($value, $post)) {
							$errmsg .= $obj->getError() . "\n";
							$result = false;
						}
					}
				}
			}
		} elseif ($result) {
			if ((int)$this->_fld->required) {
				$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
				$result = false;
			}
		}
		if (!$result) {
			$this->setError($errmsg);
			return false;
		}
		return true;
	}
	
	public function getHtml($value, $id = null) {
		$class = 'datebox';
		if ((int)$this->_fld->required) $class .= ' required';
		$value = trim($value);
		if (mb_strlen($value)) {
			if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $value, $m)) {
				$value = array('year' => $m[1], 'month' => $m[2], 'day' => $m[3]);
				if (!(int)$value['year']) $value['year'] = '';
				if (!(int)$value['month']) $value['month'] = 1;
				if ((int)$value['month'] < 1) $value['month'] = 1;
				if ((int)$value['month'] > 12) $value['month'] = 12;
				if (!(int)$value['day']) $value['day'] = '';
			}
		} else {
			$value = array('year' => '', 'month' => 1, 'day' => '');
		}
		$this->getJavascptValidation();
		
		return '
			<input class="date-day' . (trim($class) ? ' ' . $class : '') . '"' . (trim($id) ? ' id="' . $id . '-day"' : '') . ' type="text" name="' . $this->_fld->name . '[]" value="' . $value['day'] . '" maxlength="2" size="3" />
			<select class="date-month' . (trim($class) ? ' ' . $class : '') . '"' . (trim($id) ? ' id="' . $id . '-month"' : '') . ' name="' . $this->_fld->name . '[]">
				<option value="1"' . ((int)$value['month'] == 1 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH1') . '</option>
				<option value="2"' . ((int)$value['month'] == 2 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH2') . '</option>
				<option value="3"' . ((int)$value['month'] == 3 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH3') . '</option>
				<option value="4"' . ((int)$value['month'] == 4 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH4') . '</option>
				<option value="5"' . ((int)$value['month'] == 5 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH5') . '</option>
				<option value="6"' . ((int)$value['month'] == 6 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH6') . '</option>
				<option value="7"' . ((int)$value['month'] == 7 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH7') . '</option>
				<option value="8"' . ((int)$value['month'] == 8 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH8') . '</option>
				<option value="9"' . ((int)$value['month'] == 9 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH9') . '</option>
				<option value="10"' . ((int)$value['month'] == 10 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH10') . '</option>
				<option value="11"' . ((int)$value['month'] == 11 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH11') . '</option>
				<option value="12"' . ((int)$value['month'] == 12 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_MONTH12') . '</option>
			</select>
			<input class="date-year' . (trim($class) ? ' ' . $class : '') . '"' . (trim($id) ? ' id="' . $id . '-year"' : '') . ' type="text" name="' . $this->_fld->name . '[]" value="' . $value['year'] . '" maxlength="4" size="5" />
		';
	}
}