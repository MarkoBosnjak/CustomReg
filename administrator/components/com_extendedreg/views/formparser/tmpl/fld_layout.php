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
<div class="control-group er-fld-holder er-fld-<?php echo $this->fieldObj->type; ?>">
	<div class="control-label">
		<label for="<?php echo $this->fieldObj->fieldIDAttr; ?>" id="<?php echo $this->fieldObj->name; ?>-lbl"<?php if ((int)$this->fieldObj->required) : ?> class="required"<?php endif; ?>><?php echo JText::_($this->fieldObj->title); ?><?php if ((int)$this->fieldObj->required) : ?><span class="star">&nbsp;*</span><?php endif; ?><?php echo $this->fieldObj->tooltip; ?></label>
	</div>
	<div class="controls">
		<?php echo $this->fieldObj->html; ?>
		<span class="er-error er-error-<?php echo $this->fieldObj->name; ?>" style="display: none;"></span>
	</div>
</div>