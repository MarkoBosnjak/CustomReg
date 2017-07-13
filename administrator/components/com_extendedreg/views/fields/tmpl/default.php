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

$document = JFactory::getDocument();
ob_start();
?>
table.adminlist .grouprow td {
	background: #cccccc;
	color: #000000;
	font-size: 14px;
}

.grouprow td a {
	color: #000000;
}
<?php
$style = ob_get_clean();
$document->addStyleDeclaration($style);

erHelperJavascript::OnDomBegin('', false);

?>
<script language="JavaScript">
function isGrpChecked(isitchecked){
	if (isitchecked == true){
		document.adminForm.grpchecked.value++;
	} else {
		document.adminForm.grpchecked.value--;
	}
}
</script>
<?php

if (JvitalsDefines::compatibleMode() == '30>') {
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
}

$app = JFactory::getApplication();
$listOrder = $this->escape($app->getUserState('com_extendedreg.list.ordering'));
$listDirn = $this->escape($app->getUserState('com_extendedreg.list.direction'));

$ordering = ($listOrder == 'f.ord');
$saveOrder = ($listOrder == 'f.ord' && mb_strtolower($listDirn) == 'asc');
$canEdit = JvitalsHelper::canDo('fields.manage', 'com_extendedreg');
$canEditGroup = JvitalsHelper::canDo('fields.groups', 'com_extendedreg');
$sortFields = $this->getSortFields();

if (JvitalsDefines::compatibleMode() == '30>' && $canEdit && $saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_extendedreg&task=forms.fld_saveorder&tmpl=component';
	JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

$publishStates = array(
	1 => array('fld_unpublish', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('fld_publish', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

$editableStates = array(
	1 => array('fld_noteditable', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('fld_editable', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
);

$requiredStates = array(
	1 => array('fld_notrequired', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('fld_required', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
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
		Joomla.tableOrdering(order, dirn, 'forms.fields');
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
						<?php echo JHtml::_('select.options', $this->fieldStates, 'value', 'text', $app->getUserState('com_extendedreg.filter.fld_state'));?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_type" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_TYPE');?></label>
					<select name="filter_type" id="filter_type" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_TYPE'); ?></option>
						<?php echo JHtml::_('select.options', $this->fieldTypes, 'value', 'text', $app->getUserState('com_extendedreg.filter.fld_type'));?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_required" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_REQUIRED');?></label>
					<select name="filter_required" id="filter_required" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_REQUIRED'); ?></option>
						<?php echo JHtml::_('select.options', $this->fieldRequiredOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.fld_required'));?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_group" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_GROUP');?></label>
					<select name="filter_group" id="filter_group" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_GROUP'); ?></option>
						<?php echo JHtml::_('select.options', $this->fieldGroups, 'value', 'text', $app->getUserState('com_extendedreg.filter.fld_group'));?>
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
					<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" id="filter_search" value="<?php echo $this->escape($app->getUserState('com_extendedreg.filter.fld_search')); ?>" title="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" />
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
			<div class="clearfix"></div>
			<table class="table table-striped adminlist" id="itemList">
				<thead>
					<tr>
						<th width="1%" class="nowrap hidden-phone"></th>
						<th width="1%" class="nowrap hidden-phone">
							<?php if ($canEdit) : ?>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							<?php else : ?>
							-
							<?php endif; ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FIELDS_TITLE'), 'f.title', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th class="nowrap" >
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FIELDS_NAME'), 'f.name', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th class="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FIELDS_TYPE'), 'f.type', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FIELDS_REQUIRED'), 'f.required', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FIELDS_EDITABLE'), 'f.editable', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_STATE'), 'f.published', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FIELDS_ORDER'), 'f.ord', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'ID', 'f.id', $listDirn, $listOrder, 'forms.fields'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php 
					$originalOrders = array();
					$oldGrp = -1;
					$gk = 0;
					$iii = 0;
					
					foreach ($this->items as $i => $item) : 
						if ($oldGrp != $item->grpid) : ?>
							<tr class="grouprow">
								<td width="1%" class="hidden-phone"></td>
								<td width="1%" class="hidden-phone center">
									<?php if ($canEditGroup) : ?>
									<input type="checkbox" id="grp<?php echo $gk; ?>" name="grpid[]" value="<?php echo $item->grpid; ?>" onclick="isGrpChecked(this.checked);" />
									<?php endif; ?>
								</td>
								<td colspan="9">
									<b><?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP'); ?>:</b> 
									<?php if ($canEditGroup) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=forms.fldgrp_edit&grpid=' . (int)$item->grpid, false); ?>"><?php echo $item->grpname; ?></a>
									<?php else : ?>
									<?php echo $item->grpname; ?>
									<?php endif; ?>
								</td>
							</tr>
							<?php
							$gk ++;
							$oldGrp = $item->grpid;
						endif;
						$orderkey = array_search($item->id, $this->ordering[$item->grpid]);
					?>
					<tr class="row<?php echo $iii % 2; ?><?php echo (!in_array($item->type, $this->checktypes) ? ' row-warning' : ''); ?>" sortable-group-id="<?php echo $item->grpid; ?>">
						<td width="1%" class="order nowrap center hidden-phone">
							<?php if ($canEdit) :
								$disableClassName = '';
								$disabledLabel = '';
								if (!$saveOrder) :
									$disabledLabel = JText::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
									<i class="icon-menu"></i>
								</span>
								<input type="text" style="display:none"  name="order[<?php echo $item->id; ?>]" size="5" value="<?php echo $orderkey + 1; ?>" class="width-20 text-area-order " />
								<?php $originalOrders[] = $orderkey + 1; ?>
							<?php else : ?>
								<span class="sortable-handler inactive" >
									<i class="icon-menu"></i>
								</span>
							<?php endif; ?>
						</td>
						<td width="1%" class="center hidden-phone">
						<?php if ($canEdit) : ?>
							<?php echo JHtml::_('grid.id', $iii, $item->id); ?>
						<?php endif; ?>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid='.(int)$item->id, false); ?>"><?php echo $item->title; ?></a>
							<?php else : ?>
								<?php echo $item->title; ?>
							<?php endif; ?>
							</div>
							<?php if ($canEdit && JvitalsDefines::compatibleMode() == '30>') : ?>
							<div class="pull-left">
								<?php
									// Create dropdown items
									JHtmlDropdown::addCustomItem(JText::_('JACTION_EDIT'), JRoute::_('index.php?option=com_extendedreg&task=forms.fld_edit&cid='.(int)$item->id, false));
									JHtml::_('dropdown.divider');
									if ((int)$item->published) :
										JHtmlDropdown::addCustomItem(JText::_('JTOOLBAR_UNPUBLISH'), 'javascript:void(0)', 'onclick="contextAction(\'cb' . $iii . '\', \'forms.fld_unpublish\')"');
									else :
										JHtmlDropdown::addCustomItem(JText::_('JTOOLBAR_PUBLISH'), 'javascript:void(0)', 'onclick="contextAction(\'cb' . $iii . '\', \'forms.fld_publish\')"');
									endif;
									// render dropdown list
									echo JHtml::_('dropdown.render');
								?>
							</div>
							<?php endif; ?>
						</td>
						<td>
							<?php echo $item->name; ?>
						</td>
						<td>
							<?php echo $item->type; ?>
							<?php if (!in_array($item->type, $this->checktypes)) : ?>
							<img src="<?php echo JvitalsDefines::comBackPath('com_extendedreg', true); ?>assets/images/16x16/publish_y.png" class="hasTip" alt="warning" title="<?php echo JText::_('COM_EXTENDEDREG_WARNING'); ?>::<p><?php echo JText::_('COM_EXTENDEDREG_FIELDS_TYPE_DOES_NOT_EXIST'); ?></p>" />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $requiredStates, $item->required, $iii, 'forms.', $canEdit); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $editableStates, $item->editable, $iii, 'forms.', $canEdit); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.state', $publishStates, $item->published, $iii, 'forms.', $canEdit); ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->ord;?>
						</td>
						<td>
							<?php echo $item->grpname; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php $iii++; ?>
					<?php endforeach; ?>
					<?php foreach ($this->emptyGroups as $i => $group) : ?>
					<tr class="grouprow">
						<td width="1%" class="hidden-phone"></td>
						<td width="1%" class="center hidden-phone">
							<?php if ($canEditGroup) : ?>
							<input type="checkbox" id="grp<?php echo $gk; ?>" name="grpid[]" value="<?php echo $group->grpid; ?>" onclick="isGrpChecked(this.checked);" />
							<?php endif; ?>
						</td>
						<td colspan="9">
							<b><?php echo JText::_('COM_EXTENDEDREG_FIELDS_GROUP'); ?>:</b> 
							<?php if ($canEditGroup) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=forms.fldgrp_edit&grpid='.(int)$group->grpid, false); ?>"><?php echo $group->name; ?></a>
							<?php else : ?>
							<?php echo $group->name; ?>
							<?php endif; ?>
						</td>
					</tr>
					<?php $gk++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div>
				<input type="hidden" name="extension" value="com_extendedreg" />
				<input type="hidden" name="task" value="forms.fields" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="grpchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
		<!-- End Content -->
	</div>
</form>
<?php

erHelperJavascript::OnDomReady('', false);

echo $this->html->wrapperEnd();