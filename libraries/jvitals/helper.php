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

class JvitalsHelper {
	
	public static function canDo($action, $option = '') {
		$user = JFactory::getUser();
		if (!trim($option)) {
			$option = JFactory::getApplication()->input->get('option');
		}
		return $user->authorise($action, $option);
	}
	
	public static function execAvailable() {
		static $available;

		if (!isset($available)) {
			$available = true;
			if (ini_get('safe_mode')) {
				$available = false;
			} else {
				$d = ini_get('disable_functions');
				$s = ini_get('suhosin.executor.func.blacklist');
				if ("$d$s") {
					$array = preg_split('/,\s*/', "$d,$s");
					if (in_array('exec', $array)) {
						$available = false;
					}
				}
			}
		}

		return $available;
	}
	
	public static function validateEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");

		if (is_bool($atIndex) && !$atIndex) {
			return false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} elseif ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} elseif ($local[0] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} elseif (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} elseif (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} elseif (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
				// character not valid in local part unless 
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			} elseif (!preg_match('/^.+\.[a-z]{2,4}$/', $domain)) {
				$isValid = false;
			}
		}
		return $isValid;
	}
	
	public static function checkForProxy($fsock = false) {
		$found = false;
		$proxy_headers = array(
			'HTTP_VIA',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED',
			'HTTP_CLIENT_IP',
			'HTTP_FORWARDED_FOR_IP',
			'VIA',
			'X_FORWARDED_FOR',
			'FORWARDED_FOR',
			'X_FORWARDED',
			'FORWARDED',
			'CLIENT_IP',
			'FORWARDED_FOR_IP',
			'HTTP_PROXY_CONNECTION'
		);
		foreach($proxy_headers as $x){
			if ($found) break;
			if (isset($_SERVER[$x])) {
				$found = true;
			}
		}
		if ($found) return true;
		
		$ports = array(8080,80,81,1080,6588,8000,3128,553,554,4480);
		if (function_exists("fsockopen") && $fsock) {
			foreach($ports as $port) {
				if ($found) break;
				$sock_errno = '';
				$sock_errstr = '';
				if ($sock = @fsockopen($_SERVER['REMOTE_ADDR'], $port, $sock_errno, $sock_errstr, 30)) {
					$found = true;
				}
			}
			if ($found) return true;
		}
		if (in_array($_SERVER['REMOTE_PORT'], $ports)) {
			return true;
		}
		
		return false;
	}
	
	public static function utf16ToUtf8($str) {
		$c0 = ord($str[0]);
		$c1 = ord($str[1]);

		if ($c0 == 0xFE && $c1 == 0xFF) {
			$be = true;
		} else if ($c0 == 0xFF && $c1 == 0xFE) {
			$be = false;
		} else {
			return $str;
		}

		$str = substr($str, 2);
		$len = strlen($str);
		$dec = '';
		for ($i = 0; $i < $len; $i += 2) {
			$c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) :
					ord($str[$i + 1]) << 8 | ord($str[$i]);
			if ($c >= 0x0001 && $c <= 0x007F) {
				$dec .= chr($c);
			} else if ($c > 0x07FF) {
				$dec .= chr(0xE0 | (($c >> 12) & 0x0F));
				$dec .= chr(0x80 | (($c >>  6) & 0x3F));
				$dec .= chr(0x80 | (($c >>  0) & 0x3F));
			} else {
				$dec .= chr(0xC0 | (($c >>  6) & 0x1F));
				$dec .= chr(0x80 | (($c >>  0) & 0x3F));
			}
		}
		return $dec;
	}
	
	public static function removeBOM($string) {
		// check for UTF-8 BOM
		if (mb_substr($string, 0, 3) == pack('CCC', 239, 187, 191)) {
			$string = mb_substr($string, 3);
		// check for UTF-16 BOM
		} elseif (mb_substr($string, 0, 2) == pack('CC', 254, 255) || mb_substr($string, 0, 2) == pack('CC', 255, 254)) {
			// convert the string to utf-8 if we want it to be usable. We don't use mb_convert_encoding because it gives strange results.
			$string = self::utf16ToUtf8($string);
		}
		return $string;
	}
	
	public static function buildCookie($array) {
		$ret = '';
		if (is_array($array) && count($array)) {
			foreach ($array as $key => $val) {
				$ret .= (trim($val) != '') ? trim($key) . '=' . trim($val) . '|' : '';
			}
			$ret = rtrim($ret,'|');
		}
		return $ret;
	}
	
	public static function breakCookie($cookie_string) {
		$ret = array();
		$tmp = array();
		$cookie_array = trim((string)$cookie_string) ? explode('|', trim((string)$cookie_string)) : array();
		if (is_array($cookie_array) && count($cookie_array)) {
			foreach ($cookie_array as $key => $val) {
				$tmp = explode('=', $val);
				$ret[$tmp[0]] = $tmp[1];
				unset($tmp);
			}
		}
		unset($cookie_array);
		return $ret;
	}
	

	public static function getCookie($name) {
		jimport('joomla.utilities.utility');
		jimport('joomla.utilities.simplecrypt');
		$cookie_name = JApplication::getHash($name);
		if (!isset($_COOKIE[$cookie_name])) {
			return false;
		}
		$cookie = JRequest::getString($cookie_name, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM);
		$crypt = new JSimpleCrypt(JApplication::getHash(@$_SERVER['HTTP_USER_AGENT']));
		$cookie_array = self::breakCookie(unserialize($crypt->decrypt($cookie)));
		return $cookie_array;
	}

	public static function setCookie($name, $array) {
		jimport('joomla.utilities.utility');
		jimport('joomla.utilities.simplecrypt');
		$app = JFactory::getApplication();
		$agent = @$_SERVER['HTTP_USER_AGENT'];
		if (($agent != '') && ($agent != $name)) {
			$crypt = new JSimpleCrypt(JApplication::getHash($agent));
			setcookie(JApplication::getHash($name), $crypt->encrypt(serialize(self::buildCookie($array))), (time() + 60*60*30*24), $app->getCfg('cookie_path', '/'), $app->getCfg('cookie_domain', ''));
		}
	}
	
	public static function snippet($text, $length = 64, $tail = "...") {
		$text = trim($text);
		$txtl = mb_strlen($text);
		if ($txtl > $length) {
			if ($txtl > ($length * 2)) {
				$text = mb_substr($text, 0, ($length * 2));
			}
			for ($i = 1; preg_match('~\s+~', $text[$length - $i]); $i ++) {
				if ($i == $length) {
					return mb_substr($text, 0, $length) . $tail;
				}

			}
			$text = mb_substr($text, 0, $length - $i + 1) . $tail;
		}

		return $text;
	}
	
	// It behaves greedy, gets length characters or goes for more
	public static function snippetGreedy($text, $length = 64, $tail = "...") {
		$text = trim($text);
		if (mb_strlen($text) > $length) {
			if (mb_strlen($text) > ($length * 2)) {
				$text = mb_substr($text, 0, ($length * 2));
			}
			for ($i = 0; preg_match('~\s+~', $text[$length + $i]); $i ++) {
				if (!$text[$length + $i]) {
					return $text;
				}
			}
			$text = mb_substr($text, 0, $length + $i) . $tail;
		}

		return $text;
	}
	
	// The same as the snippet but removing latest low punctuation chars,
	// if they exist (dots and commas). It performs a later suffixal trim of spaces
	public static function snippetWop($text, $length = 64, $tail = "...") {
		$text = trim($text);
		$txtl = mb_strlen($text);
		if ($txtl > $length) {
			if ($txtl > ($length * 2)) {
				$text = mb_substr($text, 0, ($length * 2));
			}
			for ($i = 1; preg_match('~\s+~', $text[$length - $i]); $i ++) {
				if ($i == $length) {
					return mb_substr($text, 0, $length) . $tail;
				}
			}
			for (; $text[$length - $i] == "," || $text[$length - $i] == "." || $text[$length - $i] == " "; $i ++) {;}
			$text = mb_substr($text, 0, $length - $i + 1) . $tail;
		}

		return $text;
	}
	
	public static function params2object($params) {
		$obj = new JObject();
		$raw = trim($params);
		$raw = str_replace("\r", '', $raw);
		if ($raw) {
			if (substr($raw, 0, 1) == '{' && substr($raw, -1) == '}') {
				$decoded = json_decode($raw);
				if (is_array($decoded)) {
					// nothing to do
				} elseif (is_object($decoded)) {
					$decoded = get_object_vars($decoded);
				}
				foreach ($decoded as $key => $value) {
					$value = str_replace('<!--NL-->', "\n", $value);
					if (strpos($value, '|') !== false) {
						$value = explode('|', $value);
					}
					$obj->set($key, $value);
				}
			} else {
				$lines = explode("\n", $raw);
				foreach ($lines as $line) {
					$arr = explode('=', $line);
					$key = trim(array_shift($arr));
					$value = implode('=', $arr);
					$value = str_replace('<!--NL-->', "\n", $value);
					if (strpos($value, '|') !== false) {
						$value = explode('|', $value);
					}
					$obj->set($key, $value);
				}
			}
			$obj->set('_raw', $raw);
		}
		return $obj;
	}
	
	// for example 'ap-changelog', 'agora-pro', 'com_agorapro', 'COM_AGORAPRO_CHANGELOG'
	public static function parseChangelog($fname, $compKey, $component, $lang_prefix) {
		$config = JFactory::getConfig();
		$savePath = $config->get('tmp_path') . DIRECTORY_SEPARATOR . $fname . '.xml';
		$defaultFile = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . 'changelog.xml';
		
		$dorequest = false;
		$filefound = false;
		
		if (is_file($savePath)) {
			$filefound = true;
			if ((filemtime($savePath) + (60 * 60 * 24)) < time()) {
				// only once per day
				$dorequest = true;
			}
		}
		
		if (!$filefound) $dorequest = true;
		
		if ($dorequest) JvitalsServer::download('http://www.jvitals.com/index.php?option=com_jvitalsversions&task=changelog&format=raw&com=' . $compKey, $savePath);
		
		$xmlFile = (is_file($savePath) && is_readable($savePath) ? $savePath : $defaultFile);
		
		if (!is_file($xmlFile)) {
			return '';
		}
		
		$xml = simplexml_load_file($xmlFile);
		$output = '<dl class="changelog">';
		foreach ($xml->version as $version) {
			$output .= '<dt>';
			$output .= '<h4>' . JText::_($lang_prefix . '_VERSION') . ': ' . (string)$version['number'] . '</h4>';
			$output .= '<b>' . JText::_($lang_prefix . '_DATE') . ':</b> ' . (string)$version->date . '<br/>';
			$output .= '<b>' . JText::_($lang_prefix . '_DESCRIPTION') . ':</b> ' . (string)$version->description;
			$output .= '</dt>';
			$output .= '<dd><ul>';
			if(isset($version->list) && count($version->list->children())) {
				foreach($version->list->children() as $item) {
					$output .= '<li><span class="' . (string)$item['type'] . '">' . (string)$item['type'] . '</span> ' . (string)$item . '</li>';
				}
			}
			$output .= '</ul></dd>';
		}
		$output .= '</dl>';

		return $output;
	}
	
	public static function formatVersion($version) {
		$result = '';
		$parts = explode('.', $version);
		foreach ($parts as $key => $num) {
			if ((int)$key == 0) {
				$result .= (int)$num;
			} else {
				$result .= '.' . str_pad($num, 3, '0', STR_PAD_RIGHT);
			}
		}
		return $result;
	}
	
	// for example 'er-version-compare.txt', 'extendedreg', '2.1'
	public static function getVersionFile($file, $compKey, $compVersion) {
		$config = JFactory::getConfig();
		$savePath = $config->get('tmp_path') . DIRECTORY_SEPARATOR . $file;
		
		$dorequest = false;
		$filefound = false;
		
		if (is_file($savePath)) {
			$filefound = true;
			if ((filemtime($savePath) + (15 * 60)) < time()) {
				// older then 15 minutes
				$dorequest = true;
			}
		}
		if (!$filefound) $dorequest = true;
		
		if ($dorequest) JvitalsServer::download('http://www.jvitals.com/index.php?option=com_jvitalsversions&task=version_info&format=raw&com=' . $compKey . '&myversion=' . urlencode($compVersion) . '&jversion=' . urlencode(JvitalsDefines::joomlaVersion()), $savePath);
		
		if (!is_file($savePath)) {
			touch($savePath);
		}
		
		return $savePath;
	}
	
	// for example 'er-version-compare.txt', 'extendedreg', '2.1'
	public static function versionNotice($file, $compKey, $compVersion) {
		$versionJson = trim(file_get_contents(self::getVersionFile($file, $compKey, $compVersion)));
		if (!$versionJson) return false;
		
		$versionInfo = json_decode($versionJson, true);
		
		if (version_compare(self::formatVersion($versionInfo['version']), self::formatVersion($compVersion), '>')) {
			$message = trim(rawurldecode($versionInfo['message']));
			$type = trim(rawurldecode($versionInfo['type']));
			if ($message) {
				$session = JFactory::getSession();
				$sessionQueue = $session->get('application.queue');
				if (!is_array($sessionQueue) || !in_array(array('message' => $message, 'type' => $type), $sessionQueue)) {
					$app = JFactory::getApplication();
					$app->enqueueMessage($message, $type);
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	// for example 'er-version-compare.txt', 'extendedreg', '2.1'
	public static function versionInfo($file, $compKey, $compVersion) {
		$versionJson = trim(file_get_contents(self::getVersionFile($file, $compKey, $compVersion)));
		if (!$versionJson) return '';
		
		$versionInfo = json_decode($versionJson, true);
		
		$color = '#4dbd33';
		if (version_compare(self::formatVersion($versionInfo['version']), self::formatVersion($compVersion), '>')) {
			$color = '#cc1100';
		}
		
		return '<span style="color: ' . $color . ';">' . $versionInfo['version'] . '</span>';
	}
	
	// for example 'er-version-compare.txt', 'extendedreg', '2.1'
	public static function versionCompare($file, $compKey, $compVersion) {
		$versionJson = trim(file_get_contents(self::getVersionFile($file, $compKey, $compVersion)));
		if (!$versionJson) return true;
		
		$versionInfo = json_decode($versionJson, true);
		if (version_compare(self::formatVersion($versionInfo['version']), self::formatVersion($compVersion), '>')) {
			return false;
		}
		return true;
	}
	
	// for example 'extendedreg', 'Default'
	public static function loadModel($component, $name) {
		static $inst;
		if (!is_array($inst)) $inst = array();
		$key = ($component . $name);
		if (!isset($inst[$key])) {
			jimport('joomla.filesystem.file');
			
			$_ModelFilename = JFile::makeSafe(strtolower($name) . '.php');
			$_ModelPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_' . strtolower($component) . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $_ModelFilename;
			if (!is_file($_ModelPath)) {
				throw new RuntimeException(sprintf('File not found: %s', $_ModelFilename));
			}
			require_once ($_ModelPath);
			$classname = ucfirst(strtolower($component)) . 'Model' . ucfirst(strtolower($name));
			$inst[$key] = new $classname();
		}
		return $inst[$key];
	}
	
	public static function getMimeType($file_name, $upload_type) {
		$mime_type = '';
		/*
		  Fileinfo library might fail to determine the mime type in PHP versions prior to 5.3
		  due to troubles locating the magic file. In this case it will return application/octet-stream.
		  On some hosting environments application/x-character-device is returned for all file types.
		  So we will assume these values are incorrect and will keep trying to determine the mime type.
		  We will use the FILEINFO_MIME constant instead FILEINFO_MIME_TYPE as the latter exist only in PHP 5.3+
		*/
		$bad_mime = array('application/octet-stream', 'application/x-character-device');
		if (!$mime_type && function_exists('finfo_open')) {
			$fileInfo = finfo_open(FILEINFO_MIME);
			$mime_type = finfo_file($fileInfo, $file_name);
			$mime_type = explode(';', $mime_type);
			$mime_type = trim($mime_type[0]);
			$finfo_close = ($fileInfo);
		}
		if ((!$mime_type || in_array($mime_type, $bad_mime)) && function_exists('mime_content_type')) {
			$mime_type = mime_content_type($file_name);
		}
		if (self::execAvailable() && (!$mime_type || in_array($mime_type, $bad_mime))) {
			$cmd = 'file -ib ' . escapeshellarg($file_name) . ' 2> /dev/null';
			$mime_type = exec($cmd);
			$mime_type = explode(';', $mime_type);
			$mime_type = trim($mime_type[0]);
		}
		
		// this only works with php upload
		if ((!$mime_type || in_array($mime_type, $bad_mime)) && $upload_type) {
			$mime_type = $upload_type;
		}
		return $mime_type;
	}
	
	public static function sanitize($str) {
		static $san = null;
		if (empty($san)) {
			$san = new JvitalsSanitizer;
		}
		return $san->sanitize($str);
	}
	
	public static function componentEnabled($component) {
		$dbo = JFactory::getDBO();
		
		// Construct the query
		$query = $dbo->getQuery(true)
			->select($dbo->quoteName('extension_id'))
			->from("#__extensions")
			->where($dbo->quoteName('type') . " = " . $dbo->quote('component'))
			->where($dbo->quoteName('enabled') . " = 1")
			->where($dbo->quoteName('element') . " = " . $dbo->quote($component));
			
		// Setup the query
		$dbo->setQuery($query);
		
		$extension_id = (int)$dbo->loadResult();
		
		if ($extension_id) {
			// TODO - maybe checks if directory also exists
			return true;
		}
		return false;
	}
	
}