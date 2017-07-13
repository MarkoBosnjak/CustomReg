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

$lang = JFactory::getLanguage();

?>
<!-- begin Register Form -->
<div class="clrboth fltnone"></div>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" id="josForm" name="josForm" autocomplete="off" class="form-horizontal form-<?php echo str_replace('_', '-', $this->form->form_style_align); ?> er-form er-form-validate<?php echo ($lang->isRTL() ? ' er-form-rtl' : ''); ?>" target="_top"  enctype="multipart/form-data"<?php echo (trim($this->form->form_style_width) ? ' style="width: ' . $this->form->form_style_width . ';"' : ''); ?> data-formid="<?php echo $this->form->id; ?>">
	<?php if ($this->user && (int)$this->user->user_id) : ?>
		<input type="hidden" name="task" value="users.do_save" />
		<input type="hidden" name="id" value="<?php echo (int)$this->user->user_id; ?>" />
		<input type="hidden" name="cid" value="<?php echo (int)$this->user->user_id; ?>" />
	<?php else : ?>
		<input type="hidden" name="task" value="users.do_register" />
		<?php if (trim($this->lret)) : ?>
		<input type="hidden" name="lret" value="<?php echo trim($this->lret); ?>" />
		<?php endif; ?>
	<?php endif; ?>
	<input type="hidden" name="fid" value="<?php echo $this->form->id; ?>" />
	<input type="hidden" name="fh" value="<?php echo $this->fieldsHash; ?>" />
	<?php if (JvitalsHelper::componentEnabled('com_k2')) : ?>
	<input type="hidden" name="K2UserForm" value="1" />
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>
	<div class="er-form-holder span12">
		<h5 class="er-form-required-info"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_REQUIRED_INFO'); ?></h5>
		<?php echo $this->formHTML; ?>
		<?php if ($this->user && (int)$this->user->user_id) : ?>
		<div class="clrboth fltnone"></div>
		<button class="btn btn-success er-button-finish" name="action-finish"><?php echo JText::_('COM_EXTENDEDREG_SAVE'); ?></button>
		<?php if ((int)$this->conf->allow_terminate) : ?>
			<button class="btn btn-warning er-button-finish" style="float: right;" name="action-terminate" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_extendedreg&task=users.terminate'); ?>';return false;"><?php echo JText::_('COM_EXTENDEDREG_ACCOUNT_TERMINATE_LINK'); ?></button>
		<?php endif; ?>
		<?php elseif (!(int)$this->stepsCount) : ?>
		<div class="clrboth fltnone"></div>
		<button class="btn btn-primary er-button-finish" name="action-finish"><?php echo JText::_('COM_EXTENDEDREG_REGISTER'); ?></button>
		<?php endif; ?>
	</div>
	<div class="clrboth fltnone"></div>
</form>
<div class="clrboth fltnone"></div>
<!-- end Register Form -->
<br/>