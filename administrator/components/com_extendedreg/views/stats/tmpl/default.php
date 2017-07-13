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

JHtml::_('behavior.multiselect');

$app = JFactory::getApplication();
$loggeduser = JFactory::getUser();
$listOrder = $this->escape($app->getUserState('com_extendedreg.list.ordering'));
$listDirn = $this->escape($app->getUserState('com_extendedreg.list.direction'));
$canEdit = JvitalsHelper::canDo('stats.manage', 'com_extendedreg');
$sortFields = $this->getSortFields();

erHelperJavascript::OnDomReady('(function($) { 
	$("#stattabs-links-0").tab("show");
	
	$(\'a[data-toggle="tab"]\').on("shown", function (e) {
		$(e.target).blur();
		window.location.href = $(e.target).attr("data-link");
		return false;
	});
})(jQuery); ');

?>
<script language="JavaScript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, 'default.stats');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_extendedreg', false); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="row-fluid">
		<!-- Begin Sidebar -->
		<div id="sidebar" class="span2">
			<div class="sidebar-nav">
				<?php include (JvitalsDefines::comBackPath('com_extendedreg') . 'toolbar.extendedreg.php'); ?>
				<hr />
				<div class="filter-select">
					<h4 class="page-header"><?php echo JText::_('COM_EXTENDEDREG_FILTER');?></h4>
					<label for="filter_action" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_STATS_ACTION');?></label>
					<select name="filter_action" id="filter_action" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_STATS_ACTION'); ?></option>
						<?php echo JHtml::_('select.options', $this->statsActionOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.stats_action')); ?>
					</select>
					<hr class="hr-condensed" />
					<label for="filter_proxy" class="element-invisible"><?php echo JText::_('COM_EXTENDEDREG_FILTER_STATS_PROXY');?></label>
					<select name="filter_proxy" id="filter_proxy" class="span12 small" onchange="this.form.submit()">
						<option value="*"><?php echo JText::_('COM_EXTENDEDREG_FILTER_STATS_PROXY'); ?></option>
						<?php echo JHtml::_('select.options', $this->statsProxyOptions, 'value', 'text', $app->getUserState('com_extendedreg.filter.stats_proxy')); ?>
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
					<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" id="filter_search" value="<?php echo $this->escape($app->getUserState('com_extendedreg.filter.stats_search')); ?>" title="<?php echo JText::_('COM_EXTENDEDREG_SEARCH'); ?>" />
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
			<div class="row-fluid">
				<!-- Begin Content -->
				<div class="span12 form-horizontal">
					<ul class="nav nav-tabs">
						<li><a id="stattabs-links-0" href="#stattabs-0" data-link="<?php echo JRoute::_('index.php?option=com_extendedreg&task=default.stats&layout=default', false);?>" data-toggle="tab"><?php echo JText::_('COM_EXTENDEDREG_STATS_ACTIVITY'); ?></a></li>
						<li><a id="stattabs-links-1" href="#stattabs-1" data-link="<?php echo JRoute::_('index.php?option=com_extendedreg&task=default.stats&layout=inactive', false);?>" data-toggle="tab"><?php echo JText::_('COM_EXTENDEDREG_STATS_INACTIVE_USERS'); ?></a></li>
						<li><a id="stattabs-links-2" href="#stattabs-2" data-link="<?php echo JRoute::_('index.php?option=com_extendedreg&task=default.stats&layout=ipaddr', false);?>" data-toggle="tab"><?php echo JText::_('COM_EXTENDEDREG_STATS_USERS_IPADDR'); ?></a></li>
					</ul>
					<div class="tab-content">
						<!-- Begin Tabs -->
						<div class="tab-pane" id="stattabs-0">
							<div class="row-fluid">
								<div class="span12">
									<!-- Begin Tab Content -->
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
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_NAME'), 'u.name', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th >
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_USERNAME'), 'u.username', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th>
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_EMAIL'), 'u.email', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th width="5%" class="nowrap">
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_STATS_IPADDR'), 's.ip_addr', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th width="1%" class="nowrap">
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_STATS_PORT'), 's.port', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th width="1%" class="nowrap">
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_STATS_PROXY'), 's.proxy', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th>
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_STATS_ACTION'), 's.action', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th>
													<?php echo JHtml::_('grid.sort', JText::_('COM_EXTENDEDREG_TIME'), 's.tstamp', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
												<th width="1%" class="nowrap hidden-phone">
													<?php echo JHtml::_('grid.sort', 'ID', 's.id', $listDirn, $listOrder, 'default.stats'); ?>
												</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<td colspan="10">
													<?php echo $this->pagination->getListFooter(); ?>
												</td>
											</tr>
										</tfoot>
										<tbody>
											<?php if (!count($this->items)) : ?>
											<tr class="row0">
												<td colspan="10">
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
												<td>
													<?php echo $item->name; ?>
												</td>
												<td>
													<?php echo $item->username; ?>
												</td>
												<td>
													<?php echo $item->email; ?>
												</td>
												<td>
													<a href="http://tools.whois.net/whoisbyip/?host=<?php echo $item->ip_addr; ?>" target="_blank"><?php echo $item->ip_addr; ?></a>
												</td>
												<td>
													<?php echo $item->port; ?>
												</td>
												<td>
													<?php echo JText::_((int)$item->proxy ? 'COM_EXTENDEDREG_YES' : 'COM_EXTENDEDREG_NO'); ?>
												</td>
												<td>
													<?php echo $item->action; ?>
												</td>
												<td>
													<?php echo erHelperHTML::formatDate($item->tstamp, $this->dateFormat); ?>
												</td>
												<td class="hidden-phone">
													<?php echo $item->id; ?>
												</td>
											</tr>
											<?php endforeach; ?>
											<?php endif; ?>
										</tbody>
									</table>
									<!-- End Tab Content -->
								</div>
							</div>
						</div>
						<div class="tab-pane" id="stattabs-1"></div>
						<div class="tab-pane" id="stattabs-2"></div>
						<!-- End Tabs -->
					</div>
				</div>
				<!-- End Content -->
			</div>
			<div class="clearfix"></div>
			<div>
				<input type="hidden" name="task" value="default.stats" />
				<input type="hidden" name="layout" value="default" />
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