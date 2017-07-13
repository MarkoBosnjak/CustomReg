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

erHelperJavascript::OnDomReady('(function($) { 
	$("#clear-fromdate").datepicker({
		dateFormat: "yy-mm-dd",
		showOn: "button",
		buttonImage: "' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/calendar.png",
		buttonImageOnly: true
	});
	
	$("#clear-todate").datepicker({
		dateFormat: "yy-mm-dd",
		showOn: "button",
		buttonImage: "' . JvitalsDefines::comBackPath('com_extendedreg', true) . 'assets/images/16x16/calendar.png",
		buttonImageOnly: true
	});
})(jQuery); ');

?>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="extension" value="com_extendedreg" />
	<input type="hidden" name="task" value="default.doClearStats" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="row-fluid">
		<div class="span12">
			<table class="table adminform">
				<thead></thead>
				<tfoot></tfoot>
				<tbody>
					<tr>
						<td>
							<?php echo JText::_('COM_EXTENDEDREG_STATS_FROM'); ?>:
							<input type="text" name="fromdate" id="clear-fromdate" value="" title="<?php echo JText::_('COM_EXTENDEDREG_STATS_FROM'); ?>" />
						</td>
						<td>
							<?php echo JText::_('COM_EXTENDEDREG_STATS_TO'); ?>:
							<input type="text" name="todate" id="clear-todate" value="" title="<?php echo JText::_('COM_EXTENDEDREG_STATS_TO'); ?>" />
						</td>
						<td nowrap="nowrap">
							<button type="submit"><?php echo JText::_('COM_EXTENDEDREG_GO_BUTTON'); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</form>
<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();