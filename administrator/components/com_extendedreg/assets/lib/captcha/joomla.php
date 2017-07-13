<?php 
/**
 * @package		ExtendedReg
 * @version		2.02
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erCaptchaJoomla extends JObject implements erCaptchaInterface {
	private $conf;
	private $captcha;
	
	function __construct($conf) {
		$this->conf = $conf;
		$this->captcha = null;
		
		$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
		if (!($plugin === 0 || $plugin === '0' || $plugin === '' || $plugin === null)) {
			$this->captcha = JCaptcha::getInstance($plugin);
		}
	}
	
	public function validate($post) {
		if (!$this->captcha) {
			return true;
		}
		$result = $this->captcha->checkAnswer($post['captcha-code']);
		if ($result === true) {
			return true;
		}
		return false;
	}
	
	public function write() {
		if (!$this->captcha) {
			return '';
		}
		return $this->captcha->display('captcha-code', 'captcha-code', '');
	}
		
	public function output() {
		return;
	}
	
}