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

erHelperJavascript::OnDomBegin('', false);

?>
<script language="JavaScript">
	function setinstalltype(t) {
		var installtype = document.getElementById('installtype_fld');
		installtype.value = t;
	}
	
	function checkInstallForm(form) {
		if(form.task.value != 'addons.browse') {
			if (form.installtype.value == 'upload') {
				if (form.install_package.value == '') {
					alert('<?php echo JText::_('COM_EXTENDEDREG_ADDON_PLEASE_SELECT_FILE'); ?>');
					return false;
				}
			} else if (form.installtype.value == 'folder') {
				if (form.install_directory.value == '') {
					alert('<?php echo JText::_('COM_EXTENDEDREG_ADDON_PLEASE_SELECT_DIR'); ?>');
					return false;
				}
			}
		}
		return true;
	}
</script>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm" onsubmit="return checkInstallForm(this);">
	<input type="hidden" name="extension" value="com_extendedreg" />
	<input type="hidden" name="task" value="addons.doInstall" />
	<input type="hidden" name="installtype" id="installtype_fld" value="upload" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="row-fluid">
		<!-- Begin Sidebar -->
		<div id="sidebar" class="span2">
			<div class="sidebar-nav">
				<?php include (JvitalsDefines::comBackPath('com_extendedreg') . 'toolbar.extendedreg.php'); ?>
				<hr />
			</div>
		</div>
		<!-- End Sidebar -->
		<div class="span10">
			<table class="table adminform">
				<thead></thead>
				<tfoot></tfoot>
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_EXTENDEDREG_ADDON_UPLOAD_FILE'); ?></th>
					</tr>
					<tr>
						<td>
							<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
							<button class="button" type="submit" id="install_package_but" onclick="setinstalltype('upload');"><?php echo JText::_('COM_EXTENDEDREG_ADDON_UPLOAD'); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="adminform">
				<thead></thead>
				<tfoot></tfoot>
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_EXTENDEDREG_ADDON_FROM_DIRECTORY'); ?></th>
					</tr>
					<tr>
						<td>
							<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->tmp_path; ?>" />
							<button class="button" type="submit" id="install_directory_but" onclick="setinstalltype('folder');"><?php echo JText::_('COM_EXTENDEDREG_ADDON_INSTALL'); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</form>
<?php

erHelperJavascript::OnDomReady('', false);