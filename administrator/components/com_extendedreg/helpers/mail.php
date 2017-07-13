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

jimport('joomla.mail.helper');
jimport('joomla.user.helper');

class erHelperMail {
	
	public static function handle_raw_url($link) {
		$linkarr = explode(',',$link);
		$link = $linkarr[0];
		$suffix = '';
		if (count($linkarr) > 1) {
			array_shift($linkarr);
			$suffix = ',' . implode(',', $linkarr);
		}
		
		$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $link);
		$full_url = trim(strip_tags($full_url));
		
		if (strpos($link, 'www.') === 0) { // If it starts with www, we add http://
			$full_url = 'http://'.$full_url;
		} else if (strpos($link, 'ftp.') === 0) { // Else if it starts with ftp, we add ftp://
			$full_url = 'ftp://'.$full_url;
		} else if (!preg_match('#^([a-z0-9]{3,6})://#', $link, $bah)) { // Else if it doesn't start with abcdef://, we add http://
			$full_url = 'http://'.$full_url;
		}
		return "<a href=\"$full_url\">$link</a>" . $suffix;
	}
	
	public static function changeHtml($text) {
		$search = array('<br />', '<br/>', '<p>', '</p>');
		$replace = array(' <br> ', ' <br> ', '', ' <br><br> ');
		$html = str_replace($search, $replace, $text);
		$html = preg_replace('#([\s\(\)])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?\S+(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.erHelperMail::handle_raw_url(\'$2://$3\')', $html);
		return $html;
	}
	
	public static function renderMail($message, $user, $admin = '') {
		$app = JFactory::getApplication();
		$userModel = JvitalsHelper::loadModel('extendedreg', 'Users');
		$user = $userModel->loadUserByEmail($user->email);
		
		$session = JFactory::getSession();
		$clear_password = $session->get('erClearPassword', null, 'extendedreg');
		if (!trim($clear_password)) $clear_password = '';
		
		$strippedmessage = trim(strip_tags($message));
		if ($strippedmessage && !preg_match('~\s+~', $strippedmessage)) {
			$message = JText::_($strippedmessage);
		}

		$name = $user->name;
		$email = $user->email;
		$username = $user->username;
		$whois = 'http://tools.whois.net/whoisbyip/?host=';
		$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$sitename = $app->getCfg('sitename');
		$uri = JURI::getInstance();
		$siteURL = $uri->toString(array('scheme', 'host', 'port'));
		if (preg_match('~^(.*?)/modules/.*?$~smi', $siteURL, $m)) {
			$siteURL = trim($m[1]);
		}
		$activate_link = $siteURL . JRoute::_('index.php?option=com_extendedreg&task=users.activate&activation=' . $user->activation, false);
		$register_link = $siteURL . JRoute::_('index.php?option=com_extendedreg&task=users.register' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.register') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.register') : ''), false);
		$login_link = $siteURL . JRoute::_('index.php?option=com_extendedreg&task=users.login' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.login') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.login') : ''), false);
		$reset_confirm_link = $siteURL . JRoute::_('index.php?option=com_extendedreg&task=users.reset&layout=confirm' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=confirm') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset&layout=confirm') : ''), false);
		$admin_approve_link = $siteURL . JRoute::_('index.php?option=com_extendedreg&task=users.approve&user_id=' . (int)$user->user_id . '&activation=' . $user->approve_hash, false);
		
		$dateFormat = 'Y-m-d';
		$datetimeFormat = 'Y-m-d H:i:s';
		$register_date = erHelperHTML::formatDate($user->registerDate, $dateFormat);
		$register_datetime = erHelperHTML::formatDate($user->registerDate, $datetimeFormat);
		
		if (preg_match('~^(.*?)/modules/mod_[^/]+(.*?)$~smi', $activate_link, $m)) {
			$activate_link = $m[1] . $m[2];
		}
		if (preg_match('~^(.*?)/modules/mod_[^/]+(.*?)$~smi', $register_link, $m)) {
			$register_link = $m[1] . $m[2];
		}
		if (preg_match('~^(.*?)/modules/mod_[^/]+(.*?)$~smi', $login_link, $m)) {
			$login_link = $m[1] . $m[2];
		}
		if (preg_match('~^(.*?)/modules/mod_[^/]+(.*?)$~smi', $reset_confirm_link, $m)) {
			$reset_confirm_link = $m[1] . $m[2];
		}
		if (preg_match('~^(.*?)/modules/mod_[^/]+(.*?)$~smi', $admin_approve_link, $m)) {
			$admin_approve_link = $m[1] . $m[2];
		}
		
		$search = array('{user_id}', '{hi_name}', '{name}', '{username}', '{email}', '{sitename}', '{siteurl}', '{activate_link}', '{register_link}', '{login_link}', '{reset_confirm_link}','{ip_addr}', '{whois}', '{password}', '{approve_link}', '{register_date}', '{register_datetime}');
		$replace = array((int)$user->user_id, ($admin ? $admin : $name), $name, $username, $email, $sitename, $siteURL, $activate_link, $register_link, $login_link, $reset_confirm_link, $ip_addr, $whois, $clear_password, $admin_approve_link, $register_date, $register_datetime);
		$message = str_replace($search, $replace, $message);
		
		// Replace Custom Fields
		$customFields = '';
		$search = array();
		$replace = array();
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$cf_names = $model->getExtraFieldsInfo();
		if (is_array($cf_names) && count($cf_names)) {
			foreach($cf_names as $cf) {
				$fieldname = $cf->name;
				$search[] = '{' . $fieldname . '}';
				if (isset($user->$fieldname)) {
					$userval = explode('#!#', $user->$fieldname);
					foreach ($userval as $ukey => $uval) {
						$userval[$ukey] = JText::_($uval);
					}
					if (is_array($userval)) {
						$userval = implode(', ', $userval);
					}
					$customFields .= $cf->title . ': ' . $userval . '<br/>';
					$replace[] = $userval;
				} else {
					$replace[] = '';
				}
			}
		}
		$search[] = '{customFields}';
		$replace[] = $customFields;
		$message = str_replace($search, $replace, $message);
		
		return $message;
	}
	
	public static function getMailFrom($form_id = 0) {
		$from_email = '';
		$from_name = '';
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		if ((int)$form_id) {
			$form = $model->loadForm((int)$form_id);
			$from_email = trim($form->mailfrom);
		}
		
		if (!mb_strlen($from_email)) {
			$conf = $model->getConfObj();
			if (mb_strlen($conf->default_mailfrom)) {
				$from_email = $conf->default_mailfrom;
			}
		}
		
		if (!mb_strlen($from_email)) {
			$app = JFactory::getApplication();
			$from_email = $app->getCfg('mailfrom');
			$from_name = $app->getCfg('fromname');
		}
		
		return array($from_email, $from_name);
	}
	
	public static function send($recipient, $subject, $message, $from_email, $from_name) {
		$mail = JFactory::getMailer();
		$mail->setSender(array($from_email, $from_name));
		$mail->addRecipient($recipient);
		$mail->setSubject($subject);
		$message = self::changeHtml($message);
		$mail->MsgHTML($message);
		$mail->Send();
		
		if ($mail->IsError()) {
			return false;
		}
		return true;
	}
	
	public static function sendRemindMail($user) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$email_subj = $conf->remind_email_user_subj;
		$email_msg = $conf->remind_email_user;
		if ((int)$conf->email_for_username) {
			$email_subj = str_replace('{username}','{email}', $email_subj);
			$email_msg = str_replace('{username}','{email}', $email_msg);
		}
		
		$subject = html_entity_decode(self::renderMail($email_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($email_msg, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom();
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendResetMail($user) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->reset_email_user_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->reset_email_user, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom();
		
		$token = JApplication::getHash(JUserHelper::genRandomPassword());
		$salt = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token.$salt).':'.$salt;
		if (!$model->setActivationString((int)$user->id, $hashedToken)) {
			return false;
		}
		$message = str_replace('{reset_token}', $token, $message);
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendUserActivationMail($user, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->activation_email_user_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->activation_email_user, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom($form_id);
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendUserNeedApprovalMail($user, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->approve_email_user_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->approve_email_user, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom($form_id);
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendUserApproveDoneMail($user, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->approve_email_user_done_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->approve_email_user_done, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom($form_id);
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendUserRegistrationInfoMail($user, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->reginfo_email_user_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->reginfo_email_user, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom($form_id);
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}

	public static function sendAdminNeedApprovalMail($user, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->approve_email_admin_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->approve_email_admin, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom($form_id);
		
		$admin_mails = self::getAdminMails($conf, $form_id);
		return self::send($admin_mails, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendAdminRegistrationInfoMail($user, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->reginfo_email_admin_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->reginfo_email_admin, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom($form_id);
		
		$admin_mails = self::getAdminMails($conf, $form_id);
		return self::send($admin_mails, $subject, $message, $from_email, $from_name);
	}
	
	public static function getAdminMails($conf, $form_id) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$form = $model->loadForm((int)$form_id);
		
		// get admin mails
		$admin_mails = array();
		if (mb_strlen(trim($form->admin_mails))) {
			$admin_mails = explode(',', $form->admin_mails);
		} elseif (mb_strlen(trim($conf->admin_mails))) {
			$admin_mails = explode(',', $conf->admin_mails);
		} 
		
		if (!count($admin_mails)) {
			$app = JFactory::getApplication();
			$admin_mails[] = $app->getCfg('mailfrom');
		} else {
			$admin_mails = array_map('trim', $admin_mails);
		}
		
		return $admin_mails;
	}
	
	public static function sendTerminateMail($user) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->delete_account_user_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->delete_account_user, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom();
		
		// build the termination link
		$uri = JURI::getInstance();
		$siteURL = $uri->toString(array('scheme', 'host', 'port'));
		if (preg_match('~^(.*?)/modules/.*?$~smi', $siteURL, $m)) {
			$siteURL = trim($m[1]);
		}		
		$termination_link = $siteURL . JRoute::_('index.php?option=com_extendedreg&task=users.do_terminate&hash=' . $user->terminate_hash, false);
		$message = str_replace('{termination_link}', $termination_link, $message);
		
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendAdminTerminateMail($user) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->delete_account_admin_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->delete_account_admin, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom(0);
		
		$admin_mails = self::getAdminMails($conf, 0);
		return self::send($admin_mails, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendFailedLoginsMail($user, $block_time) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = str_replace('{block_time}', $block_time, html_entity_decode(self::renderMail($conf->max_login_blocked_subj, $user), ENT_QUOTES));
		$message = str_replace('{block_time}', $block_time, html_entity_decode(self::renderMail($conf->max_login_blocked, $user), ENT_QUOTES));
		list($from_email, $from_name) = self::getMailFrom();
		return self::send($user->email, $subject, $message, $from_email, $from_name);
	}
	
	public static function sendAdminFailedLoginsMail($user, $block_time) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = str_replace('{block_time}', $block_time, html_entity_decode(self::renderMail($conf->max_login_blocked_admin_subj, $user), ENT_QUOTES));
		$message = str_replace('{block_time}', $block_time, html_entity_decode(self::renderMail($conf->max_login_blocked_admin, $user), ENT_QUOTES));
		list($from_email, $from_name) = self::getMailFrom();
		$admin_mails = self::getAdminMails($conf, 0);
		return self::send($admin_mails, $subject, $message, $from_email, $from_name);
	}

	public static function sendAdminSomeoneLoggedMail($user) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		$subject = html_entity_decode(self::renderMail($conf->backend_someone_logged_subj, $user), ENT_QUOTES);
		$message = html_entity_decode(self::renderMail($conf->backend_someone_logged, $user), ENT_QUOTES);
		list($from_email, $from_name) = self::getMailFrom();
		
		$admin_mails = self::getAdminMails($conf, 0);
		return self::send($admin_mails, $subject, $message, $from_email, $from_name);
	}
}