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

class erHelperJavascript {
	public static function load() {
		static $loaded;
		
		if (!$loaded) {
			$document = JFactory::getDocument();
			$app = JFactory::getApplication();
			$model = JvitalsHelper::loadModel('extendedreg', 'Default');
			$conf = $model->getConfObj();
			
			erHelperHooks::loadHooks('javascript');
			
			$doLoadJQueryUI = true;
			$doLoadJQueryPlugins = true;
			$doLoadValidations = true;
			
			$hook = erHelperHooks::get_hook('javascript.loadLibraries');
			if ($hook) eval($hook);
			
			// This should load the scripts
			$html = JvitalsHtml::getInstance('com_extendedreg');
			
			if ($doLoadJQueryUI) self::load_jQueryUI($app->isAdmin(), $document, $conf);
			if ($doLoadJQueryPlugins) self::load_jQueryPlugins($app->isAdmin(), $document, $conf);
			if ($doLoadValidations) self::load_Validations($app->isAdmin(), $document, $conf);
			
			$declaration = '
				var ER_LANG_STRINGS = \'' . addslashes(json_encode(erHelperLanguage::getLangsForJavascript())) . '\';
				var ER_AJAX_URL = \'' . JURI::base(true) . '/\';
				var ER_FORM_VALIDATION = {};
			';
			
			if ((int)$conf->pass_strength_enable) {
				$declaration .= 'var ER_PASSWORD_STRENGTH_CHECK = 1;' . "\n";
				$declaration .= 'var ER_PASSWORD_STRENGTH_COLORS = ["' . $conf->pass_color_veryweak . '", "' . $conf->pass_color_weak . '", "' . $conf->pass_color_medium . '", "' . $conf->pass_color_strong . '", "' . $conf->pass_color_verystrong . '"];' . "\n";
				$common = trim($conf->pass_common_words);
				if ($common) {
					$common = explode("\n", $common);
					$common = array_map("trim", $common);
					$common = array_map("addslashes", $common);
					$declaration .= 'var ER_PASSWORD_STRENGTH_COMMON = ["' . implode('","', $common) . '"];' . "\n";
				} else {
					$declaration .= 'var ER_PASSWORD_STRENGTH_COMMON = [];' . "\n";
				}
				$declaration .= 'var ER_PASSWORD_STRENGTH_ALLOWED = ' . (int)$conf->pass_allowed_chars . ';' . "\n";
				$declaration .= 'var ER_PASSWORD_STRENGTH_EXPECTED = ' . (int)$conf->pass_expected_chars . ';' . "\n";
				$declaration .= 'var ER_PASSWORD_STRENGTH_MINCHAR = ' . (int)$conf->pass_min_chars . ';' . "\n";
				$declaration .= 'var ER_PASSWORD_STRENGTH_MINLEVEL = ' . (int)$conf->pass_min_level . ';' . "\n";
			} else {
				$declaration .= 'var ER_PASSWORD_STRENGTH_CHECK = 0;' . "\n";
			}
			
			
			$declaration .= 'var ER_VALIDATE_JOOMLA_USERNAME = ' . (int)$conf->validate_joomla_username . ';' . "\n";
			$declaration .= 'var ER_VALIDATE_JOOMLA_EMAIL = ' . (int)$conf->validate_joomla_email . ';' . "\n";
			
			// This will be used in many places so it is moved from the validation to here
			if ((int)$conf->include_jquery_formvalidation) {
				$declaration .= '
					function erValidationUnique(val, id) {
						if (!(val && val.toString().length)) return true;
						var result = true;
						var message = "";
						jQuery.ajax({
							async: false,
							type: "POST",
							url: "' . JURI::base(true) . '/index.php?option=com_extendedreg&task=default.checkunique&s=" + val + "&id=" + id,
							success: function(data) {
								var checkUnique = parseInt(data);
								if (checkUnique != 0) {
									result = false;
									if (id == -1) {
										message = "' . JText::_('COM_EXTENDEDREG_USERNAME_NOTUNIQUE_ERROR') . '";
									} else if (id == -2) {
										message = "' . JText::_('COM_EXTENDEDREG_EMAIL_NOTUNIQUE_ERROR') . '";
									} else {
										message = "' . JText::_('COM_EXTENDEDREG_VALUE_NOTUNIQUE_ERROR') . '";
									}
								}
							}
						});
						
						if (!result) {
							throw message;
							return false;
						}
						
						return true;
					}
					
					function erValidationEmail(val) {
						if (!(val && val.toString().length)) return true;
						var result = true;
						var message = "";
						jQuery.ajax({
							async: false,
							type: "POST",
							url: "' . JURI::root(true) . '/index.php?option=com_extendedreg&task=default.checkemail&s=" + val,
							success: function(data) {
								var checkEmail = parseInt(data);
								if (checkEmail == 1) {
									result = false;
									message = "' . JText::_('COM_EXTENDEDREG_EMAIL_NOTVALID_ERROR') . '";
								} else if (checkEmail == 2) {
									result = false;
									message = "' . JText::_('COM_EXTENDEDREG_EMAIL_BLACKLISTED_ERROR') . '";
								}
							}
						});
						
						if (!result) {
							throw message;
							return false;
						}
						
						return true;
					}
				' . "\n";
			}
			
			erHelperJavascript::OnDomBegin($declaration);
			
			$loaded = true;
		}
	}
	
	private static function load_jQueryUI($isadmin, &$document, &$conf) {
		if (JvitalsDefines::compatibleMode() == '25>') {
			if ((int)$conf->include_jquery_ui == 2) {
				// Google CDN
				$_jQueryURL = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js';
			} else {
				// Local
				$_jQueryURL = JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery-ui.min.js';
			}
		}
		
		if ((boolean)$isadmin || (int)$conf->include_jquery_ui) {
			if (JvitalsDefines::compatibleMode() == '25>') {
				$document->addScript($_jQueryURL);
			} else {
				JHtml::_('jquery.ui', array('core', 'sortable'));
				$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery-ui-joomla3.min.js');
			}
			return true;
		}
		return false;
	}
	
	private static function load_jQueryPlugins($isadmin, &$document, &$conf) {
		$result = false;
		
		if ((boolean)$isadmin || (int)$conf->include_jquery_sprintf) {
			$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery.sprintf.min.js');
			$result = true;
		}

		if ((boolean)$isadmin || (int)$conf->include_jquery_formvalidation) {
			$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery.form_validation.pack.js');
			self::OnDomReady('(function($) { 
				var formcnt = $("form.er-form-validate").length;
				for (i = 0; i < formcnt; i++) {
					$($("form.er-form-validate")[i]).form_validation(); 
				}
			})(jQuery); ');
			
			$result = true;
		}
		
		if ((boolean)$isadmin || (int)$conf->include_jquery_steps) {
			$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery.steps.pack.js');
			self::OnDomReady('(function($) { 
				$("form.er-form").formSteps();
			})(jQuery); ');
			
			$result = true;
		}
		
		if ((boolean)$isadmin) {
			$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery.form_builder.pack.js');
			$result = true;
		}
		
		
		//~ if ((int)$conf->include_jquery_formlayout) {
			$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/js/jquery.form_layout.pack.js');
			if (!(boolean)$isadmin) {
				self::OnDomReady('(function($) { 
					$("form.er-form").fixFormLayout();
				})(jQuery); ');
			}
			$result = true;
		//~ }
		
		return $result;
	}
	
	public static function load_Validations($isadmin, &$document, &$conf) {
		$validations = erHelperAddons::loadAddons('validation');
		if ($validations) {
			foreach ($validations as $lib) {
				$obj = erHelperAddons::getFieldValidation($lib);
				$obj->loadJavascript();
			}
		}
	}
	
	public static function OnDomBegin($string = '', $add = true) {
		static $execute = '';
		if ($add) {
			$execute .= $string . "\n\n";
		} else {
			echo '<script type="text/javascript">' . $execute . '</script>';
		}
		return true;
	}
	
	public static function OnDomReady($string = '', $add = true) {
		static $execute = '';
		if ($add) {
			$execute .= $string . "\n\n";
		} else {
			echo '<script type="text/javascript">' . $execute . '</script>';
		}
		return true;
	}
	
	public static function AddTooltipsAgain() {
		echo '<script type="text/javascript">
			window.addEvent(\'domready\', function() {
				$$(\'.hasTip\').each(function(el) {
					var title = el.get(\'title\');
					if (title) {
						var parts = title.split(\'::\', 2);
						el.store(\'tip:title\', parts[0]);
						el.store(\'tip:text\', parts[1]);
					}
				});
				var JTooltips = new Tips($$(\'.hasTip\'), { maxTitleChars: 50, fixed: false});
			});
		</script>';
		return true;
	}
	
}