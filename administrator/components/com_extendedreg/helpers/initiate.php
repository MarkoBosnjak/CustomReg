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

@define('ER_VERSION', '2.11');

// For now we keep those constants so we can handle legacy addons
@define('ER_BACK_PATH', JvitalsDefines::comBackPath('com_extendedreg'));
@define('ER_BACK_LIVEPATH', JvitalsDefines::comBackPath('com_extendedreg', true));
@define('ER_FRONT_PATH', JvitalsDefines::comFrontPath('com_extendedreg'));
@define('ER_FRONT_LIVEPATH', JvitalsDefines::comFrontPath('com_extendedreg', true));

// Create some functions if they are missing
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'functions.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'interfaces.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'assets' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'field_prototype.php');

require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'router.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'models.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'language.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'javascript.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'html.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'mail.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'addons.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'password.php');
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'hooks.php');

global $extreg_hooks;
if (!($extreg_hooks && is_array($extreg_hooks))) {
	$extreg_hooks = array();
}