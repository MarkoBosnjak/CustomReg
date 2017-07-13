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

class erHelperHTML {
	
	public static function boolean($i, $value, $taskOn = null, $taskOff = null) {
		$html = JHtml::_('grid.boolean', $i, $value, $taskOn, $taskOff);
		return $html;
	}
	
	public static function behavior() {
		static $loaded;
		if (!$loaded) {
			// Build the behavior script.
			erHelperJavascript::OnDomReady('(function($) { 
				var actions = $.merge($(\'a.grid_true\'), $(\'a.grid_false\'));
				actions.each(function(){
					$(this).click(function() {
						eval(\'var args = \' + $(this).attr(\'rel\')); 
						listItemTask(args.id, args.task);
						return false;
					});
				});
			})(jQuery); ');
			$loaded = true;
		}
	}
	
	public static function parseForm($form, $user = null, $viewName = 'formparser') {
		if (!$form) return '';
		if (!trim($form->layout)) return '';
		
		erHelperHooks::loadHooks('html');
		
		$session = JFactory::getSession();
		$fldsession = $session->get('erFldSession', null, 'extendedreg');
		if (trim($fldsession)) {
			$fldsession = unserialize(base64_decode($fldsession));
		}
		if (!is_array($fldsession)) $fldsession = array();
		
		$app = JFactory::getApplication();
		$loggeduser = JFactory::getUser();
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$usersmodel = JvitalsHelper::loadModel('extendedreg', 'Users');
		$conf = $model->getConfObj();
		
		// Custom fields
		$efvals = $model->getExtraFieldsValues();
		$ef = $model->getExtraFieldsInfo();
		
		$ctrl = new JControllerLegacy(array('name' => 'extendedreg'));
		$view = $ctrl->getView($viewName, 'html', '');
		$view->assignRef('user', $user);
		$view->assignRef('form', $form);
		
		$lret = JRequest::getVar('lret', '', 'method', 'base64');
		$view->assignRef('lret', $lret);
		
		$groups = null;
		if ($user && isset($user->user_id)) {
			$groups = $usersmodel->getAssignedGroups((int)$user->user_id);
		}
		$view->assignRef('groups', $groups);
		
		// Dealing with all fields html 
		$fldarrForSession = array();
		$fldarr = array();
		$search = array();
		$replace = array();
		if (preg_match_all('~#(.+?)#~', $form->layout, $m)) {
			$fldarr = array_unique($m[1]);
			foreach ($fldarr as $field) {
				$groupHTML = '';
				$fieldHTML = '';
				$fieldObj = null;
				
				if (preg_match('~^(.+?)_fld$~', $field, $f)) {
					if (in_array($f[1], array('captcha','terms','age'))) {
						if (($view->user && (int)$view->user->id) || ($app->isAdmin())) {
							$search[] = '#' . $field . '#';
							$replace[] = '';
							continue;
						}
					}
					if (($f[1] == 'params') && $app->isAdmin()) {
							$search[] = '#' . $field . '#';
							$replace[] = '';
							continue;
					}
					
					// Default
					$fieldObj = self::getDefaultFieldObj($view, $f[1]);
					$fldarrForSession[] = $field;
				} elseif (preg_match('~^custom_(\d+)$~', $field, $f)) {
					// Custom
					if (@$ef[(int)$f[1]]) {
						$fld = $ef[(int)$f[1]];
						$fieldIDAttr = 'fld_' . $form->id . '_' . $fld->id;
						$fldname =  $fld->name;
						$defval = (isset($fldsession['extreg_fld_' . $fld->name]) ? $fldsession['extreg_fld_' . $fld->name] : (isset($view->user->$fldname) ? $view->user->$fldname : ''));
						$defval = $view->escape($defval);
						$options = (array_key_exists($fld->id, $efvals) ? $efvals[$fld->id] : array());
						
						$tempObject = erHelperAddons::getFieldType($fld);
						
						if ($tempObject->hasSpecialObject()) {
							// Very advanced case
							$fieldObj = $tempObject->getSpecialObject($defval, $options, $view, $fieldIDAttr);
						} else {
							$fieldObj = new stdClass;
							$fieldObj->html = self::renderCustomField($fld, $defval, $options, $view, $fieldIDAttr);
							$fieldObj->required = $fld->required;
							$fieldObj->editable = $fld->editable;
							$fieldObj->name = $fld->name;
							$fieldObj->title = $fld->title;
							$fieldObj->type = $fld->type;
							$fieldObj->tooltip = '';
							$fieldObj->fieldIDAttr = $fieldIDAttr;
							if (trim($fld->description)) {
								$tooltip = htmlspecialchars(JText::_(trim($fld->title)) . '::' . JText::_(trim($fld->description)));
								$fieldObj->tooltip = ' <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_(trim($fld->title))) . '" class="hasTip" title="' . $tooltip . '" border="0" />';
							}
						}
						if ($tempObject->hasFormField()) {
							$fldarrForSession[] = $field;
						}
					}
				} elseif (preg_match('~^group_(\d+)$~', $field, $f)) {
					$fieldIDAttr = 'fld_' . $form->id . '_';
					$groupHTML = self::renderFieldGroup((int)$f[1], $efvals, $view, $model, $fieldIDAttr);
					$fldarrForSession[] = $field;
				}
				
				if ($groupHTML) {
					$search[] = '#' . $field . '#';
					$replace[] = $groupHTML;
				} else {
					if ($fieldObj) {
						// Support for field types that require several fields
						if (!is_array($fieldObj)) {
							$fieldObj = array($fieldObj);
						}
						foreach ($fieldObj as $realFieldObj) {
							$view->assignRef('fieldObj', $realFieldObj);
							if ($app->isSite() && !(int)$realFieldObj->editable && ($view->user && (int)$view->user->id)) {
								$view->setLayout(($realFieldObj->title ? 'fld_noedit_layout' : 'fld_noedit_layout_nolabel'));
							} else {
								$view->setLayout(($realFieldObj->title ? 'fld_layout' : 'fld_layout_nolabel'));
							}
							$fieldHTML .= $view->loadTemplate();
						}
					}
					
					$search[] = '#' . $field . '#';
					$replace[] = $fieldHTML;
				}
			}
		}
		
		// Dealing with form html
		$formObj = json_decode($form->layout);
		
		$stepsCount = 0;
		foreach ($formObj as $obj) {
			if ($obj->type == 'step') {
				$stepsCount ++;
			}
		}
		$view->assignRef('stepsCount', $stepsCount);
		
		$formHTML = self::traceFormObj($view, $formObj);
		$fldarrForSession['secure'] = md5($session->getId() . 'secure');
		
		$fieldsHash = base64_encode(serialize($fldarrForSession));
		$view->assignRef('fieldsHash', $fieldsHash);
		$session->set('erFieldsHash' . (int)$form->id, $fieldsHash, 'extendedreg');
		
		$iAmSuperAdmin = $loggeduser->authorise('core.admin');
		
		$view->assignRef('formHTML', $formHTML);
		$view->assignRef('conf', $conf);
		$view->assignRef('iAmSuperAdmin', $iAmSuperAdmin);
		$view->setLayout('form_element_form');
		$formHTML = $view->loadTemplate();
		
		if (count($search) && count($replace) && count($search) == count($replace)) {
			$formHTML = str_replace($search, $replace, $formHTML);
		}
		
		return $formHTML;
	}
	
	public static function traceFormObj(&$view, $formObj) {
		$result = '';
		foreach ($formObj as $obj) {
			$childrenHTML = '';
			if (is_array($obj->contents)) {
				$childrenHTML = self::traceFormObj($view, $obj->contents);
			}
			$result .= self::getFormElement($view, $obj, $childrenHTML);
		}
		return $result;
	}
	
	public static function getFormElement(&$view, $obj, $childrenHTML) {
		static $stepnum;
		if (!$stepnum) {
			$stepnum = 0;
		}
		
		if (in_array($obj->type, array('fld', 'cfld', 'field', 'cfield', 'cfldgrp'))) {
			return $obj->contents;
		}
		
		if ($obj->type == 'step') {
			$view->assignRef('stepnum', $stepnum);
			$stepnum ++;
		}

		$view->assignRef('childrenHTML', $childrenHTML);
		$view->assignRef('formElement', $obj);
		$view->setLayout('form_element_' . $obj->type);
		return $view->loadTemplate();
	}
	
	public static function getDefaultFieldObj(&$view, $fld) {
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$fldsession = $session->get('erFldSession', null, 'extendedreg');
		if (trim($fldsession)) {
			$fldsession = unserialize(base64_decode($fldsession));
		}
		if (!is_array($fldsession)) $fldsession = array();
		
		$model = JvitalsHelper::loadModel('extendedreg', 'Forms');
		$conf = $model->getConfObj();
		
		$defval = (isset($fldsession['extreg_fld_' . $fld]) ? $fldsession['extreg_fld_' . $fld] : (isset($view->user->$fld) ? $view->user->$fld : ''));
		$defval = $view->escape($defval);
		
		$html = '';
		$fieldObj = null;
		
		if ($fld == 'name') {
			$html = '<input type="text" name="name" id="name-' . (int)$view->form->id . '" value="' . $defval . '" class="required ' . (trim($conf->css_default_input_class)) . '" />';
			$name = 'name';
			$title = 'COM_EXTENDEDREG_REGISTER_NAME';
		} elseif ($fld == 'username') {
			if (!(int)$conf->email_for_username) {
				$html = '<input type="text" id="username-' . (int)$view->form->id . '" name="username" value="' . $defval . '" class="required validate-username ' . (trim($conf->css_default_input_class)) . '" />';
				$name = 'username';
				$title = 'COM_EXTENDEDREG_REGISTER_USERNAME';
				
				// Hide if not register and change is not allowed
				if (!$app->isAdmin() && ($view->user && (int)$view->user->id) && !(int)$conf->allow_uname_change) {
					$html = '';
				}
				
				// Hide if is register and generate random username
				if ((int)$conf->generate_random_uname && !$app->isAdmin() && !($view->user && (int)$view->user->id)) {
					$html = '';
				}
			}
		} elseif ($fld == 'email') {
			$html = '<input type="text" id="email-' . (int)$view->form->id . '" name="email" value="' . $defval . '" class="required validate-email ' . (trim($conf->css_default_input_class)) . '" />';
			$name = 'email';
			$title = 'COM_EXTENDEDREG_REGISTER_EMAIL';
		} elseif ($fld == 'passwd') {
			$html = '<input class="' . (($view->user && (int)$view->user->id) ? '' : ' required') . ((int)$conf->pass_strength_enable ? ' validate-password' : '') . ' ' . (trim($conf->css_default_input_class)) . '" type="password" id="password-' . (int)$view->form->id . '" name="password" autocomplete="off" value="" />';
			$name = 'password';
			$title = 'COM_EXTENDEDREG_REGISTER_PASSWORD';
			
			// Show generate password button
			if ((int)$conf->show_generate_pass) {
				erHelperJavascript::OnDomReady('(function($) { 
					$("#generate-pass-' . (int)$view->form->id . '").dialog({modal: true, autoOpen: false, width: 300});
				})(jQuery); ');
				
				$html = '<div class="generate-pass-holder">
					' . $html . '
					<a href="#" onclick="jQuery(\'#generate-pass-' . (int)$view->form->id . '\').dialog(\'open\');return false;" class="btn btn-primary"><i class="icon-asterisk"></i> ' . JText::_('COM_EXTENDEDREG_REGISTER_GENERATE_PASSWORD') . '</a>
				</div>
				<span id="generate-pass-' . (int)$view->form->id . '" style="display: none;" title="' . JText::_('COM_EXTENDEDREG_REGISTER_GENERATE_PASSWORD') . '">
					<p>' . JText::_('COM_EXTENDEDREG_GENERATE_PASSWORD_DESCR') . '</p>
					<table>
						<tr>
							<td colspan="2"><input type="text" class="generate-pass-input" value=""></td>
						</tr>
						<tr>
							<td><a href="#" class="btn btn-info generate-pass-new">' . JText::_('COM_EXTENDEDREG_GENERATE_PASSWORD_NEW') . '</a></td>
							<td><a href="#" class="btn btn-info generate-pass-use">' . JText::_('COM_EXTENDEDREG_GENERATE_PASSWORD_USE') . '</a></td>
						</tr>
					</table>
				</span>
				';
			}
			
			// Hide if is register and generate random password
			if ((int)$conf->generate_random_pass && !$app->isAdmin() && !($view->user && (int)$view->user->id)) {
				$html = '';
			}
		} elseif ($fld == 'passwd2') {
			$html = '<input class="' . (($view->user && (int)$view->user->id) ? '' : ' required') . ' ' . (trim($conf->css_default_input_class)) . '" type="password" id="verify-password-' . (int)$view->form->id . '" name="verify-password" autocomplete="off" value="" />';
			$name = 'verify-password';
			$title = 'COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD';
			
			// Hide if is register and generate random password
			if ((int)$conf->generate_random_pass && !$app->isAdmin() && !($view->user && (int)$view->user->id)) {
				$html = '';
			}
		} elseif ($fld == 'captcha') {
			if (trim($conf->use_captcha)) {
				$html = self::writeCaptcha();
				$name = 'captcha';
				$title = '';
			}
		} elseif ($fld == 'params') {
			$html = self::writeUserParams($view->user);
			$name = 'params';
			$title = '';
		} elseif ($fld == 'terms') {
			if ((int)$view->form->show_terms) {
				erHelperJavascript::OnDomReady('(function($) { 
					$("#er-terms-content-' . (int)$view->form->id . '").dialog({modal: true, autoOpen: false, width: 600});
				})(jQuery); ');
				
				$terms_onclick = 'jQuery("#er-terms-content-' . (int)$view->form->id . '").dialog("open");return false;';
				
				if ((int)$view->form->terms_article_id) {
					$article = $model->loadTermsArticle((int)$view->form->terms_article_id);
					$terms_value = (trim($article->introtext) ? trim($article->introtext) . '<br/>' : '') . $article->fulltext;
				} else {
					$terms_value = $view->form->terms_value;
				}
				
				$html = '<span id="er-terms-content-' . (int)$view->form->id . '" style="display: none;">' . JText::_($terms_value) . '</span>
				<label class="checkbox"><input type="checkbox" name="acceptedterms" class="required" value="1"' . ((int)$defval ? ' checked' : '') . ' alt="' . JText::_('COM_EXTENDEDREG_ACCEPT_TERMS') . '" /> ' . JText::sprintf('COM_EXTENDEDREG_ACCEPT_TERMS_EXT', $terms_onclick) . '</label>';
				$name = 'acceptedterms';
				$title = '';
			}
		} elseif ($fld == 'age') {
			if ((int)$view->form->show_age) {
				$html = '<label class="checkbox"><input type="checkbox" name="overage" class="required" value="1"' . ((int)$defval ? ' checked' : '') . ' alt="' . JText::sprintf('COM_EXTENDEDREG_OVER_AGE', $view->form->age_value) . '" /> ' . JText::sprintf('COM_EXTENDEDREG_OVER_AGE', $view->form->age_value) . '</label>';
				$name = 'overage';
				$title = '';
			}
		}
		
		$hook = erHelperHooks::get_hook('html.getDefaultFieldObj');
		if ($hook) eval($hook);

		if ($html) {
			$fieldObj = new stdClass;
			$fieldObj->html = $html;
			$fieldObj->required = 1;
			if (($fld == 'passwd' || $fld == 'passwd2') && $view->user && (int)$view->user->id) {
				$fieldObj->required = 0;
			}
			$fieldObj->editable = 1;
			$fieldObj->name = $name;
			$fieldObj->title = $title;
			$fieldObj->type = 'standart_field';
			$fieldObj->tooltip = '';
			$fieldObj->fieldIDAttr = $name;
		}

		return $fieldObj;
	}
	
	public static function writeCaptcha() {
		$lib = erHelperAddons::getCaptchaLib();
		if ($lib) {
			return $lib->write();
		}
		return '';
	}
	
	public static function writeUserParams($user) {
		$ret = '';
		$parampath = JvitalsDefines::comBackPath('com_extendedreg') . 'models';
		$paramsfile = $parampath . DIRECTORY_SEPARATOR . 'user.xml';
		jimport('joomla.form.form');
		
		$form = JForm::getInstance('myform', $paramsfile);
		
		$data = new JRegistry();
		$data->loadString($user->params, 'JSON');
		foreach ($data->toArray() as $key => $val) {
			$form->setValue($key, 'params', $val);
		}
		
		$tmp = array();
		foreach ($form->getFieldset('settings') as $field) {
			$tmp[] = $field->label . '<br />' . $field->input;
		}
		if (count($tmp)) $ret = implode('</div><div class="er-fld-holder">', $tmp);
		unset($tmp);

		return $ret;
	}
	
	public static function renderFieldGroup($grpid, $efvals, &$view, &$model, $fieldIDAttr) {
		$app = JFactory::getApplication();
		$fldarr = $model->getExtraFieldsInfoByGroup($grpid);
		if (!count($fldarr)) return '';
		
		$session = JFactory::getSession();
		$fldsession = $session->get('erFldSession', null, 'extendedreg');
		if (trim($fldsession)) {
			$fldsession = unserialize(base64_decode($fldsession));
		}
		if (!is_array($fldsession)) $fldsession = array();
		
		$conf = $model->getConfObj();
		
		$groupHTML = '';
		foreach ($fldarr as $fld) {
			$fieldHTML = '';
			$fieldObj = null;
			
			$fldname =  $fld->name;
			$defval = (isset($fldsession['extreg_fld_' . $fld->name]) ? $fldsession['extreg_fld_' . $fld->name] : (isset($view->user->$fldname) ? $view->user->$fldname : ''));
			$defval = $view->escape($defval);
			$options = (array_key_exists($fld->id, $efvals) ? $efvals[$fld->id] : array());
			
			$tempObject = erHelperAddons::getFieldType($fld);
			
			if ($tempObject->hasSpecialObject()) {
				// Very advanced case
				$fieldObj = $tempObject->getSpecialObject($defval, $options, $view, $fieldIDAttr . $fld->id);
			} else {
				$fieldObj = new stdClass;
				$fieldObj->html = self::renderCustomField($fld, $defval, $options, $view, $fieldIDAttr . $fld->id);
				$fieldObj->required = $fld->required;
				$fieldObj->editable = $fld->editable;
				$fieldObj->name = $fld->name;
				$fieldObj->title = $fld->title;
				$fieldObj->type = $fld->type;
				$fieldObj->tooltip = '';
				$fieldObj->fieldIDAttr = $fieldIDAttr . $fld->id;
				if (trim($fld->description)) {
					$tooltip = htmlspecialchars(JText::_(trim($fld->title)) . '::' . JText::_(trim($fld->description)));
					$fieldObj->tooltip = ' <img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" alt="' . htmlspecialchars(JText::_(trim($fld->title))) . '" class="hasTip" title="' . $tooltip . '" border="0" />';
				}
			}
			
			if ($fieldObj) {
				// Support for field types that require several fields
				if (!is_array($fieldObj)) {
					$fieldObj = array($fieldObj);
				}
				foreach ($fieldObj as $realFieldObj) {
					$view->assignRef('fieldObj', $realFieldObj);
					if ($app->isSite() && !(int)$realFieldObj->editable && ($view->user && (int)$view->user->id)) {
						$view->setLayout(($realFieldObj->title ? 'fld_noedit_layout' : 'fld_noedit_layout_nolabel'));
					} else {
						$view->setLayout(($realFieldObj->title ? 'fld_layout' : 'fld_layout_nolabel'));
					}
					$groupHTML .= $view->loadTemplate();
				}
			}
		}
		
		$hook = erHelperHooks::get_hook('html.renderFieldGroup');
		if ($hook) eval($hook);
		
		return $groupHTML;
	}
	
	public static function renderCustomField(&$fld, $defval, $options, &$view, $fieldIDAttr) {
		$app = JFactory::getApplication();
		$fldObject = erHelperAddons::getFieldType($fld);
		if (($view->user && (int)$view->user->id)) {
			$fldObject->setUser($view->user);
		}
		if ($fldObject->hideTitle()) {
			$fld->hidden_title = $fld->title;
			$fld->title = '';
		}
		
		if ($app->isSite() && !(int)$fld->editable && ($view->user && (int)$view->user->id)) {
			$html = $fldObject->getNoeditHtml($defval);
		} else {
			$html = $fldObject->getHtml($defval, $fieldIDAttr);
		}
		
		$hook = erHelperHooks::get_hook('html.renderCustomField');
		if ($hook) eval($hook);
		
		return $html;
	}
	
	public static function formatDate($input, $format) {
		$user = JFactory::getUser();
		$srv_tz = new DateTimeZone(date('e'));
		$date = JFactory::getDate($input, $srv_tz);
		if ($user->getParam('timezone')) {
			$user_tz = new DateTimeZone($user->getParam('timezone'));
			$date->setTimezone($user_tz);
		}
		return $date->format($format, true);
	}
	
}