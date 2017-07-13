<?php
/**
 * @package		ExtendedReg - User
 * @version		2.11
 * @date		2014-03-29
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
	
	class plgUserExtendedreguser extends JPlugin {
	
		protected $app;
		protected $db;
		protected $joomlaVersion;
		protected $useStrongEncryption;
		
		public function __construct(&$subject, $config = array()) {
			parent::__construct($subject, $config);
			
			$JVersion = new JVersion();
			$this->joomlaVersion = preg_replace('~[^\d|\.]~', '', $JVersion->getShortVersion());
			
			if (!isset($this->db) || !$this->db) {
				$this->db = JFactory::getDbo();
			}
			
			if (!isset($this->app) || !$this->app) {
				$this->app = JFactory::getApplication();
			}
			
			// As of CMS 3.2 strong encryption is the default.
			if (version_compare($this->joomlaVersion, '3.2.0', 'ge')) {
				$this->useStrongEncryption = $this->params->get('strong_passwords', true);
			}
		}
		
		public function onUserAfterDelete($user, $succes, $msg) {
			if (!$succes) {
				return false;
			}

			$this->db->getQuery(true)
				->delete($this->db->quoteName('#__session'))
				->where($this->db->quoteName('userid') . ' = ' . (int)$user['id'])
				->execute();

			return true;
		}
		
		public function onUserAfterSave($user, $isnew, $success, $msg) {
			// Nothing to do here
		}

		public function onUserLogin($user, $options = array()) {
			jimport('joomla.user.helper');
			
			$instance = $this->_getUser($user, $options);
			
			// if _getUser returned an error, then pass it back.
			if ($instance instanceof Exception) {
				return $instance;
			}
			
			$model = JvitalsHelper::loadModel('extendedreg', 'Users');
			$conf = $model->getConfObj();

			// If the user is blocked, redirect with an error
			if ($instance->get('block') == 1) {
				if ($this->app->getName() == 'site' && (int)$conf->enable_admin_approval) {
					$this->app->enqueueMessage(JText::_('COM_EXTENDEDREG_ERROR_NOT_APPROVED'), 'warning');
				} else {
					$this->app->enqueueMessage(JText::_('COM_EXTENDEDREG_NOLOGIN_BLOCKED'), 'warning');
				}
				return false;
			}
			
			// Authorise the user based on the group information
			if (!isset($options['group'])) {
				$options['group'] = 'USERS';
			}
			
			// Chek the user can login.
			$result = $instance->authorise($options['action']);
			if (!$result) {
				$this->app->enqueueMessage(JText::_('COM_EXTENDEDREG_LOGIN_DENIED'), 'warning');
				return false;
			}
			
			// Mark the user as logged in
			$instance->set('guest', 0);
			
			if (version_compare($this->joomlaVersion, '3.2.0', 'eq')) {
				// If the user has an outdated hash, update it.
				if (substr($user['password'], 0, 4) != '$2y$' && $this->useStrongEncryption && JCrypt::hasStrongPasswordSupport() == true) {
					if (strlen($user['password']) > 55) {
						$user['password'] = substr($user['password'], 0, 55);
						$this->app->enqueueMessage(JText::_('JLIB_USER_ERROR_PASSWORD_TRUNCATED'), 'notice');
					}
					$instance->password = password_hash($user['password'], PASSWORD_BCRYPT);
					$instance->save();
				}
			}
			
			//Set ER fields
			$extendedreg = $model->loadUserById($instance->id);
			$instance->set('extreg', $extendedreg);

			// Register the needed session variables
			$session = JFactory::getSession();
			$session->set('user', $instance);
			
			// Check to see the the session already exists.
			$this->app->checkSession();

			// Update the user related fields for the Joomla sessions table.
			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__session'))
				->set($this->db->quoteName('guest') . ' = ' . $this->db->quote($instance->guest))
				->set($this->db->quoteName('username') . ' = ' . $this->db->quote($instance->username))
				->set($this->db->quoteName('userid') . ' = ' . (int) $instance->id)
				->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($session->getId()));
			$this->db->setQuery($query)->execute();

			// Hit the user last visit field
			$instance->setLastVisit();
			
			if ((int)$conf->login_notify_admins && $this->app->getName() != 'site') {
				erHelperMail::sendAdminSomeoneLoggedMail($instance);
			}
			
			return true;
		}

		public function onUserLogout($user, $options = array()) {
			$my = JFactory::getUser();
			$session = JFactory::getSession();

			// Make sure we're a valid user first
			if ($user['id'] == 0 && !$my->get('tmp_user')) {
				return true;
			}

			// Check to see if we're deleting the current session
			if ($my->get('id') == $user['id'] && $options['clientid'] == $this->app->getClientId()) {
				// Hit the user last visit field
				$my->setLastVisit();

				// Destroy the php session for this user
				$session->destroy();
			}

			// Force logout all users with that userid
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__session'))
				->where($this->db->quoteName('userid') . ' = ' . (int) $user['id'])
				->where($this->db->quoteName('client_id') . ' = ' . (int) $options['clientid']);
			$this->db->setQuery($query)->execute();

			return true;
		}
		
		protected function &_getUser($user, $options = array()) {
			$instance = JUser::getInstance();
			$id = (int) JUserHelper::getUserId($user['username']);
			
			if ($id) {
				$instance->load($id);
				return $instance;
			}

			//TODO : move this out of the plugin
			jimport('joomla.application.component.helper');
			$config = JComponentHelper::getParams('com_users');
			// Default to Registered.
			$defaultUserGroup = $config->get('new_usertype', 2);
			
			if (version_compare($this->joomlaVersion, '3.0.0', 'lt')) {
				$acl = JFactory::getACL();
			}

			$instance->set('id', 0);
			$instance->set('name', $user['fullname']);
			$instance->set('username', $user['username']);
			$instance->set('password_clear', $user['password_clear']);
			$instance->set('email', $user['email']);	// Result should contain an email (check)
			$instance->set('usertype', 'deprecated');
			$instance->set('groups', array($defaultUserGroup));

			//If autoregister is set let's register the user
			$autoregister = isset($options['autoregister']) ? $options['autoregister'] :  $this->params->get('autoregister', 1);

			if ($autoregister) {
				if (!$instance->save()) {
					if (version_compare($this->joomlaVersion, '3.2.0', 'ge')) {
						JLog::add('Error in autoregistration for user ' .  $user['username'] . '.', JLog::WARNING, 'error');
					} else {
						$this->app->enqueueMessage($instance->getError(), 'warning');
						return false;
					}
				}
			} else {
				// No existing user and autoregister off, this is a temporary user.
				$instance->set('tmp_user', true);
			}

			return $instance;
		}
		
		public function onUserAfterLogin($options) {
			if (version_compare($this->joomlaVersion, '3.2.0', 'ge') && version_compare($this->joomlaVersion, '3.2.3', 'lt')) {
				// Currently this portion of the method only applies to Cookie based login.
				if (!isset($options['responseType']) || ($options['responseType'] != 'Cookie' && empty($options['remember']))) {
					return true;
				}

				// We get the parameter values differently for cookie and non-cookie logins.
				$cookieLifetime = empty($options['lifetime']) ? $this->app->rememberCookieLifetime : $options['lifetime'];
				$length = empty($options['length']) ? $this->app->rememberCookieLength : $options['length'];
				$secure = empty($options['secure']) ? $this->app->rememberCookieSecure : $options['secure'];

				// We need the old data to match against the current database
				$rememberArray = JUserHelper::getRememberCookieData();

				$privateKey = JUserHelper::genRandomPassword($length);

				// We are going to concatenate with . so we need to remove it from the strings.
				$privateKey = str_replace('.', '', $privateKey);

				$cryptedKey = JUserHelper::getCryptedPassword($privateKey, '', 'bcrypt', false);

				$cookieName = JUserHelper::getShortHashedUserAgent();

				// Create an identifier and make sure that it is unique.
				$unique = false;

				do {
					// Unique identifier for the device-user
					$series = JUserHelper::genRandomPassword(20);

					// We are going to concatenate with . so we need to remove it from the strings.
					$series = str_replace('.', '', $series);

					$query = $this->db->getQuery(true)
						->select($this->db->quoteName('series'))
						->from($this->db->quoteName('#__user_keys'))
						->where($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($series)));

					$results = $this->db->setQuery($query)->loadResult();

					if (is_null($results)) {
						$unique = true;
					}
				}
				while ($unique === false);

				// If a user logs in with non cookie login and remember me checked we will
				// delete any invalid entries so that he or she can use remember once again.
				if ($options['responseType'] !== 'Cookie') {
					$query = $this->db->getQuery(true)
						->delete('#__user_keys')
						->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName))
						->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['user']->username));

					$this->db->setQuery($query)->execute();
				}
				
				if (version_compare($this->joomlaVersion, '3.2.0', 'eq')) {
					$cookieValue = $privateKey . '.' . $series . '.' . $cookieName;
				} else {
					$cookieValue = $cryptedKey . '.' . $series . '.' . $cookieName;
				}

				// Destroy the old cookie.
				$this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path'), $this->app->get('cookie_domain'));

				// And make a new one.
				$this->app->input->cookie->set($cookieName, $cookieValue, $cookieLifetime, $this->app->get('cookie_path'), $this->app->get('cookie_domain'), $secure);

				$query = $this->db->getQuery(true);
				
				$cookie_condition = version_compare($this->joomlaVersion, '3.2.0', 'eq')
									? (empty($user->cookieLogin) || ($options['response'] != 'Cookie'))
									: (empty($options['user']->cookieLogin) || ($options['responseType'] != 'Cookie'));
				
				// This below was 'Coookie' in original joomla plugin
				if ($cookie_condition) {
					// For users doing login from Joomla or other systems
					$query->insert($this->db->quoteName('#__user_keys'));
				} else {
					$query
						->update($this->db->quoteName('#__user_keys'))
						->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['user']->username))
						->where($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($rememberArray[1])))
						->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName));
				}

				$query
					->set($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['user']->username))
					->set($this->db->quoteName('time') . ' = ' . $cookieLifetime)
					->set($this->db->quoteName('token') . ' = ' . $this->db->quote($cryptedKey))
					->set($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($series)))
					->set($this->db->quoteName('invalid') . ' = 0')
					->set($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName));

				$this->db->setQuery($query)->execute();
			}
			
			return true;
		}
		
		public function onUserAfterLogout($options) {
			if (version_compare($this->joomlaVersion, '3.2.0', 'ge') && version_compare($this->joomlaVersion, '3.2.3', 'lt')) {
				$rememberArray = JUserHelper::getRememberCookieData();

				// There are no cookies to delete.
				if ($rememberArray === false) {
					return true;
				}

				list($privateKey, $series, $cookieName) = $rememberArray;

				// Remove the record from the database
				$query = $this->db->getQuery(true);

				if (version_compare($this->joomlaVersion, '3.2.0', 'eq')) {
					$query
						->delete('#__user_keys')
						->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName))
						->where($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($series)))
						->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['username']));
				} else {
					$query
						->delete('#__user_keys')
						->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName))
						->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['username']));
				}

				$this->db->setQuery($query)->execute();

				// Destroy the cookie
				$this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path'), $this->app->get('cookie_domain'));
			}

			return true;
		}
		
		public static function setDefaultEncryption($userPluginParams) {
			if (version_compare($this->joomlaVersion, '3.2.0', 'ge')) {
				if ($userPluginParams->get('strong_passwords') == 1) {
					return 'bcrypt';
				}
			}
			return 'md5-hex';
		}
	}
} else {
	$plgUserJoomla = realpath(dirname(__FILE__) . '/../joomla/joomla.php');
	require_once ($plgUserJoomla);
	
	class plgUserExtendedreguser extends plgUserJoomla {
		// Nothing else to do
	}
}

