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

?>
<script language="JavaScript">
	function fldoptSubmit(t) {
		jQuery.post('<?php echo JRoute::_('index.php', false);?>', {
			"option": "com_extendedreg",
			"task": "forms.fldopt_save",
			"tmpl": "component",
			"id": t.id.value,
			"field_id": t.field_id.value,
			"val": t.val.value,
			"ord": t.ord.value
		}, function(data) {
			refreshOptions();
		});
		return false;
	}
	
	function refreshOptions() {
		var selected_val = jQuery('#adminForm #type option:selected').val().trim();
		jQuery.post('<?php echo JRoute::_('index.php', false);?>', {
			"option": "com_extendedreg",
			"task": "forms.fldopt",
			"tmpl": "component",
			"type": selected_val,
			"field_id": <?php echo (int)$this->item->id; ?>
		}, function(data) {
			jQuery('#fldoptions').html(data);
		});
		return true;
	}
	
	function showOptionsForm(id, nopt) {
		var selected_val = jQuery('#adminForm #type option:selected').val().trim();
		jQuery.post('<?php echo JRoute::_('index.php', false);?>', {
			"option": "com_extendedreg",
			"task": "forms.fldopt_form" + nopt,
			"tmpl": "component",
			"type": selected_val,
			"id": id,
			"field_id": <?php echo (int)$this->item->id; ?>
		}, function(data) {
			jQuery('#fldoptions').html(data);
		});
		return true;
	}
	
	function deleteFldOption(id) {
		jQuery.post('<?php echo JRoute::_('index.php', false);?>', {
			"option": "com_extendedreg",
			"task": "forms.fldopt_delete",
			"tmpl": "component",
			"id": id,
			"field_id": <?php echo (int)$this->item->id; ?>
		}, function(data) {
			refreshOptions();
		});
	}
	
	function refreshParams(t) {
		jQuery.post('<?php echo JRoute::_('index.php', false);?>', {
			"option": "com_extendedreg",
			"task": "forms.fld_params",
			"tmpl": "component",
			"id": <?php echo (int)$this->item->id; ?>,
			"type": t
		}, function(data) {
			jQuery('#paramsholder').html(data);
		});
		
		refreshOptions();
	}
	
	function showSqlPopup() {
		jQuery("#opts-sql").html("");
		jQuery("#opts-sql").load("<?php echo JRoute::_('index.php?option=com_extendedreg&task=forms.fieldoptsfromsql&tmpl=component&field_id=' . (int)$this->item->id, false); ?>", function() {
			jQuery("#opts-sql").dialog({modal: true, width: 600});
		});
		return false;
	}
	
</script>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="task" value="forms.fld_save" />
	<input type="hidden" name="id" value="<?php echo (int)$this->item->id; ?>" />
	<input type="hidden" name="cid" value="<?php echo (int)$this->item->id; ?>" />
	<?php echo JHtml::_( 'form.token'); ?>
	<fieldset>
		<legend><?php echo JText::_('COM_EXTENDEDREG_FIELDS_LEGEND'); ?></legend>
		<div class="row-fluid">
			<div class="span6">
				<div class="separator">
					<h4><?php echo JText::_('COM_EXTENDEDREG_GENERAL_PROPERTIES'); ?></h4>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label class="required" for="grpid" id="grpid-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP'); ?><span class="star">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<select size="1" class="inputbox required" id="grpid" name="grpid">
							<?php foreach ($this->grouplist as $grp) : ?>
								<option value="<?php echo (int)$grp->grpid; ?>"<?php echo ((int)$grp->grpid == (int)$this->item->grpid ? ' selected' : ''); ?>><?php echo $grp->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label class="required" for="type" id="type-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_TYPE'); ?><span class="star">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<select size="1" class="inputbox required" id="type" name="type" onchange="refreshParams(this.options[this.selectedIndex].value);">
							<?php foreach ($this->fld_types as $type) : ?>
								<option value="<?php echo $type->file_name; ?>"<?php echo ($this->item->type == $type->file_name ? ' selected' : ''); ?>><?php echo $type->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label class="required" for="title" id="title-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_TITLE'); ?><span class="star">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<input type="text" size="30" class="inputbox required" value="<?php echo $this->item->title; ?>" id="title" name="title">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label class="required" for="name" id="name-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_NAME'); ?><span class="star">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<?php if (!(int)$this->item->id) : ?>
						<input type="text" size="30" class="inputbox required" value="<?php echo $this->item->name; ?>" id="name" name="name">
						<?php else : ?>
						<b><?php echo $this->item->name; ?></b>
						<?php endif; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="description" id="description-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_DESCRIPTION'); ?></label>
					</div>
					<div class="controls">
						<textarea rows="3" cols="40" id="description" name="description"><?php echo $this->item->description; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="required" id="required-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_REQUIRED'); ?></label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group" id="required">
							<input type="radio" id="required_no" name="required" value="0"<?php echo ((int)$this->item->required ? '' : ' checked="checked"'); ?> /> <label for="required_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="required_yes" name="required" value="1"<?php echo ((int)$this->item->required ? ' checked="checked"' : ''); ?> /> <label for="required_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="editable" id="editable-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_EDITABLE'); ?></label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group" id="editable">
							<input type="radio" id="editable_no" name="editable" value="0"<?php echo ((int)$this->item->editable ? '' : ' checked="checked"'); ?> /> <label for="editable_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="editable_yes" name="editable" value="1"<?php echo ((int)$this->item->editable ? ' checked="checked"' : ''); ?> /> <label for="editable_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="published" id="published-lbl"><?php echo JText::_('COM_EXTENDEDREG_STATE_PUBLISHED'); ?></label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group" id="published">
							<input type="radio" id="published_no" name="published" value="0"<?php echo ((int)$this->item->published ? '' : ' checked="checked"'); ?> /> <label for="published_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
							<input type="radio" id="published_yes" name="published" value="1"<?php echo ((int)$this->item->published ? ' checked="checked"' : ''); ?> /> <label for="published_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
						</fieldset>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="separator">
					<h4><?php echo JText::_('COM_EXTENDEDREG_PARAMETERS'); ?></h4>
				</div>
				<div id="paramsholder"></div>
			</div>
		</div>
	</fieldset>
</form>
<div id="fldoptions"></div>
<script language="JavaScript">refreshParams('<?php echo $this->item->type; ?>');</script>
<?php 

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();