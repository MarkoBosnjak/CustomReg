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

class erFieldConfirmfield extends erField implements erFieldInterface {

	function __construct($record) {
		parent::__construct($record);
		
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg_field_confirmfield');
		
		$conf = $this->_model->getConfObj();
		if ((int)$conf->include_jquery_formvalidation) {
			erHelperJavascript::OnDomBegin('
				function erConfirmfieldEqual(val1, val2) {
					if (!val1.toString().length) return true;
					if (!val2.toString().length) return true;
					if (val1.toString() == val2.toString()) return true;
					return false;
				}
			');
		}
	}
	
	public function getSqlType() {
		return  "mediumtext";
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
	
	public function getJavascptValidation() {
		parent::getJavascptValidation();
		
		$conf = $this->_model->getConfObj();
		if ((int)$conf->include_jquery_formvalidation) {
			$defaultTitle = JText::sprintf('COM_EXTENDEDREG_FIELDS_CONFIRMFIELD_TITLE_DEFAULT', $this->_fld->title);
			$confirm_title = $this->_params->get('confirm_title', $defaultTitle);
			
			erHelperJavascript::OnDomBegin('
				if (!ER_FORM_VALIDATION["' . $this->_fld->name . '_confirm"]) ER_FORM_VALIDATION["' . $this->_fld->name . '_confirm"] = {};
				ER_FORM_VALIDATION["' . $this->_fld->name . '_confirm"]["erConfirmfieldEqual"] = function() {
					var er_form = jQuery("form.er-form-validate");
					var val1 = jQuery("[name=' . $this->_fld->name . ']", er_form).val();
					var val2 = jQuery("[name=' . $this->_fld->name . '_confirm]", er_form).val();

					if (!erConfirmfieldEqual(val1, val2)) {
						jQuery.error("' . JText::sprintf('COM_EXTENDEDREG_FIELDS_CONFIRMFIELD_SHOULD_BE_EQUAL', JText::_($this->_fld->title), JText::_($confirm_title)) . '");
						return false;
					}
					
					return true;
				};
			');
		}
	}
	
	public function serversideValidation(&$post) {
		$app = JFactory::getApplication();
		$result = true;
		$errmsg = '';
		$post[$this->_fld->name] = trim($post[$this->_fld->name]);
		$confirm = trim($post[$this->_fld->name . '_confirm']);
		
		$filter = new JFilterInput();
		$post[$this->_fld->name] = $filter->clean($post[$this->_fld->name]);
		$confirm = $filter->clean($confirm);
		
		$defaultTitle = JText::sprintf('COM_EXTENDEDREG_FIELDS_CONFIRMFIELD_TITLE_DEFAULT', $this->_fld->title);
		$confirm_title = $this->_params->get('confirm_title', $defaultTitle);
		
		if ((int)$this->_fld->required && !mb_strlen($post[$this->_fld->name])) {
			$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
			$result = false;
		}
		
		$tocheck = true;
		if ($app->isSite() && !(int)$this->_fld->editable && ($view->user && (int)$view->user->id)) {
			$tocheck = false;
		} elseif ($app->isAdmin()) {
			$tocheck = false;
		}		
		if ($tocheck) {
			if ((int)$this->_fld->required && !mb_strlen($confirm)) {
				$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($confirm_title)) . "\n";
				$result = false;
			}
			if (trim($post[$this->_fld->name]) != trim($confirm)) {
				$errmsg .= JText::sprintf('COM_EXTENDEDREG_FIELDS_CONFIRMFIELD_SHOULD_BE_EQUAL', JText::_($this->_fld->title), JText::_($confirm_title));
				$result = false;
			}
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
		return '<input' . (trim($class) ? ' class="' . $class . '"' : '') . (trim($id) ? ' id="' . $id . '"' : '') . ' type="text" name="' . $this->_fld->name . '" value="' . $value . '" />';
	}
	
	public function getConfirmHtml($value, $id = null) {
		$conf = $this->_model->getConfObj();
		$class = $this->_params->get('input_size', trim($conf->css_default_input_class));
		if ((int)$this->_fld->required) $class .= ' required';
		if (!mb_strlen(trim($value))) $value = '';
		return '<input' . (trim($class) ? ' class="' . $class . '"' : '') . (trim($id) ? ' id="' . $id . '"' : '') . ' type="text" name="' . $this->_fld->name . '_confirm" value="' . $value . '" />';
	}
	
	public function hasSpecialObject() {
		return true;
	}
	
	public function getSpecialObject($defval, $options, &$view, $fieldIDAttr) {
		$app = JFactory::getApplication();
		$conf = $this->_model->getConfObj();
		$result = array();
		
		if ($this->hideTitle()) {
			$this->_fld->hidden_title = $this->_fld->title;
			$this->_fld->title = '';
		}
		
		if ($app->isSite() && !(int)$this->_fld->editable && ($view->user && (int)$view->user->id)) {
			$html = $this->getNoeditHtml($defval);
		} else {
			$html = $this->getHtml($defval, $fieldIDAttr);
		}

		$fieldObj = new stdClass;
		$fieldObj->html = $html;
		$fieldObj->required = $this->_fld->required;
		$fieldObj->editable = $this->_fld->editable;
		$fieldObj->name = $this->_fld->name;
		$fieldObj->title = $this->_fld->title;
		$fieldObj->type = $this->_fld->type;
		$fieldObj->tooltip = '';
		if (trim($this->_fld->description)) {
			$tooltip = htmlspecialchars(JText::_(trim($this->_fld->title)) . '::' . JText::_(trim($this->_fld->description)));
			$fieldObj->tooltip = ' <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_(trim($this->_fld->title))) . '" class="hasTip" title="' . $tooltip . '" border="0" />';
		}
		
		$result[] = $fieldObj;
		
		if ($app->isSite() && !(int)$this->_fld->editable && ($view->user && (int)$view->user->id)) {
			return $result;
		} elseif ($app->isAdmin()) {
			// Admins should not confirm fields
			return $result;
		}
		
		$defaultTitle = JText::sprintf('COM_EXTENDEDREG_FIELDS_CONFIRMFIELD_TITLE_DEFAULT', $this->_fld->title);
		$confirm_title = $this->_params->get('confirm_title', $defaultTitle);
		$confirm_description = $this->_params->get('confirm_description');
		
		$fieldObj2 = new stdClass;
		$fieldObj2->html = $this->getConfirmHtml($defval, $fieldIDAttr . '_confirm');
		$fieldObj2->required = $this->_fld->required;
		$fieldObj2->editable = 1;
		$fieldObj2->name = $this->_fld->name . '_confirm';
		$fieldObj2->title = $confirm_title;
		$fieldObj2->type = $this->_fld->type;
		$fieldObj2->tooltip = '';
		if (trim($confirm_description)) {
			$tooltip = htmlspecialchars(JText::_(trim($confirm_title)) . '::' . JText::_(trim($confirm_description)));
			$fieldObj->tooltip = ' <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_(trim($confirm_title))) . '" class="hasTip" title="' . $tooltip . '" border="0" />';
		}
		
		$result[] = $fieldObj2;
		
		return $result;
	}
}