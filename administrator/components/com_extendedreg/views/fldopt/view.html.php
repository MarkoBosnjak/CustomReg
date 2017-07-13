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

if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewFldopt extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel();
		
		$field_id = (int)JRequest::getVar('field_id');
		$field = $model->loadField($field_id, JRequest::getVar('type'));
		$this->assignRef('field', $field);
		
		$fld_class = erHelperAddons::getFieldType($field);
		$this->assignRef('fld_class', $fld_class);
		
		$conf = $model->getConfObj();
		$this->assignRef('conf', $conf);
		
		$opt_id = (int)JRequest::getVar('id');
		
		if ($opt_id) {
			$fopts = $model->getFieldOpts($field_id, $opt_id);
		} else {
			$fopts = $fld_class->getOptions();
		}
		$this->assignRef('fopts', $fopts);
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
	
}
