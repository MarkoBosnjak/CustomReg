<?php
/**
 * @package		ExtendedReg - Authentication
 * @version		2.10
 * @date		2014-02-05
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link			http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

$init = JPATH_ROOT . DIRECTORY_SEPARATOR .  'administrator' . DIRECTORY_SEPARATOR .  'components' . DIRECTORY_SEPARATOR .  'com_extendedreg' . DIRECTORY_SEPARATOR .  'helpers' . DIRECTORY_SEPARATOR .  'initiate.php';

if (is_file($init)) {
	require_once ($init);
	
	class plgAuthenticationExtendedregauth extends JPlugin {
		
		public function onUserAuthenticate(&$credentials, $options, &$response) {
			$JVersion = new JVersion();
			$version = $JVersion->getShortVersion();
			$version = preg_replace('~[^\d|\.]~', '', $version);
			
			$responseStatusSuccess = JAuthentication::STATUS_SUCCESS;
			$responseStatusError = JAuthentication::STATUS_FAILURE;
			$response->type = 'Joomla';
			
			// Get a database object
			$app = JFactory::getApplication();
			$db = JFactory::getDbo();
			$model = JvitalsHelper::loadModel('extendedreg', 'Users');
			$conf = $model->getConfObj();
			$checkPass = true;
			$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
			
			// Check IP address
			if ($ip_addr != '0.0.0.0') {
				$blIPaddresses = array();
				if (trim($conf->blacklist_ips)) $blIPaddresses = explode("\n", trim($conf->blacklist_ips));
				
				$foundIP = false;
				if (count($blIPaddresses)) {
					foreach ($blIPaddresses as $testIP) {
						$testIP = str_replace('*', '##all##', trim($testIP));
						$testIP = preg_quote($testIP);
						$testIP = str_replace('##all##', '.*?', $testIP);
						if (preg_match('~^' . $testIP . '$~smi', trim($ip_addr))) {
							$foundIP = true;
							break;
						}
					}
				}
				
				if ($foundIP) {
					$response->error_message = JText::_('JGLOBAL_AUTH_USER_BLACKLISTED');
					$response->status = $responseStatusError;
					return false;
				}
			}
			
			if ($app->isAdmin()) {
				$checkAttempts = (boolean)((int)$conf->use_max_login_back ? true : false);
			} else {
				$checkAttempts = (boolean)((int)$conf->use_max_login_front ? true : false);
			}
			
			// Check if the account is blocked
			if ($checkAttempts) {
				if (!$model->checkLoginAttempts($ip_addr, $credentials['username'], $app->isAdmin())) {
					$response->error_message = JText::_('JGLOBAL_AUTH_USER_BLACKLISTED');
					$response->status = $responseStatusError;
					return false;
				}
			}
			
			if (is_array($options) && isset($options['er-auto-login']) && (int)$options['er-auto-login']) {
				if (!(int)$credentials['user_id']) {
					$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
					$response->status = $responseStatusError;
					if ($checkAttempts) $model->addLoginAttempts($ip_addr, $credentials['username']);
					return false;
				}
				
				$checkPass = false;
				
				$db->setQuery('SELECT ' . $db->quoteName('id') . ', ' . $db->quoteName('password') . ' 
					FROM ' . $db->quoteName('#__users') . '  
					WHERE ' . $db->quoteName('id') . ' = ' . (int)$credentials['user_id']);
				$result = $db->loadObject();
			} else {
				// Joomla does not like blank passwords
				if (empty($credentials['password'])) {
					$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
					$response->status = $responseStatusError;
					if ($checkAttempts) $model->addLoginAttempts($ip_addr, $credentials['username']);
					return false;
				}

				$db->setQuery('SELECT ' . $db->quoteName('id') . ', ' . $db->quoteName('password') . ' 
					FROM ' . $db->quoteName('#__users') . '  
					WHERE ' . $db->quoteName('username') . ' = ' . $db->Quote($credentials['username']));
				$result = $db->loadObject();
			}
			
			if ($result) {
				$passMatch = false;
				
				if ($checkPass) {
				
					// we want to cover all possible cases
					
					if (version_compare($version, '3.2.0', 'eq')) {
						
						if (substr($result->password, 0, 4) == '$2y$') {
							// BCrypt passwords are always 60 characters, but it is possible that salt is appended although non standard.
							$password60 = substr($result->password, 0, 60);
							if (JCrypt::hasStrongPasswordSupport()) {
								$passMatch = password_verify($credentials['password'], $password60);
							}
						} elseif (substr($result->password, 0, 8) == '{SHA256}') {
							// Check the password
							$parts = explode(':', $result->password);
							$crypt = $parts[0];
							$salt = @$parts[1];
							$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt, 'sha256', false);
							$passMatch = (boolean)($result->password == $testcrypt);
						} else {
							// Check the password
							$parts = explode(':', $result->password);
							$crypt = $parts[0];
							$salt = @$parts[1];
							$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt, 'md5-hex', false);
							$passMatch = (boolean)($crypt == $testcrypt);
						}
						
					} elseif (version_compare($version, '3.2.1', 'ge') || (version_compare($version, '2.5.18', 'ge') && version_compare($version, '3.0.0', 'lt'))) {
						
						$passMatch = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
						
					} else {
						
						$parts = explode(':', $result->password);
						$crypt = $parts[0];
						$salt = @$parts[1];
						$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);
						$passMatch = (boolean)($crypt == $testcrypt);
						
					}
				}

				if (!$checkPass || $passMatch) {
					$user = JUser::getInstance($result->id);
					$response->email = $user->email;
					$response->fullname = $user->name;
					$response->username = $user->username;
					$response->password = ($checkPass ? $credentials['password'] : 'notrealpass');
					if ($app->isAdmin()) {
						$response->language = $user->getParam('admin_language');
					} else {
						$response->language = $user->getParam('language');
					}
					$response->status = $responseStatusSuccess;
					$response->error_message = '';
					
					// Check the two factor authentication
					if (version_compare($version, '3.2.0', 'ge')) {
						require_once (JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php');
						
						$methods = UsersHelper::getTwoFactorMethods();
						if (count($methods) <= 1) {
							// No two factor authentication method is enabled
							return;
						}
						
						require_once (JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');
						
						$comUsersModel = new UsersModelUser;
						// Load the user's OTP (one time password, a.k.a. two factor auth) configuration
						if (!array_key_exists('otp_config', $options)) {
							$otpConfig = $comUsersModel->getOtpConfig($result->id);
							$options['otp_config'] = $otpConfig;
						} else {
							$otpConfig = $options['otp_config'];
						}
						
						// Check if the user has enabled two factor authentication
						if (empty($otpConfig->method) || ($otpConfig->method == 'none')) {
							// Warn the user if he's using a secret code but he has not
							// enabed two factor auth in his account.
							if (!empty($credentials['secretkey'])) {
								try {
									$lang = JFactory::getLanguage();
									$lang->load('com_extendedreg');
									
									$app->enqueueMessage(JText::_('COM_EXTENDEDREG_ERROR_SECRET_CODE_WITHOUT_TFA'), 'warning');
								} catch (Exception $exc) {
									// This happens when we are in CLI mode. In this case no warning is issued
									return;
								}
							}
							return;
						}
						
						// Load the Joomla! RAD layer
						if (!defined('FOF_INCLUDED')) {
							include_once (JPATH_LIBRARIES . '/fof/include.php');
						}
						
						// Try to validate the OTP
						FOFPlatform::getInstance()->importPlugin('twofactorauth');
						
						$otpAuthReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorAuthenticate', array($credentials, $options));
						
						$otpCheck = false;

						/*
						 * This looks like noob code but DO NOT TOUCH IT and do not convert
						 * to in_array(). During testing in_array() inexplicably returned
						 * null when the OTEP begins with a zero! o_O
						 */
						if (!empty($otpAuthReplies)) {
							foreach ($otpAuthReplies as $authReply) {
								$otpCheck = $otpCheck || $authReply;
							}
						}
						
						// Fall back to one time emergency passwords
						if (!$otpCheck) {
							// Did the user use an OTEP instead?
							if (empty($otpConfig->otep)) {
								if (empty($otpConfig->method) || ($otpConfig->method == 'none')) {
									/*
									 * Two factor authentication is not enabled on this account.
									 * Any string is assumed to be a valid OTEP.
									 */
									return true;
								} else {
									/*
									 * Two factor authentication enabled and no OTEPs defined. The
									 * user has used them all up. Therefore anything he enters is
									 * an invalid OTEP.
									 */
									return false;
								}
							}

							// Clean up the OTEP (remove dashes, spaces and other funny stuff
							// our beloved users may have unwittingly stuffed in it)
							$otep = $credentials['secretkey'];
							$otep = filter_var($otep, FILTER_SANITIZE_NUMBER_INT);
							$otep = str_replace('-', '', $otep);

							$otpCheck = false;

							// Did we find a valid OTEP?
							if (in_array($otep, $otpConfig->otep)) {
								// Remove the OTEP from the array
								$otpConfig->otep = array_diff($otpConfig->otep, array($otep));
								$comUsersModel->setOtpConfig($result->id, $otpConfig);
								$otpCheck = true;
							}
						}
						
						if (!$otpCheck) {
							$response->status = $responseStatusError;
							$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_SECRETKEY');
							if ($checkAttempts) $model->addLoginAttempts($ip_addr, $credentials['username']);
							return false;
						}
						
					}
					
					return true;
					
				} else {
					$response->status = $responseStatusError;
					$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
					if ($checkAttempts) $model->addLoginAttempts($ip_addr, $credentials['username']);
					return false;
				}
			}
			
			$response->status = $responseStatusError;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
			if ($checkAttempts) $model->addLoginAttempts($ip_addr, $credentials['username']);
			return false;
		}
	}
} else {
	$plgAuthenticationJoomla = realpath(dirname(__FILE__) . '/../joomla/joomla.php');
	require_once ($plgAuthenticationJoomla);
	
	class plgAuthenticationExtendedregauth extends plgAuthenticationJoomla {
		// Nothing else to do
	}
}
