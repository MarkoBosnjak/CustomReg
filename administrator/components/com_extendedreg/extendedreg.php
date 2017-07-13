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

if (!JvitalsHelper::canDo('core.manage', 'com_extendedreg')) {
	throw new RuntimeException(JText::_('JERROR_ALERTNOAUTHOR'));
}

if (JvitalsDefines::compatibleMode() == '15') {
	throw new RuntimeException(JText::_('COM_EXTENDEDREG_OLD_JOOMLA_WARNING'));
}

if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets'. DIRECTORY_SEPARATOR . 'please_use_installer.php')) {
	throw new RuntimeException(JText::_('COM_EXTENDEDREG_PLEASE_USE_INSTALLER'));
}

require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'initiate.php');

// Some includes only for admin panel
require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'linkblank.php');

erHelperRouter::route();
erHelperJavascript::load();
erHelperLanguage::load();

$app = JFactory::getApplication();
$controller = $app->input->getCmd('controller', 'default');
$task = $app->input->getCmd('task', 'display');

if ($task != 'clearadminmenus') {
	JvitalsHelper::versionNotice('er-version-compare.txt', 'extendedreg', JvitalsDefines::componentVersion('com_extendedreg'));
	erCheckJoomlaSettings();
}

erHelperHooks::loadHooks('admin');

$model = JvitalsHelper::loadModel('extendedreg', 'Default');
$conf = $model->getConfObj();

$document = JFactory::getDocument();
if ((int)$conf->css_back_extreg) {
	if (JvitalsDefines::compatibleMode() != '30>') {
		$document->addStyleSheet(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/css/extendedreg.css');
	} else {
		$document->addStyleSheet(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/css/extendedreg30.css');
	}
}
if ((int)$conf->css_back_jquery) {
	//~ if (JvitalsDefines::compatibleMode() != '30>') {
		//~ $document->addStyleSheet(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/css/smoothness/jquery-ui.css');
	//~ } else {
		$document->addStyleSheet(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/css/smoothness/jquery-ui-joomla3.css');
	//~ }
}

$_ctrlFile = JvitalsDefines::comBackPath('com_extendedreg') . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
if (is_file($_ctrlFile)) {
	require_once ($_ctrlFile);
} else {
	require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'controllers' . DIRECTORY_SEPARATOR . 'default.php');
}

$hook = erHelperHooks::get_hook('admin.beforeOutput');
if ($hook) eval($hook);

$ctrl = new ExtendedregController();
$ctrl->execute($task);
$ctrl->redirect();

$hook = erHelperHooks::get_hook('admin.afterOutput');
if ($hook) eval($hook);

