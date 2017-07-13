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

class erValidationUnique extends JObject implements erValidationInterface {
	private $lib;
	private $field;
	private $data;
	
	function __construct($lib, $field = null) {
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg_validation_unique');
		
		$this->lib = $lib;
		$this->field = $field;
		$this->data = JvitalsHelper::params2object(($this->field ? $this->field->params : ''));
		parent::__construct();
	}
	
	public function validate($value, $extradata = array()) {
		if (!strlen($value)) {
			return true;
		}
		$app = JFactory::getApplication();
		if ($app->isAdmin()) {
			$user_id = (int)$extradata['id'];
		} else {
			$user = JFactory::getUser();
			$user_id = (int)$user->id;
		}
		
		$dbo = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->select('COUNT(*)')->from('#__extendedreg_users')->where($dbo->quoteName($this->field->name) . ' = ' . $dbo->quote($value) . ' AND ' . $dbo->quoteName('user_id') . ' != ' . (int)$user_id);
		$dbo->setQuery($query);
		try {
			$result = (int)$dbo->loadResult();
		} catch (RuntimeException $e) {
		}
		if (!isset($result)) $result = false;
		
		if ($result) {
			$this->setError(JText::_('COM_EXTENDEDREG_VALUE_NOTUNIQUE_ERROR'));
			return false;
		}
		return true;
	}
	
	public function loadJavascript() {
		return true;
	}
	
	public function loadFieldJavascript() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		if ((int)$conf->include_jquery_formvalidation) {
			erHelperJavascript::OnDomBegin('
				if (!ER_FORM_VALIDATION["' . $this->field->name . '"]) ER_FORM_VALIDATION["' . $this->field->name . '"] = {};
				ER_FORM_VALIDATION["' . $this->field->name . '"]["erValidationUnique"] = function(er_form_passed) {
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
					
					try {
						var localresult = erValidationUnique(val, ' . (int)$this->field->id . ');
						if (!localresult) {
							return false;
						}
					} catch (err) {
						jQuery.error(err);
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
				<td valign="top"><b>' . JText::_('COM_EXTENDEDREG_FIELDS_UNIQUE_VALIDATION') . '</b></td>
				<td valign="top"><input type="checkbox" name="params[validations][]" value="' . (int)$this->lib->id . '"' . $checked . ' /></td>
			</tr>
		</table>';
		return $html;
	}
	
}

