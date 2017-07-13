<?php
/**
 * @package		ExtendedReg - System
 * @version		1.01
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemExtendedregsystem extends JPlugin {

	public function onAfterDispatch() {
		$app = JFactory::getApplication();
		
		$init = JPATH_ROOT . DIRECTORY_SEPARATOR .  'administrator' . DIRECTORY_SEPARATOR .  'components' . DIRECTORY_SEPARATOR .  'com_extendedreg' . DIRECTORY_SEPARATOR .  'helpers' . DIRECTORY_SEPARATOR .  'initiate.php';

		if (is_file($init)) {
			require_once ($init);
			
			$model = JvitalsHelper::loadModel('extendedreg', 'Default');
			$conf = $model->getConfObj();
			
			if ($app->isAdmin()) {
				if ((int)$conf->use_secret_hash && trim($conf->secret_hash)) {
					$user = JFactory::getUser();
					$session = JFactory::getSession();
					$alreadyChecked = (int)$session->get('erSecretHashAuthentication', 0, 'extendedreg');
					
					if (!$alreadyChecked) {
						if (!(int)$user->id && $conf->secret_hash != $_SERVER['QUERY_STRING']) {
							$app->redirect(JURI::root());
						} else {
							$session->set('erSecretHashAuthentication', 1, 'extendedreg');
						}
					}
				}
			} else {
				$uri = JFactory::getURI();
				$query = $uri->getQuery(true);
				$user = JFactory::getUser();
				
				if ((int)$user->id && (int)$conf->use_password_renew && (int)$conf->password_renew_period) {
					$current_time = JvitalsTime::getUtc()->format('U', true);
					$last_change_time = JvitalsTime::getUtc($user->extreg->last_pass_change, 'user')->format('U', true);
					$last_change_user = JvitalsTime::getUtc($user->extreg->last_pass_change, 'user')->format('d/m/Y H:i', true);
					
					// password_renew_period is in days
					if ($current_time > ($last_change_time + ((int)$conf->password_renew_period * 24 * 60 * 60))) {
						// Too long time without password change
						if (!($_REQUEST['option'] == 'com_extendedreg' && $_REQUEST['controller'] == 'users' && ($_REQUEST['task'] == 'profile' || $_REQUEST['task'] == 'do_save'))) {
							erHelperLanguage::load();
							if ($user->extreg->last_pass_change == '0000-00-00 00:00:00') {
								$renew_warning = JText::_('COM_EXTENDEDREG_PASSWORD_RENEW_WARNING_NEVER');
							} else {
								$renew_warning = JText::sprintf('COM_EXTENDEDREG_PASSWORD_RENEW_WARNING', $last_change_user);
							}
							
							$app->redirect(JRoute::_('index.php?option=com_extendedreg&task=users.profile', false), $renew_warning, 'warning');
							exit;
						}
					}
				}
				
				if (isset($_REQUEST['option']) && in_array($_REQUEST['option'], array('com_user', 'com_users'))) {
					if (count($_POST)) {
						// Make sure login and logout works
						if (isset($_REQUEST['task']) && in_array($_REQUEST['task'], array('user.logout', 'user.login', 'logout', 'login'))) {
							$_REQUEST['option'] = 'com_extendedreg';
							
							if (isset($_REQUEST['return'])) {
								$_REQUEST['lret'] = $_POST['lret'] = $_REQUEST['return'];
								unset($_REQUEST['return']);
								unset($_POST['return']);
							}
							
							if ($_REQUEST['task'] == 'user.login' || $_REQUEST['task'] == 'login') {
								$_REQUEST['task'] = $_POST['task'] = 'users.do_login';
								
								if (isset($_REQUEST['password'])) {
									$_REQUEST['passwd'] = $_POST['passwd'] = $_REQUEST['password'];
									unset($_REQUEST['password']);
									unset($_POST['password']);
								}
							} elseif ($_REQUEST['task'] == 'user.logout' || $_REQUEST['task'] == 'logout') {
								$_REQUEST['task'] = $_POST['task'] = 'users.logout';
							}
						}
					} else {
						// Make sure register, remind and etc links work
						$redir = false;
						if (isset($query['view'])) {
							if ($query['view'] == 'register' || $query['view'] == 'registration') {
								$query['task'] = 'users.register';
								$redir = true;
							} elseif ($query['view'] == 'reset') {
								$query['task'] = 'users.reset';
								$redir = true;
							} elseif ($query['view'] == 'remind') {
								$query['task'] = 'users.remind';
								$redir = true;
							}
							
							if ((int)$conf->redirect_login_screens && $query['view'] == 'login') {
								$query['task'] = 'users.login';
								$redir = true;
							}
						}
						
						if ($redir) {
							unset($query['view']);
							$query['option'] = 'com_extendedreg';
							$uri->setQuery($query);
							$url = JRoute::_('index.php' . $uri->toString(array('query', 'fragment')), false);
							$app->redirect($url);
							exit;
						}
					}
				}
				
				$integrations = erHelperAddons::loadAddons('integration');
				foreach ($integrations as $record) {
					$lib = erHelperAddons::getIntegration($record, false);
					if (!$lib) continue; 
					if (method_exists($lib, "doRedirect")) {
						$newquery = $lib->doRedirect($query);
						if (!is_array($newquery)) $newquery = array();
						if (count($newquery)) {
							$diff = array_diff(array_merge($newquery, $query), array_intersect($newquery, $query));
							if (count($diff)) {
								$url = JRoute::_('index.php' . $uri->toString(array('query', 'fragment')), false);
								$app->redirect($url);
								exit;
							}
						}
					}
				}
			}
		}
		
		return true;
	}

}

