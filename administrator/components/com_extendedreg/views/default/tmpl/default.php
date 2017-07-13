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

echo $this->html->startRow();

echo $this->html->startColumn(null, 12);
?>
<div class="well well-large">
	<h3><?php echo JText::_('COM_EXTENDEDREG_WELCOME'); ?></h3>
	<?php echo JText::_('COM_EXTENDEDREG_INFORMATION'); ?>
	<ul>
		<li><?php echo JText::_('COM_EXTENDEDREG_MY_VERSION'); ?>: <b><span><?php echo $this->currVersion; ?></span></b></li>
		<li><?php echo JText::_('COM_EXTENDEDREG_LATEST_VERSION'); ?>: <b><?php echo JvitalsHelper::versionInfo('er-version-compare.txt', 'extendedreg', $this->currVersion); ?></b></li>
	</ul>
</div>
<?php
echo $this->html->endColumn();

echo $this->html->endRow();

echo $this->html->startRow();

echo $this->html->startColumn(null, 6);
?>
	<div id="dashboard">
	<?php foreach($this->dashboard as $item): ?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $item['link']; ?>"<?php echo (isset($item['target']) ? ' target="'.$item['target'].'"' : ''); ?>>
					<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/48x48/<?php echo $item['icon']; ?>" alt="<?php echo $item['label']; ?>" />
					<span><?php echo $item['label']; ?></span>
				</a>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php
echo $this->html->endColumn();

echo $this->html->startColumn(null, 6);
?>
	<h3><?php echo JText::_('COM_EXTENDEDREG_CHANGELOG'); ?></h3>
	<div>
		<div id="changelog-wrapper">
		<?php echo JvitalsHelper::parseChangelog('er-changelog', 'extendedreg', 'com_extendedreg', 'COM_EXTENDEDREG_CHANGELOG'); ?>
		</div>
	</div>
<?php
echo $this->html->endColumn();

echo $this->html->endRow();

echo $this->html->wrapperEnd();
