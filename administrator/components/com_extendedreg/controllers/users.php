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
		
		require_once (JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_users' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'users.php');
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$model->syncUsers();
		
		$this->registerTask('unblock', 'block');
		$this->registerTask('unapprove', 'approve');
		$this->registerTask('decline_terms', 'accept_terms');
		$this->registerTask('unset_overage', 'set_overage');
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('trash', 'delete');
	}
	
	function display($cachable = false, $urlparams = false) {
		$view = $this->getView('default', 'html', '');
		$view->setLayout('default');
		$view->display();
	}
	
	function manage() {
		$session = JFactory::getSession();
		$session->set('erAdminLoadedUser', 0, 'extendedreg');
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$view = $this->getView('manage', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function block() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('block' => 1, 'unblock' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->set_block($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_BLOCKED'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_UNBLOCKED'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=users.manage', false));
	}
	
	function activate() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');

		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->activate($ids)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_ACTIVATED'));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=users.manage', false));
	}
	
	function approve() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('approve' => 1, 'unapprove' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->set_approve($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_APPROVED'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_UNAPPROVED'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=users.manage', false));
	}
	
	function accept_terms() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('accept_terms' => 1, 'decline_terms' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Change the state of the records.
			if (!$model->set_terms($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_ACCEPTEDTERMS'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_NOT_ACCEPTEDTERMS'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=users.manage', false));
	}
	
	function set_overage() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array('set_overage' => 1, 'unset_overage' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = JvitalsHelper::loadModel('extendedreg', 'Users');
			// Change the state of the records.
			if (!$model->set_overage($ids, $value)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				if ($value == 1) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_OVERAGE'));
				} else if ($value == 0) {
					$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_NOT_OVERAGE'));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=users.manage', false));
	}

	function delete() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$ids = JRequest::getVar('cid', array(), '', 'array');
		
		if (empty($ids)) {
			JError::raiseWarning(83002, JText::_('COM_EXTENDEDREG_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = JvitalsHelper::loadModel('extendedreg', 'Users');
			// Change the state of the records.
			if (!$model->delete($ids)) {
				JError::raiseWarning(83003, $model->getError());
			} else {
				$this->setMessage(JText::_('COM_EXTENDEDREG_MSG_USER_DELETED'));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_extendedreg&task=users.manage', false));
	}
	
	function set_form() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		if ($model->setUserForm()) {
			echo 'true';
		} else {
			echo 'false';
		}
		jexit();
	}
	
	function export() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$view = $this->getView('export', 'raw', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function save() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$session = JFactory::getSession();
		$session->set('erAdminLoadedUser', 0, 'extendedreg');

		$model->saveUser();
	}
	
	function edit() {
		if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$cid = JRequest::getVar('cid');
		if (is_array($cid)) $cid = $cid[0];
		$cid = (int)$cid;
		
		$session = JFactory::getSession();
		$session->set('erAdminLoadedUser', $cid, 'extendedreg');
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$view = $this->getView('user', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
}
