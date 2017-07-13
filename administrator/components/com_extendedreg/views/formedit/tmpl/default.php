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

if ((int)$this->conf->use_editor == 2) {
	$document->addScript(JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/lib/nicedit/nicEdit.js');
}

erHelperJavascript::OnDomBegin('', false);

?>
<script language="JavaScript">
	function fldSubmit(t) {
		if (t.task.value == 'forms.save' || t.task.value == 'forms.apply') {
			jQuery.convertLayoutToObject(t.layout);
		}
		return true;
	}
	
	function setFormAlign(aln) {
		jQuery("#form_style_align").val(aln);
		jQuery("#form_style_align_chooser a").removeClass("selected");
		jQuery("#form_style_align_chooser a.frm_" + aln).addClass("selected");
	}
</script>
<?php

erHelperJavascript::OnDomReady('(function($) { 
	$("#form-layout").form_builder("' . addslashes($this->form->layout) . '");
	
	$(\'input[name="terms_switcher"]\').click(function() {
		if ($("#terms_switcher_no").prop("checked")) {
			$("#terms_switcher_0").show();
			$("#terms_switcher_1").hide();
		} else {
			$("#terms_switcher_0").hide();
			$("#terms_switcher_1").show();
		}
	});
})(jQuery); ');


$document = JFactory::getDocument();
$onSaveJavascriptOperations = '';

?>
<style>
.form-horizontal .controls {
	margin-left: 250px;
}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm" onsubmit="return fldSubmit(this);" class="form-horizontal">
	<input type="hidden" name="extension" value="com_extendedreg" />
	<input type="hidden" name="task" value="forms.save" />
	<input type="hidden" name="id" value="<?php echo (int)$this->form->id; ?>" />
	<input type="hidden" name="cid" value="<?php echo (int)$this->form->id; ?>" />
	<input type="hidden" id="form_style_align" name="form_style_align" value="<?php echo $this->form->form_style_align; ?>" />
	<input type="hidden" name="layout" value="<?php echo htmlentities($this->form->layout); ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<fieldset>
		<legend><?php echo JText::_('COM_EXTENDEDREG_FIELDS_LEGEND'); ?></legend>
		<div class="row-fluid">
			<div class="span9">
				<div class="control-group">
					<label for="name" id="name-lbl" class="control-label"><?php echo JText::_('COM_EXTENDEDREG_FORMS_NAME'); ?><span class="required">*</span></label>
					<div class="controls"><input type="text" id="name" name="name" value="<?php echo $this->form->name; ?>" size="30" /></div>
				</div>
				<div class="control-group">
					<label class="control-label hasTip" id="mailfrom-lbl" for="mailfrom" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_MAILFROM')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_MAILFROM_HELP')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_MAILFROM'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" style="vertical-align:middle;" />
					</label>
					<div class="controls"><input type="text" id="mailfrom" name="mailfrom" value="<?php echo $this->form->mailfrom; ?>" size="30" /></div>
				</div>
				<div class="control-group">
					<label class="control-label hasTip" id="admin_mails-lbl" for="admin_mails" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_OPT_ADMIN_MAILS')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_HELP_ADMIN_MAILS')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_OPT_ADMIN_MAILS'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt=""  style="vertical-align:middle;" />
					</label>
					<div class="controls"><textarea id="admin_mails" name="admin_mails"><?php echo $this->form->admin_mails; ?></textarea></div>
				</div>
				<div class="control-group">
					<label class="control-label" id="description-lbl" for="description"><?php echo JText::_('COM_EXTENDEDREG_FORMS_DESCR'); ?></label>
					<div class="controls"><textarea id="description" name="description"><?php echo $this->form->description; ?></textarea></div>
				</div>
				<div class="control-group">
					<label class="control-label" id="isdefault-lbl" for="isdefault"><?php echo JText::_('COM_EXTENDEDREG_FORMS_ISDEFAULT'); ?></label>
					<div class="controls">
						<fieldset class="radio btn-group" id="isdefault">
							<input type="radio" id="isdefault_no" name="isdefault" value="0"<?php echo ((int)$this->form->isdefault ? '' : ' checked="checked"'); ?> /><label for="isdefault_no"> <?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="isdefault_yes" name="isdefault" value="1"<?php echo ((int)$this->form->isdefault ? ' checked="checked"' : ''); ?> /><label for="isdefault_yes"> <?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" id="published-lbl" for="published"><?php echo JText::_('COM_EXTENDEDREG_STATE_PUBLISHED'); ?></label>
					<div class="controls">
						<fieldset class="radio btn-group" id="published">
							<input type="radio" id="published_no" name="published" value="0"<?php echo ((int)$this->form->published ? '' : ' checked="checked"'); ?> /><label for="published_no"> <?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="published_yes" name="published" value="1"<?php echo ((int)$this->form->published ? ' checked="checked"' : ''); ?> /><label for="published_yes"> <?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label hasTip" id="groups-lbl" for="groups" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_NEWUSER_GROUP')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_NEWUSER_GROUP_INFO')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_NEWUSER_GROUP'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_NEWUSER_GROUP')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_NEWUSER_GROUP_INFO')); ?>" style="vertical-align:middle;" />
					</label>
					<?php 
						$selgroups = explode(',', $this->form->groups);
						echo JHtml::_('access.usergroups', 'groups', $selgroups);
					?>
				</div>
				<div class="control-group">
					<label class="control-label hasTip" id="show_terms-lbl" for="show_terms" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_SHOW_TERMS')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_SHOW_TERMS_HELP')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_SHOW_TERMS'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" style="vertical-align:middle;" />
					</label>
					<div class="controls">
						<fieldset class="radio btn-group" id="show_terms">
							<input type="radio" id="show_terms_no" name="show_terms" value="0"<?php echo ((int)$this->form->show_terms ? '' : ' checked="checked"'); ?> /><label for="show_terms_no"> <?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="show_terms_yes" name="show_terms" value="1"<?php echo ((int)$this->form->show_terms ? ' checked="checked"' : ''); ?> /><label for="show_terms_yes"> <?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" id="terms_switcher-lbl" for="terms_switcher"><?php echo JText::_('COM_EXTENDEDREG_FORMS_TERMS_SWITCHER'); ?></label>
					<div class="controls">
						<fieldset class="radio btn-group" id="terms_switcher">
							<input type="radio" id="terms_switcher_no" name="terms_switcher" value="0"<?php echo ((int)$this->form->terms_switcher ? '' : ' checked="checked"'); ?> /><label for="terms_switcher_no"> <?php echo JText::_('COM_EXTENDEDREG_FORMS_TERMS_SWITCHER_0'); ?></label>
							<input type="radio" id="terms_switcher_yes" name="terms_switcher" value="1"<?php echo ((int)$this->form->terms_switcher ? ' checked="checked"' : ''); ?> /><label for="terms_switcher_yes"> <?php echo JText::_('COM_EXTENDEDREG_FORMS_TERMS_SWITCHER_1'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group" id="terms_switcher_0"<?php echo ((int)$this->form->terms_switcher ? ' style="display: none;"' : ''); ?>>
					<label class="control-label hasTip" id="terms_value-lbl" for="terms_value" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_TERMS_VALUE')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_TERMS_VALUE_HELP')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_TERMS_VALUE'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" style="vertical-align:middle;" />
					</label>
					<div class="controls er-editor">
						<?php 
							if (!(int)$this->conf->use_editor) {
								$editor = JFactory::getEditor();
								echo $editor->display('terms_value', $this->form->terms_value, '95%', '250', '70', '15', false, array());
							} else {
								?>
								<textarea name="terms_value" style="width: 600px !important;height: 200px;" id="terms_value"><?php echo $this->form->terms_value; ?></textarea>
								<?php
								if ((int)$this->conf->use_editor == 2) {
									$document->addScriptDeclaration("
										bkLib.onDomLoaded(function() {
											new nicEditor({iconsPath : '" . JvitalsDefines::comBackPath('com_extendedreg', true) . "assets/lib/nicedit/nicEditorIcons.gif', buttonList : ['bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','link','unlink','forecolor','bgcolor','xhtml']}).panelInstance('terms_value');
										});
									");
									$onSaveJavascriptOperations .= 'jQuery("#terms_value").val(nicEditors.findEditor("terms_value").getContent());';
								}
							}
						?>
					</div>
				</div>
				<div class="control-group" id="terms_switcher_1"<?php echo ((int)$this->form->terms_switcher ? '' : ' style="display: none;"'); ?>>
					<label class="control-label hasTip" id="terms_article_id-lbl" for="terms_article_id" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_TERMS_ARTICLE_ID')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_TERMS_ARTICLE_ID_HELP')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_TERMS_ARTICLE_ID'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" style="vertical-align:middle;" />
					</label>
					<div class="controls"><input type="text" id="terms_article_id" name="terms_article_id" value="<?php echo $this->form->terms_article_id; ?>" size="30" /></div>
				</div>
				<div class="control-group">
					<label class="control-label hasTip" id="show_age-lbl" for="show_age" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_SHOW_AGE')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_SHOW_AGE_HELP')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_SHOW_AGE'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" style="vertical-align:middle;" />
					</label>
					<div class="controls">
						<fieldset class="radio btn-group" id="show_age">
							<input type="radio" id="show_age_no" name="show_age" value="0"<?php echo ((int)$this->form->show_age ? '' : ' checked="checked"'); ?> /><label for="show_age_no"> <?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="show_age_yes" name="show_age" value="1"<?php echo ((int)$this->form->show_age ? ' checked="checked"' : ''); ?> /><label for="show_age_yes"> <?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>								
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label hasTip" id="age_value-lbl" for="age_value" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_AGE_VALUE')); ?>::<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_FORMS_AGE_VALUE_HELP')); ?>">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_AGE_VALUE'); ?>
						<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" alt="" style="vertical-align:middle;" />
					</label>
					<div class="controls"><input type="text" id="age_value" name="age_value" value="<?php echo $this->form->age_value; ?>" size="30" /></div>
				</div>
				<div class="control-group">
					<label class="control-label" id="form_style_width-lbl" for="form_style_width">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_FORM_STYLE_WIDTH'); ?>
					</label>
					<div class="controls"><input type="text" id="form_style_width" name="form_style_width" value="<?php echo $this->form->form_style_width; ?>" size="30" /></div>
				</div>
				<div class="control-group">
					<label class="control-label" id="form_style_align-lbl" for="form_style_align">
						<?php echo JText::_('COM_EXTENDEDREG_FORMS_FORM_STYLE_ALIGN'); ?>
					</label>
					<div class="controls">
						<table cellspacing="2" cellpadding="0" border="0" id="form_style_align_chooser">
							<tr>
								<td><a onclick="setFormAlign('align_margin'); return false;" href="#" class="frm_align_margin <?php echo ($this->form->form_style_align == 'align_margin' ? ' selected' : ''); ?>"><img border="0" title="To margin" alt="To margin" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_margin.png"></a></td>
								<td><a onclick="setFormAlign('align_center'); return false;" href="#" class="frm_align_center <?php echo ($this->form->form_style_align == 'align_center' ? ' selected' : ''); ?>"><img border="0" title="To center" alt="To center" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_center.png"></a></td>
								<td><a onclick="setFormAlign('align_left'); return false;" href="#" class="frm_align_left <?php echo ($this->form->form_style_align == 'align_left' ? ' selected' : ''); ?>"><img border="0" title="To left" alt="To left" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_left.png"></a></td>
								<td><a onclick="setFormAlign('align_right'); return false;" href="#" class="frm_align_right <?php echo ($this->form->form_style_align == 'align_right' ? ' selected' : ''); ?>"><img border="0" title="To right" alt="To right" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_right.png"></a></td>
								<td><a onclick="setFormAlign('align_all_left'); return false;" href="#" class="frm_align_all_left <?php echo ($this->form->form_style_align == 'align_all_left' ? ' selected' : ''); ?>"><img border="0" title="All left" alt="All left" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_all_left.png"></a></td>
								<td><a onclick="setFormAlign('align_all_right'); return false;" href="#" class="frm_align_all_right <?php echo ($this->form->form_style_align == 'align_all_right' ? ' selected' : ''); ?>"><img border="0" title="All right" alt="All right" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_all_right.png"></a></td>
								<td><a onclick="setFormAlign('align_all_center'); return false;" href="#" class="frm_align_all_center <?php echo ($this->form->form_style_align == 'align_all_center' ? ' selected' : ''); ?>"><img border="0" title="All center" alt="All center" src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/align_all_center.png"></a></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND'); ?></legend>
		<div class="row-fluid">
			<div class="span12">
				<div id="er-form-builder-legend">
					<ul>
						<li>
							<span class="er-form-step"></span> <?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND_STEP'); ?>
							<div class="fltnone clrboth"></div>
						</li>
						<li>
							<span class="er-form-row"></span> <?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND_ROW'); ?>
							<div class="fltnone clrboth"></div>
						</li>
						<li>
							<span class="er-form-col"></span> <?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND_COLUMN'); ?>
							<div class="fltnone clrboth"></div>
						</li>
						<li>
							<span class="er-form-fld"></span> <?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND_FIELD'); ?>
							<div class="fltnone clrboth"></div>
						</li>
						<li>
							<span class="er-form-cfld"></span> <?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND_CUSTOM'); ?>
							<div class="fltnone clrboth"></div>
						</li>
						<li>
							<span class="er-form-cfldgrp"></span> <?php echo JText::_('COM_EXTENDEDREG_FORMS_LEGEND_FIELDGROUP'); ?>
							<div class="fltnone clrboth"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</fieldset>
	<a name="form-layout"></a>
	<fieldset id="form-layout-holder">
		<legend><?php echo JText::_('COM_EXTENDEDREG_FORMS_LAYOUT'); ?></legend>
		<div class="row-fluid">
			<div class="span12">
				<div class="info">
					<h4>* <?php echo JText::_('COM_EXTENDEDREG_FORMS_DRAG_TO_ORDER'); ?></h4>
					<h4>* <?php echo JText::_('COM_EXTENDEDREG_FORMS_DBLCLICK_TO_DELETE'); ?></h4>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span9">
				<div id="form-layout-parent"><div id="form-layout" class="sortable2 ui-corner-all"></div></div>
			</div>
			<div class="span3">
				<div id="menu-layout" class="ui-corner-all">
					<h3><?php echo JText::_('COM_EXTENDEDREG_FORMS_MENU'); ?></h3>
					<div id="menu-wrapper">
						<div class="sortable">
							<div class="er-element er-form-step" rel="step"><?php echo JText::_('COM_EXTENDEDREG_FORMS_MENU_STEP'); ?></div>
							<div class="er-element er-form-row" rel="row"><?php echo JText::_('COM_EXTENDEDREG_FORMS_MENU_ROW'); ?></div>
							<div class="er-element er-form-col" rel="col"><?php echo JText::_('COM_EXTENDEDREG_FORMS_MENU_COLUMN'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#name_fld#"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_NAME'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#username_fld#"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_USERNAME'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#email_fld#"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_EMAIL'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#passwd_fld#"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_PASSWORD'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#passwd2_fld#"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#captcha_fld#"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_CAPTCHA'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#terms_fld#"><?php echo JText::_('COM_EXTENDEDREG_ACCEPT_TERMS'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#age_fld#"><?php echo JText::_('COM_EXTENDEDREG_OVER_AGE'); ?></div>
							<div class="er-element er-form-fld" rel="fld" contents="#params_fld#"><?php echo JText::_('COM_EXTENDEDREG_PARAMETERS'); ?></div>
							<?php foreach ($this->custom_fields as $fld) : ?>
							<div class="er-element er-form-cfld" rel="cfld" contents="#custom_<?php echo $fld->id; ?>#"><?php echo JText::_($fld->title); ?></div>
							<?php endforeach; ?>
							<?php foreach ($this->field_groups as $group) : ?>
							<div class="er-element er-form-cfldgrp" rel="cfldgrp" contents="#group_<?php echo $group->grpid; ?>#"><?php echo JText::_($group->name); ?></div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</form>
<?php
$document->addScriptDeclaration('
	function performAllActions(task) {
		' . $onSaveJavascriptOperations . '
		submitbutton(task);
	}
');

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();