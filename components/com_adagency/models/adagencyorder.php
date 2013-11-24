<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
defined ('_JEXEC') or die ("Go away.");
jimport ("joomla.aplication.component.model");

class adagencyModeladagencyOrder extends JModel {
	var $_orders;
	var $_order;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');

		$this->setId((int)$cids[0]);

		global $mainframe, $option;

		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getListOrders();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;

		$this->_order = null;
	}

	function getlistOrders () {
		if (empty ($this->_orders)) {
			$db = JFactory::getDBO();
			$my =& JFactory::getUser();
			$sql = "SELECT o.*, a.company, ot.description FROM #__ad_agency_order AS o LEFT JOIN #__ad_agency_advertis AS a ON a.aid=o.aid LEFT JOIN #__ad_agency_order_type AS ot ON ot.tid=o.tid where a.user_id=".intval($my->id)." ORDER BY o.oid DESC";
			$this->_total = $this->_getListCount($sql);
			$this->_orders = $this->_getList($sql);
		}
		
		return $this->_orders;
	}	

	function getOrder() {
		if (empty ($this->_order)) {
			$this->_order =& $this->getTable("adagencyOrder");
			$this->_order->load($this->_id);
		}
		return $this->_order;
	}
	
	function getPackage($tid) {
		$db =& JFactory::getDBO();
		$sql="SELECT * FROM #__ad_agency_order_type WHERE `tid`=".intval($tid);
		$db->setQuery($sql);
		$this->_order = $db->loadObjectList();
		return $this->_order[0];
	}

	function store () {
		$item =& $this->getTable('adagencyOrder');
		$data = JRequest::get('post');
		if (!$item->bind($data)){
			return false;
		}

		if (!$item->check()) {
			return false;
		}

		if (!$item->store()) {
			return false;
		}

		return true;
	}	

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item =& $this->getTable('adagencyOrder');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	
	function remove () {
		$cids = JRequest::getVar('cid', array(0), 'get', 'array');
		$item =& $this->getTable('adagencyOrder');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	
	function confirm () {
		$db =& JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'get', 'array');
		$item =& $this->getTable('adagencyOrder');
		foreach ($cids as $cid) {
			$sql = "update #__ad_agency_order set status='paid' where oid in ('".implode("','", $cids)."')";
			$db->setQuery($sql);
			if (!$db->query() ){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
};
?>