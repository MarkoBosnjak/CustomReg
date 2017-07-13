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

/* Custom field type interface */
interface erFieldInterface {
	public function hasParams();
	public function getParams();
	public function renderParams();
	public function hasOptions();
	public function getOptions();
	public function isMultiselect();
	public function getJavascptValidation();
	public function serversideValidation(&$post);
	public function getHtml($value, $id = null);
	public function getField();
	public function hideTitle();
	public function getSqlType();
}

/* Validation interface */
interface erValidationInterface {
	public function validate($input, $extradata = array());
	public function loadJavascript();
	public function loadFieldJavascript();
	public function getElements();
}

/* Captcha interface */
interface erCaptchaInterface {
	public function validate($post);
	public function write();
	public function output();
}

/* Integration interface */
interface erIntegrationInterface {
	public function checkComponentInstalled();
	public function renderParams();
	public function prepareParams();
}
