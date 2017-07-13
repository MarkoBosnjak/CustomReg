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

class Pkg_ExtendedRegInstallerScript {

	public function preflight($type, $parent) {
		// $manifest = $parent->getParent()->getManifest();
		if (!$this->checkExtensions()) return false;
		return true;
	}
	
	public function postflight($type, $parent) {
		// Clear Joomla system cache.
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache();
		$cache->clean('_system');

		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache')) {
			@apc_clear_cache();
		}

		if ($type == 'uninstall') return true;
		$this->enablePlugin('system', 'jvitalsloader');
		$this->enablePlugin('authentication', 'extendedregauth');
		$this->enablePlugin('jvPlugins', 'extendedreg_integrations');
		$this->enablePlugin('system', 'extendedregsystem');
		$this->enablePlugin('user', 'extendedreguser');
		return true;
	}

	function enablePlugin($group, $element) {
		$plugin = JTable::getInstance('extension');
		if (!$plugin->load(array('type' => 'plugin', 'folder' => $group, 'element' => $element))) {
			return false;
		}
		$plugin->enabled = 1;
		return $plugin->store();
	}

	protected function checkExtensions() {
		$app = JFactory::getApplication();
		$pass = 1;
		foreach (array('gd', 'json', 'pcre', 'SimpleXML') as $name) {
			if (!extension_loaded($name)) {
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}
		return $pass;
	}

	public function install($parent) {
		return true;
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
		return self::install($parent);
	}

	public function uninstall($parent) {
		return true;
	}

}
