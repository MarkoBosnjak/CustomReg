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

if ((int)$this->params->get('show_page_heading', 0)) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
</div>
<?php endif;  ?>
<?php erHelperJavascript::OnDomBegin('', false); ?>
<noscript><?php echo JText::_('COM_EXTENDEDREG_NOSCRIPT_WARNING'); ?></noscript>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="erRemindForm" id="erRemindForm" target="_top" class="er-form er-form-validate">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="users.complete_reset" />
	<ul class="er-form-holder">
		<li class="er-fld-holder">
			<label for="password"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_PASSWORD'); ?></label><br />
			<input class="inputbox required validate-password" type="password" id="password" name="password" autocomplete="off" value="" />
			<span class="er-error er-error-password" style="display: none;"></span>
		</li>
		<li class="er-fld-holder">
			<label for="verify-password"><?php echo JText::_('COM_EXTENDEDREG_REGISTER_VERIFY_PASSWORD'); ?></label><br />
			<input class="inputbox required validate-password2" type="password" id="verify-password" name="verify-password" autocomplete="off" value="" />
			<span class="er-error er-error-verify-password" style="display: none;"></span>
		</li>
		<li>
			<input type="submit" name="submit" class="er-form-button" value="<?php echo JText::_('COM_EXTENDEDREG_SUBMIT'); ?>" />
		</li>
	</ul>
</form>

<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();