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

class erHelperPassword {
	
	public static function validate($password) {
		$model = JvitalsHelper::loadModel('extendedreg', 'Default');
		$conf = $model->getConfObj();
		if (!(int)$conf->pass_strength_enable) {
			// All is good
			return true;
		}
		$result = true;
		if (mb_strlen($conf->pass_min_chars) && (int)$conf->pass_min_chars) {
			if (mb_strlen($password) < (int)$conf->pass_min_chars) {
				$result = false;
				self::errorString(JText::sprintf('COM_EXTENDEDREG_ERROR_PASS_TOO_SHORT', $conf->pass_min_chars));
			}
		}
		if (mb_strlen(trim($conf->pass_common_words))) {
			$wordsarray = explode("\n", $conf->pass_common_words);
			$wordsarray = array_map("trim", $wordsarray);
			$wordsarray = array_map("mb_strtolower", $wordsarray);
			if (in_array(mb_strtolower(trim($password)), $wordsarray)) {
				$result = false;
				self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_COMMON_WORDS'));
			}
		}
		if ((int)$conf->pass_allowed_chars) {
			$allowed = '';
			if (($conf->pass_allowed_chars & 1) == 1) $allowed .= '[:alpha:]абвгдежзийклмнопрстуфхцчшщъьюяАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯ';
			if (($conf->pass_allowed_chars & 2) == 2) $allowed .= '[:upper:]АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯ';
			if (($conf->pass_allowed_chars & 4) == 4) $allowed .= '[:lower:]абвгдежзийклмнопрстуфхцчшщъьюя';
			if (($conf->pass_allowed_chars & 8) == 8) $allowed .= '\d';
			if (($conf->pass_allowed_chars & 16) == 16) $allowed .= '\s';
			if (($conf->pass_allowed_chars & 32) == 32) $allowed .= '\!\=\,\%\.\-\@\#\$\^\*\+\[\]\{\}\<\>\_\&';
			if (!preg_match('~^[' . $allowed . ']*$~', $password)) {
				$result = false;
				self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ALLOWED_CHARS'));
			}
		}
		if ((int)$conf->pass_expected_chars) {
			if (($conf->pass_expected_chars & 1) == 1) {
				if (!preg_match('~[[:alpha:]абвгдежзийклмнопрстуфхцчшщъьюяАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯ]+~', $password)) {
					$result = false;
					self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ATLEAST_ONE_LETTER'));
				}
			}
			if (($conf->pass_expected_chars & 2) == 2) {
				if (!preg_match('~[[:upper:]АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯ]+~', $password)) {
					$result = false;
					self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ATLEAST_ONE_UPPER'));
				}
			}
			if (($conf->pass_expected_chars & 4) == 4) {
				if (!preg_match('~[[:lower:]абвгдежзийклмнопрстуфхцчшщъьюя]+~', $password)) {
					$result = false;
					self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ATLEAST_ONE_LOWER'));
				}
			}
			if (($conf->pass_expected_chars & 8) == 8) {
				if (!preg_match('~[\d]+~', $password)) {
					$result = false;
					self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ATLEAST_ONE_NUMBER'));
				}
			}
			if (($conf->pass_expected_chars & 16) == 16) {
				if (!preg_match('~[\s]+~', $password)) {
					$result = false;
					self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ATLEAST_ONE_SPACE'));
				}
			}
			if (($conf->pass_expected_chars & 32) == 32) {
				if (!preg_match('~[\!\=\,\%\.\-\@\#\$\^\*\+\[\]\{\}\<\>\_\&]+~', $password)) {
					$result = false;
					self::errorString(JText::_('COM_EXTENDEDREG_ERROR_PASS_ATLEAST_ONE_SPECIAL'));
				}
			}
		}
		return $result;
	}
	
	public static function errorString($string = '') {
		static $errors;
		if (!$errors) $errors = '';
		if ($string) $errors .= (trim($errors) ? '<br/>' : '') . $string;
		return $errors;
	}
	
	public static function renderCharsInputs($arr) {
		$label = ($arr['optname'] == 'pass_expected_chars' ? 'EXPECTED' : '');
		ob_start();
		?>
		<label><input type="checkbox" name="<?php echo $arr['optname']; ?>[]" id="<?php echo $arr['optname']; ?>-1"<?php echo (($arr['value'] & 1) == 1 ? ' checked' : ''); ?> value="1" /><?php echo JText::_('COM_EXTENDEDREG_PASSWORD_' . $label . 'CHARS_ALL_LETTERS'); ?></label><br/>
		<label><input type="checkbox" name="<?php echo $arr['optname']; ?>[]" id="<?php echo $arr['optname']; ?>-2"<?php echo (($arr['value'] & 2) == 2 ? ' checked' : ''); ?> value="2" /><?php echo JText::_('COM_EXTENDEDREG_PASSWORD_' . $label . 'CHARS_UPPER_LETTERS'); ?></label><br/>
		<label><input type="checkbox" name="<?php echo $arr['optname']; ?>[]" id="<?php echo $arr['optname']; ?>-4"<?php echo (($arr['value'] & 4) == 4 ? ' checked' : ''); ?> value="4" /><?php echo JText::_('COM_EXTENDEDREG_PASSWORD_' . $label . 'CHARS_LOWER_LETTERS'); ?></label><br/>
		<label><input type="checkbox" name="<?php echo $arr['optname']; ?>[]" id="<?php echo $arr['optname']; ?>-8"<?php echo (($arr['value'] & 8) == 8 ? ' checked' : ''); ?> value="8" /><?php echo JText::_('COM_EXTENDEDREG_PASSWORD_' . $label . 'CHARS_NUMBERS'); ?></label><br/>
		<label><input type="checkbox" name="<?php echo $arr['optname']; ?>[]" id="<?php echo $arr['optname']; ?>-16"<?php echo (($arr['value'] & 16) == 16 ? ' checked' : ''); ?> value="16" /><?php echo JText::_('COM_EXTENDEDREG_PASSWORD_' . $label . 'CHARS_SPACE'); ?></label><br/>
		<label><input type="checkbox" name="<?php echo $arr['optname']; ?>[]" id="<?php echo $arr['optname']; ?>-32"<?php echo (($arr['value'] & 32) == 32 ? ' checked' : ''); ?> value="32" /><?php echo JText::_('COM_EXTENDEDREG_PASSWORD_' . $label . 'CHARS_SPECIAL'); ?></label><br/>
		<?php
		$result = ob_get_clean();
		return $result;
	}

}