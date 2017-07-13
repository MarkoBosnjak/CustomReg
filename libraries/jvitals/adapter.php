<?php
/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class JvitalsAdapter extends JObject {
	protected $returnUrl = '';
	protected $db = null;
	protected $uninstall_info = '';
	protected $disp_name = '';
	protected $messages = array();
	protected $component = ''; // e.g. agorapro
	protected $adapter_type = ''; // e.g. agaddon
	protected $uninstall_folder = ''; // e.g. addons
	protected $params_xpath = ''; // e.g. config/fields/fieldset
	public $manifest = null;
	
	function __construct($parent, $db, $options) {
		$this->parent = $parent;
		$this->dbo = $db;
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$this->uninstall_info = JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_' . $this->component . DIRECTORY_SEPARATOR  . 'uninstall_info' . DIRECTORY_SEPARATOR;
		if ($this->uninstall_folder != '') $this->uninstall_info .= $this->uninstall_folder . DIRECTORY_SEPARATOR;
	}
	
	// this method needs to be public as it is called by JInstaller
	public function loadLanguage($path) {
		JFactory::getLanguage()->load('files_adapter_' . $this->adapter_type, JApplicationHelper::getClientInfo(0)->path);
	}
	
	protected function getFilesPath($cid = 0) {
		if ((int)$cid == 1) {
			$dest = JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_' . $this->component . DIRECTORY_SEPARATOR;
		} else {
			$dest = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_' . $this->component . DIRECTORY_SEPARATOR;
		}
		return $dest;
	}	
	
	protected function getLanguagesPath($cid = 0) {
		if ((int)$cid == 1) {
			$dest = JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
		} else {
			$dest = JPATH_SITE . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
		}
		return $dest;
	}

	protected function getHooksHash($addon_id, $folderName) {
		return md5('adn_' . (int)$addon_id . '_' . $folderName);
	}
	
	protected function getParams() {
		$fieldset = $this->manifest->xpath($this->params_xpath);
		if ($fieldset && is_array($fieldset) && isset($fieldset[0]) && is_a($fieldset[0], 'SimpleXMLElement')) {
			// Creating the data collection variable:
			$ini = array();
			foreach($fieldset[0]->children() as $field) {
				$name = trim((string)$field['name']);
				$value = trim((string)$field['default']);
				if (!strlen($name) || !strlen($value)) {
					continue;
				}
				$ini[(string)$name] = (string)$value;
			}
			return json_encode($ini);
		} else {
			return '{}';
		}
	}

	public function parseFiles($xpath, $cid = 0) {
		
		$destPath = $this->getFilesPath($cid);
		
		$files = $this->manifest->xpath($xpath);
		if (!($files && is_array($files) && isset($files[0]) && is_a($files[0], 'SimpleXMLElement') && count($files[0]))) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return;
		}
		$files = $files[0];
		
		$folder = isset($files['folder']) ? (string)$files['folder'] : '';
		if (strlen($folder)) {
			$source = $this->parent->getPath('source') . DIRECTORY_SEPARATOR . $folder;
		} else {
			$source = $this->parent->getPath('source');
		}
		
		$copyfiles = array();
		foreach ($files->children() as $file) {
			$path = array();
			
			// get the path from the xml file (strip leading and trailing slashes), get the basename and the dir name
			$filedata = trim(trim((string)$file), '/');
			$basename = basename($filedata);
			$dirname = dirname($filedata);
			// replace slashes in the path with the actual directory separator
			$dirname = str_replace('/', DIRECTORY_SEPARATOR, $dirname);
			
			// Is this path a file or folder?
			$path['type'] = (trim($file->getName()) == 'folder') ? 'folder' : 'file';
			
			// build the source path - here we check also if src attribute is present which overrides the path tot he file/folder
			// if it is empty then we take the files from the source root
			$src_dirname = isset($file['src']) ? str_replace('/', DIRECTORY_SEPARATOR, (string)$file['src']) : $dirname;
			$path['src'] = $source . DIRECTORY_SEPARATOR . ($src_dirname ? $src_dirname . DIRECTORY_SEPARATOR : '') . $basename;
			
			// build the destination path (for folder we copy in the parent)
			//~ $path['dest'] = ($path['type'] == 'folder') ? $destPath . $dirname : $destPath . $dirname . DIRECTORY_SEPARATOR . $basename;
			$path['dest'] = $destPath . $dirname . DIRECTORY_SEPARATOR . $basename;

			/*
			 * Before we can add a file to the copyfiles array we need to ensure
			 * that the folder we are copying our file to exits and if it doesn't,
			 * we need to create it.
			 */
			$newdir = $destPath . $dirname;
			if (!JFolder::create($newdir)) {
				$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_CREATEDIR_ERROR', $this->disp_name, $newdir), 'error');
				return false;
			}
			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}
		
		if (!$this->parent->copyFiles($copyfiles, true)) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_COPYFILES_ERROR', $this->disp_name), 'error');
			return false;
		}
		
		return true;
	}

	public function deleteFiles($xpath, $cid = 0) {
		
		$destPath = $this->getFilesPath($cid);
		
		$files = $this->manifest->xpath($xpath);
		if (!($files && is_array($files) && isset($files[0]) && is_a($files[0], 'SimpleXMLElement') && count($files[0]))) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return;
		}
		$files = $files[0];
		
		foreach ($files->children() as $file) {
			
			// get the path from the xml file (strip leading and trailing slashes), get the basename and the dir name
			$filedata = trim(trim((string)$file), '/');
			$basename = basename($filedata);
			$dirname = dirname($filedata);
			// replace slashes in the path with the actual directory separator
			$dirname = str_replace('/', DIRECTORY_SEPARATOR, $dirname);
			
			// build file/folder to delete
			$path_dest = $destPath . $dirname . DIRECTORY_SEPARATOR . $basename;
			if (trim($file->getName()) == 'folder') {
				if (JFolder::exists($path_dest)) $result = JFolder::delete($path_dest);
			} else {
				if (JFile::exists($path_dest)) $result = JFile::delete($path_dest);
			}
			if (!$result) {
				$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_DELETE_ERROR', $this->disp_name, $path_dest), 'error');
				return false;
			}
		}
		return true;
	}

	public function parseLanguages($xpath, $cid = 0) {
		
		// Initialize variables
		$copyfiles = array ();
		
		$files = $this->manifest->xpath($xpath);		
		if (!($files && is_array($files) && isset($files[0]) && is_a($files[0], 'SimpleXMLElement') && count($files[0]))) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return;
		}
		$files = $files[0];

		$destination = $this->getLanguagesPath($cid);
		
		$folder = isset($files['folder']) ? (string)$files['folder'] : '';
		if (strlen($folder)) {
			$source = $this->parent->getPath('source') . DIRECTORY_SEPARATOR . $folder;
		} else {
			$source = $this->parent->getPath('source');
		}

		foreach ($files->children() as $file) {
			$path = array();
			$path['type'] = 'file';
			$path['src'] = $source . DIRECTORY_SEPARATOR . trim((string)$file);
			
			$tag = (string)$file['tag'];
			if ($tag != '') {
				$path['dest'] = $destination . $tag . DIRECTORY_SEPARATOR . basename((string)$file);
			} else {
				$path['dest'] = $destination . (string)$file;
			}
			
			/*
			 * Before we can add a file to the copyfiles array we need to ensure
			 * that the folder we are copying our file to exits and if it doesn't,
			 * we need to create it.
			 */
			if (basename($path['dest']) != $path['dest']) {
				$newdir = dirname($path['dest']);
				if (!JFolder::create($newdir)) {
					$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_CREATEDIR_ERROR', $this->disp_name, $newdir), 'error');
					return false;
				}
			}

			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}
		
		if (!$this->parent->copyFiles($copyfiles, true)) {
			$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_COPYLANGS_ERROR', $this->disp_name), 'error');
			return false;
		}
		
		return true;
	}
	
	public function deleteLanguages($xpath, $cid = 0) {
		
		$files = $this->manifest->xpath($xpath);		
		if (!($files && is_array($files) && isset($files[0]) && is_a($files[0], 'SimpleXMLElement') && count($files[0]))) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return;
		}
		$files = $files[0];

		$destination = $this->getLanguagesPath($cid);
		
		foreach ($files->children() as $file) {
			$tag = (string)$file['tag'];
			if ($tag != '') {
				$path_dest = $destination . $tag . DIRECTORY_SEPARATOR . basename((string)$file);
			} else {
				$path_dest = $destination . (string)$file;
			}
			
			if (JFile::exists($path_dest)) {
				$result = JFile::delete($path_dest);
				if (!$result) {
					$this->setMsg(JText::sprintf(strtoupper($this->adapter_type) . '_INSTALL_DELETE_ERROR', $this->disp_name, $path_dest), 'error');
					return false;
				}
			}
		}
		
		return true;
	}
	
	protected function parseQueries($sqlfile) {
	
		if (!file_exists($sqlfile)) {
			return JText::_(strtoupper($this->adapter_type) . '_SQL_FILE_ERROR');
		}
		$buffer = file_get_contents($sqlfile);
		if ($buffer === false) {
			return JText::_(strtoupper($this->adapter_type) . '_INSTALL_SQL_FILE_CANT_READ');
		}
		// Create an array of queries from the sql file
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($buffer);

		if (count($queries) == 0) {
			return true;
		}
		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query{0} != '#') {
				$this->dbo->setQuery($query);
				if (!$this->dbo->execute()) {
					return $this->dbo->getErrorMsg();
				}
			}
		}
		
		return true;
	}
	
	public function validateUrl($url) {
		$url = strtolower($url);
		return preg_match('~^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$~i', $url);
	}
	
	public function setMsg($msg, $type = 'message') {
		if (!isset($this->messages[$type])) $this->messages[$type] = array();
		$exists = array_search($msg, $this->messages[$type]);
		if (is_null($exists) || ($exists === false)) $this->messages[$type][] = $msg;
	}
	public function displayMsgs() {
		if (count($this->messages)) {
			foreach ($this->messages as $type => $messages) {
				if (is_array($messages) && count($messages)) {
					JFactory::getApplication()->enqueueMessage(implode('<br />', $messages), $type);
					/*
					// alternative display
					foreach ($messages as $message) {
						JFactory::getApplication()->enqueueMessage($message, $type);
					}
					*/
				}
			}
			$this->messages = array();
		}
	}
	
}
