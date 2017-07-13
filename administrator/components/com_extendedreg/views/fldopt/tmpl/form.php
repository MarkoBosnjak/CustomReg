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
		<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" onsubmit="return fldoptSubmit(this);">
			<input type="hidden" name="task" value="forms.fldopt_save" />
			<input type="hidden" name="id" value="<?php echo (int)$this->fopts->id; ?>" />
			<input type="hidden" name="field_id" value="<?php echo (int)$this->field->id; ?>" />
			<table class="admintable">
				<colgroup>
					<col width="150"/>
					<col width="*"/>
				</colgroup>
				<tr>
					<td class="key"><label for="val"><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_VALUE'); ?><span class="required">*</span></label></td>
					<td><input type="text" id="val" name="val" value="<?php echo @$this->fopts->val; ?>" size="30" /></td>
				</tr>
				<tr>
					<td class="key"><label for="ord"><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_ORD'); ?></label></td>
					<td><input type="text" id="ord" name="ord" value="<?php echo @$this->fopts->ord; ?>" size="30" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="erButton save" value="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_SAVE'); ?>" /></td>
				</tr>
			</table>
		</form>
	<?php endif; ?>
	</fieldset>
<?php 

erHelperJavascript::OnDomReady('', false);

endif;