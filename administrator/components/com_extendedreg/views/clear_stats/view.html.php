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

if (!JvitalsHelper::canDo('stats.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewClear_stats extends JViewLegacy {

	function display($tpl = null) {
		parent::display($tpl);
	}
	
}
