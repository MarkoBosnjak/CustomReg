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
<div class="control-group er-fld-holder er-fld-no-label er-fld-<?php echo $this->fieldObj->type; ?>">
	<div class="controls">
		<?php echo $this->fieldObj->html; ?>
		<?php echo $this->fieldObj->tooltip; ?>
		<span class="er-error er-error-<?php echo $this->fieldObj->name; ?>" style="display: none;"></span>
	</div>
</div>