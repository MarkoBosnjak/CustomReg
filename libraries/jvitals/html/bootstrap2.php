<?php
/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JvitalsHtmlBootstrap2 extends JvitalsHtml {
	
	public static function loadStyles() {
		parent::loadStyles();
		
		$document = JFactory::getDocument();
		if (JvitalsDefines::compatibleMode() == '25>') {
			$document->addStyleSheet(JvitalsDefines::vendorPath(true) . 'bootstrap2x/css/bootstrap.min.css');
		}
		$document->addStyleSheet(JvitalsDefines::vendorPath(true) . 'bootstrap2x/css/bootstrap-timepicker.min.css');
		$document->addStyleSheet(JvitalsDefines::vendorPath(true) . 'bootstrap2x/css/bootstrap-datepicker.min.css');
		$document->addStyleSheet(JvitalsDefines::vendorPath(true) . 'bootstrap2x/css/bootstrap-fileupload.min.css');
		$document->addStyleSheet(JvitalsDefines::vendorPath(true) . 'bootstrap2x/css/bootstrap-transfer.min.css');
	}
	
	public static function loadScripts() {
		parent::loadScripts();
		
		$document = JFactory::getDocument();
		if (JvitalsDefines::compatibleMode() == '25>') {
			$document->addScript(JvitalsDefines::vendorPath(true) . 'jquery/jquery.min.js');
			$document->addScript(JvitalsDefines::vendorPath(true) . 'jquery/jquery-migrate.min.js');
			$document->addScript(JvitalsDefines::vendorPath(true) . 'jquery/jquery-noconflict.js');
			$document->addScript(JvitalsDefines::vendorPath(true) . 'bootstrap2x/js/bootstrap.min.js');
		} else {
			JHtml::_('jquery.framework');
			JHtml::_('bootstrap.framework');
		}
		$document->addScript(JvitalsDefines::vendorPath(true) . 'jquery/jquery-json.min.js');
		$document->addScript(JvitalsDefines::vendorPath(true) . 'bootstrap2x/js/bootstrap-timepicker.min.js');
		$document->addScript(JvitalsDefines::vendorPath(true) . 'bootstrap2x/js/bootstrap-datepicker.min.js');
		$document->addScript(JvitalsDefines::vendorPath(true) . 'bootstrap2x/js/bootstrap-fileupload.min.js');
		$document->addScript(JvitalsDefines::vendorPath(true) . 'bootstrap2x/js/bootstrap-transfer.min.js');
	}
	
	public function formHeader($sidebar = null, $className = null, $multipart = false, $force_admin = false) {
		$is_admin = ($this->app->isAdmin() || $force_admin);
		$this->formName = ($is_admin ? 'adminForm' : 'webForm');
		$this->formID = ($is_admin ? $this->formName : $this->generateID('form-'));
		$action_url = JRoute::_('index.php?option=' . $this->component, false);
		//~ $action_url = JURI::base(true) . '/index.php';
		
		ob_start();
		?>
		<form action="<?php echo $action_url; ?>" method="post" name="<?php echo $this->formName; ?>" id="<?php echo $this->formID; ?>"<?php echo ($className ? ' class="' . $className . '"' : ''); ?><?php echo ($multipart ? ' enctype="multipart/form-data"' : ''); ?>>
			<div class="row-fluid">
				<?php if ($sidebar) : ?>
				<!-- Begin Sidebar -->
				<div id="sidebar" class="span2">
					<div class="sidebar-nav">
						<?php echo $sidebar; ?>
					</div>
				</div>
				<!-- End Sidebar -->
				<?php endif; ?>
				<!-- Begin Content -->
				<div class="<?php if ($sidebar) : ?>span10<?php else : ?>span12<?php endif; ?>">
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
	public function formFooter() {
		ob_start();
		?>
				</div>
				<!-- End Content -->
			</div>
		</form>
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
	public function startColumn($id = null, $colspan = 1) {
		static $counter;
		if (!$counter) $counter = 1;
		if (is_null($id)) $id = $this->generateID('col-', '-' . $counter);
		$counter ++;
		if ((int)$colspan > 12) {
			$colspan = 12;
		} elseif (!(int)$colspan) {
			$colspan = 1;
		}
		return '<div id="' . $id . '" class="span' . $colspan . '">';
	}
	
	public function endColumn() {
		return '</div>';
	}
	
	public function startRow($id = null) {
		static $counter;
		if (!$counter) $counter = 1;
		if (is_null($id)) $id = $this->generateID('row-', '-' . $counter);
		$counter ++;
		return '<div id="' . $id . '" class="row-fluid">';
	}
	
	public function endRow() {
		return '</div>';
	}
	
	public function buildTooltip($text, $tooltip, $placement = 'top') {
		static $counter;
		if (!$counter) $counter = 1;
		$id = $this->generateID('tooltip-', '-' . $counter);
		$counter ++;
		/*
			we show the element on "hidden" event to fix bug in bootstrap when it works with other libraries like mootools
			in general it hides the element along with the tooltip
		*/
		return '<span class="make-inline-block" data-toggle="tooltip" data-placement="' . $placement . '" title="' . $this->prepAttrValue($tooltip) . '" id="' . $id . '">' . $text . '</span>
			<script type="text/javascript">jQuery(\'#' . $id . '\').tooltip().on(\'hidden\', function(event) { jQuery(this).show(); });</script>';
	}
	
	public function buildPopover($text, $title, $popover, $placement = 'top') {
		static $counter;
		if (!$counter) $counter = 1;
		$id = $this->generateID('popover-', '-' . $counter);
		$counter ++;
		/*
			we show the element on "hidden" event to fix bug in bootstrap when it works with other libraries like mootools
			in general it hides the element along with the tooltip
		*/
		return '<span class="make-inline-block" data-toggle="popover" data-placement="' . $placement . '" id="' . $id . '">' . $text . '</span>
			<script type="text/javascript">jQuery(\'#' . $id . '\').popover({html: true, trigger: \'hover focus\', title: \'' . $title . '\', content: \'' . $popover . '\'}).on(\'hidden\', function(event) { jQuery(this).show(); });</script>';
	}
	
	public function buildIcon($icon, $class = '') {
		return '<i class="icon-' . $icon . (trim($class) ? ' ' . $class : '') . '"></i>';
	}
	
	public function buildButton($config) {
		if (!$config || !is_array($config)) {
			throw new RuntimeException('HTML class buildButton not configured!!!');
		}
		
		if (!isset($config['text']) || !trim($config['text'])) {
			throw new RuntimeException('HTML class buildButton text not set!!!');
		}
		
		if (!isset($config['type']) || !trim($config['type'])) {
			$config['type'] = 'button';
		}
		
		ob_start();
		?>
		<button type="<?php echo $config['type']; ?>" class="btn<?php echo (isset($config['class']) && trim($config['class']) ? ' ' . $config['class'] : ''); ?>"<?php echo (isset($config['onclick']) && trim($config['onclick']) ? ' onclick="' . $config['onclick'] . '"' : ''); ?>><?php echo (isset($config['icon']) && trim($config['icon']) ? $this->buildIcon($config['icon'], 'icon-white') . ' ' : ''); ?><?php echo $config['text']; ?></button>
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
	public function buildModal($config) {
		static $counter;
		if (!$counter) $counter = 1;
		
		if (!$config || !is_array($config)) {
			throw new RuntimeException('HTML class buildModal not configured!!!');
		}
		
		if (!isset($config['title']) || !trim($config['title'])) {
			throw new RuntimeException('HTML class buildModal title not set!!!');
		}
		
		if (!isset($config['body']) || !trim($config['body'])) {
			throw new RuntimeException('HTML class buildModal body not set!!!');
		}
		
		if (!isset($config['buttons']) || !is_array($config['buttons'])) {
			throw new RuntimeException('HTML class buildModal buttons not set!!!');
		}
		
		if (!isset($config['id'])) $config['id'] = $this->generateID('modal-', '-' . $counter);
		$counter ++;
		
		ob_start();
		?>
		<!-- Begin Popup <?php echo $config['title']; ?> -->
		<div class="modal" id="<?php echo $config['id']; ?>" style="display: none;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="<?php echo $config['id']; ?>-title"><?php echo $config['title']; ?></h3>
			</div>
			<div class="modal-body" id="<?php echo $config['id']; ?>-body"><?php echo $config['body']; ?></div>
			<div class="modal-footer" id="<?php echo $config['id']; ?>-footer">
				<?php foreach ($config['buttons'] as $button) : echo $this->buildButton($button); endforeach; ?>
			</div>
		</div>
		<script type="text/javascript">jQuery('#<?php echo $config['id']; ?>').modal({show: false});</script>
		<!-- End Popup <?php echo $config['title']; ?> -->
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
	public function buildTabs($config) {
	
	}
	
	public function buildAccordion($config) {
	
	}
	
	public function buildList($config) {
		if (!$config || !is_array($config)) {
			throw new RuntimeException('HTML class buildList not configured!!!');
		}
		
		if (!isset($config['row_template']) || !$config['row_template']) {
			throw new RuntimeException('HTML class buildList - row template is not set!!!');
		}
		
		$controller = $this->app->input->get('controller');
		$task = $this->app->input->get('task');
		$listOrder = $this->app->getUserState($this->component . '.list' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.ordering');
		$listDirn = $this->app->getUserState($this->component . '.list' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.direction');
		
		$do_sorting = ((int)$config['show_sorting'] && isset($config['fields']) && is_array($config['fields']));
		$sortFields = array();
		
		if ($do_sorting) {
			foreach ($config['fields'] as $col) {
				if (isset($col['sorting'])) {
					$title = (isset($col['title']) ? $col['title'] : $col['name']);
					$sortFields[$col['sorting']] = $title;
				}
			}
		}
		
		$filter = '';
		if ((int)$config['show_search'] || isset($config['pagination']) || (int)$config['show_sorting']) {
		
			// Filter bar - search, ordering, pagination, etc.
			ob_start();
			?>
			<?php if ($do_sorting) : ?>
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
					Joomla.tableOrdering(order, dirn, '<?php echo $controller . '.' . $task; ?>');
				}
			</script>
			<?php endif; ?>
			<div id="filter-bar" class="btn-toolbar">
				<?php if ((int)$config['show_search']) : ?>
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_($this->languageKey . '_SEARCH'); ?></label>
					<input type="text" name="filter_search" placeholder="<?php echo JText::_($this->languageKey . '_SEARCH'); ?>" id="filter_search" value="<?php echo $this->app->getUserState($this->component . '.filter' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.filter_search'); ?>" title="<?php echo JText::_($this->languageKey . '_SEARCH'); ?>" />
				</div>
				<div class="btn-group pull-left hidden-phone">
					<button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_($this->languageKey . '_GO_BUTTON'); ?>"><i class="icon-search"></i></button>
					<button class="btn tip" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_($this->languageKey . '_RESET_BUTTON'); ?>"><i class="icon-remove"></i></button>
				</div>
				<?php endif; ?>
				<?php if (isset($config['pagination']) && (!isset($config['pagination_dropdown']) || (int)$config['pagination_dropdown'])) : ?>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_($this->languageKey . '_PAGINATION_LIMITBOX_DESC'); ?></label>
					<?php echo $config['pagination']->getLimitBox(); ?>
				</div>
				<?php endif; ?>
				<?php if ($do_sorting && (!isset($config['sorting_dropdown']) || (int)$config['sorting_dropdown'])) : ?>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_($this->languageKey . '_ORDERING_DESC'); ?></label>
					<select name="directionTable" id="directionTable" class="input-small" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_($this->languageKey . '_ORDERING_DESC'); ?></option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_($this->languageKey . '_ORDER_ASCENDING'); ?></option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_($this->languageKey . '_ORDER_DESCENDING'); ?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_($this->languageKey . '_SORT_BY'); ?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_($this->languageKey . '_SORT_BY'); ?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
					</select>
				</div>
				<?php endif; ?>
			</div>
			<?php
			$filter = ob_get_clean();
		}
		
		$list = '';
		if (isset($config['items']) && is_array($config['items'])) {
			$className = '';
			$listID = $this->generateID('list-');
			$column_width = 12;
			if (isset($config['list_id']) && $config['list_id']) {
				$listID = $config['list_id'];
			}
			if (isset($config['list_class']) && $config['list_class']) {
				$className .= $config['list_class'];
			}
			if (isset($config['column_width']) && $config['column_width']) {
				$column_width = $config['column_width'];
			}
			if ($className) $className = ' class="' . $className . '"';
			
			// Start the list
			$list .= '<div' . $className . ' id="' . $listID . '">' . "\n";
			
			$list .= $this->startRow();
			if (!count($config['items'])) {
				$list .= $this->startColumn(null, 12);
				$list .= '<p class="text-nowrap text-center text-error"><b>' . JText::_($this->languageKey . '_NO_RECORDS_FOUND') . '</b></p>' . "\n";
				$list .= $this->endColumn();
			} else {
				$i = 1;
				$cnt = 12/$column_width;
				$fields = array();
				foreach ($config['items'] as $row) {
					$list .= $this->startColumn(null, $column_width);
					$list .= $this->parseTemplate($config['row_template'], $row, null) . "\n";
					$list .= $this->endColumn();
					if ($i%$cnt == 0) {
						$list .= $this->endRow();
						$list .= $this->startRow();
					}
					
					$i++;
				}
			}
			$list .= $this->endRow();
			
			if (isset($config['pagination']) && count($config['items'])) {
				if ((int)$config['pagination']->get('pages.total') > 1) {
					$list .= $this->startRow();
					$list .= $this->startColumn(null, 12);
					$list .= $this->buildPagination($config['pagination']);
					$list .= '<input type="hidden" name="limitstart" value="0" />';
					$list .= $this->endColumn();
					$list .= $this->endRow();
				}
			}
			
			// End the list
			$list .= '</div>' . "\n";
		}
		
		$output = $filter . $list;
		
		return $output;
	}
	
	public function buildTable($config) {
		if (!$config || !is_array($config)) {
			throw new RuntimeException('HTML class buildTable not configured!!!');
		}
		
		if (!isset($config['columns']) || !is_array($config['columns'])) {
			throw new RuntimeException('HTML class buildTable columns not set!!!');
		}
		
		$controller = $this->app->input->get('controller');
		$task = $this->app->input->get('task');
		$listOrder = $this->app->getUserState($this->component . '.list' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.ordering');
		$listDirn = $this->app->getUserState($this->component . '.list' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.direction');
		
		$sortImages = array('sort_asc.png', 'sort_desc.png');
		$sortIndex = intval($listDirn == 'desc');
		
		if ((int)$config['show_sorting']) {
			$sortFields = array();
			foreach ($config['columns'] as $col) {
				if (isset($col['sorting'])) {
					$title = (isset($col['title']) ? $col['title'] : $col['name']);
					$sortFields[$col['sorting']] = $title;
				}
			}
		}
		
		$filter = '';
		if ((int)$config['show_search'] || isset($config['pagination']) || (int)$config['show_sorting']) {
			// We will have filter bar
			ob_start();
			?>
			<?php if ((int)$config['show_sorting']) : ?>
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
					Joomla.tableOrdering(order, dirn, '<?php echo $controller . '.' . $task; ?>');
				}
			</script>
			<?php endif; ?>
			<div id="filter-bar" class="btn-toolbar">
				<?php if ((int)$config['show_search']) : ?>
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_($this->languageKey . '_SEARCH'); ?></label>
					<input type="text" name="filter_search" placeholder="<?php echo JText::_($this->languageKey . '_SEARCH'); ?>" id="filter_search" value="<?php echo $this->app->getUserState($this->component . '.filter' . ($this->userStateConstant ? '.' . $this->userStateConstant : '') . '.filter_search'); ?>" title="<?php echo JText::_($this->languageKey . '_SEARCH'); ?>" />
				</div>
				<div class="btn-group pull-left hidden-phone">
					<button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_($this->languageKey . '_GO_BUTTON'); ?>"><i class="icon-search"></i></button>
					<button class="btn tip" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_($this->languageKey . '_RESET_BUTTON'); ?>"><i class="icon-remove"></i></button>
				</div>
				<?php endif; ?>
				<?php if (isset($config['pagination']) && (!isset($config['pagination_dropdown']) || (int)$config['pagination_dropdown'])) : ?>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_($this->languageKey . '_PAGINATION_LIMITBOX_DESC'); ?></label>
					<?php echo $config['pagination']->getLimitBox(); ?>
				</div>
				<?php endif; ?>
				<?php if ((int)$config['show_sorting'] && (!isset($config['sorting_dropdown']) || (int)$config['sorting_dropdown'])) : ?>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_($this->languageKey . '_ORDERING_DESC'); ?></label>
					<select name="directionTable" id="directionTable" class="input-small" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_($this->languageKey . '_ORDERING_DESC'); ?></option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_($this->languageKey . '_ORDER_ASCENDING'); ?></option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_($this->languageKey . '_ORDER_DESCENDING'); ?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_($this->languageKey . '_SORT_BY'); ?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_($this->languageKey . '_SORT_BY'); ?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
					</select>
				</div>
				<?php endif; ?>
			</div>
			<?php
			$filter = ob_get_clean();
		}
		
		$table = '';
		if (isset($config['items']) && is_array($config['items'])) {
			$className = 'table';
			$tableID = $this->generateID('table-');
			
			if (isset($config['border']) && (int)$config['border']) {
				$className .= ' table-bordered';
			}
			if (isset($config['zebra']) && (int)$config['zebra']) {
				$className .= ' table-striped';
			}
			if (isset($config['hover']) && (int)$config['hover']) {
				$className .= ' table-hover';
			}
			
			$table .= '<table class="' . $className . '" id="' . $tableID . '">' . "\n";
			
			// Start header
			$table .= '<thead>' . "\n";
			foreach ($config['columns'] as $col) {
				$title = (isset($col['title']) ? $col['title'] : $col['name']);
				if ((int)$config['show_sorting'] && isset($col['sorting'])) {
					if ($listOrder != $col['sorting']) {
						$new_direction = $listDirn;
					} else {
						$new_direction = ($listDirn == 'desc') ? 'asc' : 'desc';
					}
					
					$table .= '<th>';
					$table .= '<a href="#" onclick="Joomla.tableOrdering(\'' . $col['sorting'] . '\',\'' . $new_direction . '\',\'' . $controller . '.' . $task . '\', document.getElementById(\'' . $this->formID . '\'));return false;" title="' . JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';
					$table .= $title;
					if ($listOrder == $col['sorting']) {
						$table .= '&nbsp;' . JHtml::_('image', 'system/' . $sortImages[$sortIndex], '', null, true);
					}
					$table .= '</th>' . "\n";
				} else {
					$table .= '<th>' . $title . '</th>' . "\n";
				}
			}
			$table .= '</thead>' . "\n";
			// End header
			
			// Start table rows
			$table .= '<tbody>' . "\n";
			if (!count($config['items'])) {
				$table .= '<tr class="warning">' . "\n";
				$table .= '<td colspan="' . (int)count($config['columns']) . '"><p class="text-nowrap text-center text-error"><b>' . JText::_($this->languageKey . '_NO_RECORDS_FOUND') . '</b></p></td>' . "\n";
				$table .= '</tr>' . "\n";
			} else {
				foreach ($config['items'] as $row) {
					$table .= '<tr>' . "\n";
					foreach ($config['columns'] as $col) {
						$tmpl = '{' . $col['name'] . '}';
						if (isset($col['tmpl'])) {
							$tmpl = $col['tmpl'];
						}
						$table .= '<td>' . $this->parseTemplate($tmpl, $row, $col['name']) . '</td>' . "\n";
					}
					$table .= '</tr>' . "\n";
				}
			}
			$table .= '</tbody>' . "\n";
			// End table rows
			
			if (isset($config['pagination']) && count($config['items'])) {
				if ((int)$config['pagination']->get('pages.total') > 1) {
					// Start footer
					$table .= '<tfoot>
						<tr>
							<td colspan="' . (int)count($config['columns']) . '">
								' . $this->buildPagination($config['pagination']) . '
								<input type="hidden" name="limitstart" value="0" />
							</td>
						</tr>
					</tfoot>';
					// End footer
				}
			}
			
			$table .= '</table>' . "\n";
		}
		
		
		$output = $filter . $table;
		
		return $output;
	}
	
	public function buildForm($config) {
		if (!$config || !is_array($config)) {
			throw new RuntimeException('HTML class buildForm not configured!!!');
		}
		
		if (!isset($config['fields']) || !is_array($config['fields'])) {
			throw new RuntimeException('HTML class buildForm fields not set!!!');
		}
		
		$output = '';
		$counter = 0;
		$allowedTypes = array('text', 'textarea', 'password', 'select', 'checkbox', 'radio', 'hidden', 'submit', 'button', 'reset', 'html', 'editor', 'upload', 'avatar', 'date', 'time', 'datetime');
		
		foreach ($config['fields'] as $field) {
			// check if we support this type
			$type = strtolower(trim($field['type']));
			unset($field['type']);
			if (!in_array($type, $allowedTypes)) {
				throw new RuntimeException(sprintf('HTML class buildForm unsupported type: %s', var_export($type, true)));
			}
			
			// Handle important data
			$name = isset($field['name']) && trim($field['name']) ? trim($field['name']) : $type . $counter;
			unset($field['name']);
			
			$label = isset($field['label']) && trim($field['label']) ? trim($field['label']) : $name;
			unset($field['label']);
			
			$value = isset($field['value']) ? $field['value'] : '';
			unset($field['value']);
			
			$options = isset($field['options']) && is_array($field['options']) ? $field['options'] : array();
			unset($field['options']);
			
			$prepend = isset($field['prepend']) && trim($field['prepend']) ? trim($field['prepend']) : '';
			unset($field['prepend']);
			
			$append = isset($field['append']) && trim($field['append']) ? trim($field['append']) : '';
			unset($field['append']);
			
			$help = isset($field['help']) && trim($field['help']) ? trim($field['help']) : '';
			unset($field['help']);
			
			$placeholder = isset($field['placeholder']) && trim($field['placeholder']) ? trim($field['placeholder']) : '';
			unset($field['placeholder']);
			
			$class = isset($field['class']) && trim($field['class']) ? trim($field['class']) : '';
			unset($field['class']);
			
			$id = isset($field['id']) ? $field['id'] : $this->generateID('fld-') . '-' . $counter;
			unset($field['id']);
			
			$required = isset($field['required']) ? (int)$field['required'] : 0;
			unset($field['required']);
			
			if ($required) {
				$class .= ' required';
			}
			
			// Special cases
			if (in_array($type, array('submit', 'button', 'reset'))) {
				$icon = isset($field['icon']) ? $field['icon'] : '';
				unset($field['icon']);
				
				$useLink = (isset($field['useLink']) && $field['useLink'] ? true : false);
				unset($field['useLink']);
			} elseif ($type == 'html') {
				$source = isset($field['source']) && trim($field['source']) ? trim($field['source']) : '';
				unset($field['source']);
			} elseif ($type == 'avatar') {
				$width = isset($field['width']) ? (int)$field['width'] : 200;
				unset($field['width']);
				
				$height = isset($field['height']) ? (int)$field['height'] : 150;
				unset($field['height']);
				
				$imgpath = isset($field['imgpath']) && trim($field['imgpath']) ? trim($field['imgpath']) : '';
				unset($field['imgpath']);
				
				$fileButtonClass = isset($field['fileButtonClass']) && trim($field['fileButtonClass']) ? trim($field['fileButtonClass']) : '';
				unset($field['fileButtonClass']);
				
				$removeButtonClass = isset($field['removeButtonClass']) && trim($field['removeButtonClass']) ? trim($field['removeButtonClass']) : '';
				unset($field['removeButtonClass']);
				
			} elseif ($type == 'date' || $type == 'datetime') {
				$format = isset($field['format']) && trim($field['format']) ? trim($field['format']) : 'yyyy/mm/dd';
				unset($field['format']);
				
				$config_object = isset($field['config_object']) && trim($field['config_object']) ? trim($field['config_object']) : '';
				unset($field['config_object']);
			} elseif ($type == 'select') {
				$multiple = isset($field['multiple']) ? true : false;
				unset($field['multiple']);
				
				$includeEmpty = isset($field['includeEmpty']) ? true : false;
				unset($field['includeEmpty']);
			}
			
			// Others are attributes - lets filter them
			$attr = $this->filterAttributes($type, $field);
			// Just in case we need this
			$attr['data-fldnum'] = $counter;
			
			$html = '';
			if (in_array($type, array('submit', 'button', 'reset'))) {
				if ($useLink) {
					$html = '<a href="#" id="' . $id . '" name="' . $name . '" class="btn ' . $class . '">' . (trim($icon) ? '<i class="' . trim($icon) . '"></i>' : '') . $label . '</a>';
				} else {
					$html = '<button type="' . $type . '" id="' . $id . '" name="' . $name . '" class="btn ' . $class . '" ' . $this->parseAttributes($attr, array()) . '>' . (trim($icon) ? '<i class="' . trim($icon) . '"></i>' : '') . $label . '</button>';
				}
			} elseif ($type == 'hidden') {
				$html = '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '" />';
			} elseif ($type == 'html') {
				$html = $source;
			} elseif ($type == 'avatar') {
				$html = '<div class="control-group">
					<label class="control-label" for="' . $id . '">' . $label . ((int)$required ? '<span class="star">&nbsp;*</span>' : '') . '</label>
					<div class="controls">
						<div id="' . $id . '" class="fileupload fileupload-' . ((int)$value ? 'exists' : 'new') . (trim($class) ? ' ' . trim($class) : '') . '" data-provides="fileupload" data-name="' . $name . '" ' . $this->parseAttributes($attr, array()) . '>
							<div class="fileupload-new thumbnail" style="width: ' . (int)$width . 'px; height: ' . (int)$height . 'px;">' . ((int)$value ? '' : '<img src="' . $imgpath . '" alt="' . basename($imgpath) . '" />') . '</div>
							<div class="fileupload-preview fileupload-exists thumbnail" style="width: ' . (int)$width . 'px; height: ' . (int)$height . 'px;">' . ((int)$value ? '<img src="' . $imgpath . '" alt="' . basename($imgpath) . '" />' : '') . '</div>
							<div>
								<span class="btn btn-file' . (trim($fileButtonClass) ? ' ' . trim($fileButtonClass) : '') . '"><span class="fileupload-new">' . JText::_($this->languageKey . '_FILEUPLOAD_SELECT_IMAGE') . '</span><span class="fileupload-exists">' . JText::_($this->languageKey . '_FILEUPLOAD_CHANGE_IMAGE') . '</span><input id="' . $id . '-uplfld" type="file"></span>
								<a href="#" class="btn fileupload-exists' . (trim($removeButtonClass) ? ' ' . trim($removeButtonClass) : '') . '" data-dismiss="fileupload">' . JText::_($this->languageKey . '_FILEUPLOAD_REMOVE_IMAGE') . '</a>
							</div>
						</div>
						' . ($help ? '<span class="help-block">' . $help . '</span>' : '') . '
					</div>
				</div>';
			} elseif ($type == 'datetime') {
				$date = '0000-00-00';
				$time = '00:00:00';
				if (preg_match('~^(\d{4})\-(\d{2})\-(\d{2})\s+(\d{2}):(\d{2}):(\d{2})$~', $value)) {
					$userTime = JvitalsTime::getUser($value, 'utc');
					$date = $userTime->format('d/m/Y');
					$time = $userTime->format('H:i');
				}
				
				$html = '<div class="control-group">
					<label class="control-label" for="' . $id . '-date">' . $label . ((int)$required ? '<span class="star">&nbsp;*</span>' : '') . '</label>
					<div class="controls">
						<div class="input-append bootstrap-datepicker" id="' . $id . '-date-holder">
							<input id="' . $id . '-date" name="' . $name . '_date" type="text"' . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' ' . $this->parseAttributes($attr, array()) . ' value="" data-default="' . $date . '" data-format="' . $format . '">
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker" id="' . $id . '-time-holder">
							<input id="' . $id . '-time" name="' . $name . '_time" type="text"' . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' ' . $this->parseAttributes($attr, array()) . ' value="' . $time . '">
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
						' . ($help ? '<span class="help-block">' . $help . '</span>' : '') . '
					</div>
				</div>
				<script type="text/javascript">
					jQuery(\'#' . $id . '-date\').datepicker(' . $config_object . ');
					jQuery(\'#' . $id . '-time\').timepicker({
						minuteStep: 1,
						template: \'dropdown\',
						defaultTime: \'00:00\',
						showMeridian: false,
						showInputs: false,
						disableFocus: true
					});
				</script>';
			} elseif ($type == 'date') {
				if (preg_match('~^(\d{4})\-(\d{2})\-(\d{2})$~', $value)) {
					$date = JvitalsTime::getUser($value, 'utc')->format('d/m/Y');
				}
				// Stuped fix because we return - instead of empty string
				if ($value == '-') $value = '';
				
				$html = '<div class="control-group">
					<label class="control-label" for="' . $id . '">' . $label . ((int)$required ? '<span class="star">&nbsp;*</span>' : '') . '</label>
					<div class="controls">
						<div class="input-append bootstrap-datepicker" id="' . $id . '-holder">
							<input id="' . $id . '" name="' . $name . '" type="text"' . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' ' . $this->parseAttributes($attr, array()) . ' value="" data-default="' . $value . '" data-format="' . $format . '">
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						' . ($help ? '<span class="help-block">' . $help . '</span>' : '') . '
					</div>
				</div>
				<script type="text/javascript">
					jQuery(\'#' . $id . '\').datepicker(' . $config_object . ');
				</script>';
			} elseif ($type == 'time') {
				$html = '<div class="control-group">
					<label class="control-label" for="' . $id . '">' . $label . ((int)$required ? '<span class="star">&nbsp;*</span>' : '') . '</label>
					<div class="controls">
						<div class="input-append bootstrap-timepicker" id="' . $id . '-holder">
							<input id="' . $id . '" name="' . $name . '" type="text"' . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' ' . $this->parseAttributes($attr, array()) . ' value="' . $value . '">
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
						' . ($help ? '<span class="help-block">' . $help . '</span>' : '') . '
					</div>
				</div>
				<script type="text/javascript">
					jQuery(\'#' . $id . '\').timepicker({
						minuteStep: 1,
						template: \'dropdown\',
						defaultTime: \'00:00\',
						showMeridian: false,
						showInputs: false,
						disableFocus: true
					});
				</script>';
			} else {
				// Wrapper Opening Start
				$html = '<div class="control-group">
					<label class="control-label" for="' . $id . '">' . $label . ((int)$required ? '<span class="star">&nbsp;*</span>' : '') . '</label>
					<div class="controls">';
				// Wrapper Opening End
				
				// Addons Opening Start
				if ($prepend || $append) {
					$html .= '<div class="' . trim(($prepend ? 'input-prepend ' : '') . ($append ? 'input-append ' : '')) . '">';
				}
				if ($prepend) {
					$html .= '<span class="add-on">' . $prepend . '</span>';
				}
				// Addons Opening End
				
				// Input Start
				if ($type == 'text' || $type == 'password') {
					$value = (($type == 'password') ? '' : $value);
					$html .= '<input type="' . $type . '" '. (trim($placeholder) ? ' placeholder="' . trim($placeholder) . '"' : '') . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' name="' . $name . '" id="' . $id . '" value="' . $value . '" ' . $this->parseAttributes($attr, array()) . '> ';
				} elseif ($type == 'upload') {
					$html .= '<input type="file" name="' . $name . '" id="' . $id . '"' . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' ' . $this->parseAttributes($attr, array()) . '>';
				} elseif ($type == 'textarea') {
					$html .= '<textarea '. (trim($placeholder) ? ' placeholder="' . trim($placeholder) . '"' : '') . (trim($class) ? ' class="' . trim($class) . '"' : '') . ' name="' . $name . '" id="' . $id . '" ' . $this->parseAttributes($attr, array()) . '>' . $value . '</textarea> ';
				} elseif ($type == 'checkbox') {
					if (count($options)) {
						if (!is_array($value) && (trim($value) || $value === 0 || $value === '0')) {
							$value = array($value);
						}
						$mopt = '';
						if (count($options) > 1) {
							$mopt = '[]';
						}
						$optCount = 0;
						foreach ($options as $box_value => $box_label) {
							$checked = (is_array($value) && in_array($box_value, $value) ? true : false);
							$html .= '<label class="checkbox ' . $class . '">
								<input type="checkbox" id="' . $id . '-' . $optCount . '" name="' . $name . $mopt . '" value="' . $box_value . '" ' . ($checked ? ' checked="checked"' : '') . ' ' . $this->parseAttributes($attr, array()) . '>' . $box_label . '
							</label> ';
							
							$optCount ++;
						}
					}
				} elseif ($type == 'select') {
					if (count($options)) {
						if ($multiple && !is_array($value) && (trim($value) || $value === 0 || $value === '0')) {
							$value = array($value);
						}
						
						$mopt = '';
						if ($multiple) {
							$mopt = '[]';
						}
						
						$html .= '<select name="' . $name . $mopt . '" id="' . $id . '"'. (trim($class) ? ' class="' . trim($class) . '"' : '') . ($multiple ? ' multiple="multiple"' : '') . ' ' . $this->parseAttributes($attr, array()) . '>';
						$html .= ($includeEmpty ? '<option value="">-</option>' : '');
						foreach ($options as $opt_value => $opt_label) {
							if ($multiple) {
								$selected = (is_array($value) && in_array($opt_value, $value) ? true : false);
							} else {
								$selected = ((trim($value) || $value === 0 || $value === '0') && $opt_value == $value ? true : false);
							}
							$html .= '<option value="' . $opt_value . '" ' . ($selected ? ' selected="selected"' : '') . '>' . $opt_label . '</option>';
						}
						$html .= '</select> ';
					}
				} elseif ($type == 'radio') {
					if (count($options)) {
						if ((trim($value) || $value === 0 || $value === '0') && !is_array($value)) {
							$value = array($value);
						}
						
						$optCount = 0;
						foreach ($options as $radio_value => $radio_label) {
							$checked = (is_array($value) && in_array($radio_value, $value) ? true : false);
							$html .= '<label class="radio ' . $class . '">
								<input type="radio" id="' . $id . '-' . $optCount . '" name="' . $name . '" value="' . $radio_value . '" ' . ($checked ? ' checked="checked"' : '') . ' ' . $this->parseAttributes($attr, array()) . '>' . $radio_label . '
							</label> ';
							
							$optCount ++;
						}
					}
				} elseif ($type == 'editor') {
					$html .= $this->editor->display($name, $value, '95%', '250', '70', '15', false, array());
				}
				// Input End
				
				// Addons Closing Start
				if ($append) {
					$html .= '<span class="add-on">' . $append . '</span>';
				}
				if ($prepend || $append) {
					$html .= '</div>';
				}
				// Addons Closing End
				
				// Field Help
				if ($help) {
					$html .= '<span class="help-block">' . $help . '</span>';
				}
				
				// Wrapper Closing Start
				$html .= '</div>
				</div>';
				// Wrapper Closing End
			}
			
			if ($html) {
				$output .= $html . "\n";
			}
			
			$counter ++;
		}
		
		return $output;
	}
	
	public function buildPagination($pagination, $link = '') {
		if ((int)$pagination->get('pages.total') <= 1) {
			return '';
		}
		
		if (!trim($link)) {
			// Default
			$href = '';
			$onclick = 'document.' . $this->formName . '.limitstart.value={limitstart}; Joomla.submitform(\'\', document.' . $this->formName . ');return false;';
		} else {
			// Custom
			$href = $link;
			$onclick = '';
		}
		
		ob_start();
		?>
		<div class="pagination">
			<ul>
				<?php if ((int)$pagination->get('pages.current') == 1) : ?>
				<li class="disabled"><span>&laquo;</span></li>
				<li class="disabled"><span>&lsaquo;</span></li>
				<?php else: ?>
				<li><a href="<?php echo ($href ? $this->parsePaginationLink($href, 0, $pagination->get('limit')) : '#'); ?>"<?php echo ($onclick ? ' onclick="' . $this->parsePaginationLink($onclick, 0, $pagination->get('limit')) . '"' : ''); ?> title="<?php echo JText::_($this->languageKey . '_PAGINATION_BUTTON_START'); ?>">&laquo;</a></li>
				<li><a href="<?php echo ($href ? $this->parsePaginationLink($href, (((int)$pagination->get('pages.current') - 2) * $pagination->get('limit')), $pagination->get('limit')) : '#'); ?>"<?php echo ($onclick ? ' onclick="' . $this->parsePaginationLink($onclick, (((int)$pagination->get('pages.current') - 2) * $pagination->get('limit')), $pagination->get('limit')) . '"' : ''); ?> title="<?php echo JText::_($this->languageKey . '_PAGINATION_BUTTON_PREV'); ?>">&lsaquo;</a></li>
				<?php endif; ?>
				
				<?php for ($page = (int)$pagination->get('pages.start'); $page <= (int)$pagination->get('pages.stop'); $page ++) : ?>
					<?php if ((int)$pagination->get('pages.current') == (int)$page) : ?>
					<li class="active"><span><?php echo $page; ?></span></li>
					<?php else: ?>
					<li><a href="<?php echo ($href ? $this->parsePaginationLink($href, (((int)$page - 1) * $pagination->get('limit')), $pagination->get('limit')) : '#'); ?>"<?php echo ($onclick ? ' onclick="' . $this->parsePaginationLink($onclick, (((int)$page - 1) * $pagination->get('limit')), $pagination->get('limit')) . '"' : ''); ?> title="<?php echo $page; ?>"><?php echo $page; ?></a></li>
					<?php endif; ?>
				<?php endfor; ?>
				
				<?php if ((int)$pagination->get('pages.current') == (int)$pagination->get('pages.total')) : ?>
				<li class="disabled"><span>&rsaquo;</span></li>
				<li class="disabled"><span>&raquo;</span></li>
				<?php else: ?>
				<li><a href="<?php echo ($href ? $this->parsePaginationLink($href, ((int)$pagination->get('pages.current') * $pagination->get('limit')), $pagination->get('limit')) : '#'); ?>"<?php echo ($onclick ? ' onclick="' . $this->parsePaginationLink($onclick, ((int)$pagination->get('pages.current') * $pagination->get('limit')), $pagination->get('limit')) . '"' : ''); ?> title="<?php echo JText::_($this->languageKey . '_PAGINATION_BUTTON_NEXT'); ?>">&rsaquo;</a></li>
				<li><a href="<?php echo ($href ? $this->parsePaginationLink($href, (((int)$pagination->get('pages.total') - 1) * $pagination->get('limit')), $pagination->get('limit')) : '#'); ?>"<?php echo ($onclick ? ' onclick="' . $this->parsePaginationLink($onclick, (((int)$pagination->get('pages.total') - 1) * $pagination->get('limit')), $pagination->get('limit')) . '"' : ''); ?> title="<?php echo JText::_($this->languageKey . '_PAGINATION_BUTTON_END'); ?>">&raquo;</a></li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
		$html = ob_get_clean();
		
		return $html;
	}
	
}