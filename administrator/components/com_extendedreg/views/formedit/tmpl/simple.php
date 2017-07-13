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
<script language="JavaScript">
	alert("<?php echo $this->escape(JText::_('COM_EXTENDEDREG_FORMS_SIMPLE_DISABLED')); ?>");
	window.location.href = '<?php echo JURI::base(true); ?>/index.php?option=com_extendedreg&task=settings';
</script>