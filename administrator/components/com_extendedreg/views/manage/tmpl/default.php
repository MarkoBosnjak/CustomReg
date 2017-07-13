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

$countExtraFields = count($this->ef);

$customCols = '
var customCols = {};
';

if ($this->ef && $countExtraFields) {
	foreach ($this->ef as $customField) {
		$customCols .= 'customCols[\'' . $customField->name . '\'] = \'' . str_replace("'", "\'", JText::_($customField->title)) . '\';' . "\n";
	}
}

erHelperJavascript::OnDomReady('(function($) { 
	$("#er-selectable-fields").sortable({
		receive: function(event, ui) {
			ui.item.toggleClass("ui-state-default");
			ui.item.toggleClass("ui-state-highlight");
			$(".fld-" + ui.item.attr("rel")).hide();
			
			var c_value = [];
			$("#er-selected-fields li.ui-state-highlight").each(function(index, value) {
				c_value.push($(this).attr("rel"));
			});
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + 7);
			document.cookie = "er_user_manage=" + escape(c_value.toString()) + "; expires=" + exdate.toUTCString();
		},
		cancel: ".er-sel-title",
		connectWith: "#er-selected-fields"
	});
	
	$("#er-selected-fields").sortable({
		receive: function(event, ui) {
			ui.item.toggleClass("ui-state-default");
			ui.item.toggleClass("ui-state-highlight");
			$(".fld-" + ui.item.attr("rel")).show();
			
			var c_value = [];
			$("#er-selected-fields li.ui-state-highlight").each(function(index, value) {
				c_value.push($(this).attr("rel"));
			});
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + 7);
			document.cookie = "er_user_manage=" + escape(c_value.toString()) + "; expires=" + exdate.toUTCString();
		},
		cancel: ".er-sel-title",
		connectWith: "#er-selectable-fields"
	});
	
	' . $customCols . '
	
	$.each(customCols, function(index, value) {
		var selectedFld = getCookie("er_user_manage");
		var selectedArr = [];
		if (selectedFld) {
			selectedArr = selectedFld.toString().split(",");
		}
		if ($.inArray(index, selectedArr) >= 0) {
			$("#er-selected-fields").append("<li rel=\"" + index + "\" class=\"ui-state-highlight\">" + value + "</li>");
			$(".fld-" + index).show();
		} else {
			$("#er-selectable-fields").append("<li rel=\"" + index + "\" class=\"ui-state-default\">" + value + "</li>");
			$(".fld-" + index).hide();
		}
	});
	
	$("#er-custom-fields-holder legend a.close-panel").click(function() {
		$("#er-custom-fields").fadeOut();
		$("#er-custom-fields-holder legend a.close-panel").hide();
		$("#er-custom-fields-holder legend a.open-panel").show();
		
		var exdate = new Date();
		exdate.setDate(exdate.getDate() + 7);
		document.cookie = "er_user_manage_open=0; expires=" + exdate.toUTCString();
		
		return false;
	});
	$("#er-custom-fields-holder legend a.open-panel").click(function() {
		$("#er-custom-fields").fadeIn();
		$("#er-custom-fields-holder legend a.close-panel").show();
		$("#er-custom-fields-holder legend a.open-panel").hide();
		
		var exdate = new Date();
		exdate.setDate(exdate.getDate() + 7);
		document.cookie = "er_user_manage_open=1; expires=" + exdate.toUTCString();
		
		return false;
	});
	
	var panelOpened = parseInt(getCookie("er_user_manage_open"));
	if (panelOpened) {
		$("#er-custom-fields").show();
		$("#er-custom-fields-holder legend a.close-panel").show();
		$("#er-custom-fields-holder legend a.open-panel").hide();
	} else {
		$("#er-custom-fields").hide();
		$("#er-custom-fields-holder legend a.close-panel").hide();
		$("#er-custom-fields-holder legend a.open-panel").show();
	}
})(jQuery); ');

?>
<script language="JavaScript">

function getCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0; i < ARRcookies.length; i++) {
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name) {
			return unescape(y);
		}
	}
}
</script>
<?php

if (JvitalsDefines::compatibleMode() == '30>') {
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
}

$app = JFactory::getApplication();
$loggeduser = JFactory::getUser();
$listOrder = $this->escape($app->getUserState('com_extendedreg.list.ordering'));
$listDirn = $this->escape($app->getUserState('com_extendedreg.list.direction'));
$countExtraFields = count($this->ef);
$sortFields = $this->getSortFields();
$canEdit = JvitalsHelper::canDo('users.manage', 'com_extendedreg');

$blockStates = array(
	1 => array('block', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('unblock', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

$activationStates = array(
	1 => array('manage', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('activate', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

$approveStates = array(
	1 => array('unapprove', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('approve', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

$termStates = array(
	1 => array('decline_terms', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('accept_terms', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

$overageStates = array(
	1 => array('unset_overage', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('set_overage', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, 'users.manage');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false);?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<!-- Begin Sidebar -->
		<div id="sidebar" class="span2">
			<div class="sidebar-nav">
				<?php include (JvitalsDefines::comBackPath('com_extendedreg') . 'toolbar.extendedreg.php'); ?>
				<hr />
				<div class="filter-select">
					<h4 class="page-header"><?php echo JText::_('COM_EXTENDEDREG_FILTER');?></h4>
					<label for="filter_state" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_STATE');?></label>
					<select name="filter_state" id="filter_state" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_STATE'); ?></option>
						<?php echo JHtml::_('select.options', $this->userStateOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.users_state')); ?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_active" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_ACTIVE');?></label>
					<select name="filter_active" id="filter_active" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_ACTIVE'); ?></option>
						<?php echo JHtml::_('select.options', $this->userActiveOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.users_active')); ?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_approved" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_APPROVED');?></label>
					<select name="filter_approved" id="filter_approved" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_APPROVED'); ?></option>
						<?php echo JHtml::_('select.options', $this->userApprovedOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.users_approved')); ?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_terms" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_TERMS');?></label>
					<select name="filter_terms" id="filter_terms" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_TERMS'); ?></option>
						<?php echo JHtml::_('select.options', $this->userTermsOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.users_terms')); ?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_age" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_OVERAGE');?></label>
					<select name="filter_age" id="filter_age" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_OVERAGE'); ?></option>
						<?php echo JHtml::_('select.options', $this->userAgeOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.users_age')); ?>
					</select>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->
		<!-- Begin Content -->
		<div class="span10">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_SEARCH');?></label>
					<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" id="filter_search" value="<?php echo $this->escape($app->getUserState('com_extendedreg.filter.users_search')); ?>" title="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" />
				</div>
				<div class="btn-group pull-left hidden-phone">
					<button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_('COM_EXTENDEDREG_GO_BUTTON'); ?>"><i class="icon-search"></i></button>
					<button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('COM_EXTENDEDREG_RESET_BUTTON'); ?>"><i class="icon-remove"></i></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable" class="input-small" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('COM_EXTENDEDREG_ORDER_ASCENDING');?></option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('COM_EXTENDEDREG_ORDER_DESCENDING');?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_SORT_BY');?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('COM_EXTENDEDREG_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
					</select>
				</div>
			</div>
			<?php if ($this->ef && $countExtraFields) : ?>
			<div class="clearfix"></div>
			<fieldset id="er-custom-fields-holder" class="span11">
				<legend>
					<a href="#" class="close-panel" style="display: none;"><?php echo JText::_('COM_EXTENDEDREG_CLOSE'); ?>&nbsp;&nbsp;&nbsp;<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/sort_asc.png" alt="<?php echo JText::_('COM_EXTENDEDREG_CLOSE'); ?>" /></a>
					<a href="#" class="open-panel"><?php echo JText::_('COM_EXTENDEDREG_OPEN'); ?>&nbsp;&nbsp;&nbsp;<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/sort_desc.png" alt="<?php echo JText::_('COM_EXTENDEDREG_OPEN'); ?>" /></a>
				</legend>
				<div id="er-custom-fields" style="display: none;">	
					<div class="er-custom-fields-panel"><h3><?php echo JText::_('COM_EXTENDEDREG_USERMANAGE_FIELDS_SELECTABLE'); ?></h3><ul id="er-selectable-fields"></ul></div>
					<div class="er-custom-fields-panel"><h3><?php echo JText::_('COM_EXTENDEDREG_USERMANAGE_FIELDS_SELECTED'); ?></h3><ul id="er-selected-fields"></ul></div>
					<div class="fltnone clrboth"></div>
				</div>
				<div class="clrboth"><br/></div>
			</fieldset>
			<?php endif; ?>
			<div class="clearfix"></div>
			<table class="table table-striped" id="itemList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<?php if ($canEdit) : ?>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							<?php else : ?>
							-
							<?php endif; ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_NAME'), 'u.name', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_USERNAME'), 'u.username', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_EMAIL'), 'u.email', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_ENABLED'), 'u.block', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_ACTIVATED'), 'u.activation', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_APPROVED'), 'er.approve', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_TERMS_HEADER'), 'er.acceptedterms', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_OVERAGE_HEADER'), 'er.overage', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="10%" class="hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_REGISTER_DATE'), 'u.registerDate', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'ID', 'u.id', $listDirn, $listOrder, 'users.manage'); ?>
						</th>
						<?php if ($this->ef && $countExtraFields) : ?>
						<?php foreach ($this->ef as $customField):
							$fldname = $customField->name;
						?>
						<th class="fld-<?php echo $fldname; ?>" style="display: none;"><?php echo JHtml::_('grid.sort', $customField->title, 'er.' . $fldname, $listDirn, $listOrder, 'users.manage'); ?></th>
						<?php endforeach; ?>
						<?php endif; ?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo ($this->ef && $countExtraFields ? $countExtraFields + 11 : 11); ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php if (!count($this->items)) : ?>
					<tr class="row0">
						<td colspan="<?php echo ($this->ef && $countExtraFields ? $countExtraFields + 11 : 11); ?>">
							<?php echo JText::_('COM_EXTENDEDREG_NO_RECORDS_FOUND'); ?>
						</td>
					</tr>
					<?php else : ?>
					<?php 
					$iii = 0;
					foreach ($this->items as $i => $item) : 
						$canEdit = JvitalsHelper::canDo('users.manage', 'com_extendedreg');
						// If this group is super admin and this user is not super admin, $canEdit is false
						if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin')) {
							$canEdit = false;
						}
						
						$userInfo = "<nobr><b>" . JText::_('COM_EXTENDEDREG_REGISTER_DATE') . "</b> " . erHelperHTML::formatDate($item->registerDate, $this->dateFormat). "</nobr><br/>";
						$userInfo .= "<nobr><b>" . JText::_('COM_EXTENDEDREG_LASTVISIT_DATE') . "</b> " . ($item->lastvisitDate != '0000-00-00 00:00:00' ? erHelperHTML::formatDate($item->lastvisitDate, $this->dateFormat) : JText::_('COM_EXTENDEDREG_NEVER')) . "</nobr><br/>";
						$userInfo .= "<nobr><b>" . JText::_('COM_EXTENDEDREG_IPADDR') . "</b> " . (trim($item->ip_addr) ? $item->ip_addr : " - ") . "</nobr><br/><br/>";
						$userInfo .= "<b>" . JText::_('COM_EXTENDEDREG_USERGROUPS') . "</b> " . nl2br($item->group_names);
					?>
					<tr class="row<?php echo $iii % 2; ?>">
						<td class="center hidden-phone">
						<?php if ($canEdit) : ?>
							<?php echo JHtml::_('grid.id', $iii, $item->id); ?>
						<?php endif; ?>
						</td>
						<td class="nowrap">
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=users.edit&cid='.(int)$item->id, false); ?>"><?php echo $item->name; ?></a>
							<?php else : ?>
								<?php echo $item->name; ?>
							<?php endif; ?>
							<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/hint.png" class="hasTip" alt="" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_INFO')); ?>::<?php echo htmlspecialchars($userInfo); ?>" style="vertical-align:middle;" />
							<?php if (trim($item->notes)) : ?>
							<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/publish_y.png" class="hasTip" alt="" title="<?php echo htmlspecialchars(JText::_('COM_EXTENDEDREG_ADMIN_NOTES')); ?>::<?php echo htmlspecialchars($item->notes); ?>" style="vertical-align:middle;" />
							<?php endif; ?>
						</td>
						<td><?php echo $item->username; ?></td>
						<td><a href="mailto:<?php echo $item->email; ?>"><?php echo $item->email; ?></a></td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $blockStates, !$item->block, $iii, 'users.', $canEdit); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $activationStates, !$item->activation, $iii, 'users.', $canEdit); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $approveStates, $item->approve, $iii, 'users.', $canEdit); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $termStates, $item->acceptedterms, $iii, 'users.', $canEdit); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $overageStates, $item->overage, $iii, 'users.', $canEdit); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo erHelperHTML::formatDate($item->registerDate, $this->dateFormat); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo $item->id; ?>
						</td>
						<?php if ($this->ef && $countExtraFields) : ?>
						<?php 
							foreach ($this->ef as $customField) : 
								$fldname = $customField->name;
						?>
						<td class="fld-<?php echo $fldname; ?>" style="display: none;"><?php if (isset($item->$fldname)) { echo str_replace('#!#', ', ', $item->$fldname); } else { echo '-'; } ?></td>
						<?php endforeach; ?>
						<?php endif; ?>
					</tr>
					<?php $iii ++; endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			<div>
				<input type="hidden" name="extension" value="com_extendedreg" />
				<input type="hidden" name="task" value="users.manage" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
		<!-- End Content -->
	</div>
</form>
<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();