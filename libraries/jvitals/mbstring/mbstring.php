<?php
/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright		(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     		http://jvitals.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Create functions that do not exist
$_INTERNAL_ENCODING = "UTF-8";

if (!function_exists("mb_internal_encoding")) {
	function mb_internal_encoding($encoding = null) {
		global $_INTERNAL_ENCODING;
		if (is_null($encoding)) return $_INTERNAL_ENCODING;
		$_INTERNAL_ENCODING = strtoupper($encoding);
		return true;
	}
}

if (!function_exists("mb_strlen")) {
	function mb_strlen($str, $encoding = null) {
		global $_INTERNAL_ENCODING;
		if (is_null($encoding)) $encoding = $_INTERNAL_ENCODING;
		if (strtoupper($encoding) == "UTF-8") return strlen(utf8_decode($str));
		return strlen($str);
	}
}

if (!function_exists("mb_strtolower")) {
	function mb_strtolower($str, $encoding = null) {
		global $_INTERNAL_ENCODING;
		if (is_null($encoding)) $encoding = $_INTERNAL_ENCODING;
		if (strtoupper($encoding) == "UTF-8") return utf8_encode(strtolower(utf8_decode($str)));
		return strtolower($str);
	}
}

if (!function_exists("mb_strtoupper")) {
	function mb_strtoupper($str, $encoding = null) {
		global $_INTERNAL_ENCODING;
		if (is_null($encoding)) $encoding = $_INTERNAL_ENCODING;
		if (strtoupper($encoding) == "UTF-8") return utf8_encode(strtoupper(utf8_decode($str)));
		return strtoupper($str);
	}
}

if (!function_exists("mb_substr")) {
	function mb_substr($str, $start, $length = null, $encoding = null) {
		global $_INTERNAL_ENCODING;
		if (is_null($encoding)) $encoding = $_INTERNAL_ENCODING;
		if (is_null($length)) $length = mb_strlen($str, $encoding);
		if (strtoupper($encoding) == "UTF-8") return utf8_encode(substr(utf8_decode($str), $start, $length));
		return substr($str, $start, $length);
	}
}

if (!function_exists("mb_strpos")) {
	function mb_strpos($haystack, $needle, $offset = null, $encoding = null) {
		global $_INTERNAL_ENCODING;
		if (is_null($encoding)) $encoding = $_INTERNAL_ENCODING;
		if (strtoupper($encoding) == "UTF-8") return strpos(utf8_decode($haystack), utf8_decode($needle), $offset);
		return strpos($haystack, $needle, $offset);
	}
}


if (!function_exists("mb_str_replace")) {
	function mb_str_replace($search, $replace, $subject) {
		if (is_array($subject)) {
			$ret = array();
			foreach($subject as $key => $val) {
				$ret[$key] = mb_str_replace($search, $replace, $val);
			}
			return $ret;
		}

		foreach ((array)$search as $key => $s) {
			if ($s == '') {
				continue;
			}
			$r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
			$pos = mb_strpos($subject, $s);
			while ($pos !== false) {
				$subject = mb_substr($subject, 0, $pos) . $r . mb_substr($subject, $pos + mb_strlen($s));
				$pos = mb_strpos($subject, $s, $pos + mb_strlen($r));
			}
		}
		return $subject;
	}
}

if (!function_exists('mb_trim')) {
	function mb_trim($string, $charlist='\\\\s', $ltrim=true, $rtrim=true) {
		$both_ends = $ltrim && $rtrim;

		$char_class_inner = preg_replace(
			array( '/[\^\-\]\\\]/S', '/\\\{4}/S' ),
			array( '\\\\\\0', '\\' ),
			$charlist
		);

		$work_horse = '[' . $char_class_inner . ']+';
		$ltrim && $left_pattern = '^' . $work_horse;
		$rtrim && $right_pattern = $work_horse . '$';

		if ($both_ends) {
			$pattern_middle = $left_pattern . '|' . $right_pattern;
		} elseif ($ltrim) {
			$pattern_middle = $left_pattern;
		} else {
			$pattern_middle = $right_pattern;
		}
		return preg_replace("/$pattern_middle/usSD", '', $string);
	}
}

if (!function_exists("mb_ucwords")) {
	if (!function_exists("mb_convert_case")) {
		function mb_ucwords($str) {
			return ucwords($str);
		}
	} else {
		function mb_ucwords($str) {
			$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
			return ($str);
		}
	}
}