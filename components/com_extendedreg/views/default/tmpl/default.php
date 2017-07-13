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

$ajax = (int)JRequest::getVar('ajax', 0);
if (!$ajax) :
	if ((int)$this->params->get('show_page_heading', 0)) : ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif;
endif;

$hook = erHelperHooks::get_hook('html.beforeForm');
if ($hook) eval($hook);

?>
<?php if (trim($this->conf->html_before_default)) : ?><div class="clrboth fltnone"><?php echo $this->conf->html_before_default; ?></div><?php endif; ?>
<?php erHelperJavascript::OnDomBegin('', false); ?>
<noscript><?php echo JText::_('COM_EXTENDEDREG_NOSCRIPT_WARNING'); ?></noscript>
<div class="extreg_forms">
	<?php if ((int)$this->conf->allow_user_login) : ?>
	<div class="er-form-row span12">
		<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="erLoginForm" id="erLoginForm" target="_top" class="form-horizontal form er-form">
			<h5 class="er-form-required-info">&nbsp;</h5>
			<div class="er-form-row span12">
				<?php echo JHtml::_('form.token'); ?>
				<input type="hidden" name="task" value="users.do_login" />
				<?php if (trim($this->lret)) : ?>
				<input type="hidden" name="lret" value="<?php echo trim($this->lret); ?>" />
				<?php endif; ?>
				<div class="er-form-holder">
					<div class="er-form-column span12">
						<div class="er-fld-holder <?php echo $this->fld_span; ?>" id="log-username-holder">
							<label for="username"><?php echo ((!(int)$this->conf->email_for_username) ? JText::_('COM_EXTENDEDREG_REGISTER_USERNAME') : JText::_('COM_EXTENDEDREG_REGISTER_EMAIL')); ?></label>
							<input class="username span12" id="username" type="text" name="username" class="inputbox" alt="<?php echo $this->escape((!(int)$this->conf->email_for_username) ? JText::_('COM_EXTENDEDREG_REGISTER_USERNAME') : JText::_('COM_EXTENDEDREG_REGISTER_EMAIL')); ?>" size="18" />
						</div>
						<div class="er-fld-holder <?php echo $this->fld_span; ?>" id="log-passwd-holder">
							<label for="passwd"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_PASSWORD'); ?></label>
							<input class="passwd span12" id="passwd" type="password" name="passwd" class="inputbox" size="18" alt="<?php echo $this->escape(JText::_('COM_EXTENDEDREG_REGISTER_PASSWORD')); ?>" />
						</div>
						<?php if ($this->is_32 && (count($this->twofactormethods) > 1)): ?>
						<div class="er-fld-holder span4" id="log-secretkey-holder">
							<label for="secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
							<input class="passwd span12" id="secretkey" type="text" name="secretkey" class="inputbox" size="18" alt="<?php echo $this->escape(JText::_('JGLOBAL_SECRETKEY')); ?>" />
						</div>
						<?php endif; ?>
						<div class="clrboth fltnone"></div>
						<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div class="er-fld-holder" id="log-remember-holder">
							<label for="remember"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_REMEMBERME'); ?></label>
							<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo $this->escape(JText::_('COM_EXTENDEDREG_REGISTER_REMEMBERME')); ?>" />
						</div>
						<?php endif; ?>
						<div class="er-login-button">
							<input type="submit" name="submit" class="er-form-button" value="<?php echo JText::_('COM_EXTENDEDREG_REGISTER_LOGIN'); ?>" />
						</div>
					</div>
					<div class="clrboth fltnone"></div>
					<ul class="span12">
						<li class="span4">
							<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=users.reset' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.reset') : '')); ?>"  target="_top"><?php echo JText::_('COM_EXTENDEDREG_FORGOT_YOUR_PASSWORD'); ?></a>
						</li>
						<li class="span4">
							<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=users.remind' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.remind') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.remind') : '')); ?>"  target="_top"><?php echo JText::_('COM_EXTENDEDREG_FORGOT_YOUR_USERNAME'); ?></a>
						</li>
						<?php if ((int)$this->conf->enable_user_activation && (int)$this->conf->enable_request_activation_mail) : ?>
						<li class="span4">
							<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=users.request_activation_mail' . (erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.request_activation_mail') > 0 ? '&Itemid=' . erHelperRouter::getItemid('index.php?option=com_extendedreg&task=users.request_activation_mail') : '')); ?>" target="_top"><?php echo JText::_('COM_EXTENDEDREG_REQUEST_ACTIVATION_MAIL'); ?></a>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</form>
	</div>
	<?php endif; ?>
	<?php if ((int)$this->conf->allow_user_registration) : ?>
	<div class="er-form-row span12">
		<?php echo $this->formHTML; ?>
	</div>
	<?php endif; ?>
</div>
<div class="clrboth fltnone"></div>
<?php if (trim($this->conf->html_after_default)) : ?><div class="clrboth fltnone"><?php echo $this->conf->html_after_default; ?></div><?php endif; ?>
<?php

$hook = erHelperHooks::get_hook('html.afterForm');
if ($hook) eval($hook);

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();
