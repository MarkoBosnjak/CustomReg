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

class ExtendedregViewRegister extends JViewLegacy {
	
	function display($tpl = null) {
		$app = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel();
		
		$conf = $model->getConfObj();
		$this->assignRef('conf', $conf);
		
		$fid = (int)JRequest::getVar('fid');
		$form = $model->loadForm($fid);
		$this->assignRef('form', $form);
		
		$formHTML = erHelperHTML::parseForm($form);
		$this->assignRef('formHTML', $formHTML);
		
		$params = $app->getParams();
		$this->assignRef('params', $params);
		
		erHelperHooks::loadHooks('html');
		
		$html = JvitalsHtml::getInstance('com_extendedreg');
		$html->setStateConstant('dashboard');
		//~ $html->setFunctionsClass('ExtendedregHtml');
		$this->assignRef('html', $html);
		
		parent::display($tpl);
	}
	
}