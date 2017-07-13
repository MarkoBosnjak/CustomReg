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

class erValidationAllowedlist extends JObject implements erValidationInterface {
	private $lib;
	private $field;
	private $data;
	
	function __construct($lib, $field = null) {
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg_validation_allowedlist');
		
		$this->lib = $lib;
		$this->field = $field;
		$this->data = JvitalsHelper::params2object(($this->field ? $this->field->params : ''));
		parent::__construct();
	}
	
	public function validate($value, $extradata = array()) {
		$arr = explode(',', $this->data->get('allowedlist'));
		$result = (boolean)in_array($value, $arr);
		if (!$result) {
			$this->setError(JText::sprintf('COM_EXTENDEDREG_ERROR_ALLOWEDLIST', $this->data->get('allowedlist')));
		}
		return $result;
	}
	
	public function loadJavascript() {
		static $loaded;
		
		if (!$loaded) {
			$model = JvitalsHelper::loadModel('extendedreg', 'Default');
			$conf = $model->getConfObj();
			if ((int)$conf->include_jquery_formvalidation) {
				erHelperJavascript::OnDomBegin('
					function erValidationAllowedlist(val, arr) {
						if (!(val && val.toString().length)) return true;
						if (!arr) arr = [];
						if (jQuery.inArray(val.toString(), arr) > -1) {
							return true;
						}
						return false;
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
			erHelperJavascript::OnDomBegin('
				if (!ER_FORM_VALIDATION["' . $this->field->name . '"]) ER_FORM_VALIDATION["' . $this->field->name . '"] = {};
				ER_FORM_VALIDATION["' . $this->field->name . '"]["erValidationAllowedlist"] = function(er_form_passed) {
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
					
					var arr = ' . json_encode(explode(',', $this->data->get('allowedlist'))) . ';
					if (!arr) arr = [];

					if (!erValidationAllowedlist(val, arr)) {
						jQuery.error("' . JText::sprintf('COM_EXTENDEDREG_ERROR_ALLOWEDLIST', $this->data->get('allowedlist')) . '");
						return false;
					}
					
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

		$html = '<table>
			<tr>
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST_VALIDATION') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST_VALIDATION')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST_VALIDATION') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST_VALIDATION_TOOLTIP')) . '" border="0" /></td>
				<td valign="top"><input type="checkbox" name="params[validations][]" value="' . (int)$this->lib->id . '"' . $checked . ' /></td>
			</tr>
			<tr>
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST') . '</b> <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST')) . '" class="hasTip" title="' . htmlspecialchars(JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST') . '::' . JText::_('COM_EXTENDEDREG_FIELDS_ALLOWEDLIST_TOOLTIP')) . '" border="0" /></td>
				<td valign="top"><textarea name="params[allowedlist]">' . $this->data->get('allowedlist') . '</textarea></td>
			</tr>
		</table>';
		return $html;
	}
	
}

