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

class erCaptchaMathguard extends JObject implements erCaptchaInterface {
	private $conf;
	private $numberTypes;
	private $numberValues;
	private $operations;
	private $operationsTypes;
	private $operationsValues;
	
	/** Sessionname to store the original text */
	private $session_private = 'extreg_captcha_code';

	function __construct($conf) {
		$this->conf = $conf;
		
		$this->numberTypes = array(0, 1, 2);
		$this->numberValues = array(
			0 => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
			1 => array('0', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'),
			2 => array(
				JText::_('COM_EXTENDEDREG_MATHGUARD_ZERO'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_ONE'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_TWO'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_THREE'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_FOUR'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_FIVE'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_SIX'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_SEVEN'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_EIGHT'),
				JText::_('COM_EXTENDEDREG_MATHGUARD_NINE'),
			),
		);
		
		$this->operations = array(0, 1, 2, 3);
		$this->operationsTypes = array(0, 1);
		$this->operationsValues = array(
			0 => array('+', '-', '*', '/'),
			1 => array(
				JText::_('COM_EXTENDEDREG_MATHGUARD_PLUS'), 
				JText::_('COM_EXTENDEDREG_MATHGUARD_MINUS'), 
				JText::_('COM_EXTENDEDREG_MATHGUARD_MULTIPLIEDBY'), 
				JText::_('COM_EXTENDEDREG_MATHGUARD_DEVIDEDBY'), 
			),
		);
	}
	
	public function validate($post) {
		$session = JFactory::getSession();
		$captcha_code = $session->get($this->session_private);
		if (array_key_exists('captcha-code', $post) && ($captcha_code != trim($post['captcha-code']) || mb_strlen($post['captcha-code']) == 0 )) {
			return false;
		}
		return true;
	}
	
	public function write() {
		ob_start();
		?>
		<script language="JavaScript">
			function erChangeCaptcha() {
				var set_rand = Math.floor(Math.random()*10000000001);
				var refreshURL = "<?php echo JURI::base(true); ?>/index.php?option=com_extendedreg&task=captcha&rand=" + set_rand;
				jQuery("#div_captcha_img").load(refreshURL);
			}
		</script>
		<div id="div_captcha">
			<div id="div_captcha_info"><?php echo JText::_('COM_EXTENDEDREG_MATHGUARD_INFO'); ?></div>
			<div id="div_captcha_img"><?php $this->output(); ?></div>
			<div id="div_captcha_new">
				<a href="javascript:void(0);" onclick="erChangeCaptcha();" id="change-image"><?php echo JText::_('COM_EXTENDEDREG_MATHGUARD_GHANGE'); ?></a>
			</div>
			<div style="margin-top:5px;" id="div_captcha_code"><input type="text" name="captcha-code" id="captcha-code" class="inputbox" /></div>
		</div>
		<?php
		return ob_get_clean();
	}
		
	public function output() {
		// Generate two random numbers between 0 and 9
		$a = mt_rand() % 10;
		$b = mt_rand() % 10;
		// Get the operation
		$op = (int)array_rand($this->operations);
		switch ($op) {
			case 1:
				$code = ($a - $b);
				break;
			case 2:
				$code = ($a * $b);
				break;
			case 3:
				if ($b > 0) {
					// Only round numbers when deviding otherwise multiply
					if ($a % $b != 0) {
						$op = 2;
						$code = ($a * $b);
					} else {
						$code = ($a / $b);
					}
				} else {
					$code = $a;
					$b = 1;
				}
				break;
			case 0:
			default:
				$code = ($a + $b);
				break;
		}
		
		$session = JFactory::getSession();
		$session->set($this->session_private, $code);
		
		$result = array();
		// First number
		$result[] = $this->getTranslatedNumber($a);
		// Operation
		$result[] = $this->getTranslatedOperation($op);
		// Second number
		$result[] = $this->getTranslatedNumber($b);
		
		echo implode(' ', $result);
	}
	
	private function getTranslatedNumber($num) {
		$key = (int)array_rand($this->numberTypes);
		return $this->numberValues[$key][$num];
	}
	
	private function getTranslatedOperation($op) {
		$key = (int)array_rand($this->operationsTypes);
		return $this->operationsValues[$key][$op];
	}
	
}