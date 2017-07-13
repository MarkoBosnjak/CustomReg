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

class JvitalsParameter {

	protected $_raw = null;
	protected $_xml = null;
	protected $_data = null;
	protected $_paramsXpath = null;
	protected $_elements = array();
	protected $_elementPath = array();
	public $dummy_form;
	
	public function __construct($data = '', $path = '', $paramsXpath = 'params') {
		$this->_raw = $data;
		$this->_data = JvitalsHelper::params2object($data);
		$this->_paramsXpath = $paramsXpath;
		if (!trim($this->_paramsXpath) || (trim($this->_paramsXpath) == '/params')) {
			$this->_paramsXpath = 'params';
		}
		
		// Set base path.
		$this->_elementPath[] = JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'fields';
		$this->dummy_form = new JForm('er', array('control' => 'params'));
		
		if ($path) {
			$this->_xml = simplexml_load_file($path);
		}
	}
	
	public function setXML(&$xml) {
		if (is_object($xml) && is_a($xml,'SimpleXMLElement')) {
			$this->_xml = $xml;
		} elseif (trim($xml)) {
			unset($this->_xml);
			if (is_file($xml)) {
				$this->_xml = simplexml_load_file($xml);
			} else {
				$this->_xml = simplexml_load_string($xml);
			}
		}
	}
	
	public function addElementPath($path) {
		if (is_dir(JPATH_ROOT . $path)) {
			$this->_elementPath[] = JPATH_ROOT . $path;
			$this->_elementPath = array_unique($this->_elementPath);
		}
	}
	
	public function loadElement($type, $new = false) {
		$signature = md5($type);

		if (isset($this->_elements[$signature]) && is_object($this->_elements[$signature]) && !is_a($this->_elements[$signature], '__PHP_Incomplete_Class') && ($new === false)) {
			return $this->_elements[$signature];
		}
		
		$elementClass = 'JFormField' . ucfirst(mb_strtolower($type));
		
		if (!class_exists($elementClass)) {
			if (isset($this->_elementPath)) {
				$dirs = $this->_elementPath;
			} else {
				$dirs = array();
			}

			$file = JFilterInput::getInstance()->clean(str_replace('_', DIRECTORY_SEPARATOR, $type) . '.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = JPath::find($dirs, $file)) {
				include_once $elementFile;
			} else {
				return false;
			}
		}

		if (!class_exists($elementClass)) {
			return false;
		}

		$this->_elements[$signature] = new $elementClass($this);

		return $this->_elements[$signature];
	}
	
	function get($name, $default = null) {
		return $this->_data->get($name, $default);
	}
	
	private function getParams() {
		if (!$this->_xml) {
			return false;
		}
		if (!is_a($this->_xml, 'SimpleXMLElement')) {
			return false;
		}
		$params = $this->_xml->xpath($this->_paramsXpath);
		if (!count($params)) {
			return false;
		}
		$params = $params[0];
		
		if (isset($params['addpath']) && (string)$params['addpath']) {
			$this->addElementPath((string)$params['addpath']);
		} elseif (isset($params['addfieldpath']) && (string)$params['addfieldpath']) {
			$this->addElementPath((string)$params['addfieldpath']);
		}
		
		$results = array();
		foreach ($params->children() as $node) {
			// Get the type of the parameter.
			$type = (string)$node['type'];

			$element = $this->loadElement($type);

			// Check for an error.
			if ($element === false) {
				throw new RuntimeException(sprintf('Field type parameters file not found! %s', $type));
			}
			$element->setForm($this->dummy_form);
			
			// Get value.
			$value = $this->_data->get((string)$node['name'], (string)$node['default']);
			
			// Some magic for new Joomla ideas... NO COMMENT
			$xmlstr = $node->asXml();
			$xmlstr = str_replace('<param', '<field id="' . (string)$node['name'] . '" ', $node->asXml());
			$xmlstr = str_replace('param>', 'field>', $xmlstr);
			
			$xmlElement = simplexml_load_string($xmlstr);
			$element->setup($xmlElement, $value);
			
			$results[] = array(
				0 => $element->label,
				1 => $element->input,
			);
		}
		
		return $results;
	}
	
	public function render() {
		$params = $this->getParams();
		$html = array();
		$html[] = '<fieldset class="panelform">';
		$html[] = '<ul class="adminformlist">';
		foreach ($params as $param) {
			$html[] = '<li>';
			if ($param[0]) {
				$html[] = $param[0];
				$html[] = $param[1];
			} else {
				$html[] = $param[1];
			}
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		$html[] = '</fieldset>';
		
		return implode(PHP_EOL, $html);
	}
	
}
