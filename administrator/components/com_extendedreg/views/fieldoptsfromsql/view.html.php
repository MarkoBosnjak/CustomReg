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

class ExtendedregViewFieldoptsfromsql extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel();
		
		$conf = $model->getConfObj();
		if (!(int)$conf->use_opts_sql) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$this->assignRef('conf', $conf);
		
		$field_id = (int)JRequest::getVar('field_id');
		$field = $model->loadField($field_id);
		$this->assignRef('field', $field);
		
		$fld_class = erHelperAddons::getFieldType($field);
		$this->assignRef('fld_class', $fld_class);
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
	
}
