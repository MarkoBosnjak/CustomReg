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

if (!JvitalsHelper::canDo('users.manage', 'com_extendedreg')) {
	JError::raiseError(404, JText::_('COM_EXTENDEDREG_NO_ACCESS_ERROR'));
	jexit();
}

class ExtendedregViewExport extends JViewLegacy {
	
	function display($tpl = null) {
		$model = $this->getModel();
		$items = $model->getUsersForExport();
		
		$csv = '';
		$header = '';
		
		foreach($items as $row) {
			$assoc = JArrayHelper::fromObject($row);
			$line = '';
			$line_hdr = '';
			foreach ($assoc as $key => $val) {
				$val = str_replace('"', '""', $val);
				$val = str_replace('#!#', ';', $val);
				if (!$header) $key = str_replace('"', '""', $key);
				$line .= ",\"$val\"";
				if (!$header) $line_hdr .= ",\"$key\"";
			}
			$line = substr($line, 1);
			if (!$header) $line_hdr = substr($line_hdr, 1);
			if (!$header) {
				$header = $line_hdr;
				$csv .= $line_hdr . "\n" . $line . "\n";
			} else {
				$csv .= $line . "\n";
			}
		}
		
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=users-" . date('d-m-Y-H-i') . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		
		echo $csv;
		jexit();
	}
}
