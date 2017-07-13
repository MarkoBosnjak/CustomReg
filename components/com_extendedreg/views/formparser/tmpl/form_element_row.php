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
<div class="er-form-row span12">
	<?php if (@$this->formElement->label) : ?><h5><?php echo $this->formElement->label; ?></h5><?php endif; ?>
	<?php echo $this->childrenHTML; ?>
</div>