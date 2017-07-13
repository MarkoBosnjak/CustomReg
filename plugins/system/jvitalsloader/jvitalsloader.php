<?php
/**
* @package		jVitals Library Loader
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

function jvitalsLibAutoloader($className) {
	if (preg_match('~^Jvitals(.+?)$~', $className, $m)) {
		$inclFile = JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jvitals' . DIRECTORY_SEPARATOR . strtolower($m[1]) . '.php';
		if (is_file($inclFile)) {
			require_once ($inclFile);
			return true;
		}
	}
	return false;
}

// For use in installers
function jvitalsLibInit() {
	$libraryDir = JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jvitals';
	
	if (is_dir($libraryDir)) {		
		// Create MB functions if they do not exist
		$mbFile = $libraryDir . DIRECTORY_SEPARATOR . 'mbstring' . DIRECTORY_SEPARATOR . 'mbstring.php';
		if (is_file($mbFile)) {
			require_once ($mbFile);
		}
		// Register the autoload function
		spl_autoload_register('jvitalsLibAutoloader');
	}
}

class plgSystemJvitalsloader extends JPlugin {
	
	public function onAfterInitialise() {
		jvitalsLibInit();
	}
	
}
