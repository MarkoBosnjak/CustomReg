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
	
	function display($cachable = false, $urlparams = false) {
		$this->login_and_register();
	}

	function login_and_register() {
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app = JFactory::getApplication();
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		if (!(int)$conf->allow_user_login && !(int)$conf->allow_user_registration) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$view = $this->getView('default', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
		
		$ajax = (int)JRequest::getVar('ajax', 0);
		if ($ajax) {
			jexit();
		}
	}

	function register() {
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app = JFactory::getApplication();
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		if (!(int)$conf->allow_user_registration) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$view = $this->getView('register', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
		
		$ajax = (int)JRequest::getVar('ajax', 0);
		if ($ajax) {
			jexit();
		}
	}
	
	function login() {
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app = JFactory::getApplication();
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		if (!(int)$conf->allow_user_login) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$view = $this->getView('login', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
		
		$ajax = (int)JRequest::getVar('ajax', 0);
		if ($ajax) {
			jexit();
		}
	}
	
	function remind() {
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app = JFactory::getApplication();
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('remind', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function reset() {
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app = JFactory::getApplication();
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
			
		$layout = JRequest::getVar('layout', 'default');
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$view = $this->getView('reset', 'html', '');
		$view->setLayout($layout);
		$view->setModel($model, true);
		$view->display();
	}
	
	function request_activation_mail() {
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app = JFactory::getApplication();
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		if (!((int)$conf->enable_user_activation &&(int)$conf->enable_request_activation_mail)) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$view = $this->getView('request_activation_mail', 'html', '');
		$view->setLayout('default');
		$view->setModel($model, true);
		$view->display();
	}
	
	function do_login() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		if (!(int)$conf->allow_user_login) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'), 'error');
			jexit();
		}

		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $return;

		$credentials = array();
		$username = JRequest::getVar('username', '', 'method', 'username');
		if ((int)$conf->email_for_username) {
			$username = $model->getUsernameByEmail($username);
		}
		$credentials['username'] = $username;
		$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
		$credentials['secretkey'] = JRequest::getString('secretkey', '');
		
		if (!(int)$conf->enable_admin_approval) {
			$testForApprove = $model->testForApprove($credentials);
			if ((int)$testForApprove == 0) {
				$app->redirect(erHelperRouter::getUrl($conf->redir_url_wrong_password, $conf->redir_url_wrong_password_other, $conf->redir_url_default), JText::_('COM_EXTENDEDREG_USER_NOT_APPROVED_ERROR'), 'error');
				jexit();
			}
		}
		
		$proxy = (int)JvitalsHelper::checkForProxy();
		if ((int)$conf->forbid_proxies && $proxy) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_wrong_password, $conf->redir_url_wrong_password_other, $conf->redir_url_default), JText::_('COM_EXTENDEDREG_USING_PROXY_FORBIDDEN_ERROR'), 'error');
			jexit();
		}
		
		//perform the login action
		$error = $app->login($credentials, $options);
		
		if ((boolean)$error === false) {
			// log the failed login attempt
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_wrong_password, $conf->redir_url_wrong_password_other, $conf->redir_url_default));
			jexit();
		}
		
		$user = JFactory::getUser();
		
		$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		$model->setLastKnownIP((int)$user->id, $ip_addr);
		$model->addStats($ip_addr, (int)$user->id, 'login', $proxy, ($_SERVER['REMOTE_PORT'] ? $_SERVER['REMOTE_PORT'] : ''));
		
		$lret = JRequest::getVar('lret', '', 'method', 'base64');
		if (trim($lret)) {
			$app->redirect(base64_decode($lret));
			jexit();
		}
		$app->redirect(erHelperRouter::getUrl($conf->redir_url_login, $conf->redir_url_login_other, $conf->redir_url_default));
		jexit();
	}
	
	function do_remind() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		// Get the input
		$email = JRequest::getVar('email', null, 'post', 'string');
		
		if (!$model->remindUsername($email)) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.remind' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.remind') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.remind') : ''), false), JText::_('COM_EXTENDEDREG_REMIND_WRONG_EMAIL_ERROR'), 'error');
			jexit();
		}
		
		$app->redirect(erHelperRouter::getUrl($conf->redir_url_forgot_username, $conf->redir_url_forgot_username_other, $conf->redir_url_default), JText::_('COM_EXTENDEDREG_REMIND_SUCCESS'));
		jexit();
	}
	
	function do_reset() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		// Get the input
		$email = JRequest::getVar('email', null, 'post', 'string');

		if (!$model->resetPassword($email)) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.reset' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset') : ''), false), JText::_('COM_EXTENDEDREG_RESET_WRONG_EMAIL_ERROR'), 'error');
			jexit();
		}
		
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.reset&layout=confirm' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=confirm') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=confirm') : ''), false), JText::_('COM_EXTENDEDREG_RESET_EMAIL_SEND'));
		jexit();
	}
	
	function confirm_reset() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		// Get the input
		$token = JRequest::getVar('token', null, 'post', 'alnum');
		$username = JRequest::getVar('username', null, 'post');
		if ((int)$conf->email_for_username) {
			$username = $model->getUsernameByEmail($username);
		}
		
		if (!$model->confirmReset($token, $username)) {
			$message = JText::sprintf('COM_EXTENDEDREG_RESET_CONFIRMATION_FAILED', $model->getError());
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.reset&layout=confirm' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=confirm') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=confirm') : ''), false), $message, 'error');
			jexit();
		}
		
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.reset&layout=complete' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=complete') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=complete') : ''), false), JText::_('COM_EXTENDEDREG_RESET_CHANGE_PASSWORD'));
		jexit();
	}
	
	function complete_reset() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}

		// Get the input
		$password1 = JRequest::getVar('password', null, 'post', 'string', JREQUEST_ALLOWRAW);
		$password2 = JRequest::getVar('verify-password', null, 'post', 'string', JREQUEST_ALLOWRAW);
		
		if (!$model->completeReset($password1, $password2)) {
			$message = JText::sprintf('COM_EXTENDEDREG_PASSWORD_RESET_FAILED', $model->getError());
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.reset&layout=complete' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=complete') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=complete') : ''), false), $message, 'error');
			jexit();
		}
		
		$app->redirect(erHelperRouter::getUrl($conf->redir_url_forgot_password, $conf->redir_url_forgot_password_other, $conf->redir_url_default), JText::_('COM_EXTENDEDREG_PASSWORD_RESET_SUCCESS'));
		jexit();
	}
	
	function logout() {
		$app = JFactory::getApplication();
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$user = JFactory::getUser();
		$user_id = (int)$user->id;
		
		//preform the logout action
		$error = $app->logout();

		if (JError::isError($error)) {
			if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
				$return = base64_decode($return);
				if (!JURI::isInternal($return)) {
					$return = '';
				}
			}
			$app->redirect($return);
			jexit();
		}
		
		$proxy = (int)JvitalsHelper::checkForProxy();
		$model->addStats($ip_addr, (int)$user_id, 'logout', $proxy, ($_SERVER['REMOTE_PORT'] ? $_SERVER['REMOTE_PORT'] : ''));
		
		$app->redirect(erHelperRouter::getUrl($conf->redir_url_logout, $conf->redir_url_logout_other, $conf->redir_url_default));
		jexit();
	}
	
	function do_register() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		$model->registerUser();
	}
	
	function profile() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if (!(int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$layout = $app->input->getCmd('layout', 'default');
		$view = $this->getView('profile', 'html', '');
		$view->setLayout($layout);
		$view->setModel($model, true);
		$view->display();
	}
	
	function save_tfasetup() {
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$model->saveTfasetup();
	}
	
	function do_save() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		$model->saveUser();
	}
	
	function activate() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		if (!((int)$conf->allow_user_registration && (int)$conf->enable_user_activation)) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Do we even have an activation string?
		$activation = JRequest::getVar('activation', '', '', 'alnum');
		if (empty($activation)) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), JText::_('COM_EXTENDEDREG_ACTIVATE_NOT_FOUND'));
			jexit();
		}
		
		$user = $model->loadUserByActivation($activation);
		if (!(int)$user->id) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), JText::_('COM_EXTENDEDREG_ACTIVATE_NOT_FOUND'));
			jexit();
		}
		
		$useridArr = array((int)$user->id);
		if (!$model->activate($useridArr)) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), $model->getError());
			jexit();
		}
		
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$form = $formsModel->loadForm((int)$user->form_id);
		
		$userEmailSent = false;
		if ((int)$conf->enable_admin_approval) {
			$userEmailSent = erHelperMail::sendUserNeedApprovalMail($user, (int)$form->id);
			$returnUrl = erHelperRouter::getUrl($conf->redir_url_activ_need_approval, $conf->redir_url_activ_need_approval_other, $conf->redir_url_default);
			$warningMessage = JText::_('COM_EXTENDEDREG_ACTIVATE_WAITING_APPROVE_WARNING');
			$successMessage = JText::_('COM_EXTENDEDREG_ACTIVATE_WAITING_APPROVE');
		} else {
			$userEmailSent = erHelperMail::sendUserRegistrationInfoMail($user, (int)$form->id);
			$returnUrl = erHelperRouter::getUrl($conf->redir_url_activation, $conf->redir_url_activation_other, $conf->redir_url_default);
			$warningMessage = JText::_('COM_EXTENDEDREG_ACTIVATE_COMPLETE_WARNING');
			$successMessage = JText::_('COM_EXTENDEDREG_ACTIVATE_COMPLETE');
		}
		
		if (!$userEmailSent) {
			$app->redirect(JRoute::_($returnUrl, false), $warningMessage, 'warning');
			jexit();
		}
		
		if ((int)$conf->enable_admin_approval) {
			erHelperMail::sendAdminNeedApprovalMail($user, (int)$form->id);
		} else {
			erHelperMail::sendAdminRegistrationInfoMail($user, (int)$form->id);
		}
		
		// Load plugins from jvPlugins
		$jvPlugins = JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'jvPlugins';
		if (is_dir($jvPlugins)) {
			JPluginHelper::importPlugin('jvPlugins');
		}
		
		// Trigger event for pluigns
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onExtregUserActivate', array((int)$form->id, $user));
		
		$app->redirect(JRoute::_($returnUrl, false), $successMessage);
		jexit();
	}
	
	function approve() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		if (!((int)$conf->allow_user_registration && (int)$conf->enable_admin_approval)) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		// Do we even have an activation string?
		$activation = JRequest::getVar('activation', '', '', 'alnum');
		if (empty($activation)) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), JText::_('COM_EXTENDEDREG_ACTIVATE_NOT_FOUND'));
			jexit();
		}
		
		$user = $model->loadUserByApproveHash($activation, (int)JRequest::getVar('user_id'));
		if (!(int)$user->id) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), JText::_('COM_EXTENDEDREG_ACTIVATE_NOT_FOUND'));
			jexit();
		}
		
		if (!$model->set_approve_front((int)$user->id)) {
			$app->redirect(erHelperRouter::getUrl($conf->redir_url_default), $model->getError());
			jexit();
		}
		
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$form = $formsModel->loadForm((int)$user->form_id);
		
		// Load plugins from jvPlugins
		$jvPlugins = JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'jvPlugins';
		if (is_dir($jvPlugins)) {
			JPluginHelper::importPlugin('jvPlugins');
		}
		
		// Trigger event for pluigns
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onExtregUserApprove', array((int)$form->id, $user));
		
		$returnUrl = erHelperRouter::getUrl($conf->redir_url_default);
		$app->redirect(JRoute::_($returnUrl, false),  JText::sprintf('COM_EXTENDEDREG_APPROVE_COMPLETE', $user->username));
		jexit();
	}
	
	function do_request_activation() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if ((int)$user->id) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ALREADY_LOGGED_IN'));
			jexit();
		}

		if (!((int)$conf->enable_user_activation &&(int)$conf->enable_request_activation_mail)) {
			JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		
		$email = JRequest::getVar('email', null, 'post', 'string');

		if (!$model->requestActivationEmail($email)) {
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.request_activation_mail' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.request_activation_mail') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.request_activation_mail') : ''), false), JText::_('COM_EXTENDEDREG_REQUEST_ACTIVATION_FAIL'), 'error');
			jexit();
		}
		
		$app->redirect(erHelperRouter::getUrl($conf->redir_url_request_activation, $conf->redir_url_request_activation_other, $conf->redir_url_default), JText::_('COM_EXTENDEDREG_REQUEST_ACTIVATION_SUCCESS'));
		jexit();
	}
	
	function terminate() {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		
		$iAmSuperAdmin = $user->authorise('core.admin');
		
		if (!(int)$user->id || !(int)$conf->allow_terminate) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		if ($iAmSuperAdmin) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_SUPERADMIN_WARN'));
			jexit();
		}
		
		$layout = JRequest::getVar('layout', 'default');
		$view = $this->getView('terminate', 'html', '');
		$view->setLayout($layout);
		$view->setModel($model, true);
		$view->display();
	}
	
	function send_terminate() {
		// Get the model.
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$iAmSuperAdmin = $user->authorise('core.admin');
		
		if (!(int)$user->id || !(int)$conf->allow_terminate) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
			jexit();
		}
		if ($iAmSuperAdmin) {
			$app->redirect(JURI::base(true) . '/', JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_SUPERADMIN_WARN'));
			jexit();
		}
		
		// Get the input
		$password1 = JRequest::getVar('password', null, 'post', 'string', JREQUEST_ALLOWRAW);
		$password2 = JRequest::getVar('verify-password', null, 'post', 'string', JREQUEST_ALLOWRAW);
		
		if (!$model->sendTerminate($password1, $password2)) {
			$message = $model->getError();
			$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.terminate' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.terminate') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.terminate') : ''), false), $message, 'error');
			jexit();
		}
		
		$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.terminate&layout=sent' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.terminate&layout=sent') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.terminate&layout=sent') : ''), false));
		jexit();
	}
	
	function do_terminate() {
		$user = JFactory::getUser();
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		$hash = JRequest::getVar('hash', '', '', 'alnum');

		$iAmSuperAdmin = $user->authorise('core.admin');
		
		$message = JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_SUCCESS');
		if (!(int)$user->id || !(int)$conf->allow_terminate) {
			$message = JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR');
		} elseif ($iAmSuperAdmin) {
			$message = JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_SUPERADMIN_WARN');
		} elseif (empty($hash)) {
			$message = JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_EMPTYHASH');
		} elseif (!$model->terminateAccount($hash)) {
			$message = $model->getError();
		}
		
		$app = JFactory::getApplication();
		$app->redirect(JURI::base(true) . '/', $message);
		jexit();
	}
	
}
