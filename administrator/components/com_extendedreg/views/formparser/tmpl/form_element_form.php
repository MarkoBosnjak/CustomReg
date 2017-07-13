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
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal er-form-validate"  autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="task" value="users.save" />
	<input type="hidden" name="id" value="<?php echo (int)$this->user->user_id; ?>" />
	<input type="hidden" name="cid" value="<?php echo (int)$this->user->user_id; ?>" />
	<input type="hidden" name="fid" value="<?php echo $this->form->id; ?>" />
	<input type="hidden" name="fh" value="<?php echo $this->fieldsHash; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<table cellspacing="0" cellpadding="0" style="width: 100%">
		<tr>
			<td style="width: 45%;" valign="top">
				<fieldset>
					<legend><?php echo JText::_('COM_EXTENDEDREG_USER_DETAILS'); ?></legend>
					<div class="row-fluid">
						<div class="span8">
							<?php echo $this->formHTML; ?>
						</div>
					</div>
				</fieldset>
				<div class="clearfix"></div>
				<fieldset>
					<legend><?php echo JText::_('COM_EXTENDEDREG_USERGROUPS'); ?></legend>
					<div class="row-fluid">
						<div class="span8">
							<?php echo JHtml::_('access.usergroups', 'groups', $this->groups, true); ?>
						</div>
					</div>
				</fieldset>
			</td>
			<td style="width: 45%;" valign="top">
				<fieldset>
					<legend><?php echo JText::_('COM_EXTENDEDREG_USER_ADDITIONAL_SETTINGS'); ?></legend>
					<div class="row-fluid">
						<div class="span8">
							<div class="control-group">
								<div class="control-label">
									<label for="block" id="block-lbl"><?php echo JText::_('COM_EXTENDEDREG_BLOCKED'); ?></label>
								</div>
								<div class="controls">
									<fieldset class="radio btn-group" id="block">
										<input type="radio" id="block_no" name="block" value="0"<?php echo ((int)$this->user->block ? '' : ' checked="checked"'); ?> /> <label for="block_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
										<input type="radio" id="block_yes" name="block" value="1"<?php echo ((int)$this->user->block ? ' checked="checked"' : ''); ?> /> <label for="block_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
									</fieldset>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label for="approve" id="approve-lbl"><?php echo JText::_('COM_EXTENDEDREG_APPROVED'); ?></label>
								</div>
								<div class="controls">
									<fieldset class="radio btn-group" id="approve">
										<input type="radio" id="approve_no" name="approve" value="0"<?php echo ((int)$this->user->approve ? '' : ' checked="checked"'); ?> /> <label for="approve_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
										<input type="radio" id="approve_yes" name="approve" value="1"<?php echo ((int)$this->user->approve ? ' checked="checked"' : ''); ?> /> <label for="approve_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
									</fieldset>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label for="acceptedterms" id="acceptedterms-lbl"><?php echo JText::_('COM_EXTENDEDREG_TERMS_HEADER'); ?></label>
								</div>
								<div class="controls">
									<fieldset class="radio btn-group" id="acceptedterms">
										<input type="radio" id="acceptedterms_no" name="acceptedterms" value="0"<?php echo ((int)$this->user->acceptedterms ? '' : ' checked="checked"'); ?> /> <label for="acceptedterms_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
										<input type="radio" id="acceptedterms_yes" name="acceptedterms" value="1"<?php echo ((int)$this->user->acceptedterms ? ' checked="checked"' : ''); ?> /> <label for="acceptedterms_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
									</fieldset>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label for="overage" id="overage-lbl"><?php echo JText::_('COM_EXTENDEDREG_OVERAGE_HEADER'); ?></label>
								</div>
								<div class="controls">
									<fieldset class="radio btn-group" id="overage">
										<input type="radio" id="overage_no" name="overage" value="0"<?php echo ((int)$this->user->overage ? '' : ' checked="checked"'); ?> /> <label for="overage_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
										<input type="radio" id="overage_yes" name="overage" value="1"<?php echo ((int)$this->user->overage ? ' checked="checked"' : ''); ?> /> <label for="overage_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
									</fieldset>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label for="sendEmail" id="sendEmail-lbl"><?php echo JText::_('COM_EXTENDEDREG_RECEIVE_SYSTEM_EMAILS'); ?></label>
								</div>
								<div class="controls">
									<fieldset class="radio btn-group" id="sendEmail">
										<input type="radio" id="sendEmail_no" name="sendEmail" value="0"<?php echo ((int)$this->user->sendEmail ? '' : ' checked="checked"'); ?> /> <label for="sendEmail_no"><?php echo JText::_('COM_EXTENDEDREG_NO'); ?></label>
										<input type="radio" id="sendEmail_yes" name="sendEmail" value="1"<?php echo ((int)$this->user->sendEmail ? ' checked="checked"' : ''); ?> /> <label for="sendEmail_yes"><?php echo JText::_('COM_EXTENDEDREG_YES'); ?></label>
									</fieldset>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label for="notes" id="notes-lbl"><?php echo JText::_('COM_EXTENDEDREG_ADMIN_NOTES'); ?></label>
								</div>
								<div class="controls">
									<textarea rows="3" cols="40" id="notes" name="notes"><?php echo $this->user->notes; ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
				<div class="clearfix"></div>
				<fieldset>
					<legend><?php echo JText::_('COM_EXTENDEDREG_PARAMETERS'); ?></legend>
					<div class="row-fluid">
						<div class="span8">
						<?php
							$parampath = JvitalsDefines::comBackPath('com_extendedreg') . 'models';
							$type = str_replace(' ', '_', mb_strtolower(isset($this->user->usertype) ? $this->user->usertype : ''));
							$paramsfile = $parampath . DIRECTORY_SEPARATOR . $type . '.xml';
							
							if (!is_file($paramsfile)) {
								$paramsfile = $parampath . DIRECTORY_SEPARATOR . 'user.xml';
							}
							
							jimport('joomla.form.form');
							$form = JForm::getInstance('myform', $paramsfile);
							
							$data = new JRegistry();
							$data->loadString($this->user->params, 'JSON');
							foreach ($data->toArray() as $key => $val) {
								$form->setValue($key, 'params', $val);
							}
							?>
							<?php foreach ($form->getFieldset('settings') as $field) : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input ?>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</fieldset>
			</td>
		</tr>
		<?php if ((int)$this->form->is_32 && !empty($this->form->tfaform) && (int)$this->user->user_id): ?>
		<tr>
			<td style="width: 100%;" colspan="2" valign="top">
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
			</td>
		</tr>
		<?php endif; ?>
	</table>
</form>

<?php if ((int)$this->form->is_32 && !empty($this->form->tfaform) && (int)$this->user->user_id): ?>
<script type="text/javascript">
	Joomla.twoFactorMethodChange = function(e) {
		var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();
		jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el) {
			if (el.id != selectedPane) {
				jQuery('#' + el.id).hide(0);
			} else {
				jQuery('#' + el.id).show(0);
			}
		});
	}
</script>
<?php endif; ?>