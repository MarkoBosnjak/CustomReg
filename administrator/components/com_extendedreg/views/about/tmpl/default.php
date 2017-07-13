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
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="task" value="default.about" />
	<div class="well well-large">
		<h3>CEO</h3>
		<ul>
			<li>Hazzaa Kassis (support@jvitals.com)</li>
		</ul>
		<h3>Developers</h3>
		<ul>
			<li>Radoslav Salov (rammstein4o@jvitals.com)</li>
			<li>Iskar Enev (nuclear@jvitals.com)</li>
			<li>Norman Smith (norman@jvitals.com)</li>
		</ul>
		<h3>Translations</h3>
		<p>From the beginning of November 2013 we started to use Transifex.com for the ExtendedReg translations. Everyone can apply for translators in their own language at our project in Transifex. Here is the link to it - <a href="https://www.transifex.com/projects/p/extendedregtranslations/" target="_blank">https://www.transifex.com/projects/p/extendedregtranslations/</a></p>
		<p>If you are not familiar with Transifex, here is a short tutorial for translators - <a href="https://sites.google.com/site/transjoomla/transifex-for-translators/how-translate" target="_blank">https://sites.google.com/site/transjoomla/transifex-for-translators/how-translate</a></p>
		<ul>
			<li>Bulgarian - Radoslav Salov (rammstein4o@jvitals.com)</li>
			<li>Dutch - Ezet Webdevelopment (ezet-webdevelopment.nl)</li>
			<li>French - Fabien Peyret</li>
			<li>Finnish - Joel Dyer</li>
			<li>German - Hanjo Hingsen, Tim Schnarr</li>
			<li>Greek - Vasilis Poulios</li>
			<li>Italian - Claudio Canaccini (www.ccla.it)</li>
		</ul>
	</div>
</form>
<?php
echo $this->html->endColumn();

echo $this->html->endRow();

echo $this->html->wrapperEnd();