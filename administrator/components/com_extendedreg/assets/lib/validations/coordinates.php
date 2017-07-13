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

class erValidationCoordinates extends JObject implements erValidationInterface {
	private $lib;
	private $field;
	private $data;
	
	function __construct($lib, $field = null) {
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg_validation_coordinates');
		
		$this->lib = $lib;
		$this->field = $field;
		$this->data = JvitalsHelper::params2object(($this->field ? $this->field->params : ''));
		parent::__construct();
	}
	
	public function validate($value, $extradata = array()) {
		if (!strlen(trim($value))) return true;
		$coordinates_type = (int)$this->data->get('coordinates_type');
		if ($coordinates_type == 1) {
			if (!preg_match('~^([-+]?\d{1,2}[.]\d+)$~smi', $value)) {
				$this->setError(JText::_('COM_EXTENDEDREG_ERROR_LATITUDE'));
				return false;
			}
		} elseif ($coordinates_type == 2) {
			if (!preg_match('~^([-+]?\d{1,3}[.]\d+)$~smi', $value)) {
				$this->setError(JText::_('COM_EXTENDEDREG_ERROR_LONGITUDE'));
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
					function erValidationCoordinates(val, patt) {
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
			$coordinates_type = (int)$this->data->get('coordinates_type');
			if ((int)$coordinates_type > 0 && (int)$coordinates_type < 3) {
				
				$regexp = $coordinates_type == 1 ? '/^([-+]?\d{1,2}[.]\d+)$/gi' : '/^([-+]?\d{1,3}[.]\d+)$/gi' ;
				$errormsg = $coordinates_type == 1 ? JText::_('COM_EXTENDEDREG_ERROR_LATITUDE') : JText::_('COM_EXTENDEDREG_ERROR_LONGITUDE') ;
				
				erHelperJavascript::OnDomBegin('
					if (!ER_FORM_VALIDATION["' . $this->field->name . '"]) ER_FORM_VALIDATION["' . $this->field->name . '"] = {};
					ER_FORM_VALIDATION["' . $this->field->name . '"]["erValidationCoordinates"] = function(er_form_passed) {
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
						var patt = ' . $regexp . ';
						if (!erValidationCoordinates(val, patt)) {
							jQuery.error("' . $errormsg . '");
							return false;
						}
						return true;
					};
				');
			}
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
		
		$coordinates_type = (int)$this->data->get('coordinates_type');
		
		$html = '<table>
			<tr>
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_VALIDATION') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_VALIDATION')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_VALIDATION') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_VALIDATION_TOOLTIP')) . '" border="0" /></td>
				<td valign="top"><input type="checkbox" name="params[validations][]" value="' . (int)$this->lib->id . '"' . $checked . ' /></td>
			</tr>
			<tr>
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_TYPE') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_TYPE')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_TYPE') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_TYPE_TOOLTIP')) . '" border="0" /></td>
				<td valign="top">
					<select name="params[coordinates_type][]">
						<option value="1"' . ($coordinates_type == 1 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_TYPE_1') . '</option>
						<option value="2"' . ($coordinates_type == 2 ? ' selected' : '') . '>' . JText::_('COM_EXTENDEDREG_FIELDS_COORDINATES_TYPE_2') . '</option>
					</select>
				</td>
			</tr>
		</table>';
		return $html;
	}
	
}

