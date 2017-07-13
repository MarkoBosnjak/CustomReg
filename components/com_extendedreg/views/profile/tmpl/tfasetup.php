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

?>
<?php if ((int)$this->form->is_32 && !empty($this->form->tfaform) && (int)$this->user->user_id): ?>
<?php 
	erHelperJavascript::OnDomBegin('', false);
	erHelperJavascript::OnDomReady('Joomla.twoFactorMethodChange = function(e) {
		var selectedPane = \'com_users_twofactor_\' + jQuery(\'#jform_twofactor_method\').val();
		jQuery.each(jQuery(\'#com_users_twofactor_forms_container>div\'), function(i, el) {
			if (el.id != selectedPane) {
				jQuery(\'#\' + el.id).hide(0);
			} else {
				jQuery(\'#\' + el.id).show(0);
			}
		});
	}');
?>
<noscript><?php echo JText::_('COM_EXTENDEDREG_NOSCRIPT_WARNING'); ?></noscript>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm" target="_top" class="form-horizontal er-form">
	<fieldset>
		<legend><?php echo JText::_('COM_USERS_USER_TWO_FACTOR_AUTH'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
					   title="<strong><?php echo JText::_('COM_USERS_USER_FIELD_TWOFACTOR_LABEL') ?></strong><br/><?php echo JText::_('COM_USERS_USER_FIELD_TWOFACTOR_DESC') ?>">
					<?php echo JText::_('COM_USERS_USER_FIELD_TWOFACTOR_LABEL'); ?>
				</label>
			</div>
			<div class="controls">
				<?php echo JHtml::_('select.genericlist', $this->form->tfamethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->form->otpConfig->method, 'jform_twofactor_method', false) ?>
			</div>
		</div>
		<div id="com_users_twofactor_forms_container">
			<?php foreach($this->form->tfaform as $form): ?>
			<?php $style = $form['method'] == $this->form->otpConfig->method ? 'display: block' : 'display: none'; ?>
			<div id="com_users_twofactor_<?php echo $form['method'] ?>" style="<?php echo $style; ?>">
				<?php echo $form['form'] ?>
			</div>
			<?php endforeach; ?>
		</div>
	</fieldset>
	<fieldset>
		<legend>
			<?php echo JText::_('COM_USERS_USER_OTEPS') ?>
		</legend>
		<div class="alert alert-info">
			<?php echo JText::_('COM_USERS_USER_OTEPS_DESC') ?>
		</div>
		<?php if (empty($this->form->otpConfig->otep)): ?>
		<div class="alert alert-warning">
			<?php echo JText::_('COM_USERS_USER_OTEPS_WAIT_DESC') ?>
		</div>
		<?php else: ?>
		<?php foreach ($this->form->otpConfig->otep as $otep): ?>
		<span class="span3">
			<?php echo substr($otep, 0, 4) ?>-<?php echo substr($otep, 4, 4) ?>-<?php echo substr($otep, 8, 4) ?>-<?php echo substr($otep, 12, 4) ?>
		</span>
		<?php endforeach; ?>
		<div class="clearfix"></div>
		<?php endif; ?>
	</fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
		<input type="hidden" name="option" value="com_extendedreg" />
		<input type="hidden" name="task" value="users.save_tfasetup" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php erHelperJavascript::OnDomReady('', false); ?>
<?php endif; ?>
<?php echo $this->html->wrapperEnd();?>
