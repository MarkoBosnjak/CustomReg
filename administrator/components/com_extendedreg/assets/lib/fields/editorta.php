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

class erFieldEditorta extends erField implements erFieldInterface {

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
	
	private function loadNicEdit() {
		static $loaded;
		if (!$loaded) {
			$document = JFactory::getDocument();
			$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/lib/nicedit/nicEdit.js');
			$loaded = true;
		}
		return true;
	}
	
	public function getHtml($value, $id = null) {
		if (!mb_strlen(trim($value))) $value = '';
		$this->getJavascptValidation();
		
		$editor_type = (int)$this->_params->get('editor_type', 0);
		
		if (!(int)$editor_type) {
			$editor = JFactory::getEditor();
			return $editor->display($this->_fld->name, $value, '95%', '200', '40', '15', false, array());
		} else {
			$return = '<textarea name="' . $this->_fld->name . '" style="width: 95%;height: 200px;" id="' . $this->_fld->name . '">' . $value . '</textarea>';
			if ((int)$editor_type == 2) {
				$this->loadNicEdit();
				$document = JFactory::getDocument();
				$document->addScriptDeclaration("
					bkLib.onDomLoaded(function() {
						var myEditor = new nicEditor({iconsPath : '" . JvitalsDefines::comBackPath('com_extendedreg', true) . "assets/lib/nicedit/nicEditorIcons.gif', buttonList : ['bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','link','unlink','forecolor','bgcolor','xhtml']}).panelInstance('" . $this->_fld->name . "');
						myEditor.addEvent('blur', function() {
							jQuery('#" . $this->_fld->name . "').val(nicEditors.findEditor('" . $this->_fld->name . "').getContent());
						});
					});
				");
			}
		}
	}
}