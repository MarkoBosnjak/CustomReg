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

if (JvitalsDefines::compatibleMode() != '30>') {
	jimport('joomla.html.toolbar.button');
	require_once (JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'toolbar'. DIRECTORY_SEPARATOR . 'button' . DIRECTORY_SEPARATOR . 'link.php');
	
	if (!class_exists('JButtonLinkblank')) {
		class JButtonLinkblank extends JButtonLink {
			var $_name = 'Linkblank';

			function fetchButton($type = 'Linkblank', $name = 'back', $text = '', $url = null) {
				$text = JText::_($text);
				$class = $this->fetchIconClass($name);
				$doTask = $this->_getCommand($url);

				$html = "<a href=\"$doTask\" target=\"_blank\">\n";
				$html .= "<span class=\"$class\" title=\"$text\">\n";
				$html .= "</span>\n";
				$html .= "$text\n";
				$html .= "</a>\n";

				return $html;
			}
		}
	}
} else {
	jimport('cms.toolbar.button');
	jimport('cms.toolbar.button.link');
	
	if (!class_exists('JToolbarButtonLinkblank')) {
		class JToolbarButtonLinkblank extends JToolbarButtonLink {
			protected $_name = 'Linkblank';
			
			public function fetchButton($type = 'Link', $name = 'back', $text = '', $url = null) {
				$text = JText::_($text);
				$class = $this->fetchIconClass($name);
				$doTask = $this->_getCommand($url);

				$html = "<button class=\"btn\" onclick=\"window.open('$doTask');\">\n";
				$html .= "<span class=\"$class\">\n";
				$html .= "</span>\n";
				$html .= "$text\n";
				$html .= "</button>\n";

				return $html;
			}
		}
	}
}
