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

if ($this->fld_class->hasOptions()) {
	
	erHelperJavascript::OnDomBegin('', false);
	
?>
<fieldset class="adminform">
	<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm"  enctype="multipart/form-data">
		<input type="hidden" name="extension" value="com_extendedreg" />
		<input type="hidden" name="task" value="forms.save_custom_sql" />
		<input type="hidden" name="field_id" value="<?php echo (int)$this->field->id; ?>" />
		<div class="row-fluid">
			<div class="span12">
				<table width="100%">
					<thead></thead>
					<tfoot></tfoot>
					<tbody>
						<tr>
							<th colspan="2"><?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_CUSTOM_SQL_TITLE'); ?></th>
						</tr>
						<tr>
							<td>
								<textarea rows="8" cols="40" name="custom_sql" id="custom_sql"><?php echo $this->field->custom_sql; ?></textarea>
							</td>
							<td align="right">
								<input class="erButton add" type="submit" value="<?php echo JText::_('COM_EXTENDEDREG_FIELDOPT_CUSTOM_SQL_SAVE'); ?>" /> 
								<input class="erButton add" type="button" onclick="document.getElementById('custom_sql').value = '';this.form.submit();return false;" value="<?php echo JText::_('COM_EXTENDEDREG_DELETE'); ?>" /> 
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</fieldset>
<?php
	
	erHelperJavascript::OnDomReady('', false);
}