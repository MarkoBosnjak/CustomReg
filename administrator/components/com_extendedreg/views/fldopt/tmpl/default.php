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

if ($this->fld_class->hasOptions()) : erHelperJavascript::OnDomBegin('', false); ?>
	<a name="fieldopts"></a>
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_TITLE'); ?></legend>
	<?php if (!(int)$this->fld_class->getField()->id) : ?>
	<p><?php echo JText::_('COM_EXTENDEDREG_FIELDS_FIRST_SAVE_YOUR_FIELD'); ?></p>
	<?php else : ?>
		<?php if ($this->conf->use_opts_sql && trim($this->field->custom_sql)): ?>
		<p class="error" align="center"><br/><br/><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_CUSTOM_SQL_INFO'); ?><br/><br/></p>
		<?php endif; ?>
		<table width="100%">
		<tr>
			<?php if (!trim($this->field->custom_sql)) : ?>
			<td><input class="erButton add" type="button" value="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_NEW'); ?>" onclick="showOptionsForm(0, 'new');" />&nbsp;&nbsp;&nbsp;</td>
			<td align="right" width="30%">
				<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="task" value="forms.fldopt_fromtxt" />
					<input type="hidden" name="field_id" value="<?php echo (int)$this->field->id; ?>" />
					<input type="checkbox" class="hasTip" title="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_FROM_FILE_OVERRIDE_TITLE'); ?>::<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_FROM_FILE_OVERRIDE'); ?>" value="1" name="fldopt_override" />
					<input type="file" class="hasTip" title="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_FROM_FILE_BOM'); ?>::" name="from_txt" id="from_txt"/>
					<input class="erButton add" type="submit" value="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_FROM_FILE'); ?>" />
				</form>
			</td>
			<?php endif; ?>
			<?php if($this->conf->use_opts_sql): ?>
			<span id="opts-sql" style="display: none;"></span>
			<td align="right" width="9%"><input class="erButton add" type="button" style="float: none;" value="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_FROM_SQL'); ?>" onclick="showSqlPopup();" />&nbsp;&nbsp;&nbsp;</td></td>
			<?php endif; ?>
		</tr>
		<tr>
			<?php if (!trim($this->field->custom_sql)) : ?>
			<td></td>
			<td align="right"><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_FROM_FILE_DESC'); ?></td>
			<?php endif; ?>
			<?php if($this->conf->use_opts_sql): ?>
			<td></td>
			<?php endif; ?>
		</tr>
		</table>
		<table class="adminlist">
			<thead>
				<tr>
					<th nowrap="nowrap" style="text-align: center; width: 5%;">ID</th>
					<th nowrap="nowrap" style="text-align: left;"><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_VALUE'); ?></th>
					<?php if (!trim($this->field->custom_sql)) : ?>
					<th nowrap="nowrap" style="text-align: center; width: 5%;"><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_ORD'); ?></th>
					<th nowrap="nowrap" style="width: 100px;">-</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
		<?php
			$i = 0;
			foreach ($this->fopts as $k => $obj) {
		?>
				<tr class="<?php echo "row$i"; ?>">
					<td style="text-align: center;"><?php echo $obj->id; ?></td>
					<td style="text-align: left;"><?php echo $obj->val; ?></td>
					<?php if (!trim($this->field->custom_sql)) : ?>
					<td style="text-align: center;"><?php echo $obj->ord; ?></td>
					<td style="text-align: center;">
						<a href="javascript: void(0);" onclick="deleteFldOption(<?php echo $obj->id; ?>);"><img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>/assets/images/16x16/publish_r.png" alt="" /></a>
						<a href="javascript: void(0);" onclick="showOptionsForm(<?php echo $obj->id; ?>, '');"><img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>/assets/images/16x16/edit.png" alt="" /></a>
					</td>
					<?php endif; ?>
				</tr>
		<?php
				if ($i == 0) {
					$i = 1;
				} else {
					$i = 0;
				}
			}
		?>
			</tbody>
		</table>
	<?php endif; ?>
	</fieldset>
<?php 

erHelperJavascript::OnDomReady('', false);

endif;