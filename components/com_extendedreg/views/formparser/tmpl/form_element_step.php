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

?>
<!-- begin Step <?php echo (int)$this->stepnum; ?> -->
<?php if ($this->user && $this->user->user_id) : ?>
	<?php echo $this->childrenHTML; ?>
<?php else : ?>
	<fieldset class="er-form-step" style="display: none;">
		<?php if (@$this->formElement->label) : ?>
		<legend><?php echo $this->formElement->label; ?></legend>
		<?php else : ?>
		<legend><?php echo JText::sprintf('COM_EXTENDEDREG_STEPS_XOFY', $this->stepnum, $this->stepsCount); ?></legend>
		<?php endif; ?>
		<?php echo $this->childrenHTML; ?>
		<div class="clrboth fltnone"></div>
		<?php if ((int)$this->stepnum > 1) : ?>
			<button class="btn btn-danger er-button-back" name="action-back"><?php echo JText::_('COM_EXTENDEDREG_STEPS_BACK'); ?></button>
			&nbsp;
		<?php endif; ?>
		<?php if ((int)$this->stepnum != (int)$this->stepsCount) : ?>
		<button class="btn btn-primary er-button-next" name="action-next"><?php echo JText::_('COM_EXTENDEDREG_STEPS_NEXT'); ?></button>
		<?php else : ?>
		<button class="btn btn-primary er-button-finish" name="action-finish"><?php echo JText::_('COM_EXTENDEDREG_STEPS_FINISH'); ?></button>
		<?php endif; ?>
		<div class="clrboth fltnone"></div>
	</fieldset>
<?php endif; ?>
<!-- end Step <?php echo (int)$this->stepnum; ?> -->


