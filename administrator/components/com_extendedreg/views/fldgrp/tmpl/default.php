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
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="task" value="forms.fldgrp_save" />
	<input type="hidden" name="id" value="<?php echo (int)$this->item->grpid; ?>" />
	<input type="hidden" name="cid" value="<?php echo (int)$this->item->grpid; ?>" />
	<?php echo JHtml::_( 'form.token'); ?>
	<fieldset>
		<legend><?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP'); ?></legend>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<div class="control-label">
						<label class="required" for="name" id="name-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP_NAME'); ?><span class="star">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<input type="text" size="30" class="inputbox required" value="<?php echo $this->item->name; ?>" id="name" name="name">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="description" id="description-lbl"><?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP_DESCR'); ?></label>
					</div>
					<div class="controls">
						<textarea rows="3" cols="40" id="description" name="description"><?php echo $this->item->description; ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</form>
<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();