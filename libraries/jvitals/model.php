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

class JvitalsModel extends JModelLegacy {
	public $dbo;
	protected $_pagination = null;
	protected $_error = null;
	
	function __construct() {
		parent::__construct();
		$this->dbo = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = $app->input->getCmd('option', '');
		$task = $app->input->getCmd('task', 'default');
		$controller = $app->input->getCmd('controller', 'default');
		$identifier = $option . (($controller ? '.' . $controller : '')) . ($task ? '.' . $task : '');
		
		// Get pagination request variables
		$limit = $app->getUserStateFromRequest($identifier . '.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($identifier . '.limitstart', 'limitstart', 0, 'int');
 
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	public function getPagination($total, $limitstart = false, $limit = false) {
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			if ($limitstart === false) $limitstart = $this->getState('limitstart');
			if ($limit === false) $limit = $this->getState('limit');
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}
		return $this->_pagination;
	}
	
	public function setError($error) {
		$this->_error = $error;
	}
	
	public function getError($i = NULL, $toString = true) {
		return $this->_error;
	}
	
}
