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

abstract class JvitalsHtml {
	protected static $instances = array();
	protected $app = null;
	protected $component = null;
	protected $mode = null;
	protected $languageKey = null;
	protected $userStateConstant = null;
	protected $formID = null;
	protected $formName = null;
	protected $htmlFunctionsClass = null;
	protected $parserActions = array();
	protected $editor = null;
	
	private $_valid_atts = array(
		'form' => 'name|onsubmit|autocomplete|novalidate',
		'time' => 'disabled|onblur|onchange|onfocus|onselect|readonly|tabindex|autocomplete',
		'date' => 'disabled|onblur|onchange|onfocus|onselect|readonly|tabindex|autocomplete',
		'datetime' => 'disabled|onblur|onchange|onfocus|onselect|readonly|tabindex|autocomplete',
		'text' => 'disabled|maxlength|onblur|onchange|onfocus|onselect|readonly|size|tabindex|autocomplete|required|pattern|list|form|autofocus',
		'password' => 'disabled|maxlength|onblur|onchange|onfocus|onselect|readonly|size|tabindex|autocomplete|required|form|autofocus',
		'upload' => 'accept|disabled|onblur|onchange|onfocus|onselect|readonly|size|tabindex|autocomplete|required|form|autofocus',
		'radio' => 'disabled|onblur|onchange|onfocus|onclick|readonly|tabindex|required|form',
		'checkbox' => 'disabled|onblur|onchange|onfocus|onclick|readonly|tabindex|required|form',
		'select' => 'disabled|multiple|onblur|onchange|onfocus|size|tabindex|autofocus|form|required',
		'textarea' => 'cols|rows|disabled|onblur|onchange|onfocus|onselect|readonly|tabindex|required|wrap|maxlength|form',
		'button' => 'disabled|onclick|tabindex|form|formaction|formenctype|formmethod|formnovalidate|formtarget',
		'submit' => 'disabled|onclick|tabindex|form|formaction|formenctype|formmethod|formnovalidate|formtarget',
		'reset' => 'disabled|onclick|tabindex|form|formaction|formenctype|formmethod|formnovalidate|formtarget',
	);
	
	public function __construct($component, $mode) {
		$this->app = JFactory::getApplication();
		$this->component = $component;
		$this->mode = $mode;
		$this->languageKey = strtoupper($component);
		$this->editor = JFactory::getEditor();
		
		$this->loadStyles();
		$this->loadScripts();
	}
	
	public static function getInstance($component, $mode = 'bootstrap2') {
		$signature = ($mode . $component);
		if (empty(self::$instances[$signature])) {
			$class = 'JvitalsHtml' . ucfirst(strtolower($mode));
			if (!class_exists($class)) {
				$classFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . strtolower($mode) . '.php';
				if (!is_file($classFile)) {
					throw new RuntimeException(sprintf('File not found: %s', $classFile));
				}
				require_once ($classFile);
			}
			try {
				$instance = new $class($component, $mode);
			} catch (RuntimeException $e) {
				throw new RuntimeException(sprintf('HTML class error: %s', $e->getMessage()));
			}
			self::$instances[$signature] = $instance;
		}
		return self::$instances[$signature];
	}
	
	protected function filterAttributes($type, $attr) {
		if (!is_array($attr)) return array();
		if (!count($attr)) return array();
		
		$filtered = array();
		foreach ($attr as $key => $value) {
			if (
				(array_key_exists($type, $this->_valid_atts) && in_array($key, explode('|', $this->_valid_atts[$type]))) || 
				substr($key, 0, 5) == 'data-'
			) {
				$filtered[$key] = $value;
			}
		}
		return $filtered;
	}
	
	protected function prepAttrValue($str = '', $field_name = '') {
		static $prepped_fields = array();
		// if the field name is an array we do this recursively
		if (is_array($str)) {
			foreach ($str as $key => $val) {
				$str[$key] = $this->prepAttrValue($val);
			}
			return $str;
		}

		if ($str === '') {
			return '';
		}

		if (isset($prepped_fields[$field_name])) {
			return $str;
		}

		$str = htmlspecialchars($str);
		// In case htmlspecialchars misses these.
		$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);

		if ($field_name != '') {
			$prepped_fields[$field_name] = $field_name;
		}
		return $str;
	}

	function parseAttributes($attributes, $default) {
		if (is_array($attributes)) {
			foreach ($default as $key => $val) {
				if (isset($attributes[$key])) {
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}
			if (count($attributes) > 0) {
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';
		foreach ($default as $key => $val) {
			if ($key == 'value') {
				$val = $this->prepAttrValue($val, $default['name']);
			}
			$att .= $key . '="' . $val . '" ';
		}
		return $att;
	}
	
	protected function generateID($prefix = 'id-', $suffix = '') {
		$uri = JFactory::getURI();
		$signature = md5(serialize($uri->getQuery(true)));
		$unique = $prefix . substr($signature, 0, 5) . $suffix;
		return $unique;
	}
	
	protected function parsePaginationLink($link, $limitstart, $limit) {
		if (!trim($link)) return '';
		return str_replace(array('{limitstart}', '{limit}'), array((int)$limitstart, (int)$limit), $link);
	}
	
	protected function parseTemplate($tmpl, $row, $name) {
		$parsed = $tmpl;
		if (preg_match_all('~\{([^\}]+?)\}~smi', $parsed, $m)) {
			$search = array();
			$replace = array();
			foreach ($m[1] as $chunk) {
				$result = '';
				$switch = substr($chunk, 0, 1);
				if ($switch == '_') {
					if ($this->htmlFunctionsClass) {
						$htmlFunctionsClass = $this->htmlFunctionsClass;
						$func = substr($chunk, 1);
						$params = array($this, $row, $this->parserActions, $name);
						// check if we have paramaters passed to the function in the template
						if (strpos($func, '|') !== false) {
							$extra_params = explode('|', $func);
							// $func is the first element in the array, $extra_params - the array without the first element
							$func = array_shift($extra_params);
							// merge the extra params to the main params
							$params = array_merge($params, $extra_params);
						}
						$result = call_user_func_array(array($htmlFunctionsClass, $func), $params);
					} else {
						$result = '';
					}
				} elseif ($switch == '$') {
					$url = substr($chunk, 1);
					$url = preg_replace('~\^(\w+?)\^~smie', "\$row->\\1", $url);
					$result = JRoute::_($url, false);
				} elseif ($switch == '%') {
					$col = substr($chunk, 1);
					$result = JText::_($this->languageKey .  '_' . strtoupper($this->userStateConstant . '_' . $col));
				} elseif (isset($row->$chunk)) {
					$result = $row->$chunk;
				}
				$search[] = '{' . $chunk . '}';
				$replace[] = $result;
			}
			$parsed = str_replace($search, $replace, $parsed);
		}
		return $parsed;
	}
	
	//~ function parseListTemplate($tmpl, $fields) {
		//~ $parsed = $tmpl;
		//~ if (preg_match_all('~\{([^\}]+?)\}~smi', $parsed, $m)) {
			//~ $search = array();
			//~ $replace = array();
			//~ foreach ($m[1] as $chunk) {
				//~ $result = '';
				//~ $switch = substr($chunk, 0, 1);
				//~ if ($switch == '_') {
					//~ $col = substr($chunk, 1);
					//~ if (isset($fields[$col]['title'])) {
						//~ $result = $fields[$col]['title'];
					//~ }
				//~ } elseif (isset($fields[$chunk]['html'])) {
					//~ $result = $fields[$chunk]['html'];
				//~ }
				//~ $search[] = '{' . $chunk . '}';
				//~ $replace[] = $result;
			//~ }
			//~ $parsed = str_replace($search, $replace, $parsed);
		//~ }
		//~ return $parsed;
	//~ }
	
	public function setStateConstant($userStateConstant) {
		$this->userStateConstant = $userStateConstant;
	}
	
	public function setParserActions($parserActions) {
		$this->parserActions = $parserActions;
	}
	
	public function setFunctionsClass($htmlFunctionsClass) {
		$this->htmlFunctionsClass = $htmlFunctionsClass;
		$inclFile = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $this->component . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'htmlclass.php';
		if ($this->htmlFunctionsClass && is_file($inclFile)) {
			require_once ($inclFile);
		}
	}
	
	public function wrapperStart() {
		$wrapperID = strtolower(str_replace('com_', '', $this->component));
		$wrapperClass = 'joomla' . str_replace(array('<','>'), '', JvitalsDefines::compatibleMode());
		return '<div id="' . $wrapperID . '" class="enable-bootstrap-style ' . $wrapperClass . '">';
	}
	
	public function wrapperEnd() {
		return '</div>';
	}

	public function formHiddenList() {
		$controller = $this->app->input->getCmd('controller');
		$task = $this->app->input->getCmd('task');
		$listOrder = $this->app->getUserState($this->component . '.list' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.ordering');
		$listDirn = $this->app->getUserState($this->component . '.list' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.direction');
		
		ob_start();
		?>
		<div>
			<input type="hidden" name="option" value="<?php echo $this->component; ?>" />
			<input type="hidden" name="extension" value="<?php echo $this->component; ?>" />
			<input type="hidden" name="task" value="<?php echo $controller; ?>.<?php echo $task; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		</div>
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
	public function formHiddenEdit($default_task, $id = 0) {
		ob_start();
		?>
		<div>
			<input type="hidden" name="option" value="<?php echo $this->component; ?>" />
			<input type="hidden" name="extension" value="<?php echo $this->component; ?>" />
			<input type="hidden" name="task" value="<?php echo $default_task; ?>" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="cid" value="<?php echo $id; ?>" />
		</div>
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
	abstract public function formHeader($sidebar = null, $className = null, $multipart = false, $force_admin = false);
	
	abstract public function formFooter();
	
	abstract public function startColumn($id = null, $colspan = 1);
	
	abstract public function endColumn();
	
	abstract public function startRow($id = null);
	
	abstract public function endRow();
	
	abstract public function buildTooltip($text, $tooltip, $placement = 'top');
	
	abstract public function buildPopover($text, $title, $popover, $placement = 'top');
	
	abstract public function buildIcon($icon, $class = '');
	
	abstract public function buildButton($config);
	
	abstract public function buildModal($config);
	
	abstract public function buildTabs($config);
	
	abstract public function buildAccordion($config);
	
	abstract public function buildList($config);
	
	abstract public function buildTable($config);
	
	abstract public function buildForm($config);
	
	abstract public function buildPagination($pagination, $link = '');
	
	public static function loadStyles() {
	
	}
	
	public static function loadScripts() {
		$document = JFactory::getDocument();
		$document->addScript(JvitalsDefines::vendorPath(true) . 'datejs/date.js');
	}
	
}