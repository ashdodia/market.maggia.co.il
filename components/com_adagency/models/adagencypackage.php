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


class adagencyModeladagencyPackage extends JModel {
	var $_packages;
	var $_package;
	var $_tid = null;
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

	function getConf(){
		$db = &JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadObject();
		return $res;
	}

	function getAid(){
		$database = &JFactory::getDBO();
		$my =& JFactory::getUser();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."'";
		$database->setQuery($sql);
	    $advertiserid = $database->loadResult();
		return $advertiserid;
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getListPackages();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_tid = $id;
		$this->_package = null;
	}

	function getShowZInfo(){
		$db = &JFactory::getDBO();
		$sql = "SELECT `show` FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($sql);
		$shown = $db->loadResult();
		if(strpos(" ".$shown,"zinfo") > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getlistPackages ($visibility=0) {
		if (empty ($this->_packages)) {
			$db = JFactory::getDBO();
			if($visibility==0)
			{
				//$sql = "SELECT * FROM #__ad_agency_order_type WHERE `published`=1 AND `visibility`<>0 ORDER BY ordering";
				$sql = "SELECT o.*,z.zoneid,z.banners,z.banners_cols,z.z_title,z.rotatebanners,z.adparams
						FROM #__ad_agency_order_type AS o
						LEFT JOIN #__ad_agency_zone AS z
						ON o.location = z.zoneid
						WHERE o.`published`=1 AND o.`visibility`<>0
						ORDER BY ordering";
			} else {
				$sql = "SELECT * FROM #__ad_agency_order_type WHERE `published`=1 ORDER BY ordering";
			}
			$db->setQuery($sql);
			$this->_total = $this->_getListCount($sql);
			$this->_packages = $this->_getList($sql);
		}

		return $this->_packages;
	}

	function getZonesForPacks($packs){
		$db = &JFactory::getDBO();
		if(isset($packs)&&(is_array($packs))){
			foreach($packs as $pack){
				$sql = "SELECT m.title, m.id, z.banners as rows, z.banners_cols as cols, z.adparams, z.rotatebanners
						FROM #__ad_agency_package_zone AS pz
						LEFT JOIN #__modules AS m ON pz.zone_id = m.id
						LEFT JOIN #__ad_agency_zone AS z ON z.zoneid = m.id
						WHERE pz.package_id = ".intval($pack->tid);
				$db->setQuery($sql);
				$pack->location = $db->loadObjectList();
			}
		}
		return $packs;
	}

	function getPackage() {
		if (empty ($this->_package)) {
			$this->_package =& $this->getTable("adagencyPackage");
			$this->_package->load($this->_tid);
			$data = JRequest::get('post');

			if (!$this->_package->bind($data)){
				$this->setError($item->getErrorMsg());
				return false;

			}

			if (!$this->_package->check()) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}
		return $this->_package;

	}

	function store () {
		$item =& $this->getTable('adagencyPackage');
		$data = JRequest::get('post');
		$data['validity'] = ($data['amount']>0 && $data['duration']!="") ? $data['amount'] . "|" . $data['duration'] : "";
		if ($data['type']=='fr') $data['quantity']=0;
		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		return true;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item =& $this->getTable('adagencyPackage');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}

		return true;
	}


	function publish () {
		$db =& JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item =& $this->getTable('adagencyPackage');
		if ($task == 'publish')
			$sql = "update #__ad_agency_order_type set published='1' where tid in ('".implode("','", $cids)."')";

		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	function unpublish () {
		$db =& JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item =& $this->getTable('adagencyPackage');
		if ($task == 'unpublish')
			$sql = "update #__ad_agency_order_type set published='0' where tid in ('".implode("','", $cids)."')";

		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return -1;
	}

	function getTemplate(){
		$db =& JFactory::getDBO();
		$sql="SELECT template FROM #__template_styles WHERE client_id = '0' AND home = '1' ";
		$db->setQuery($sql);
		$default_template=$db->loadResult();
		return $default_template;
	}

	function getItemidLink(){
		$db =& JFactory::getDBO();
		$get = JRequest::get('get');
		$sql = "SELECT m. * , n.link
			FROM #__modules_menu AS m
			LEFT JOIN #__menu AS n ON m.menuid = n.id
			WHERE n.access = 0
			AND n.published =1
			AND moduleid = ".intval($get['cid'])."
			ORDER BY menuid ASC
			LIMIT 1 ";
		$db->setQuery($sql);
		$res = $db->loadObject();
		if($res != NULL){
			return $res->link.'&Itemid='.$res->menuid;
		} else { return NULL; }
	}

	function getFreePermission($advertiserid,$packageid){
		if(!isset($advertiserid)||($advertiserid=='')||($advertiserid==0)) { return true; }

		$db =& JFactory::getDBO();
		$sql="SELECT oid FROM #__ad_agency_order WHERE aid='".intval($advertiserid)."' AND payment_type='Free' AND tid='".intval($packageid)."' LIMIT 1";
		$db->setQuery($sql);
		$free_permission=$db->loadResult();
		$sql="SELECT hide_after FROM #__ad_agency_order_type WHERE tid='".intval($packageid)."'";
		$db->setQuery($sql);
		$hide_after = $db->loadResult();
		if(isset($free_permission)&&($hide_after==1)){ return false;}

		return true;
	}
};
?>
