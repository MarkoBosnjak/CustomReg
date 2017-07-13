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

/**
 * Script file of HelloWorld component
 */
class com_extendedregInstallerScript {
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) {
		// Nothing to do
	}
	
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) {
		$app = JFactory::getApplication();
		if (!(function_exists("jvitalsLibAutoloader") && JPluginHelper::isEnabled('system', 'jvitalsloader'))) {
			$plugin = JTable::getInstance('extension');
			if (!$plugin->load(array('type' => 'plugin', 'folder' => 'system', 'element' => 'jvitalsloader'))) {
				$app->enqueueMessage('Please install and enable jVitals Library Loader Plugin (plg_system_jvitals-X.XX.zip). We are sorry for the inconvenience.', 'error');
				return false;
			}
			$plugin->enabled = 1;
			if (!$plugin->store()) {
				$app->enqueueMessage('Please install and enable jVitals Library Loader Plugin (plg_system_jvitals-X.XX.zip). We are sorry for the inconvenience.', 'error');
				return false;
			}
			// Because it is without reload and joomla still don't know about it
			require_once (JPATH_SITE . '/plugins/system/jvitalsloader/jvitalsloader.php');
			jvitalsLibInit();
		}
		
		if (!is_dir(JPATH_LIBRARIES . '/jvitals')) {
			$app->enqueueMessage('Please install and enable jVitals Framework (lib_jvitals-X.XX.zip). We are sorry for the inconvenience.', 'error');
			return false;
		} else {
			require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'initiate.php');
			erHelperLanguage::load();
			
			@define('ER_DO_INSTALL', 1);
			require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'install.php');
			$installer = new extendedregInstall();
			$installer->run();
			
			echo JText::_('COM_EXTENDEDREG_INSTALL_ADMIN_MENUS_HEADER');
			echo JText::sprintf('COM_EXTENDEDREG_INSTALL_ADMIN_MENUS_NOTICE', JRoute::_('index.php?option=com_extendedreg&task=default.clearadminmenus', false));
			
			if (count($installer->logList)) {
				echo JText::_('COM_EXTENDEDREG_INSTALL_LOG');
				foreach ($installer->logList as $entry) {
					echo "<p>" . $entry->message . "</p>";
				}
			}
		}
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) {
		require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'initiate.php');
		erHelperLanguage::load();
		
		$dbo = JFactory::getDBO();
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		if ((int)$conf->remove_tables_on_uninstall) {
			$sqlfile = JvitalsDefines::comBackPath('com_extendedreg') . 'uninstall.extendedreg.sql';
			
			if (!file_exists($sqlfile)) {
				return false;
			}
			$buffer = file_get_contents($sqlfile);
			if ($buffer === false) {
				return false;
			}
			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) == 0) {
				return false;
			}
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query{0} != '#') {
					$dbo->setQuery($query);
					if (!$dbo->execute()) {
						return false;
					}
				}
			}
		}
		
		echo '<div style="width: 100%; font-family: Verdana; color: #333;">
			<fieldset style="background: #fff; padding: 10px; ">
				<h2>ExtendedReg Uninstalled</h2>
				<h3>Make sure you go to Plug-In Manager and:</h3>
				<ol>
					<li><b style="color: #FF0000;">Enable</b> (if it is not enabled) <b>"User - Joomla! plugin"</b></li>
					<li><b style="color: #FF0000;">Enable</b> (if it is not enabled) <b>"Authentication - Joomla"</b></li>
					<li><b style="color: #FF0000;">Disable</b> (if it is not disabled) <b>"ExtendedReg - User"</b></li>
					<li><b style="color: #FF0000;">Disable</b> (if it is not disabled) <b>"ExtendedReg - System"</b></li>
					<li><b style="color: #FF0000;">Disable</b> (if it is not disabled) <b>"ExtendedReg integrations"</b></li>
					<li><b style="color: #FF0000;">Disable</b> (if it is not disabled) <b>"ExtendedReg - Authentication"</b></li>
				</ol>
			</fieldset>
		</div>';
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) {
		return $this->install($parent);
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) {
		// Nothing to do
	}
	
}
