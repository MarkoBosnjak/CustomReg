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

if (JvitalsDefines::compatibleMode() == '15') {
	throw new RuntimeException(JText::_('COM_EXTENDEDREG_OLD_JOOMLA_WARNING'));
}

if (is_file(JvitalsDefines::comBackPath('com_extendedreg') . 'assets'. DIRECTORY_SEPARATOR . 'please_use_installer.php')) {
	throw new RuntimeException(JText::_('COM_EXTENDEDREG_PLEASE_USE_INSTALLER'));
}

require_once (JvitalsDefines::comBackPath('com_extendedreg') . 'helpers' . DIRECTORY_SEPARATOR . 'initiate.php');

erHelperRouter::route();
erHelperJavascript::load();
erHelperLanguage::load();

erHelperHooks::loadHooks('frontend');

$model = JvitalsHelper::loadModel('extendedreg', 'Default');
$conf = $model->getConfObj();

$document = JFactory::getDocument();

if ((int)$conf->css_front_extreg) {
	$document->addStyleSheet(JvitalsDefines::comFrontPath('com_extendedreg', true) . 'assets/extendedreg.css');
}

if ((int)$conf->css_front_jquery) {
	if ((int)$conf->include_jquery_ui) {
		//~ if (JvitalsDefines::compatibleMode() != '30>') {
			//~ $document->addStyleSheet(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/css/smoothness/jquery-ui.css');
		//~ } else {
			$document->addStyleSheet(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/css/smoothness/jquery-ui-joomla3.css');
		//~ }
	}
}

$cssfile = trim($conf->css_theme);
if ($cssfile) {
	$document->addStyleSheet(JvitalsDefines::comFrontPath('com_extendedreg', true) . 'assets/themes/' . $cssfile . '.css');
}

$app = JFactory::getApplication();
$controller = $app->input->getCmd('controller', 'default');
$task = $app->input->getCmd('task', 'display');

$_ctrlFile = JvitalsDefines::comFrontPath('com_extendedreg') . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
if (is_file($_ctrlFile)) {
	require_once ($_ctrlFile);
} else {
	require_once (JvitalsDefines::comFrontPath('com_extendedreg') . 'controllers' . DIRECTORY_SEPARATOR . 'default.php');
}

$hook = erHelperHooks::get_hook('frontend.beforeOutput');
if ($hook) eval($hook);

$ctrl = new ExtendedregController();
$ctrl->execute($task);
$ctrl->redirect();

$hook = erHelperHooks::get_hook('frontend.afterOutput');
if ($hook) eval($hook);
