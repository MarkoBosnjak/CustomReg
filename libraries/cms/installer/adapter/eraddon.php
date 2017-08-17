<?php 
/**
 * @package		ExtendedReg
 * @version		1.0
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/libraries/jvitals/adapter.php');

class JInstallerEraddon extends JvitalsAdapter {
	
	function __construct($parent, $db, $options) {
		$this->component = 'extendedreg';
		$this->adapter_type = 'eraddon';
		$this->params_xpath = 'config/params';
		parent::__construct($parent, $db, $options);
	}
	
	public function install() {
		
		// Get the extension manifest object
		$this->manifest = $this->parent->getManifest();
		if (!($this->manifest && is_object($this->manifest) && is_a($this->manifest, 'SimpleXMLElement'))) {
			$this->setMsg(JText::_(strtoupper($this->adapter_type) . '_INSTALL_NOMANIFEST_ERROR'), 'error');
			$this->displayMsgs();
			return false;
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */
		
		// Set the extensions name
		$name = (string)$this->manifest->name;
		$this->disp_name = trim(str_replace(array('Addon', 'addon'), '', $name));
		
		$fileName = strtolower(JFilterInput::getInstance()->clean((string)$this->manifest->filename, 'cmd'));
		
		$description = (string)$this->manifest->description;
		
		$published = (string)$this->manifest->published;
		$published = (int)$published;
		
		$author = (string)$this->manifest->author;
		
		$authorEmail = (string)$this->manifest->authorEmail;
		
		$authorUrl = (string)$this->manifest->authorUrl;
		//~ if (!$this->validateUrl($authorUrl)) $authorUrl = '';
		
		$license = (string)$this->manifest->license;
		
		$version = (string)$this->manifest->version;
		
		$type = (string)$this->manifest->type;
		
		$this->uninstall_folder = $type;
		$this->uninstall_info .= $this->uninstall_folder . DIRECTORY_SEPARATOR;
		
		/**
		* ---------------------------------------------------------------------------------------------
		* Filesystem Processing Section
		* ---------------------------------------------------------------------------------------------
		*/
		
		// Find files and languages to copy to frontend or backend
		$this->parseFiles('files');
		$this->parseFiles('administration/files', 1);		
		$this->parseLanguages('languages');
		$this->parseLanguages('administration/languages', 1);
		
		/**
		* ---------------------------------------------------------------------------------------------
		* Database Processing Section
		* ---------------------------------------------------------------------------------------------
		*/
		
		$this->dbo->setQuery("SELECT " . $this->dbo->quoteName('id') . " FROM #__extendedreg_addons WHERE " . $this->dbo->quoteName('file_name') . " = " . $this->dbo->Quote($fileName) . " AND " . $this->dbo->quoteName('type') . " = " . $this->dbo->Quote($type));
		$addon_id = (int)trim($this->dbo->loadResult());
		
		if (!(int)$addon_id) {
			$fresh_install = true;
			$query = "INSERT INTO #__extendedreg_addons (" . $this->dbo->quoteName('name') . ", " . $this->dbo->quoteName('file_name') . ", " . $this->dbo->quoteName('author') . ", " 
				. $this->dbo->quoteName('author_email') . ", " . $this->dbo->quoteName('author_url') . ", " . $this->dbo->quoteName('description') . ", " . $this->dbo->quoteName('license') . ", " . $this->dbo->quoteName('version') . ", " . $this->dbo->quoteName('published') . ", " . $this->dbo->quoteName('type') . ") " . 
				"VALUES (" . $this->dbo->Quote($name) . ", " . $this->dbo->Quote($fileName) . ", " . $this->dbo->Quote($author) . ", " 
				. $this->dbo->Quote($authorEmail) . ", " . $this->dbo->Quote($authorUrl) . ", " . $this->dbo->Quote($description) . ", " . $this->dbo->Quote($license) . ", "
				. $this->dbo->Quote($version) . ", " . $this->dbo->Quote($published). ", " . $this->dbo->Quote($type) . ")";
		} else {
			$fresh_install = false;
			$query = "UPDATE #__extendedreg_addons SET " . 
						$this->dbo->quoteName('name') . " = " . $this->dbo->Quote($name) . ", " . 
						$this->dbo->quoteName('author') . " = " . $this->dbo->Quote($author) . ", " . 
						$this->dbo->quoteName('author_email') . " = " . $this->dbo->Quote($authorEmail) . ", " . 
						$this->dbo->quoteName('author_url') . " = " . $this->dbo->Quote($authorUrl) . ", " . 
						$this->dbo->quoteName('description') . " = " . $this->dbo->Quote($description) . ", " . 
						$this->dbo->quoteName('license') . " = " . $this->dbo->Quote($license) . ", " . 
						$this->dbo->quoteName('version') . " = " . $this->dbo->Quote($version) . 
					" WHERE " . $this->dbo->quoteName('id') . " = " . $addon_id;
		}
		
		$this->dbo->setQuery($query);
		if (!$this->dbo->execute()) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_DB_ERROR', $this->disp_name, $this->dbo->getErrorMsg()), 'error');
			$this->displayMsgs();
			return false;
		}
		
		if (!(int)$addon_id) {
			$addon_id = (int)$this->dbo->insertid();
		}
		
		// SQL files
		$path_uninstall = array();
		$sql = $this->manifest->sql;
		if ($sql && is_object($sql) && is_a($sql, 'SimpleXMLElement')) {
			$folder = isset($sql['folder']) ? (string)$sql['folder'] : '';
			if (strlen($folder)) {
				$extension_administrator = $this->parent->getPath('source') . DIRECTORY_SEPARATOR . $folder;
			} else {
				$extension_administrator = $this->parent->getPath('source');
			}
			$this->parent->setPath('extension_administrator', $extension_administrator);
			
			$install_sql = $this->manifest->sql->install->file;
			if ($install_sql && is_object($install_sql) && is_a($install_sql, 'SimpleXMLElement') && strlen(trim((string)$install_sql))) {
				$sqlfile = $extension_administrator . DIRECTORY_SEPARATOR . basename(trim((string)$install_sql));
				$utfresult = $this->parseQueries($sqlfile);
				if ($utfresult !== true) {
					$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_SQLFILE_ERROR', $this->disp_name, $sqlfile . ' ' . (string)$utfresult), 'error');
					$this->displayMsgs();
					return false;
				}
			}
			
			// execute queries if installing for the first time
			if ($fresh_install) {
				$fresh_sql = $this->manifest->sql->fresh->file;
				if ($fresh_sql && is_object($fresh_sql) && is_a($fresh_sql, 'SimpleXMLElement') && strlen(trim((string)$fresh_sql))) {
					$freshsqlfile = $extension_administrator . DIRECTORY_SEPARATOR . basename(trim((string)$fresh_sql));
					$utfresult = $this->parseQueries($freshsqlfile);
					if ($utfresult !== true) {
						$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_SQLFILE_ERROR', $this->disp_name, $freshsqlfile . ' ' . (string)$utfresult), 'error');
						$this->displayMsgs();
						return false;
					}
				}
			}
			
			// We will copy the uninstall file into the uninstall folder
			$uninstall_sql = $this->manifest->sql->uninstall->file;
			if ($uninstall_sql && is_object($uninstall_sql) && is_a($uninstall_sql, 'SimpleXMLElement') && strlen(trim((string)$uninstall_sql))) {
				$path_uninstall['src'] = $extension_administrator . DIRECTORY_SEPARATOR . basename(trim((string)$uninstall_sql));
				$path_uninstall['dest'] = $this->uninstall_info . basename(trim((string)$uninstall_sql));
			}
		}

		/**
		* ---------------------------------------------------------------------------------------------
		* Finalization and Cleanup Section
		* ---------------------------------------------------------------------------------------------
		*/
		
		// exec install script
		$install_script = dirname($this->parent->getPath('manifest')) . DIRECTORY_SEPARATOR . 'install.' . $fileName  . '.php';
		if (is_file($install_script)) {
			include_once $install_script;
		}
		
		// copy manifest to uninstall folder
		$copyfiles = array();
		$path = array();
		$path['type'] = 'file';
		$path['src'] = $this->parent->getPath('manifest');
		$path['dest'] = $this->uninstall_info . $fileName . '.xml';
		$copyfiles[] = $path;
		
		// copy uninstall sql to uninstall folder
		if (count($path_uninstall)) {
			$copyfiles[] = $path_uninstall;
		}
		
		// copy uninstall script file to uninstall folder
		$uninstall_script = dirname($path['src']) . DIRECTORY_SEPARATOR . 'uninstall.' . $fileName  . '.php';
		if (is_file($uninstall_script)) {
			$path_user1['type'] = 'file';
			$path_user1['src'] = $uninstall_script;
			$path_user1['dest'] = $this->uninstall_info . basename($path_user1['src']);
			$copyfiles[] = $path_user1;
		}
		
		// Check if the addon uninstall folder exists
		if (!is_dir($this->uninstall_info)) {
			if (!JFolder::create($this->uninstall_info)) {
				$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_CREATEDIR_ERROR', $this->disp_name, $this->disp_name), 'error');
				$this->displayMsgs();
				return false;
			}
		}
		if (!$this->parent->copyFiles($copyfiles, true)) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_COPYMANIFEST_ERROR', $this->disp_name), 'error');
			$this->displayMsgs();
			return false;
		}
		
		// delete old manifest file
		$old_manifest_path = $this->uninstall_info . $fileName . '30.xml';
		if (is_file($old_manifest_path)) JFile::delete($old_manifest_path);
		
		$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_ADDONS_OK', $this->disp_name));
		
		$this->displayMsgs();
		return true;
	}
	
	public function update() {
		$this->install();
	}
	
	public function uninstall($uninstall_item) {
		
		// Get the addon data
		$this->dbo->setQuery("SELECT * FROM #__extendedreg_addons WHERE " . $this->dbo->quoteName('id') . " = " . $this->dbo->Quote($uninstall_item));
		$addon_data = $this->dbo->loadObject();
		$this->disp_name = trim(str_replace(array('Addon', 'addon'), '', $addon_data->name));
		
		$uninstall_info = $this->uninstall_info . $addon_data->type . DIRECTORY_SEPARATOR;
		
		// Check if the manifest XML exists
		$old_manifest_path = $uninstall_info . $addon_data->file_name . '30.xml';
		if (is_file($old_manifest_path)) JFile::delete($old_manifest_path);
		$manifest_path = $uninstall_info . $addon_data->file_name . '.xml';
		
		if (!is_file($manifest_path)) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_UNINSTALL_NOMANIFEST_ERROR', $this->disp_name), 'error');
			$this->displayMsgs();
			return false;
		}
		
		// Get the manifest XML
		$installer = JInstaller::getInstance();
		$this->manifest = $installer->isManifest($manifest_path);
		if (!($this->manifest && is_object($this->manifest) && is_a($this->manifest, 'SimpleXMLElement'))) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_UNINSTALL_EMPTYMANIFEST_ERROR', $this->disp_name), 'error');
			$this->displayMsgs();
			return false;
		}
		
		/**
		* ---------------------------------------------------------------------------------------------
		* Filesystem Processing Section
		* ---------------------------------------------------------------------------------------------
		*/
		
		// Find files and languages to delete in frontend or backend
		$this->deleteFiles('files');
		$this->deleteFiles('administration/files', 1);
		$this->deleteLanguages('languages');
		$this->deleteLanguages('administration/languages', 1);
		
		/**
		* ---------------------------------------------------------------------------------------------
		* Database Processing Section
		* ---------------------------------------------------------------------------------------------
		*/
		
		// Addons table
		
		$this->dbo->setQuery("DELETE FROM #__extendedreg_addons WHERE " . $this->dbo->quoteName('id') . " = " . $this->dbo->Quote($addon_data->id));
		if (!$this->dbo->execute()) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_DB_ERROR', $this->disp_name, $this->dbo->getErrorMsg()), 'error');
			$this->displayMsgs();
			return false;
		}
		
		// SQL files
		$uninstall_sql = '';
		$uninstall_data = $this->manifest->xpath('sql/uninstall/file');
		if ($uninstall_data && is_array($uninstall_data) && isset($uninstall_data[0]) && is_a($uninstall_data[0], 'SimpleXMLElement')) {
			$uninstall_data = $uninstall_data[0];
			$uninstall_sql = $uninstall_info . basename(trim((string)$uninstall_data));
			if (is_file($uninstall_sql)) {
				$buffer = file_get_contents($uninstall_sql);
				if ($buffer) {
					jimport('joomla.installer.helper');
					$queries = JInstallerHelper::splitSql($buffer);
					if (count($queries)) {
                        foreach ($queries as $query) {
							$query = trim($query);
							if ($query != '' && $query{0} != '#') {
								$this->dbo->setQuery($query);
								if (!$this->dbo->execute()) {
									$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_DB_ERROR', $this->disp_name, $this->dbo->getErrorMsg()), 'error');
									$this->displayMsgs();
									return false;
								}
							}
                        }
					}
				}
			}
		}
		
		/**
		* ---------------------------------------------------------------------------------------------
		* Delete manifest XML and other files
		* ---------------------------------------------------------------------------------------------
		*/

		// exec uninstall script and delete it after this
		$uninstall_script = $uninstall_info . 'uninstall.' . $addon_data->file_name . '.php';
		if (is_file($uninstall_script)) {
			include_once $uninstall_script;
			JFile::delete($uninstall_script);
		}
		
		// delete manifest
		JFile::delete($manifest_path);
		
		// delete uninstall sql file
		if ($uninstall_sql && is_file($uninstall_sql)) {
			JFile::delete($uninstall_sql);
		}
		
		$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_UNINSTALL_ADDONS_OK', $this->disp_name));
		
		$this->displayMsgs();
		return true;
	}
}

class JInstallerAdapterEraddon extends JInstallerEraddon {
}
