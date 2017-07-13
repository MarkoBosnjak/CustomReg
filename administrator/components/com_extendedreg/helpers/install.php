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
defined('ER_DO_INSTALL') or die('Restricted access');

class extendedregInstall {
	var $dbo;
	var $logList;
	var $options;
	var $del_options;
	var $columns;
	
	function __construct() {
		$this->dbo = JFactory::getDBO();
		$this->dbo->setDebug(0);
		$this->logList = array();
		
		$this->options = array(
			// option name, option value, order, group, description, override
			// default group
			array('er_version', ER_VERSION, '0', 'default', '', 1),
			array('allow_user_registration', '1', '1', 'default', 'COM_EXTENDEDREG_HELP_ALLOW_USER_REGISTRATION', 0),
			array('allow_user_login', '1', '2', 'default', 'COM_EXTENDEDREG_HELP_ALLOW_USER_LOGIN', 0),
			array('enable_user_activation', '1', '3', 'default', 'COM_EXTENDEDREG_HELP_ENABLE_USER_ACTIVATION', 0),
			array('enable_request_activation_mail', '1', '4', 'default', 'COM_EXTENDEDREG_HELP_ENABLE_REQUEST_ACTIVATION_MAIL', 0),
			array('request_activation_timeout', '24', '5', 'default', 'COM_EXTENDEDREG_HELP_REQUEST_ACTIVATION_TIMEOUT', 0),
			array('enable_admin_approval', '0', '6', 'default', 'COM_EXTENDEDREG_HELP_ENABLE_ADMIN_APPROVAL', 0),
			array('email_for_username', '0', '7', 'default', 'COM_EXTENDEDREG_HELP_EMAIL_FOR_USERNAME', 0),
			array('allow_uname_change', '0', '8', 'default', 'COM_EXTENDEDREG_HELP_ALLOW_UNAME_CHANGE', 0),
			array('allow_terminate', '0', '9', 'default', 'COM_EXTENDEDREG_HELP_ALLOW_TERMINATE', 0),
			array('terminate_type', '2', '10', 'default', 'COM_EXTENDEDREG_HELP_TERMINATE_TYPE', 0),
			array('use_editor', '0', '11', 'default', '', 0),
			array('use_opts_sql', '0', '12', 'default', 'COM_EXTENDEDREG_HELP_USE_OPTS_SQL', 0),
			array('generate_random_uname', '0', '13', 'default', 'COM_EXTENDEDREG_HELP_GENERATE_RANDOM_UNAME', 0),
			array('generate_random_pass', '0', '14', 'default', 'COM_EXTENDEDREG_HELP_GENERATE_RANDOM_PASS', 0),
			array('show_generate_pass', '0', '15', 'default', 'COM_EXTENDEDREG_HELP_SHOW_GENERATE_PASS', 0),
			array('redirect_login_screens', '0', '16', 'default', 'COM_EXTENDEDREG_HELP_REDIRECT_LOGIN_SCREENS', 0),
			array('liveupdate_license', '', '17', 'default', 'COM_EXTENDEDREG_HELP_LIVEUPDATE_LICENSE', 0),
			array('liveupdate_email', '', '18', 'default', 'COM_EXTENDEDREG_HELP_LIVEUPDATE_EMAIL', 0),
			array('disable_ip_track', '0', '19', 'default', 'COM_EXTENDEDREG_HELP_DISABLE_IP_TRACK', 0),
			array('remove_tables_on_uninstall', '0', '20', 'default', 'COM_EXTENDEDREG_HELP_REMOVE_TABLES_ON_UNINSTALL', 0),
			array('replace_name_with', '', '21', 'default', 'COM_EXTENDEDREG_HELP_REPLACE_NAME_WITH', 0),
			array('replace_username_with', '', '22', 'default', 'COM_EXTENDEDREG_HELP_REPLACE_USERNAME_WITH', 0),
			array('replace_email_with', '', '23', 'default', 'COM_EXTENDEDREG_HELP_REPLACE_EMAIL_WITH', 0),
			array('replace_pass_with', '', '24', 'default', 'COM_EXTENDEDREG_HELP_REPLACE_PASS_WITH', 0),
			array('replace_confirmpass_with', '', '25', 'default', 'COM_EXTENDEDREG_HELP_REPLACE_CONFIRMPASS_WITH', 0),
			
			// option name, option value, order, group, description, override
			// emails group
			array('admin_mails', '', '1', 'emails', 'COM_EXTENDEDREG_HELP_ADMIN_MAILS', 0),
			array('default_mailfrom', '', '2', 'emails', 'COM_EXTENDEDREG_HELP_DEFAULT_MAILFROM', 0),
			array('approve_email_user_subj', JText::_('COM_EXTENDEDREG_ACCOUNT_DETAILS_FOR'), '3', 'emails', 'COM_EXTENDEDREG_HELP_APPROVE_EMAIL_USER_SUBJ', 0),
			array('approve_email_user', JText::_('COM_EXTENDEDREG_MAILS_APPROVE_EMAIL_USER'), '4', 'emails', 'COM_EXTENDEDREG_HELP_APPROVE_EMAIL_USER', 0),
			array('approve_email_admin_subj', JText::_('COM_EXTENDEDREG_ACCOUNT_DETAILS_FOR'), '5', 'emails', 'COM_EXTENDEDREG_HELP_APPROVE_EMAIL_ADMIN_SUBJ', 0),
			array('approve_email_admin', JText::_('COM_EXTENDEDREG_MAILS_APPROVE_EMAIL_ADMIN'), '6', 'emails', 'COM_EXTENDEDREG_HELP_APPROVE_EMAIL_ADMIN', 0),
			array('approve_email_user_done_subj', JText::_('COM_EXTENDEDREG_ACCOUNT_DETAILS_FOR'), '7', 'emails', 'COM_EXTENDEDREG_HELP_APPROVE_EMAIL_USER_DONE_SUBJ', 0),
			array('approve_email_user_done', JText::_('COM_EXTENDEDREG_MAILS_APPROVE_EMAIL_USER_DONE'), '8', 'emails', 'COM_EXTENDEDREG_HELP_APPROVE_EMAIL_USER_DONE', 0),
			array('reginfo_email_user_subj', JText::_('COM_EXTENDEDREG_ACCOUNT_DETAILS_FOR'), '9', 'emails', 'COM_EXTENDEDREG_HELP_REGINFO_EMAIL_USER_SUBJ', 0),
			array('reginfo_email_user', JText::_('COM_EXTENDEDREG_MAILS_REGINFO_EMAIL_USER'), '10', 'emails', 'COM_EXTENDEDREG_HELP_REGINFO_EMAIL_USER', 0),
			array('reginfo_email_admin_subj', JText::_('COM_EXTENDEDREG_ACCOUNT_DETAILS_FOR'), '11', 'emails', 'COM_EXTENDEDREG_HELP_REGINFO_EMAIL_ADMIN_SUBJ', 0),
			array('reginfo_email_admin', JText::_('COM_EXTENDEDREG_MAILS_REGINFO_EMAIL_ADMIN'), '12', 'emails', 'COM_EXTENDEDREG_HELP_REGINFO_EMAIL_ADMIN', 0),
			array('activation_email_user_subj', JText::_('COM_EXTENDEDREG_ACCOUNT_DETAILS_FOR'), '13', 'emails', 'COM_EXTENDEDREG_HELP_ACTIVATION_EMAIL_USER_SUBJ', 0),
			array('activation_email_user', JText::_('COM_EXTENDEDREG_MAILS_ACTIVATION_EMAIL_USER'), '14', 'emails', 'COM_EXTENDEDREG_HELP_ACTIVATION_EMAIL_USER', 0),
			array('remind_email_user_subj', JText::_('COM_EXTENDEDREG_MAILS_USERNAME_REMINDER_EMAIL_TITLE'), '15', 'emails', 'COM_EXTENDEDREG_HELP_REMIND_EMAIL_USER_SUBJ', 0),
			array('remind_email_user', JText::_('COM_EXTENDEDREG_MAILS_USERNAME_REMINDER_EMAIL_TEXT'), '16', 'emails', 'COM_EXTENDEDREG_HELP_REMIND_EMAIL_USER', 0),
			array('reset_email_user_subj', JText::_('COM_EXTENDEDREG_MAILS_PASSWORD_RESET_CONFIRMATION_EMAIL_TITLE'), '17', 'emails', 'COM_EXTENDEDREG_HELP_RESET_EMAIL_USER_SUBJ', 0),
			array('reset_email_user', JText::_('COM_EXTENDEDREG_MAILS_PASSWORD_RESET_CONFIRMATION_EMAIL_TEXT'), '18', 'emails', 'COM_EXTENDEDREG_HELP_RESET_EMAIL_USER', 0),
			array('new_fromadmin_user_subj', JText::_('COM_EXTENDEDREG_MAILS_NEWUSER_FROMADMIN_TITLE'), '19', 'emails', 'COM_EXTENDEDREG_HELP_NEW_FROMADMIN_USER_SUBJ', 0),
			array('new_fromadmin_user', JText::_('COM_EXTENDEDREG_MAILS_NEWUSER_FROMADMIN_TEXT'), '20', 'emails', 'COM_EXTENDEDREG_HELP_NEW_FROMADMIN_USER_SUBJ', 0),
			array('delete_account_user_subj', JText::_('COM_EXTENDEDREG_MAILS_DELETE_ACCOUNT_TITLE'), '21', 'emails', 'COM_EXTENDEDREG_HELP_DELETE_ACCOUNT_USER_SUBJ', 0),
			array('delete_account_user', JText::_('COM_EXTENDEDREG_MAILS_DELETE_ACCOUNT_TEXT'), '22', 'emails', 'COM_EXTENDEDREG_HELP_DELETE_ACCOUNT_USER', 0),
			array('delete_account_admin_subj', JText::_('COM_EXTENDEDREG_MAILS_DELETE_ACCOUNT_ADMIN_TITLE'), '23', 'emails', 'COM_EXTENDEDREG_HELP_DELETE_ACCOUNT_ADMIN_SUBJ', 0),
			array('delete_account_admin', JText::_('COM_EXTENDEDREG_MAILS_DELETE_ACCOUNT_ADMIN_TEXT'), '24', 'emails', 'COM_EXTENDEDREG_HELP_DELETE_ACCOUNT_ADMIN', 0),
			array('max_login_blocked_subj', JText::_('COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_TITLE'), '25', 'emails', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCKED_SUBJ', 0),
			array('max_login_blocked', JText::_('COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_TEXT'), '26', 'emails', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCKED', 0),
			array('max_login_blocked_admin_subj', JText::_('COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_ADMIN_TITLE'), '27', 'emails', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCKED_ADMIN_SUBJ', 0),
			array('max_login_blocked_admin', JText::_('COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_ADMIN_TEXT'), '28', 'emails', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCKED_ADMIN', 0),
			array('backend_someone_logged_subj', JText::_('COM_EXTENDEDREG_BACKEND_SOMEONE_LOGGED_TITLE'), '29', 'emails', '', 0),
			array('backend_someone_logged', JText::_('COM_EXTENDEDREG_BACKEND_SOMEONE_LOGGED_TEXT'), '30', 'emails', 'COM_EXTENDEDREG_HELP_BACKEND_SOMEONE_LOGGED', 0),
			
			// option name, option value, order, group, description, override
			// javascript group
			array('validate_joomla_username', '0', '1', 'javascript', 'COM_EXTENDEDREG_HELP_VALIDATE_JOOMLA_USERNAME', 0),
			array('validate_joomla_email', '0', '2', 'javascript', 'COM_EXTENDEDREG_HELP_VALIDATE_JOOMLA_EMAIL', 0),
			//~ array('include_jquery', '1', '3', 'javascript', 'COM_EXTENDEDREG_HELP_INCLUDE_JQUERY', 0),
			array('include_jquery_ui', '1', '4', 'javascript', 'COM_EXTENDEDREG_HELP_INCLUDE_JQUERY_UI', 0),
			array('include_jquery_tojson', '1', '5', 'javascript', 'COM_EXTENDEDREG_HELP_INCLUDE_JQUERY_TOJSON', 0),
			array('include_jquery_sprintf', '1', '6', 'javascript', 'COM_EXTENDEDREG_HELP_INCLUDE_JQUERY_SPRINTF', 0),
			array('include_jquery_steps', '1', '8', 'javascript', 'COM_EXTENDEDREG_HELP_INCLUDE_JQUERY_STEPS', 0),
			array('include_jquery_formvalidation', '1', '9', 'javascript', 'COM_EXTENDEDREG_HELP_INCLUDE_JQUERY_FORMVALIDATION', 0),
			
			// option name, option value, order, group, description, override
			// css group
			array('css_default_input_class', 'span12', '1', 'css', '', 0),
			array('css_theme', 'extreg', '2', 'css', 'COM_EXTENDEDREG_HELP_CSS_THEME', 0),
			array('css_front_extreg', '1', '3', 'css', 'COM_EXTENDEDREG_HELP_CSS_FRONT_EXTREG', 0),
			array('css_front_jquery', '1', '4', 'css', 'COM_EXTENDEDREG_HELP_CSS_FRONT_JQUERY', 0),
			array('css_back_extreg', '1', '5', 'css', 'COM_EXTENDEDREG_HELP_CSS_BACK_EXTREG', 0),
			array('css_back_jquery', '1', '6', 'css', 'COM_EXTENDEDREG_HELP_CSS_BACK_JQUERY', 0),
			
			// option name, option value, order, group, description, override
			// CAPTCHA group
			array('use_captcha', 'simple', '1', 'captcha', 'COM_EXTENDEDREG_HELP_USE_CAPTCHA', 0),
			array('simple_captcha_width', '200', '2', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_WIDTH', 0),
			array('simple_captcha_height', '70', '3', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_HEIGHT', 0),
			array('simple_captcha_use_random', '0', '4', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_USE_RANDOM', 0),
			array('simple_captcha_min_length', '5', '5', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_MIN_LENGTH', 0),
			array('simple_captcha_max_length', '8', '6', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_MAX_LENGTH', 0),
			array('simple_captcha_word_file', 'en.txt', '7', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_WORD_FILE', 0),
			array('simple_captcha_bg_transparent', '0', '8', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_BG_TRANSPARENT', 0),
			array('simple_captcha_bgcolor', '#FFFFFF', '9', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_BGCOLOR', 0),
			array('simple_captcha_colors', '#FF0000;#00FF00;#0000FF', '10', 'captcha', 'COM_EXTENDEDREG_HELP_SIMPLE_CAPTCHA_COLORS', 0),
			
			// option name, option value, order, group, description, override
			// redirect URL group
			array('redir_url_default', 'er_login_register', '1', 'redir_url', 'COM_EXTENDEDREG_HELP_DEFAULT_REDIR_URL', 0),
			array('redir_url_register', 'er_login', '2', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_REGISTER', 0),
			array('redir_url_register_other', '', '3', 'redir_url', '', 0),
			array('redir_url_reg_need_activation', 'er_home', '4', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_REG_NEED_ACTIVATION', 0),
			array('redir_url_reg_need_activation_other', '', '5', 'redir_url', '', 0),
			array('redir_url_reg_need_approval', 'er_home', '6', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_REG_NEED_APPROVAL', 0),
			array('redir_url_reg_need_approval_other', '', '7', 'redir_url', '', 0),
			array('redir_url_activation', 'er_home', '8', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_ACTIVATION', 0),
			array('redir_url_activation_other', '', '9', 'redir_url', '', 0),
			array('redir_url_activ_need_approval', 'er_home', '10', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_ACTIV_NEED_APPROVAL', 0),
			array('redir_url_activ_need_approval_other', '', '11', 'redir_url', '', 0),
			array('redir_url_wrong_password', 'er_login', '12', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_WRONG_PASSWORD', 0),
			array('redir_url_wrong_password_other', '', '13', 'redir_url', '', 0),
			array('redir_url_forgot_password', 'er_login', '14', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_FORGOT_PASSWORD', 0),
			array('redir_url_forgot_password_other', '', '15', 'redir_url', '', 0),
			array('redir_url_forgot_username', 'er_login', '16', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_FORGOT_USERNAME', 0),
			array('redir_url_forgot_username_other', '', '17', 'redir_url', '', 0),
			array('redir_url_login', 'er_home', '18', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_LOGIN', 0),
			array('redir_url_login_other', '', '19', 'redir_url', '', 0),
			array('redir_url_logout', 'er_home', '20', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_LOGOUT', 0),
			array('redir_url_logout_other', '', '21', 'redir_url', '', 0),
			array('redir_url_request_activation', 'er_home', '22', 'redir_url', 'COM_EXTENDEDREG_HELP_REDIR_URL_REQUEST_ACTIVATION', 0),
			array('redir_url_request_activation_other', '', '23', 'redir_url', '', 0),
			
			// option name, option value, order, group, description, override
			// password strength group
			array('pass_strength_enable', '1', '1', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_STRENGTH_ENABLE', 0),
			array('pass_min_level', '2', '2', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_MIN_LEVEL', 0),
			array('pass_min_chars', '5', '3', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_MIN_CHARS', 0),
			array('pass_allowed_chars', '0', '4', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_ALLOWED_CHARS', 0),
			array('pass_expected_chars', '57', '5', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_EXPECTED_CHARS', 0),
			array('pass_common_words', "password\nsex\ngod\n123456\n123\nliverpool\nletmein\nqwerty\nmonkey\nqweasd\n111111", '6', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_COMMON_WORDS', 0),
			array('pass_color_veryweak', '#ff0000', '7', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_COLOR_VERYWEAK', 0),
			array('pass_color_weak', '#cc0066', '8', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_COLOR_WEAK', 0),
			array('pass_color_medium', '#ff6600', '9', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_COLOR_MEDIUM', 0),
			array('pass_color_strong', '#33cc00', '10', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_COLOR_STRONG', 0),
			array('pass_color_verystrong', '#33ff00', '11', 'pass_strength', 'COM_EXTENDEDREG_HELP_PASS_COLOR_VERYSTRONG', 0),
			
			// option name, option value, order, group, description, override
			// security group
			array('blacklist_usernames', 'admin*', '1', 'security', 'COM_EXTENDEDREG_HELP_BLACKLIST_USERNAMES', 0),
			array('blacklist_emails', '', '2', 'security', 'COM_EXTENDEDREG_HELP_BLACKLIST_EMAILS', 0),
			array('blacklist_ips', '', '3', 'security', 'COM_EXTENDEDREG_HELP_BLACKLIST_IPS', 0),
			array('forbid_proxies', '0', '4', 'security', 'COM_EXTENDEDREG_HELP_FORBID_PROXIES', 0),
			array('use_password_renew', '0', '5', 'security', 'COM_EXTENDEDREG_HELP_USE_PASSWORD_RENEW', 0),
			array('password_renew_period', '30', '6', 'security', 'COM_EXTENDEDREG_HELP_PASSWORD_RENEW_PERIOD', 0),
			array('login_notify_admins', '0', '7', 'security', 'COM_EXTENDEDREG_HELP_LOGIN_NOTIFY_ADMINS', 0),
			array('use_max_login_front', '0', '8', 'security', 'COM_EXTENDEDREG_HELP_USE_MAX_LOGIN_FRONT', 0),
			array('max_login_user_front', '5', '9', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_USER', 0),
			array('max_login_attempt_time_front', '5', '10', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_ATTEMPT_TIME', 0),
			array('max_login_attempt_units_front', '0', '11', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_ATTEMPT_TIME_UNITS', 0),
			array('max_login_block_time_front', '1', '12', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCK_TIME', 0),
			array('max_login_block_units_front', '1', '13', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCK_TIME_UNITS', 0),
			array('blockip_max_login_front', '0', '14', 'security', 'COM_EXTENDEDREG_HELP_BLOCKIP_MAX_LOGIN_FRONT', 0),
			array('use_max_login_back', '0', '15', 'security', 'COM_EXTENDEDREG_HELP_USE_MAX_LOGIN_BACK', 0),
			array('max_login_user_back', '5', '16', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_USER', 0),
			array('max_login_attempt_time_back', '5', '17', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_ATTEMPT_TIME', 0),
			array('max_login_attempt_units_back', '0', '18', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_ATTEMPT_TIME_UNITS', 0),
			array('max_login_block_time_back', '1', '19', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCK_TIME', 0),
			array('max_login_block_units_back', '1', '20', 'security', 'COM_EXTENDEDREG_HELP_MAX_LOGIN_BLOCK_TIME_UNITS', 0),
			array('blockip_max_login_back', '0', '21', 'security', 'COM_EXTENDEDREG_HELP_BLOCKIP_MAX_LOGIN_BACK', 0),
			array('use_secret_hash', '0', '22', 'security', 'COM_EXTENDEDREG_HELP_USE_SECRET_HASH', 0),
			array('secret_hash', '', '23', 'security', '', 0),
			
			// option name, option value, order, group, description, override
			// html group
			array('html_before_default', '', '1', 'html', '', 0),
			array('html_after_default', '', '1', 'html', '', 0),
			array('html_before_login', '', '1', 'html', '', 0),
			array('html_after_login', '', '1', 'html', '', 0),
			array('html_before_register', '', '1', 'html', '', 0),
			array('html_after_register', '', '1', 'html', '', 0),
			array('html_before_profile', '', '1', 'html', '', 0),
			array('html_after_profile', '', '1', 'html', '', 0),
			array('html_before_remind', '', '1', 'html', '', 0),
			array('html_after_remind', '', '1', 'html', '', 0),
			array('html_before_request', '', '1', 'html', '', 0),
			array('html_after_request', '', '1', 'html', '', 0),
			array('html_before_reset', '', '1', 'html', '', 0),
			array('html_after_reset', '', '1', 'html', '', 0),
			array('html_before_terminate', '', '1', 'html', '', 0),
			array('html_after_terminate', '', '1', 'html', '', 0),
			
			// option name, option value, order, group, description, override
			// sef group
			array('sef_login', 'login', '1', 'sef', '', 0),
			array('sef_register', 'register', '2', 'sef', '', 0),
			array('sef_login_and_register', 'login_and_register', '3', 'sef', '', 0),
			array('sef_profile', 'profile', '4', 'sef', '', 0),
			array('sef_terminate', 'terminate', '5', 'sef', '', 0),
			array('sef_reset', 'reset', '6', 'sef', '', 0),
			array('sef_remind', 'remind', '7', 'sef', '', 0),
			array('sef_request_activation_mail', 'request_activation_mail', '8', 'sef', '', 0),
		);
		
		// Installer will delete options from this list
		$this->del_options = array(
			'use_landing_page',
			'landing_page_id',
			'show_terms_checkbox',
			'terms_text',
			'show_age_checkbox',
			'age_value',
			'captcha_width',
			'captcha_height',
			'captcha_use_random',
			'captcha_min_length',
			'captcha_max_length',
			'captcha_word_file',
			'captcha_bg_transparent',
			'captcha_bgcolor',
			'captcha_colors',
			'request_approve_email',
			'use_formbuilder',
			'include_jquery_formbuilder',
			'use_checktoken',
			'use_form_transforming',
			'include_jquery_uniform',
			'include_jquery_tooltip',
			'include_jquery_formlayout',
			'use_time_in_security',
			'use_max_login',
			'max_login_user',
			'max_login_attempt_time',
			'max_login_attempt_time_units',
			'max_login_block_time',
			'max_login_block_time_units',
			'include_jquery',
		);
		
		$this->columns = array(
			"ALTER TABLE #__extendedreg_users DROP " . $this->dbo->quoteName('new_cf'),
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('ip_addr') . " varchar(50) NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('form_id') . " int(11) NULL REFERENCES #__extendedreg_forms(" . $this->dbo->quoteName('id') . ") ",
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('last_activation_request') . " timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('approve_hash') . " varchar(32)",
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('terminate_hash') . " varchar(32)",
			"ALTER TABLE #__extendedreg_users DROP COLUMN " . $this->dbo->quoteName('block_tstamp'),
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('notes') . " text",
			"ALTER TABLE #__extendedreg_users ADD " . $this->dbo->quoteName('last_pass_change') . " timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'",
			// add grpid field
			"ALTER TABLE #__extendedreg_fields ADD " . $this->dbo->quoteName('grpid') . " int(11) unsigned NOT NULL DEFAULT 1 ",
			// change settings description column type
			"ALTER TABLE #__extendedreg_settings DROP COLUMN " . $this->dbo->quoteName('description'),
			"ALTER TABLE #__extendedreg_settings ADD " . $this->dbo->quoteName('description') . " varchar(255) NULL DEFAULT NULL",
			"ALTER TABLE #__extendedreg_fields ADD " . $this->dbo->quoteName('custom_sql') . " text NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_fields ADD " . $this->dbo->quoteName('editable') . " enum('0','1') NOT NULL DEFAULT '1' ",
			"ALTER TABLE #__extendedreg_fields ADD " . $this->dbo->quoteName('exportable') . " enum('0','1') NOT NULL DEFAULT '1' ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('mailfrom') . " varchar(100) NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('show_terms') . " enum('0','1') NOT NULL DEFAULT '0' ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('terms_switcher') . " enum('0','1') NOT NULL DEFAULT '0' ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('terms_article_id') . "  int(11) NOT NULL DEFAULT '0' ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('terms_value') . " text ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('show_age') . " enum('0','1') NOT NULL DEFAULT '0' ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('age_value') . " tinyint(3) ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('groups') . " varchar(100) ",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('admin_mails') . " tinytext ",
			"ALTER TABLE #__extendedreg_fields CHANGE " . $this->dbo->quoteName('type') . " " . $this->dbo->quoteName('type') . " varchar(255) NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_addons ADD " . $this->dbo->quoteName('params') . " text ",
			"ALTER TABLE #__extendedreg_stats ADD " . $this->dbo->quoteName('port') . " varchar(8) NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_stats ADD " . $this->dbo->quoteName('proxy') . " enum('0','1') NOT NULL DEFAULT '0' ",
			"ALTER TABLE #__extendedreg_stats CHANGE action action enum('login','logout','user_register','profile_edit') NOT NULL DEFAULT 'login'",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('form_style_width') . " varchar(10) NOT NULL DEFAULT '100%'",
			"ALTER TABLE #__extendedreg_forms ADD " . $this->dbo->quoteName('form_style_align') . " enum('align_margin','align_center','align_left','align_right','align_all_left','align_all_right','align_all_center') NOT NULL DEFAULT 'align_left'",
			
			// Change IP columns to support ipv6
			"ALTER TABLE #__extendedreg_users CHANGE " . $this->dbo->quoteName('ip_addr') . " " . $this->dbo->quoteName('ip_addr') . " varchar(50) NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_stats CHANGE " . $this->dbo->quoteName('ip_addr') . " " . $this->dbo->quoteName('ip_addr') . " varchar(50) NOT NULL DEFAULT '' ",
			"ALTER TABLE #__extendedreg_login_attempts CHANGE " . $this->dbo->quoteName('ip_addr') . " " . $this->dbo->quoteName('ip_addr') . " varchar(50) NOT NULL DEFAULT '' ",
		);
	}
	
	function run() {
		erHelperLanguage::installAllAvailable(true);
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg', JPATH_BASE, null, true);
		
		$this->runSQLFile(JvitalsDefines::comBackPath('com_extendedreg') . 'install.extendedreg.sql');
		
		$this->upgradeData();
		
		// Little housekeeping
		$config = JFactory::getConfig();
		if (is_file($config->get('tmp_path') . DIRECTORY_SEPARATOR . 'er-version-compare.txt')) {
			JFile::delete($config->get('tmp_path') . DIRECTORY_SEPARATOR . 'er-version-compare.txt');
		}
		if (is_file($config->get('tmp_path') . DIRECTORY_SEPARATOR . 'er-changelog.xml')) {
			JFile::delete($config->get('tmp_path') . DIRECTORY_SEPARATOR . 'er-changelog.xml');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets'. DIRECTORY_SEPARATOR . 'please_use_installer.php')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets'. DIRECTORY_SEPARATOR . 'please_use_installer.php');
		}
		
		if (is_dir(JvitalsDefines::comBackPath('com_extendedreg') . 'language' . DIRECTORY_SEPARATOR . '15')) {
			JFolder::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'language' . DIRECTORY_SEPARATOR . '15');
		}
		if (is_dir(JvitalsDefines::comBackPath('com_extendedreg') . 'language' . DIRECTORY_SEPARATOR . '16')) {
			JFolder::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'language' . DIRECTORY_SEPARATOR . '16');
		}
		
		if (is_dir(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'elements' . DIRECTORY_SEPARATOR . '15')) {
			JFolder::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'elements' . DIRECTORY_SEPARATOR . '15');
		}
		if (is_dir(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'elements' . DIRECTORY_SEPARATOR . '16')) {
			JFolder::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'elements' . DIRECTORY_SEPARATOR . '16');
		}

		if (is_dir(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'flick')) {
			JFolder::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'flick');
		}

		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.16.css')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.16.css');
		}

		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.17.css')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.17.css');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.18.css')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.18.css');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.20.css')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'smoothness' . DIRECTORY_SEPARATOR . 'jquery-ui-1.8.20.css');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery.uniform.pack.js')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery.uniform.pack.js');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'admin.extendedreg.php')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'admin.extendedreg.php');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'admin.extendedreg.php')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'admin.extendedreg.php');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'install.extendedreg.php')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'install.extendedreg.php');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'uninstall.extendedreg.php')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'uninstall.extendedreg.php');
		}
		
		if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'com_extendedreg.xml')) {
			JFile::delete(JvitalsDefines::comBackPath('com_extendedreg') . 'com_extendedreg.xml');
		}
		
		if (is_dir(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'extendedregredirect')) {
			JFolder::delete(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'extendedregredirect');
		}
		
		if (is_dir(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'extendedreg')) {
			JFolder::delete(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'extendedreg');
		}
		
		$this->dbo->setQuery("DELETE FROM #__extensions WHERE " . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote('plugin') . " 
			AND " . $this->dbo->quoteName('folder') . " = " . $this->dbo->Quote('system') . " AND " . $this->dbo->quoteName('element') . " = " . $this->dbo->Quote('extendedregredirect'));
		$this->dbo->execute();
		
		$this->dbo->setQuery("DELETE FROM #__extensions WHERE " . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote('plugin') . " 
			AND " . $this->dbo->quoteName('folder') . " = " . $this->dbo->Quote('user') . " AND " . $this->dbo->quoteName('element') . " = " . $this->dbo->Quote('extendedreg'));
		$this->dbo->execute();
		
	}
	
	function log($message, $type = 'info') {
		$entry = new stdClass();
		$entry->message = $message;
		$entry->type = $type;
		$this->logList[] = $entry;
	}
	
	function upgradeData() {
		// First change tables, then update data... 
		$this->upgradeColumns();
		
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('value') . " FROM #__extendedreg_settings WHERE " . $this->dbo->quoteName('optname') . " = "  . $this->dbo->Quote('terms_text') . " LIMIT 1");
		$terms_text = $this->dbo->loadResult();
		if (trim($terms_text)) {
			$this->dbo->setQuery("UPDATE #__extendedreg_forms SET " . $this->dbo->quoteName('terms_value') . " = " . $this->dbo->Quote($terms_text) . " WHERE " . $this->dbo->quoteName('terms_value') . " IS NULL OR " . $this->dbo->quoteName('terms_value') . " = ''");
			$this->dbo->execute();
		}
		
		$this->upgradeOptions();
		$this->deleteOptions();
		
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('grpid') . " FROM #__extendedreg_fields_groups WHERE " . $this->dbo->quoteName('grpid') . " = " . $this->dbo->Quote('1') . "");
		$grpid = (int)$this->dbo->loadResult();
		if (!$grpid) {
			$this->dbo->setQuery("INSERT INTO #__extendedreg_fields_groups (" . $this->dbo->quoteName('name') . ") VALUES (" . $this->dbo->Quote('Default') . ")");
			$this->dbo->execute();
		}
		
		// Create default form if not exists
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('id') . " FROM #__extendedreg_forms WHERE " . $this->dbo->quoteName('isdefault') . " = " . $this->dbo->Quote('1') . "");
		$frmid = (int)$this->dbo->loadResult();
		if (!$frmid) {
			$default_form = '[{"type":"row","contents":[{"type":"col","contents":[{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_NAME","contents":"#name_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_USERNAME","contents":"#username_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_EMAIL","contents":"#email_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_PASSWORD","contents":"#passwd_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD","contents":"#passwd2_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_REGISTER_CAPTCHA","contents":"#captcha_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_ACCEPT_TERMS","contents":"#terms_fld#"},{"type":"fld","label":"COM_EXTENDEDREG_OVER_AGE","contents":"#age_fld#"}]}]}]';
			$this->dbo->setQuery("INSERT INTO #__extendedreg_forms (" . $this->dbo->quoteName('name') . ", " . $this->dbo->quoteName('description') . ", " . $this->dbo->quoteName('published') . ", " . $this->dbo->quoteName('isdefault') . ", " . $this->dbo->quoteName('layout') . ") 
			VALUES (" . $this->dbo->Quote('Basic') . ", " . $this->dbo->Quote('Default form') . ", " . $this->dbo->Quote('1') . ", " . $this->dbo->Quote('1') . ", " . $this->dbo->Quote($default_form) . ")");
			$this->dbo->execute();
		}

		$this->syncUsers();
		
		$fieldTypes = erHelperAddons::loadAddons('field', false);
		$formsModel = JvitalsHelper::loadModel('extendedreg', 'Forms');
		foreach ($fieldTypes as $obj) {
			$newfield = $formsModel->loadField(0, $obj->file_name);
			$lib = erHelperAddons::getFieldType($newfield, false);
			if ($lib) {
				$this->dbo->setQuery("UPDATE #__extendedreg_fields SET " . $this->dbo->quoteName('exportable') . " = " . $this->dbo->Quote($lib->isExportable() ? 1 : 0) . " 
					WHERE " . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote($obj->file_name));
				$this->dbo->execute();
			}
		}
	}

	function upgradeOptions() {
		// update some group names
		$this->dbo->setQuery("UPDATE #__extendedreg_settings SET " . $this->dbo->quoteName('group') . " = (case 
			WHEN " . $this->dbo->quoteName('group') . " = " . $this->dbo->Quote('simple_captcha') . " THEN " . $this->dbo->Quote('captcha') . "
			WHEN " . $this->dbo->quoteName('group') . " = " . $this->dbo->Quote('jquery') . " THEN " . $this->dbo->Quote('javascript') . "
			WHEN " . $this->dbo->quoteName('optname') . " = " . $this->dbo->Quote('use_captcha') . " THEN " . $this->dbo->Quote('captcha') . "
			ELSE " . $this->dbo->quoteName('group') . "
		end)");
		$this->dbo->execute();
		
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('optname') . " FROM #__extendedreg_settings GROUP BY " . $this->dbo->quoteName('optname') . " HAVING count(*) > 1");
		$bad_options = $this->dbo->loadColumn();
		
		if (is_array($bad_options) && count($bad_options)) {
			foreach ($bad_options as $i => $optname) {
				$bad_options[$i] = $this->dbo->Quote($optname);
			}
			
			$this->dbo->setQuery("DELETE FROM #__extendedreg_settings 
				WHERE " . $this->dbo->quoteName('optname') . " IN (" . implode(',', $bad_options) . ")");
			$this->dbo->execute();
		}
		
		foreach ($this->options as $option) {
			
			list($name, $value, $order, $group, $description, $override) = $option;
			if (!in_array((int)$override, array(0, 1))) $override = 0;

			$this->dbo->setQuery("SELECT count(*) FROM #__extendedreg_settings WHERE " . $this->dbo->quoteName('optname') . " = " . $this->dbo->Quote($name));
			$option_exists = (int)$this->dbo->loadResult();			
			if (!$option_exists) {
				$this->dbo->setQuery("INSERT INTO #__extendedreg_settings (" . $this->dbo->quoteName('optname') . ", " . $this->dbo->quoteName('value') . ", " . $this->dbo->quoteName('description') . ", " . $this->dbo->quoteName('ord') . ", " . $this->dbo->quoteName('group') . ") 
					VALUES (" . $this->dbo->Quote($name) . ", " . $this->dbo->Quote($value) . ", " . $this->dbo->Quote($description) . ", " . $this->dbo->Quote($order) . ", " . $this->dbo->Quote($group) . ")");
				$this->dbo->execute();
			} else {
				$this->dbo->setQuery("UPDATE #__extendedreg_settings SET 
						" . ((int)$override ? $this->dbo->quoteName('value') . " = " . $this->dbo->Quote($value) . ", " : "") . "
						" . $this->dbo->quoteName('description') . " = " . $this->dbo->Quote($description) . ", 
						" . $this->dbo->quoteName('ord') . " = " . $this->dbo->Quote((int)$order) . ", 
						" . $this->dbo->quoteName('group') . " = " . $this->dbo->Quote($group) . "
					WHERE " . $this->dbo->quoteName('optname') . " = " . $this->dbo->Quote($name));
				$this->dbo->execute();
			}
			$name = '';
			$value = '';
			$order = '';
			$group = '';
			$description = '';
			$option_exists = '';
		}
		
		// Upgrade some wrong values
		$this->dbo->setQuery("UPDATE #__extendedreg_settings SET " . $this->dbo->quoteName('value') . " = (case 
			WHEN " . $this->dbo->quoteName('value') . " LIKE '%COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_TEXT%' THEN " . $this->dbo->Quote(JText::_('COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_TEXT')) . "
			WHEN " . $this->dbo->quoteName('value') . " LIKE '%COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_ADMIN_TEXT%' THEN " . $this->dbo->Quote(JText::_('COM_EXTENDEDREG_MAX_LOGIN_BLOCKED_ADMIN_TEXT')) . "
			ELSE " . $this->dbo->quoteName('value') . "
		end)");
		$this->dbo->execute();
	}
	
	function deleteOptions() {
		if (is_array($this->del_options) && count($this->del_options)) {
			foreach ($this->del_options as $i => $optname) {
				$this->del_options[$i] = $this->dbo->Quote($optname);
			}
			
			$this->dbo->setQuery("DELETE FROM #__extendedreg_settings WHERE " . $this->dbo->quoteName('optname') . " IN (" . implode(',', $this->del_options) . ")");
			$this->dbo->execute();
		}
	}
	
	function upgradeColumns() {
		foreach ($this->columns as $query) {
			try {
				$this->dbo->setQuery($query);
				@$this->dbo->execute();
			} catch (Exception $e) {
				// ......
			}
		}
	}
	
	function syncUsers() {
		$query = "INSERT INTO #__extendedreg_users (`user_id`, `acceptedterms`, `overage`, `approve`)
				SELECT u.id, '0', '0', (CASE WHEN u.block = '0' THEN '1' ELSE '0' END) 
				FROM #__users AS u
					LEFT JOIN #__extendedreg_users AS er ON u.id = er.user_id 
				WHERE er.user_id IS NULL";
		$this->dbo->setQuery($query);
		$this->dbo->execute();
	}
	
	function getPluginName($dir) {
		$name = '';
		// Search the install dir for an xml file
		$files = JFolder::files($dir, '\.xml$', 1, true);
		if (count($files) > 0) {
			foreach ($files as $file) {
				$name = '';
				$xml = JFactory::getXML($file);
				if ($xml) {
					$name = (string)$xml->name;
				}
				$xml = null;
				if (!$name) {
					continue;
				}
				return $name;
			}
		}
		return $name;
	}

	function enablePlugin($type, $name) {
		if ($type == 'plugin') {
			$query = 'UPDATE #__extensions SET 
					' . $this->dbo->quoteName('enabled') . ' = ' . $this->dbo->Quote(1) . ' 
				WHERE ' . $this->dbo->quoteName('type') . ' = ' . $this->dbo->Quote('plugin') . ' 
					AND ' . $this->dbo->quoteName('name') . ' = ' . $this->dbo->Quote($name);
			try {
				$this->dbo->setQuery($query);
				@$this->dbo->execute();
			} catch (Exception $e) {
				// ......
			}
		}
	}
	
	function runSQLFile($file) {
		$buffer = file_get_contents($file);

		if ($buffer === false) {
			$this->log('runSQLFile: ' . $file, 'error');
			return false;
		}

		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($buffer);

		if (count($queries) == 0) {
			// No queries to process
			return true;
		}

		// Process each query in the $queries array (split out of sql file).
		$this->runQueryArray($queries);
		
		return count($queries);
	}
	
	function runQueryArray($queries) {
		if (is_array($queries)) {
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query{0} != '#') {
					$this->dbo->setQuery($query);
					if (!$this->dbo->execute()) {
						$this->log('SQL "' . $query . '" failed', 'error');
					}
				}
			}
			return count($queries);
		}
		return 0;
	}
	
}