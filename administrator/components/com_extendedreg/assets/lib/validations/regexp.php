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

class erValidationRegexp extends JObject implements erValidationInterface {
	private $lib;
	private $field;
	private $data;
	
	function __construct($lib, $field = null) {
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg_validation_regexp');
		
		$this->lib = $lib;
		$this->field = $field;
		$this->data = JvitalsHelper::params2object(($this->field ? $this->field->params : ''));
		parent::__construct();
	}
	
	private function generateRegexp() {
		$regexp = 0;
		if (is_array($this->data->get('regexp'))) {
			foreach ($this->data->get('regexp') as $bit) if ((int)$bit) $regexp += (int)$bit;
		} else {
			$regexp = (int)$this->data->get('regexp');
		}
		$check = array();
		if (($regexp & 1) == 1) {
			$check[] = '0-9';
		}
		if (($regexp & 2) == 2) {
			$check[] = 'a-z';
		}
		if (($regexp & 4) == 4) {
			$check[] = '\w';
		}
		if (($regexp & 8) == 8) {
			$check[] = '\s';
		}
		
		$result = array(
			0 => '.*',
			1 => '',
		);
		
		if (count($check)) {
			$result[0] = '[' . implode('|', $check) . ']+';
		}
		
		if (($regexp & 16) == 16) {
			$regexp_other = $this->data->get('regexp_other');
			if ($regexp_other && is_array($regexp_other)) {
				$regexp_other = implode('|', $regexp_other);
			}
			if (trim($regexp_other)) $result[1] = $regexp_other;
		}

		return $result;
	}
	
	public function validate($value, $extradata = array()) {
		if (!strlen(trim($value))) return true;
		$regexp = $this->generateRegexp();
		if (!preg_match('~^' . $regexp[0] . '$~smi', $value)) {
			$this->setError(JText::_('COM_EXTENDEDREG_ERROR_REGEXP'));
			return false;
		}
		if (trim($regexp[1])) {
			if (!preg_match('~' . trim($regexp[1]) . '~smi', $value)) {
				$this->setError(JText::_('COM_EXTENDEDREG_ERROR_REGEXP'));
				return false;
			}
		}
		return true;
	}
	
	public function loadJavascript() {
		static $loaded;
		
		if (!$loaded) {
			$model = JvitalsHelper::loadModel('extendedreg', 'Default');
			$conf = $model->getConfObj();
			if ((int)$conf->include_jquery_formvalidation) {
				erHelperJavascript::OnDomBegin('
					function erValidationRegexp(val, patt) {
						if (!(val && val.toString().length)) return true;
						var fld_val = val.toString();
						return patt.test(fld_val);
					}
				');
			}
			$loaded = true;
		}
		
		return true;
	}
	
	public function loadFieldJavascript() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		if ((int)$conf->include_jquery_formvalidation) {
			$regexp = $this->generateRegexp();
			
			erHelperJavascript::OnDomBegin('
				if (!ER_FORM_VALIDATION["' . $this->field->name . '"]) ER_FORM_VALIDATION["' . $this->field->name . '"] = {};
				ER_FORM_VALIDATION["' . $this->field->name . '"]["erValidationRegexp"] = function(er_form_passed) {
					var er_form = er_form_passed;
					if (!er_form) {
						er_form = jQuery("form.er-form-validate");
					}
					if (!er_form) {
						return true;
					}
					
					var er_visible_step_arr = jQuery(".er-form-step", er_form);
					var er_visible_step = null;
					if (er_visible_step_arr) {
						er_visible_step_arr.each(function() {
							if (jQuery(this).css("display") == "block") {
								er_visible_step = jQuery(this);
							}
						});
					}
					
					var val = null;
					if (er_visible_step) {
						// We have steps
						val = jQuery("[name=' . $this->field->name . ']", er_visible_step).val();
					} else {
						val = jQuery("[name=' . $this->field->name . ']", er_form).val();
					}
					var patt = /^' . $regexp[0] . '$/gi;
					if (!erValidationRegexp(val, patt)) {
						jQuery.error("' . JText::_('COM_EXTENDEDREG_ERROR_REGEXP') . '");
						return false;
					}
					' . (trim($regexp[1]) ? '
					var pattadv = /' . $regexp[1] . '/gi;
					if (!erValidationRegexp(val, pattadv)) {
						jQuery.error("' . JText::_('COM_EXTENDEDREG_ERROR_REGEXP') . '");
						return false;
					}
					' : '') . '
					return true;
				};
			');
		}
		return true;
	}
	
	public function getElements() {
		$checked = '';
		$validations = $this->data->get('validations');
		if (empty($validations)) $validations = array();
		if ($validations && !is_array($validations)) {
			$validations = array($validations);
		}
		if (in_array((int)$this->lib->id, $validations)) {
			$checked = ' checked';
		}
		
		$regexp = 0;
		if (is_array($this->data->get('regexp'))) {
			foreach ($this->data->get('regexp') as $bit) if ((int)$bit) $regexp += (int)$bit;
		} else {
			$regexp = (int)$this->data->get('regexp');
		}
		
		$regexp_other = $this->data->get('regexp_other');
		if ($regexp_other && is_array($regexp_other)) {
			$regexp_other = implode('|', $regexp_other);
		}
		
		$html = '<table>
			<tr>
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_VALIDATION') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_VALIDATION')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_VALIDATION') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_VALIDATION_TOOLTIP')) . '" border="0" /></td>
				<td valign="top"><input type="checkbox" name="params[validations][]" value="' . (int)$this->lib->id . '"' . $checked . ' /></td>
			</tr>
			<tr>
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_REGEXP') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_REGEXP')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_REGEXP') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_TOOLTIP')) . '" border="0" /></td>
				<td valign="top">
					<select name="params[regexp][]" multiple="multiple" size="5">
						<option value="1"' . (($regexp & 1) == 1 ? ' selected' : '') . '>Digits (0-9)</option>
						<option value="2"' . (($regexp & 2) == 2 ? ' selected' : '') . '>Letters (a-z)</option>
						<option value="4"' . (($regexp & 4) == 4 ? ' selected' : '') . '>Word (\w)</option>
						<option value="8"' . (($regexp & 8) == 8 ? ' selected' : '') . '>Space (\s)</option>
						<option value="16"' . (($regexp & 16) == 16 ? ' selected' : '') . '>Other (define below)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><b style="color: red;">' . JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_OTHER') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_OTHER')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_OTHER') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_REGEXP_OTHER_TOOLTIP')) . '" border="0" /></td>
				<td valign="top"><input type="text" name="params[regexp_other][]" value="' . trim($regexp_other) . '" /></td>
			</tr>
		</table>';
		return $html;
	}
	
}

