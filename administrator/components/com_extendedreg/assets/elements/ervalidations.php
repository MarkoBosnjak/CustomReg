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

/**
 * Renders a ervalidations element
 */
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldErvalidations extends JFormField {
	var $type = 'Ervalidations';

	protected function getInput() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$fld_types = erHelperAddons::loadAddons('field');
		$field = null;
		$html = '';
		
		$fld_id = (int)JRequest::getVar('id');

		if (!$fld_id) {
			$field = $model->loadField($fld_id, $fld_types[0]->file_name);
		} else {
			$field = $model->loadField($fld_id);
		}
		
		$validations = erHelperAddons::loadAddons('validation');
		if ($validations) {
			$html = '<table>';
			foreach ($validations as $lib) {
				$obj = erHelperAddons::getFieldValidation($lib, $field);
				$html .= '<tr><td>' . $obj->getElements() . '</td></tr>';
			}
			$html .= '</table>';
		}
		
		return $html;
	}
}


