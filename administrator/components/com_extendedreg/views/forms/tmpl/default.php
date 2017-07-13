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

if (JvitalsDefines::compatibleMode() == '30>') {
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
}

$app = JFactory::getApplication();
$listOrder = $this->escape($app->getUserState('com_extendedreg.list.ordering'));
$listDirn = $this->escape($app->getUserState('com_extendedreg.list.direction'));
$sortFields = $this->getSortFields();
$canEdit = JvitalsHelper::canDo('forms.manage', 'com_extendedreg');

$publishStates = array(
	1 => array('unpublish', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'publish', 'publish'),
	0 => array('publish', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', 'COM_EXTENDEDREG_CLICK_TO_TOGGLE_STATE', false, 'unpublish', 'unpublish'),
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
		Joomla.tableOrdering(order, dirn, 'forms.browse');
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
						<?php echo JHtml::_('select.options', $this->formStates, 'value', 'text', $app->getUserState('com_extendedreg.filter.forms.filter_state'));?>
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
					<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" id="filter_search" value="<?php echo $this->escape($app->getUserState('com_extendedreg.filter.forms.filter_search')); ?>" title="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" />
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
			<table class="table table-striped" id="itemList">
				<thead>
					<tr>
						<th width="1%" class="nowrap hidden-phone">
							<?php if ($canEdit) : ?>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							<?php else : ?>
							-
							<?php endif; ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_ADDON_NAME'), 'f.name', $listDirn, $listOrder, 'forms.browse'); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_FORMS_ISDEFAULT'), 'f.isdefault', $listDirn, $listOrder, 'forms.browse'); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_STATE'), 'f.published', $listDirn, $listOrder, 'forms.browse'); ?>
						</th>
						<th class="hidden-phone">
							<?php echo JText::_('COM_EXTENDEDREG_FORMS_DESCR'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'ID', 'f.id', $listDirn, $listOrder, 'forms.browse'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php if (!count($this->items)) : ?>
					<tr class="row0">
						<td colspan="6">
							<?php echo JText::_('COM_EXTENDEDREG_NO_RECORDS_FOUND'); ?>
						</td>
					</tr>
					<?php else : ?>
					<?php foreach ($this->items as $i => $item) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center hidden-phone">
						<?php if ($canEdit) : ?>
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						<?php endif; ?>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_extendedreg&task=forms.edit&cid='.(int)$item->id, false); ?>"><?php echo $item->name; ?></a>
							<?php else : ?>
								<?php echo $item->name; ?>
							<?php endif; ?>
							</div>
							<?php if ($canEdit && JvitalsDefines::compatibleMode() == '30>') : ?>
							<div class="pull-left">
								<?php
									// Create dropdown items
									JHtml::_('dropdown.edit', $item->id, 'forms.');
									JHtml::_('dropdown.divider');
									if ((int)$item->published) :
										JHtml::_('dropdown.unpublish', 'cb' . $i, 'forms.');
									else :
										JHtml::_('dropdown.publish', 'cb' . $i, 'forms.');
									endif;

									JHtml::_('dropdown.divider');

									JHtml::_('dropdown.trash', 'cb' . $i, 'forms.');

									// render dropdown list
									echo JHtml::_('dropdown.render');
								?>
							</div>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.isdefault', $item->isdefault, $i, 'forms.', false);?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'forms.', $canEdit);?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->description; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			<div>
				<input type="hidden" name="extension" value="com_extendedreg" />
				<input type="hidden" name="task" value="forms.browse" />
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