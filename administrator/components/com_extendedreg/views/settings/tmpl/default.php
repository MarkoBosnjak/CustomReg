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

echo $this->html->wrapperStart();

$document = JFactory::getDocument();

if ((int)$this->confobj->use_editor == 2) {
	$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/lib/nicedit/nicEdit.js');
}

erHelperJavascript::OnDomBegin('', false);

erHelperJavascript::OnDomReady('var er_settings_changed = false;
	jQuery("' . (JvitalsDefines::compatibleMode() != '30>' ? '#conftabs' : '.tab-content') . '").on("change", "input,textarea,select", function(event){
		er_settings_changed = true;
	});
	jQuery(document).ready(function () {
		window.onbeforeunload = function(e) {
			if (er_settings_changed == true) {
				return \'Attention\';
			}
		}
	});');


$document = JFactory::getDocument();
$onSaveJavascriptOperations = '';

erHelperJavascript::OnDomReady('jQuery(document).ready(function(jQuery) {
		jQuery(\'a[data-toggle="tab"]\').on(\'shown\', function (e) {
			jQuery(e.target).blur();
			window.location.href = jQuery(e.target).attr(\'data-link\');
			return false;
		});
	});
');

?>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="config_save" value="1" />
	<input type="hidden" name="task" value="default.save_settings" />
	<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<?php $grp = 0; foreach ($this->conf_grp as $group) : ?>
				<li<?php echo $this->group == $group ? ' class="active"' : ''; ?>><a href="#conftabs-<?php echo $grp; ?>"  data-toggle="tab" data-link="<?php echo JRoute::_('index.php?option=com_extendedreg&task=settings&group=' . $group); ?>"><?php echo JText::_('COM_EXTENDEDREG_OPT_GROUP_' . mb_strtoupper($group)); ?></a></li>
				<?php $grp ++; endforeach; ?>
			</ul>
			<div class="tab-content">
				<!-- Begin Tabs -->
				<?php $grp = 0; foreach ($this->conf_grp as $group) : ?>
				<div id="conftabs-<?php echo $grp; ?>"></div>
				<?php $grp ++; endforeach; ?>
				<?php $grp = 0; foreach ($this->conf as $group => $conf) : ?>
				<div class="tab-pane<?php echo ($grp == 0 ? ' active' : ''); ?>" id="conftabs-<?php echo $grp; ?>">
					<div class="row-fluid">
						<div class="span8">
							<div class="separator"><h2><?php echo JText::_('COM_EXTENDEDREG_TAB_WARNING'); ?></h2></div>
							<?php foreach ($conf as $arr) : 
								if (preg_match('/^er_.*$/', $arr['optname'])) { continue; }
								$label = JText::_('COM_EXTENDEDREG_OPT_' . mb_strtoupper($arr['optname']));
								$description = '';
								if (trim($arr['description'])) $description = '<p>' . JText::_(trim($arr['description'])) . '</p>';
								
								$pluginParse = false;
								$hook = erHelperHooks::get_hook('admin.parseSettings');
								if ($hook) eval($hook);
								if ($pluginParse) continue;
							?>
								<?php if (mb_strtolower($group) == 'captcha' && $arr['optname'] == 'simple_captcha_width') : ?>
								<div class="separator">
									<h3>SimpleCaptcha</h3>
								</div>
								<?php elseif (mb_strtolower($group) == 'javascript' && $arr['optname'] == 'include_jquery_ui') : ?>
								<div class="separator">
									<h3><?php echo JText::_('COM_EXTENDEDREG_OPT_GROUP_JAVASCRIPT_ADVANCED'); ?></h3>
									<h4><?php echo JText::_('COM_EXTENDEDREG_OPT_GROUP_JAVASCRIPT_WARNING'); ?></h4>
								</div>
								<?php elseif (mb_strtolower($group) == 'html' && $arr['optname'] == 'html_before_default') : ?>
								<div class="separator">
									<h4><?php echo JText::_('COM_EXTENDEDREG_OPT_GROUP_HTML_DESCR'); ?></h4>
								</div>
								<?php elseif (mb_strtolower($group) == 'sef' && $arr['optname'] == 'sef_login') : ?>
								<div class="separator">
									<h4><?php echo JText::_('COM_EXTENDEDREG_OPT_GROUP_SEF_DESCR'); ?></h4>
								</div>
								<?php elseif (mb_strtolower($group) == 'security' && $arr['optname'] == 'use_max_login_front') : ?>
								<div class="separator">
									<h3><?php echo JText::_('COM_EXTENDEDREG_MAX_LOGIN_HEADER'); ?></h3>
									<h4><?php echo JText::_('COM_EXTENDEDREG_MAX_LOGIN_DESCR'); ?></h4>
								</div>
								<?php elseif (mb_strtolower($group) == 'security' && $arr['optname'] == 'use_secret_hash') : ?>
								<div class="separator">
									<h3><?php echo JText::_('COM_EXTENDEDREG_SECRET_HASH_HEADER'); ?></h3>
									<h4><?php echo JText::_('COM_EXTENDEDREG_SECRET_HASH_DESCR'); ?></h4>
								</div>
								<?php elseif (mb_strtolower($group) == 'default' && $arr['optname'] == 'replace_name_with') : ?>
								<div class="separator">
									<h3><?php echo JText::_('COM_EXTENDEDREG_REPLACE_JOOMLA_FIELDS'); ?></h3>
									<h4><?php echo JText::_('COM_EXTENDEDREG_REPLACE_JOOMLA_FIELDS_WARNING'); ?></h4>
									<b><?php echo JText::_('COM_EXTENDEDREG_REPLACE_JOOMLA_FIELDS_LEGEND'); ?></b>
								</div>
								<?php elseif (mb_strtolower($group) == 'emails' && (strpos($arr['optname'], '_subj') !== false) ) : ?>
								<div class="separator"><hr /></div>
								<?php endif; ?>
								<div class="control-group">
									<div class="control-label" style="width: 160px;">
										<label<?php echo ($description ? ' title="'.htmlspecialchars($label .'::'.$description).'" class="hasTip"' : ''); ?> for="cnf-<?php echo $arr['optname']; ?>" id="<?php echo $arr['optname']; ?>-lbl" <?php echo (in_array($arr['optname'], array('remove_tables_on_uninstall', 'disable_ip_track')) ? 'style="color: #FF0000;font-weight: bold;"' : ''); ?>>
											<?php echo $label; ?>
											<?php echo ($description ? '<img src="' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/hint.png" class="hasTip" alt="" title="'.htmlspecialchars($label .'::'.$description).'" style="vertical-align:middle;" />' : ''); ?>
										</label>
									</div>
									<div class="controls">
										<?php if (in_array($arr['optname'], array('allow_user_registration', 'allow_user_login', 'enable_user_activation', 'enable_admin_approval', 'allow_uname_change', 'email_for_username', 'include_jquery_tojson', 'include_jquery_sprintf', 'include_jquery_steps', 'use_password_renew', 'login_notify_admins'))) : ?>
										
										<fieldset class="radio btn-group" id="cnf-<?php echo $arr['optname']; ?>">
											<input type="radio" id="<?php echo $arr['optname']; ?>_no" name="<?php echo $arr['optname']; ?>" value="0"<?php echo ((int)$arr['value'] ? '' : ' checked="checked"'); ?> /> <label for="<?php echo $arr['optname']; ?>_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
											<input type="radio" id="<?php echo $arr['optname']; ?>_yes" name="<?php echo $arr['optname']; ?>" value="1"<?php echo ((int)$arr['value'] ? ' checked="checked"' : ''); ?> /> <label for="<?php echo $arr['optname']; ?>_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
										</fieldset>
										
										<?php elseif (in_array($arr['optname'], array('disable_ip_track', 'remove_tables_on_uninstall', 'include_jquery_formlayout', 'include_jquery_formvalidation', 'include_jquery_uniform', 'use_opts_sql', 'simple_captcha_use_random', 'simple_captcha_bg_transparent', 'pass_strength_enable', 'forbid_proxies', 'enable_request_activation_mail'))) : ?>
											
										<fieldset class="radio btn-group" id="cnf-<?php echo $arr['optname']; ?>">
											<input type="radio" id="<?php echo $arr['optname']; ?>_no" name="<?php echo $arr['optname']; ?>" value="0"<?php echo ((int)$arr['value'] ? '' : ' checked="checked"'); ?> /> <label for="<?php echo $arr['optname']; ?>_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
											<input type="radio" id="<?php echo $arr['optname']; ?>_yes" name="<?php echo $arr['optname']; ?>" value="1"<?php echo ((int)$arr['value'] ? ' checked="checked"' : ''); ?> /> <label for="<?php echo $arr['optname']; ?>_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
										</fieldset>
											
										<?php elseif (in_array($arr['optname'], array('validate_joomla_username', 'validate_joomla_email', 'allow_terminate', 'use_max_login_front', 'use_max_login_back', 'generate_random_uname', 'generate_random_pass', 'css_front_extreg', 'css_front_jquery', 'css_back_extreg', 'css_back_jquery', 'use_secret_hash', 'blockip_max_login_front', 'blockip_max_login_back', 'show_generate_pass', 'redirect_login_screens'))) : ?>
											
										<fieldset class="radio btn-group" id="cnf-<?php echo $arr['optname']; ?>">
											<input type="radio" id="<?php echo $arr['optname']; ?>_no" name="<?php echo $arr['optname']; ?>" value="0"<?php echo ((int)$arr['value'] ? '' : ' checked="checked"'); ?> /> <label for="<?php echo $arr['optname']; ?>_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
											<input type="radio" id="<?php echo $arr['optname']; ?>_yes" name="<?php echo $arr['optname']; ?>" value="1"<?php echo ((int)$arr['value'] ? ' checked="checked"' : ''); ?> /> <label for="<?php echo $arr['optname']; ?>_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
										</fieldset>
										
										<?php elseif ($arr['optname'] == 'terminate_type') : ?>
										
											<select name="terminate_type" id="cnf-terminate_type">
												<option value="1"<?php echo ((int)$arr['value'] == 1 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_TERMINATE_TYPE_1'); ?></option>
												<option value="2"<?php echo ((int)$arr['value'] == 2 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_TERMINATE_TYPE_2'); ?></option>
											</select>
										
										<?php elseif (in_array($arr['optname'], array('max_login_attempt_units_front', 'max_login_attempt_units_back', 'max_login_block_units_front', 'max_login_block_units_back'))) : ?>
											
											<select name="<?php echo $arr['optname']; ?>" id="cnf-<?php echo $arr['optname']; ?>">
												<option value="0"<?php echo ((int)$arr['value'] == 0 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_MINUTES'); ?></option>
												<option value="1"<?php echo ((int)$arr['value'] == 1 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_HOURS'); ?></option>
												<option value="2"<?php echo ((int)$arr['value'] == 2 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_DAYS'); ?></option>
											</select>
										
										<?php elseif ($arr['optname'] == 'include_jquery_ui') : ?>
										
											<select name="include_jquery_ui" id="cnf-include_jquery_ui">
												<option value="0"<?php echo ((int)$arr['value'] == 0 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></option>
												<option value="1"<?php echo ((int)$arr['value'] == 1 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_INCLUDE_JQUERY_LOCAL'); ?></option>
												<option value="2"<?php echo ((int)$arr['value'] == 2 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_INCLUDE_JQUERY_GOOGLE_CDN'); ?></option>
											</select>
									
										<?php elseif (mb_strtolower($group) == 'emails' && (strpos($arr['optname'], '_subj') !== false) ) : ?>
										
											<input type="text" name="<?php echo $arr['optname']; ?>" id="cnf-<?php echo $arr['optname']; ?>" value="<?php echo $arr['value']; ?>" style="width:95%;" />
											
										<?php elseif ((mb_strtolower($group) == 'emails' && (strpos($arr['optname'], '_subj') === false) && $arr['optname'] != 'admin_mails' && $arr['optname'] != 'default_mailfrom') || $arr['optname'] == 'terms_text') : ?>
										
											<?php 
												$editor_value = $arr['value'];
												if (!(int)$this->confobj->use_editor) {
													echo $this->editor->display($arr['optname'], $editor_value, '95%', '250', '70', '15', false, array());
												} else {
													?>
													<textarea name="<?php echo $arr['optname']; ?>" style="width: 600px;height: 200px;" id="cnf-<?php echo $arr['optname']; ?>"><?php echo $editor_value; ?></textarea>
													<?php
													if ((int)$this->confobj->use_editor == 2) {
														$document->addScriptDeclaration("
															bkLib.onDomLoaded(function() {
																new nicEditor({iconsPath : '" . JvitalsDefines::comBackPath('com_extendedreg', true) . "assets/lib/nicedit/nicEditorIcons.gif', buttonList : ['bold', 'italic', 'underline', 'left', 'center', 'right', 'justify', 'ol', 'ul', 'fontSize', 'fontFamily', 'fontFormat', 'link', 'unlink', 'forecolor', 'bgcolor', 'xhtml']}).panelInstance('" . $arr['optname'] . "');
															});
														");
														$onSaveJavascriptOperations .= 'jQuery("#' . $arr['optname'] . '").val(nicEditors.findEditor("' . $arr['optname'] . '").getContent());';
													}
												}
											?>
										<?php elseif (mb_strtolower($group) == 'emails' && $arr['optname'] == 'admin_mails') : ?>
										
											<textarea name="<?php echo $arr['optname']; ?>" rows="5" style="width:95%" id="cnf-<?php echo $arr['optname']; ?>"><?php echo $arr['value']; ?></textarea>

										<?php elseif (mb_strtolower($group) == 'html') : ?>
										
											<textarea name="<?php echo $arr['optname']; ?>" rows="5" style="width:95%" id="cnf-<?php echo $arr['optname']; ?>"><?php echo $arr['value']; ?></textarea>
										
										<?php elseif ($arr['optname'] == 'use_captcha') : ?>
											
											<select name="use_captcha" id="cnf-use_captcha">
												<?php 
													$captcha_sel = false;
													foreach ($this->captcha_libs as $lib) : 
														if ($arr['value'] == $lib->file_name) $captcha_sel = true;
												?>
												<option value="<?php echo $lib->file_name; ?>"<?php echo ($arr['value'] == $lib->file_name ? ' selected' : ''); ?>><?php echo JText::_($lib->name); ?></option>
												<?php endforeach; ?>
												<option value="none"<?php echo (!$captcha_sel ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_NONE'); ?></option>
											</select>
											
										<?php elseif ($arr['optname'] == 'css_theme') : ?>
											
											<select name="css_theme" id="cnf-css_theme">
												<?php 
													$css_theme_sel = false;
													foreach ($this->css_themes as $theme) : 
														$theme = JFile::stripExt(basename($theme));
														if ($arr['value'] == $theme) $css_theme_sel = true;
												?>
												<option value="<?php echo $theme; ?>"<?php echo ($arr['value'] == $theme ? ' selected' : ''); ?>><?php echo JText::_($theme); ?></option>
												<?php endforeach; ?>
												<option value=""<?php echo (!$css_theme_sel ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_NONE'); ?></option>
											</select>
											
										<?php elseif (in_array($arr['optname'], array('blacklist_usernames', 'blacklist_emails', 'blacklist_ips', 'simple_captcha_colors', 'pass_common_words'))) : ?>
											
											<textarea name="<?php echo $arr['optname']; ?>" rows="<?php echo ($arr['optname'] == 'simple_captcha_colors' ? 3 : 10); ?>" cols="50" id="cnf-<?php echo $arr['optname']; ?>"><?php echo $arr['value']; ?></textarea>
											
										<?php elseif ($arr['optname'] == 'use_editor') : ?>
											
											<select name="use_editor" id="cnf-use_editor">
												<option value="0"<?php echo ((int)$arr['value'] == 0 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_USE_EDITOR_JOOMLA'); ?></option>
												<option value="1"<?php echo ((int)$arr['value'] == 1 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_NONE'); ?></option>
												<option value="2"<?php echo ((int)$arr['value'] == 2 ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_USE_EDITOR_NICEDIT'); ?></option>
											</select>
											
										<?php elseif ($arr['optname'] == 'redir_url_default') : ?>
											
											<select name="redir_url_default" id="cnf-redir_url_default">
												<option value="er_home"<?php echo ($arr['value'] == 'er_home' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_HOME'); ?></option>
												<option value="er_login_register"<?php echo ($arr['value'] == 'er_login_register' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_LOGINREGISTER'); ?></option>
												<option value="er_login"<?php echo ($arr['value'] == 'er_login' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_LOGIN'); ?></option>
												<option value="er_register"<?php echo ($arr['value'] == 'er_register' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_REGISTER'); ?></option>
											</select>
											
										<?php elseif ($arr['optname'] == 'pass_min_level') : ?>
											
											<select name="pass_min_level" id="cnf-pass_min_level">
												<option value="0"<?php echo ($arr['value'] == '0' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_PASS_LEVEL_VERYWEAK'); ?></option>
												<option value="1"<?php echo ($arr['value'] == '1' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_PASS_LEVEL_WEAK'); ?></option>
												<option value="2"<?php echo ($arr['value'] == '2' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_PASS_LEVEL_MEDIUM'); ?></option>
												<option value="3"<?php echo ($arr['value'] == '3' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_PASS_LEVEL_STRONG'); ?></option>
												<option value="4"<?php echo ($arr['value'] == '4' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_OPT_PASS_LEVEL_VERYSTRONG'); ?></option>
											</select>
											
										<?php elseif ($arr['optname'] == 'pass_allowed_chars' || $arr['optname'] == 'pass_expected_chars') : ?>
											<?php echo erHelperPassword::renderCharsInputs($arr); ?>
										<?php elseif (in_array($arr['optname'], array('redir_url_register', 'redir_url_reg_need_activation', 'redir_url_reg_need_approval', 'redir_url_activation', 'redir_url_activ_need_approval', 'redir_url_wrong_password', 'redir_url_forgot_password', 'redir_url_forgot_username', 'redir_url_login', 'redir_url_logout', 'redir_url_request_activation'))) : ?>
											
											<select name="<?php echo $arr['optname']; ?>" id="cnf-<?php echo $arr['optname']; ?>">
												<option value="er_default"<?php echo ($arr['value'] == 'er_default' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_DEFAULT'); ?></option>
												<option value="er_home"<?php echo ($arr['value'] == 'er_home' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_HOME'); ?></option>
												<option value="er_login_register"<?php echo ($arr['value'] == 'er_login_register' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_LOGINREGISTER'); ?></option>
												<option value="er_login"<?php echo ($arr['value'] == 'er_login' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_LOGIN'); ?></option>
												<option value="er_register"<?php echo ($arr['value'] == 'er_register' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_REGISTER'); ?></option>
												<option value="er_other"<?php echo ($arr['value'] == 'er_other' ? ' selected' : ''); ?>><?php echo JText::_('COM_EXTENDEDREG_REDIR_OTHER'); ?></option>
											</select>
											
										<?php else : ?>
											<?php $style= ''; if (in_array($arr['optname'], array('redir_url_register_other', 'redir_url_reg_need_activation_other', 'redir_url_reg_need_approval_other', 'redir_url_activation_other', 'redir_url_activ_need_approval_other', 'redir_url_wrong_password_other', 'redir_url_forgot_password_other', 'redir_url_forgot_username_other', 'redir_url_login_other', 'redir_url_logout_other', 'redir_url_request_activation_other'))) : ?>
											<span><?php echo JURI::root(); $style= ' style="width: 70%;"'; ?></span>
											<?php endif; ?>
											
											<input class="inputbox" type="text" name="<?php echo $arr['optname']; ?>" id="cnf-<?php echo $arr['optname']; ?>" value="<?php echo $arr['value']; ?>"<?php echo $style; ?> />
											
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<?php if (mb_strtolower($group) == 'emails') : ?>
						<div class="span4">
							<fieldset>
								<legend><?php echo JText::_('COM_EXTENDEDREG_LEGEND_LABEL'); ?></legend>
								<p><?php echo JText::_('COM_EXTENDEDREG_OPT_GROUP_EMAILS_LEGEND'); ?></p>
							</fieldset>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php $grp ++; endforeach; ?>
				<!-- End Tabs -->
			</div>
		</div>
		<!-- End Content -->
	</div>
</form>
<?php
$document->addScriptDeclaration('
	function performAllActions(task) {
		er_settings_changed = false;
		' . $onSaveJavascriptOperations . '
		Joomla.submitbutton(task);
	}
');

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();