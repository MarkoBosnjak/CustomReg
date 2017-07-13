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

/**
 * Renders a erfomrslist element
 */
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldErfomrslist extends JFormField {
	var $type = 'Erfomrslist';

	protected function getInput() {
		$db = JFactory::getDBO();
		
		$query = 'SELECT f.' . $db->quoteName('id') . ', f.' . $db->quoteName('name') . ' 
			FROM #__extendedreg_forms f WHERE f.' . $db->quoteName('published') . ' = ' . $db->Quote('1');
		$db->setQuery($query);
		$forms = $db->loadObjectList();
		
		$options = array();
		$options[] = JHtml::_('select.option', 0, '-');
		foreach ($forms as $frm) {
			$options[] = JHtml::_('select.option', $frm->id, $frm->name);
		}
		
		// Construct the various argument calls that are supported.
		$attribs = '';
		
		if ($v = $this->element['class']) {
			$attribs .= ' class="' . $v . '"';
		} else {
			$attribs .= ' class="inputbox"';
		}

		return JHtml::_('select.genericlist', $options, $this->name, trim($attribs), 'value', 'text', $this->value, $this->id);
	}
}

