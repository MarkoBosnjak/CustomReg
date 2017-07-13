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

class ExtendedregController extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();

		$this->registerTask('new', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('fld_new', 'fld_edit');
		$this->registerTask('fld_apply', 'fld_save');
		$this->registerTask('fld_savenew', 'fld_save');
		$this->registerTask('fld_unpublish', 'fld_publish');
		$this->registerTask('fld_notrequired', 'fld_required');
		$this->registerTask('fld_noteditable', 'fld_editable');
		$this->registerTask('fldgrp_new', 'fldgrp_edit');
		$this->registerTask('fldgrp_apply', 'fldgrp_save');
		$this->registerTask('fldgrp_savenew', 'fldgrp_save');
		$this->registerTask('fldopt_formnew', 'fldopt_form');
	}
	
	function display($cachable = false, $urlparams = false) {
		$view = $this->getView('default', 'html', '');
		$view->setLayout('default');
		$view->display();
	}
	
	function fields() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$view = $this->getView('fields', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function fld_edit() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}		
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$view = $this->getView('fldedit', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function fld_save() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model->saveCustomField();
	}
	
	function fld_publish() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('fld_publish' => 1, 'fld_unpublish' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->setFldPublished($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_PUBLISHED'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_UNPUBLISHED'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
	}
	
	function fld_required() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('fld_required' => 1, 'fld_notrequired' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->setFldRequired($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_REQUIRED'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_NOTREQUIRED'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
	}
	
	function fld_editable() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('fld_editable' => 1, 'fld_noteditable' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->setFldEditable($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_EDITABLE'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FIELDS_NOTEDITABLE'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
	}
	
	function fld_params() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			echo '<div class="field_no_params">'.JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR').'</div>';
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$field = $model->loadField((int)JRequest::getVar('id'), trim(JRequest::getVar('type')));
		$fld_class = erHelperAddons::getFieldType($field);
		$found = false;
		if ($fld_class->hasParams()) {
			$result = $fld_class->renderParams();
			if ($result) {
				echo $result;
				$found = true;
			}
		}
		if (!$found) {
			echo '<div class="field_no_params">'.JText::_('COM_EXTENDEDREG_FIELDS_NO_PARAMS').'</div>';
		}
		erHelperJavascript::AddTooltipsAgain();
		jexit();
	}
	
	function fldopt() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			echo '<p align="center">'.JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR').'</p>';
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$view = $this->getView('fldopt', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
		erHelperJavascript::AddTooltipsAgain();
		jexit();
	}
	
	function fldopt_form() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			echo '<p align="center">'.JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR').'</p>';
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$view = $this->getView('fldopt', 'html', '');
		$view->setLayout('form');
		$view->setModel($model, true);
		$view->display();
		erHelperJavascript::AddTooltipsAgain();
		jexit();
	}
	
	function fldopt_save() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$model->saveFieldOption();
		jexit();
	}
	
	function fldopt_delete() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$model->deleteFieldOption();
		jexit();
	}
	
	function fldopt_fromtxt() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$model->fieldOptionsFromTxt();
	}
	
	function fldgrp_edit() {
		if (!JvitalsHelper::canDo('fields.groups', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$view = $this->getView('fldgrp', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function fldgrp_save() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('fields.groups', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model->saveFieldGroup();
	}
	
	function browse() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$view = $this->getView('forms', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function edit() {
		if (!JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		$layout = 'default';
		//~ if (!(int)$conf->use_formbuilder) {
			//~ $layout = 'simple';
		//~ }
		$view = $this->getView('formedit', 'html', '');
		$view->setLayout($layout);
		$view->setModel($model, true);
		$view->display();
	}
	
	function save() {
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id', 0);
		$task = $app->input->getCmd('task');
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		
		if (!JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
			$app->enqueueMessage(JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'), 'error');
		} else {
			if (!$model->saveForm()) {
				$this->setMessage($model->getError(), 'error');
				$url = 'index.php?option=com_extendedreg&task=forms.' . ($id ? 'edit&cid=' . $id : 'new');
			} else {
				$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FORMS_SAVED'));
				if ($task == 'apply') {
					$url = 'index.php?option=com_extendedreg&task=forms.edit&cid=' . $id;
				} elseif ($task == 'savenew') {
					$url = 'index.php?option=com_extendedreg&task=forms.new';
				} else {
					$url = 'index.php?option=com_extendedreg&task=forms.browse';
				}
			}
			
			$this->setRedirect(JRoute::_($url, false));
		}
	}
	
	function set_default() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model->setDefaultForm();
	}
	
	function publish() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('publish' => 1, 'unpublish' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->setFormPublished($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FORMS_PUBLISHED'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_FORMS_UNPUBLISHED'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.browse', false));
	}
	
	function delete() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('forms.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model->deleteForm();
	}
	
	function fld_delete() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model->deleteCustomField();
	}
	
	function fldgrp_delete() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		if (!JvitalsHelper::canDo('fields.groups', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model->deleteFieldGroup();
	}
	
	function fld_saveorder() {
		// Get the arrays from the Request
		$order = JRequest::getVar('order', null, 'post', 'array');
		$originalOrder = explode(',', JRequest::getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder)) {
			// Get the input
			$pks = JRequest::getVar('cid', null, 'post', 'array');
			
			if (empty($pks)) {
				$this->setMessage(JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
			} else {
				// Sanitize the input
				JArrayHelper::toInteger($pks);
				JArrayHelper::toInteger($order);
				
				$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
				// Change the state of the records.
				if (!$model->fld_saveorder($pks, $order)) {
					JError::raiseWarning(83009, $model->getError());
				} else {
					$this->setMessage(JText::_('COM_EXTENDEDREG_FIELDS_ORDER_SAVED'));
				}
			}
		} else {
			$this->setMessage(JText::_('COM_EXTENDEDREG_FIELDS_ORDER_NOTHING_TODO'));
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
	}
	
	function fld_orderup() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		if (!$model->fld_reorder(-1)) {
			JError::raiseWarning(83009, $model->getError());
		} else {
			$this->setMessage(JText::_('COM_EXTENDEDREG_FIELDS_ORDER_SAVED'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
	}
	
	function fld_orderdown() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		if (!$model->fld_reorder(1)) {
			JError::raiseWarning(83009, $model->getError());
		} else {
			$this->setMessage(JText::_('COM_EXTENDEDREG_FIELDS_ORDER_SAVED'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=forms.fields', false));
	}
	
	function fieldoptsfromsql() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			echo '<p align="center">'.JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR').'</p>';
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		if (!(int)$conf->use_opts_sql) {
			echo '<p align="center">'.JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR').'</p>';
			jexit();
		}
		
		$view = $this->getView('fieldoptsfromsql', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
		jexit();
	}
	
	function save_custom_sql() {
		if (!JvitalsHelper::canDo('fields.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$model->saveFieldCustomSQL();
	}
}
