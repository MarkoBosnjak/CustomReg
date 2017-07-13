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

class erHelperHooks {
	
	public static function add_hook($hook_id, $code) {
		global $extreg_hooks;
		if (!isset($extreg_hooks[$hook_id])) $extreg_hooks[$hook_id] = array();
		$extreg_hooks[$hook_id][] = $code;
	}
	
	public static function get_hook($hook_id) {
		global $extreg_hooks, $_PROFILER;
		if (isset($extreg_hooks[$hook_id])) {
			// mark the call of this hook
			JDEBUG ? $_PROFILER->mark('get hook ' . $hook_id) : null;
			$arr = array_unique($extreg_hooks[$hook_id]);
			return implode("\n", $arr);
		}
		return false;
	}
	
	public static function loadHooks($type) {
		$dir = JvitalsDefines::comBackPath('com_extendedreg') . 'hooks' . DIRECTORY_SEPARATOR . $type;
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$hooksArray = array();
						if (is_file($dir . DIRECTORY_SEPARATOR . $file)) {
							include_once ($dir . DIRECTORY_SEPARATOR . $file);
							if (count($hooksArray)) {
								foreach ($hooksArray as $hook_id => $code) {
									erHelperHooks::add_hook(trim($hook_id), trim($code));
								}
							}
						}
					}
				}
				closedir($handle);
			}
		}
		return true;
	}
	
}