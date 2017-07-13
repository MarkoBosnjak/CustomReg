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

erHelperJavascript::OnDomBegin('', false);

erHelperJavascript::OnDomReady('(function($) { 
	$("#change-form-dialog").dialog({
		modal: true,
		autoOpen: false,
		buttons: {
			"' . JText::_('COM_EXTENDEDREG_SAVE') . '": function() {
				var formID = $("#selected-form-box option:selected").val();
				if (' . (int)$this->user->user_id . ' != 0) {
					$.post("' . JRoute::_('index.php', false) . '", {
						"option": "com_extendedreg",
						"task": "users.set_form",
						"tmpl": "component",
						"id": ' . (int)$this->user->user_id . ',
						"form_id": formID
					}, function(data) {
						if (data == "true") {
							window.location.href = "' . JRoute::_('index.php?option=com_extendedreg&task=users.edit&cid=' . (int)$this->user->user_id, false) . '";
						} else {
							alert("' .JText::_('COM_EXTENDEDREG_AJAX_ERROR') . '");
						}
					});
				} else {
					window.location.href = "' . JRoute::_('index.php', false) . '?option=com_extendedreg&task=users.edit&fid=" + formID;
				}
				$(this).dialog("close");
			},
			"' . JText::_('COM_EXTENDEDREG_CANCEL') . '": function() {
				$(this).dialog("close");
			}
		}
	});
	
	$("#change-form-dialog-trigger").click(function() {
		$("#change-form-dialog").dialog("open");
	});
})(jQuery); ');

?>
<div id="change-form-dialog" style="display: none;" title="<?php echo JText::_('COM_EXTENDEDREG_CHANGE_USER_FORM'); ?>">
	<label for="selected-form-box"><?php echo JText::_('COM_EXTENDEDREG_FORM'); ?>:</label>
	<select name="selected-form-box" id="selected-form-box" class="inputbox">
		<?php 
		$frmselected = false;
		foreach ($this->allforms as $obj) : 
		?>
		<option value="<?php echo $obj->id; ?>"<?php if ((int)$obj->id == (int)$this->form->id) : $frmselected = true; ?> selected<?php endif; ?>><?php echo $obj->name; ?></option>
		<?php endforeach; ?>
		<option value=""<?php echo ($frmselected ? '' : ' selected'); ?>><?php echo JText::_('COM_EXTENDEDREG_FORMS_USE_DEFAULT'); ?></option>
	</select>
</div>
<fieldset>
	<legend><?php echo JText::_('COM_EXTENDEDREG_USER_INFORMATION'); ?></legend>
	<div class="row-fluid">
		<div class="span10">
			<table class="admintable" style="width: 80%;">
				<tr>
					<td class="key" valign="top" nowrap="nowrap"><?php echo JText::_('COM_EXTENDEDREG_USER_USE_FORM'); ?></td>
					<td><?php echo $this->form->name; ?> <a href="javascript: void(0);" id="change-form-dialog-trigger"><small><?php echo JText::_('COM_EXTENDEDREG_CHANGE'); ?></small></a></td>
					<td class="key" valign="top" nowrap="nowrap">ID</td>
					<td><?php echo (int)$this->user->user_id; ?></td>
					<td class="key" valign="top" nowrap="nowrap"><?php echo JText::_('COM_EXTENDEDREG_IPADDR'); ?></td>
					<td>
						<?php if (trim($this->user->ip_addr)) : ?>
						<a target="_blank" href="http://tools.whois.net/whoisbyip/?host=<?php echo $this->user->ip_addr; ?>"><?php echo $this->user->ip_addr; ?></a>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key" valign="top" nowrap="nowrap"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_DATE'); ?></td>
					<td>
						<?php if (trim($this->user->registerDate)) : ?>
						<?php echo erHelperHTML::formatDate($this->user->registerDate, $this->dateFormat); ?>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
					<td class="key" valign="top" nowrap="nowrap"><?php echo JText::_('COM_EXTENDEDREG_LASTVISIT_DATE'); ?></td>
					<td>
						<?php if (trim($this->user->lastvisitDate)) : ?>
							<?php if ($this->user->lastvisitDate != '0000-00-00 00:00:00') : ?>
								<?php echo erHelperHTML::formatDate($this->user->lastvisitDate, $this->dateFormat); ?>
							<?php else : ?>
								<?php echo JText::_('COM_EXTENDEDREG_NEVER'); ?>
							<?php endif; ?>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</div>
	</div>
</fieldset>
<div class="clearfix"></div>
<?php

echo $this->formHTML;

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();