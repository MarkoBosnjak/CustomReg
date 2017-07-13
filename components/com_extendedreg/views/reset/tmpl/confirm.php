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
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="erRemindForm" id="erRemindForm" target="_top" class="er-form">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="users.confirm_reset" />
	<ul class="er-form-holder">
		<li>
			<label for="username"><?php echo ((!(int)$this->conf->email_for_username) ? JText::_('COM_EXTENDEDREG_REGISTER_USERNAME') : JText::_('COM_EXTENDEDREG_REGISTER_EMAIL') ); ?></label><br />
			<input type="text" id="username" name="username" value="" class="inputbox required" />
		</li>
		<li>
			<label for="token"><?php echo JText::_('COM_EXTENDEDREG_TOKEN'); ?></label><br />
			<input type="text" id="token" name="token" value="" class="inputbox required" />
		</li>
		<li>
			<input type="submit" name="submit" class="er-form-button" value="<?php echo JText::_('COM_EXTENDEDREG_SUBMIT'); ?>" />
		</li>
	</ul>
</form>

<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();