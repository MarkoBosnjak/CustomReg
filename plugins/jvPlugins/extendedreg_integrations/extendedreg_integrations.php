<?php
/**
 * @package		ExtendedReg Integrations
 * @version		2.01
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgJvPluginsExtendedreg_integrations extends JPlugin {

	public function onExtregUserRegister($form_id = 0, $joomla_user = null, $er_user = null) {
		$this->performIntegrationActions($form_id, $joomla_user, $er_user);
	}
	
	public function onExtregUserProfile($form_id = 0, $joomla_user = null, $er_user = null) {
		$this->performIntegrationActions($form_id, $joomla_user, $er_user);
	}
	
	private function performIntegrationActions($form_id = 0, $joomla_user = null, $er_user = null) {
		require_once (JPATH_ROOT . DIRECTORY_SEPARATOR .  'administrator' . DIRECTORY_SEPARATOR .  'components' . DIRECTORY_SEPARATOR .  'com_extendedreg' . DIRECTORY_SEPARATOR .  'helpers' . DIRECTORY_SEPARATOR .  'initiate.php');
		$integrations = erHelperAddons::loadAddons('integration');
		foreach ($integrations as $record) {
			$lib = erHelperAddons::getIntegration($record);
			if (method_exists($lib, "doPerfomActions")) {
				$lib->doPerfomActions($form_id, $joomla_user, $er_user);
			}
		}
	}

}

