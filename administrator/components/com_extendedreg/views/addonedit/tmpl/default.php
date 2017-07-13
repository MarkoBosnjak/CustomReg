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

$paramsHtml = $this->lib->renderParams();
?>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="extension" value="com_extendedreg" />
	<input type="hidden" name="task" value="addons.save" />
	<input type="hidden" name="id" value="<?php echo (int)$this->addon->id; ?>" />
	<input type="hidden" name="cid" value="<?php echo (int)$this->addon->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="row-fluid">
		<div class="span12">
			<?php echo (trim($paramsHtml) ? $paramsHtml : JText::_('COM_EXTENDEDREG_FIELDS_NO_PARAMS')); ?>
		</div>
	</div>
</form>
<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();