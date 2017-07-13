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

class JvitalsAddon {

	protected static $instance = array();
	protected $dbo;
	
	protected $hooks_prepared = false;
	protected $hooks = array();
	protected $hooks_by_owner = array();
	protected $addon_params = array();
	protected $loaded_addons = array();
	protected $existing_addons = array(0 => array(), 1 => array(), 2 => array());
	
	protected $com = '';
	protected $valid_types = array();
	protected $table_hooks = '';
	protected $table_addons = '';
	protected $path_front = '';
	protected $path_back = '';
	protected $folder_col = '';
	
	/*
	  TODO: think of a way to remove this - may be in each component create a class
	  that inherits JvitalsAddon and sets these things in its own constructor.
	  Or standartize the folder_name columns, addon types (now only 'adn' exists)
	  and pass 'component' from com_component to the constructor.. for the filesystem paths
	  it will be OK but not for the table names (extendeddb != extmovies)
	*/ 
	public function __construct($com) {
		$this->dbo = JFactory::getDbo();
		$this->com = $com;
		
		if ($this->com == 'extmovies') {
			$this->folder_col = 'file_name';
			$this->valid_types = array('movies', 'people', 'adn');
			$this->table_hooks = $this->dbo->quoteName('#__extmovies_hooks');
			$this->table_addons = $this->dbo->quoteName('#__extmovies_hooks');
			$this->path_front = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_extendeddb' . DIRECTORY_SEPARATOR;
			$this->path_back = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_extendeddb' . DIRECTORY_SEPARATOR;
		} elseif ($this->com == 'agorapro') {
			$this->folder_col = 'folder_name';
			$this->valid_types = array('adn');
			$this->table_hooks = $this->dbo->quoteName('#__agorapro_hooks');
			$this->table_addons = $this->dbo->quoteName('#__agorapro_addons');
			$this->path_front = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_agorapro' . DIRECTORY_SEPARATOR;
			$this->path_back = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_agorapro' . DIRECTORY_SEPARATOR;
		}
	}
	
	/*
	public function __construct($com) {
		$this->dbo = JFactory::getDbo();
		$this->com = $com;
		
		$this->valid_types = array('adn');
		$this->path_front = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_' . $this->com . DIRECTORY_SEPARATOR;
		$this->path_back = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_' . $this->com . DIRECTORY_SEPARATOR;
		$this->folder_col = 'folder_name';
		$this->table_hooks = $this->dbo->quoteName('#__' . $this->com . '_hooks');
		$this->table_addons = $this->dbo->quoteName('#__' . $this->com . '_addons');
		
		if ($this->com == 'extendeddb') {
			$this->folder_col = 'file_name';
			$this->table_hooks = $this->dbo->quoteName('#__extmovies_hooks');
			$this->table_addons = $this->dbo->quoteName('#__extmovies_hooks');
		}
	}
	*/
	
	public static function create($com) {
		if (!isset(self::$instance[$com])) {
			try {
				$instance = new JvitalsAddon($com);
			} catch (RuntimeException $e) {
				throw new RuntimeException(sprintf('JvitalsAddon::create: Cannot instantiate class', $e->getMessage()));
			}
			self::$instance[$com] = $instance;
		}
		return self::$instance[$com];
	}

	// JvitalsAddon::create('agorapro')->getAddons();
	public function getAddons() {
		if (!(is_array($this->loaded_addons) && count($this->loaded_addons))) {
			$this->dbo->setQuery('SELECT * FROM ' . $this->table_addons . ' WHERE ' . $this->dbo->quoteName('published') . ' = ' . $this->dbo->Quote('1') . ' ORDER BY id ASC');
			$this->loaded_addons = $this->dbo->loadObjectList($this->folder_col);
		}
		return $this->loaded_addons;
	}
	
	// $end: 0 - frontend, 1 - backend, 2 - both
	// JvitalsAddon::create('extendeddb')->addonExistsAndActive('poster');
	public function addonExistsAndActive($addon, $end = 0) {
		/*
		$addons = $this->getAddons();
		$addon = JPath::clean(JString::strtolower($addon));
		$helper_front = is_file($this->path_front . 'helpers' . DIRECTORY_SEPARATOR . $addon . '.php');
		$helper_back = is_file($this->path_back . 'helpers' . DIRECTORY_SEPARATOR . $addon . '.php');
		$controller_front = is_file($this->path_front . 'controllers' . DIRECTORY_SEPARATOR . $addon . '.php');
		$controller_back = is_file($this->path_back . 'controllers' . DIRECTORY_SEPARATOR . $addon . '.php');
		$manifest = is_file($this->path_back . 'uninstall_info' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . $addon . '.xml');
		
		switch($end) {
			case 1:
				$exists = $controller_back;
				break;
			case 2:
				$exists = ($helper_back || $controller_back || $helper_front || $controller_front || $manifest));
				break;
			case 0:
			default:
				$exists = ($helper_front || $controller_front || $manifest));
		}
		
		if ($exists) {
			return isset($addons[$addon]);
		}
		return false;
		*/
		$existing = $this->getExistingAddons($end);
		return isset($existing[$addon]);
	}
	
	// $end: 0 - frontend, 1 - backend, 2 - both
	// JvitalsAddon::create('agorapro')->getExistingAddons();
	public function getExistingAddons($end = 0) {
		if (!(is_array($this->existing_addons[$end]) && count($this->existing_addons[$end]))) {
			$addons = $this->getAddons();
			foreach($addons as $folder_name => $obj) {
				$addon = JPath::clean(JString::strtolower($folder_name));
				$helper_front = is_file($this->path_front . 'helpers' . DIRECTORY_SEPARATOR . $addon . '.php');
				$helper_back = is_file($this->path_back . 'helpers' . DIRECTORY_SEPARATOR . $addon . '.php');
				$controller_front = is_file($this->path_front . 'controllers' . DIRECTORY_SEPARATOR . $addon . '.php');
				$controller_back = is_file($this->path_back . 'controllers' . DIRECTORY_SEPARATOR . $addon . '.php');
				$manifest = is_file($this->path_back . 'uninstall_info' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . $addon . '.xml');
				
				foreach (array(0, 1, 2) as $e) {
					switch($e) {
						case 1:
							$exists = ($helper_back || $controller_back || $manifest);
							break;
						case 2:
							$exists = ($helper_back || $controller_back || $helper_front || $controller_front || $manifest);
							break;
						case 0:
						default:
							$exists = ($helper_front || $controller_front || $manifest);
					}
					if ($exists) {
						$this->existing_addons[$e][$folder_name] = $obj;
					}
				}
			}
		}
		return $this->existing_addons[$end];	
	}
	
	// JvitalsAddon::create('agorapro')->getAddonParams('notifications');
	public function getAddonParams($addon, $params = '') {
		if ($params != '') {
			return json_decode($params);
		} else {
			if (!(is_array($this->addon_params) && count($this->addon_params) && isset($this->addon_params[$addon]))) {
				$this->dbo->setQuery('SELECT params FROM ' . $this->table_addons . ' WHERE ' . $this->dbo->quoteName($this->folder_col) . ' = ' . $this->dbo->Quote($addon));
				$params = $this->dbo->loadResult();
				$this->addon_params[$addon] = json_decode($params);
			}
			return $this->addon_params[$addon];
		}
	}
	
	// $end 0 - frontend, 1 - backend, 2 - both
	// JvitalsAddon::create('agorapro')->prepareHooks();
	public function prepareHooks($end = 0) {
		if (!$this->hooks_prepared) {
			$this->dbo->setQuery('SELECT * FROM ' . $this->table_hooks . ' WHERE ' . $this->dbo->quoteName('published') . ' = ' . $this->dbo->Quote('1') . ' ORDER BY ' . $this->dbo->quoteName('hook') . ', ' . $this->dbo->quoteName('hash') . ', ' . $this->dbo->quoteName('order'));
			$result = $this->dbo->loadObjectList();
			if (is_array($result) && count($result)) {
				foreach ($result as $obj) {
					$type = trim($obj->type);
					$owner = trim($obj->owner);
					$hook = trim($obj->hook);
					$body = trim($obj->body);
					
					// add only system type or existing and active valid_types or languages
					if (($type == 'sys') || (in_array($type, $this->valid_types) && $this->addonExistsAndActive($owner, $end)) || preg_match('~^.+\.loadLanguages$~', $hook)) {
					
						// by hook
						if (!isset($this->hooks[$hook])) $this->hooks[$hook] = array();
						$this->hooks[$hook][] = $body;
						
						// by owner (for easy access later)
						if (!isset($this->hooks_by_owner[$owner])) $this->hooks_by_owner[$owner] = array();
						if (!isset($this->hooks_by_owner[$owner][$hook])) $this->hooks_by_owner[$owner][$hook] = array();
						$this->hooks_by_owner[$owner][$hook][] = $body;
					}
				}
			}
			$this->hooks_prepared = true;
		}
		return true;
	}
	
	// JvitalsAddon::create('agorapro')->get_hook('admin.loadLanguages');
	public function get_hook($hook_id) {
		$result = '';
		if (isset($this->hooks[$hook_id])) {
			$arr = array_unique($this->hooks[$hook_id]);
			$result = implode("\n", $arr);
		}
		return $result;
	}
	
	// JvitalsAddon::create('agorapro')->get_hook_by_owner('admin.loadLanguages', 'moderation');
	public function get_hook_by_owner($hook_id, $owner)) {
		$result = '';
		if (isset($this->hooks_by_owner[$owner][$hook_id])) {
			$arr = array_unique($this->hooks_by_owner[$owner][$hook_id]);
			$result = implode("\n", $arr);
		}
		return $result;
	}
}
