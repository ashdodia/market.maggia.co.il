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

class adagencyModeladagencyPopup extends JModel {
	var $_licenses;
	var $_license;
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
			if (!$this->_total) $this->getListPackages();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_tid = $id;
		$this->_package = null;
	}

	function getlistPackages () {
		if (empty ($this->_packages)) {
			$db = JFactory::getDBO();
			$sql = "select * from #__ad_agency_order_type";
			$this->_total = $this->_getListCount($sql);
			$this->_packages = $this->_getList($sql);

		}

		return $this->_packages;
	}

	function getad() {
		if (empty ($this->_package)) {
			$this->_package =& $this->getTable("adagencyAds");
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

	function getad2($id) {
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__ad_agency_banners WHERE id=".intval($id);
		$db->setQuery($query);
		$result = $db->loadObject();
		if(isset($result->parameters)) { $result->parameters = @unserialize($result->parameters);}
		return $result;
	}

	function getAdvInfo($id){
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__ad_agency_advertis WHERE aid=".intval($id);
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}

	function getCurrentAdvertiser(){
		$db = & JFactory::getDBO();
		$my = & JFactory::getUser();
		$sql = "SELECT * FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	function getCampsByAid($adv_id, $distinct = 0) {
		$db = JFactory::getDBO();
		$sqls = "
			SELECT c.id, c.name, c.params, z.*
			FROM #__ad_agency_campaign AS c
			JOIN #__ad_agency_order_type AS o
			ON c.otid = o.tid
			JOIN #__ad_agency_package_zone AS pz
			ON pz.package_id = o.tid
			JOIN #__ad_agency_zone AS z
			ON z.zoneid = pz.zone_id
			WHERE c.aid = ".intval($adv_id)." AND z.adparams LIKE '%popup%'
		";
        if ($distinct == 1) { $sqls .= "GROUP BY c.id"; }
        //echo $sqls;die();
		$db->setQuery($sqls);
		$camps = $db->loadObjectList();
		foreach($camps as &$element) {
			$element->adparams = @unserialize($element->adparams);
            $element->params = @unserialize($element->params);

            // Select total banners for each campaign
            $sql = "SELECT COUNT( campaign_id )
                       FROM `#__ad_agency_campaign_banner`
                       WHERE `campaign_id` = " .intval($element->id);
            $db->setQuery($sql);
            $element->totalbanners = $db->loadResult();
		}
		//echo "<pre>";var_dump($camps);die();
		return $camps;
	}

	function processCampZones($camps){
		$zones = array();
		if(isset($camps)&&(is_array($camps))){
			foreach($camps as $camp){
				if(!isset($zones[$camp->id])) { $zones[$camp->id] = $this->getAllZonesForCampByCampId($camps,$camp->id); }
			}
		}
		return $zones;
	}

	function getAllZonesForCampByCampId($camps, $camp_id){
		$resp = array();
		if(isset($camps)&&(is_array($camps))){
			$i = 1;
			foreach($camps as $camp){
				if($camp->id == $camp_id) {
					$resp[$i]['zoneid'] = $camp->zoneid;
					$resp[$i]['z_title'] = $camp->z_title;
					$i++;
				}
			}
		}
		return $resp;
	}

	function createSelectBox($czones,$ad_id){
		$db = &JFactory::getDBO();
		$select = array();
		if(isset($czones)&&(is_array($czones))){
			foreach($czones as $key=>$value){
				$sql = "SELECT zone FROM #__ad_agency_campaign_banner WHERE banner_id = ".intval($ad_id)." AND campaign_id = ".intval($key);
				$db->setQuery($sql);
				$sel_zone = $db->loadResult();
				$select[$key] = "<select name='czones[".$key."]' id='czones_".$key."' class='w145'>";
				$select[$key] .= "<option value='0'>".JText::_('ADAG_ZONE_FOR_AD')."</option>";
				if(is_array($value)){
					foreach($value as $val){
						if($sel_zone == $val['zoneid']) { $this_selected = " selected='selected' "; } else { $this_selected = NULL; }
						$select[$key] .= "<option value='".$val['zoneid']."' ".$this_selected.">".$val['z_title']."</option>";
					}
				}
				$select[$key] .= "</select>";
			}
		}
		return $select;
	}

	function getSelectedCamps($advertiser_id,$adid){
		$db = JFactory::getDBO();
        $advertiser_id = (int) $advertiser_id;
        $adid = (int) $adid;
		$sql="SELECT DISTINCT cb.campaign_id FROM #__ad_agency_banners AS b LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id=b.id WHERE b.advertiser_id=".intval($advertiser_id)." AND b.id=".intval($adid);
		$db->setQuery($sql);
		$these_campaigns=$db->loadResultArray();
		return $these_campaigns;
	}

	function getAdById($id){
		$db = & JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_banners WHERE id = ".intval($id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}


	function getChannel($bid) {
		$db = &JFactory::getDBO();
		if(isset($bid)) {
			$sql = "SELECT * FROM `#__ad_agency_channels` WHERE banner_id =".intval($bid)." LIMIT 1";
			$db->setQuery($sql);
			$result = $db->loadObject();
			if(!isset($result->id)) { return NULL; }
			$sql = "SELECT s.type, s.option, s.logical, s.data FROM `#__ad_agency_channels` AS c, `#__ad_agency_channel_set` AS s WHERE c.id = ".intval($result->id)." AND c.id = s.channel_id ORDER BY s.id ASC";
			$db->setQuery($sql);
			$result->sets = $db->loadObjectList();

			//echo "<pre>";var_dump($result);die();
		} else {
			$result = NULL;
		}
		return $result;
	}

	function delete_geo($bid, $aid) {
		$db = &JFactory::getDBO();
		$temp = NULL;
		if(isset($bid)&&isset($aid)) {
			$sql0 = 'SELECT id FROM #__ad_agency_channels WHERE banner_id = "'.intval($bid).'" AND advertiser_id = "'.intval($aid).'" LIMIT 1';
			$db->setQuery($sql0);
			$id_to_del = $db->loadResult();

			if(isset($id_to_del)&&($id_to_del != NULL)) {
				/*$sql1 = 'DELETE FROM #__ad_agency_channels WHERE id = "'.$id_to_del.'"';
				$db->setQuery($sql1);
				if(!$db->query()) { return false; }*/

				$sql2 = 'DELETE FROM #__ad_agency_channel_set WHERE channel_id = "'.intval($id_to_del).'"';
				$db->setQuery($sql2);
				if(!$db->query()) { return false; }
			}
		}

		if(isset($id_to_del)) { return $id_to_del; } else { return -1; }
	}


	function store_geo($bid = NULL){
		require_once('components/com_adagency/helpers/channel_fcs.php');
	}

	function store () {
		$database = & JFactory::getDBO();
		$db = & JFactory::getDBO();
		$link_label = 'ad_url';
		$item =& $this->getTable('adagencyAds');
        $adsModel = $this->getInstance("adagencyAds", "adagencyModel");
		$configs = $this->getInstance("adagencyConfig", "adagencyModel");
		$configs = $configs->getConfigs();
		$notify = JRequest::getInt('id');
		$changed = "new";
		$advid = $this->getCurrentAdvertiser();
		if(isset($advid->aid)) { $advid = $advid->aid; } else { $advid = 0;}
		$data = JRequest::get('post');
		if($data['id']==0){
			$data['created'] = date("Y-m-d");
			$data['key'] = md5(rand(1000,9999));
		} else {
			$data['key'] = NULL;
		}
		$adv_cmp = $data['adv_cmp'];
		//echo "<pre>";var_dump($adv_cmp);echo "</pre><hr />";//die();
		if ($notify!='0') {
			$data['width'] = $data['parameters']['window_width'];
			$data['height'] = $data['parameters']['window_height'];
		} else {
			$data['parameters']['window_width'] = '';
			$data['parameters']['window_height'] = '';
			$data['parameters']['toolbar'] = '';
			$data['parameters']['scrollbars'] = '';
			$data['parameters']['status'] = '';
			$data['parameters']['menubar'] = '';
			$data['parameters']['resizable'] = '';
			$data['parameters']['window_type'] = '';
		}

		$data['parameters']['show_on'] = $data['show_on'];
		$data['parameters']['show_ad'] = $data['show_ad'];
		$showon = "window.onload = checkCount;";/// : "window.onunload = checkCount;";
		$cookie_name = md5(uniqid(rand(), 1));
		$script = '';

		//echo "<pre>";var_dump($data['parameters']);die('123');

		$expday = "9999";
		$data['parameters']["window_type"] = 'popup';
		$data['parameters']["window_width"] = 600;
		$data['parameters']["window_height"] = 600;
		$data['parameters']["status"] = 'no';
		$data['parameters']["toolbar"] = 'no';
		$data['parameters']["resizable"] = 'yes';
		$data['parameters']["scrollbars"] = 'yes';

		switch($data['parameters']["popup_type"]) {
				case 'webpage':

				$data['parameters']["preview_script"] = "window.open('".$data['parameters']["page_url"]."', 'popupad', 'width=".$data['parameters']["window_width"].",height=".$data['parameters']["window_height"].",toolbar=".$data['parameters']["toolbar"].",status=".$data['parameters']["status"].",menubar=".$data['parameters']["menubar"].",scrollbars=".$data['parameters']["scrollbars"].",location=no,resizable=".$data['parameters']["resizable"]."');";

					if ($data['parameters']["window_type"] == 'popup') {
$script = <<<EOD
<script language="JavaScript">
var expDays = {$expday};
var page = "{$data['parameters']["page_url"]}";
var windowprops = "width={$data['parameters']["window_width"]},height={$data['parameters']["window_height"]},toolbar={$data['parameters']["toolbar"]},status={$data['parameters']["status"]},menubar={$data['parameters']["menubar"]},scrollbars={$data['parameters']["scrollbars"]},location=no,resizable={$data['parameters']["resizable"]}";
function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	}
	return null;
}
function SetCookie (name, value) {
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}
function DeleteCookie (name) {
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie (name);
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}
var exp = new Date();
exp.setTime(exp.getTime() + (expDays));
function amt(){
	var count = GetCookie("$cookie_name");
	if(count == null) {
		SetCookie("$cookie_name","1");
		return 1;
	} else {
		var newcount = parseInt(count) + 1;
		DeleteCookie("$cookie_name");
		SetCookie("$cookie_name",newcount,exp);
		return count;
	}
}
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}
function checkCount() {
	var count = GetCookie("$cookie_name");
	if (count == null) {
		count=1;
		SetCookie("$cookie_name", count, exp);
		window.open(page, "popupad", windowprops);
	} else {
		count++;
		SetCookie("$cookie_name", count, exp);
	}
}
{$showon}
</script>
EOD;
					}
					if ($data['parameters']["window_type"] == 'popunder') {
$script = <<<EOD
<script language="JavaScript">
var expDays = {$expday};
var page = "{$data['parameters']["page_url"]}";
var windowprops = "width={$data['parameters']["window_width"]},height={$data['parameters']["window_height"]},toolbar={$data['parameters']["toolbar"]},status={$data['parameters']["status"]},menubar={$data['parameters']["menubar"]},scrollbars={$data['parameters']["scrollbars"]},location=no,resizable={$data['parameters']["resizable"]}";
function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	}
	return null;
}
function SetCookie (name, value) {
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}
function DeleteCookie (name) {
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie (name);
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}
var exp = new Date();
exp.setTime(exp.getTime() + (expDays));
function amt(){
	var count = GetCookie("$cookie_name");
	if(count == null) {
		SetCookie("$cookie_name","1");
		return 1;
	} else {
		var newcount = parseInt(count) + 1;
		DeleteCookie("$cookie_name");
		SetCookie("$cookie_name",newcount,exp);
		return count;
	}
}
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}
function checkCount() {
	var count = GetCookie("$cookie_name");
	if (count == null) {
		count=1;
		SetCookie("$cookie_name", count, exp);
		window.open(page, "popupad", windowprops).blur();
		window.focus();
	} else {
		count++;
		SetCookie("$cookie_name", count, exp);
	}
}
{$showon}
</script>
EOD;
					}
					break;
				case 'image':
				$mosConfig_live_site = JURI::base();
				$data['parameters']['preview_script'] = "var newPopup=window.open('','PopupWindow','width=".$data['parameters']['window_width'].",height=".$data['parameters']['window_height'].",toolbar=".$data['parameters']['toolbar'].",status=".$data['parameters']['status'].",menubar=".$data['parameters']['menubar'].",scrollbars=".$data['parameters']['scrollbars'].",location=no,resizable=".$data['parameters']['resizable']."'); var popContent='<HTML><HEAD><TITLE>Advertisement</TITLE></HEAD><BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0>';
				popContent+= '<a target=\'_blank\' href=\'{$data["target_url"]}\'><img src=\'{$mosConfig_live_site}/images/stories/ad_agency/{$data["advertiser_id"]}/{$data["image_url"]}\' height=\'{$data["height"]}\' width=\'{$data["width"]}\' border=0></a>';
				popContent+='</BODY></HTML>';
				newPopup.document.write(popContent); newPopup.document.close(); newPopup.focus();";

					if ($data['parameters']['window_type'] == 'popup') {
$script = <<<EOD
<script language="javascript">

var expDays = {$expday};
var windowprops = "width={$data['parameters']['window_width']},height={$data['parameters']['window_height']},toolbar={$data['parameters']['toolbar']},status={$data['parameters']['status']},menubar={$data['parameters']['menubar']},scrollbars={$data['parameters']['scrollbars']},location=no,resizable={$data['parameters']['resizable']}";
function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	}
	return null;
}
function SetCookie (name, value) {
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}
function DeleteCookie (name) {
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie (name);
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}
var exp = new Date();
exp.setTime(exp.getTime() + (expDays));
function amt(){
	var count = GetCookie("$cookie_name");
	if(count == null) {
		SetCookie("$cookie_name","1");
		return 1;
	} else {
		var newcount = parseInt(count) + 1;
		DeleteCookie("$cookie_name");
		SetCookie("$cookie_name",newcount,exp);
		return count;
	}
}
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}
function checkCount() {
	var count = GetCookie("$cookie_name");
	if (count == null) {
		count=1;
		SetCookie("$cookie_name", count, exp);
		var newPopup=window.open("","PopupWindow",windowprops);
		var popContent='<HTML><HEAD><TITLE>Advertisement</TITLE></HEAD><BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0>';
		popContent+= '<a target="_blank" href="{$link_label}"><img src="{$mosConfig_live_site}/images/stories/ad_agency/{$data["advertiser_id"]}/{$data["image_url"]}" height="{$data["height"]}" width="{$data["width"]}" border=0></a>';
		popContent+='</BODY></HTML>';
		newPopup.document.write(popContent);
		newPopup.document.close();
		newPopup.focus();
	} else {
		count++;
		SetCookie("$cookie_name", count, exp);
	}
}
{$showon}
</script>
EOD;
					}
					if ($data['parameters']['window_type'] == 'popunder') {
$script = <<<EOD
<script language="javascript">

var expDays = {$expday};
var windowprops = "width={$data['parameters']['window_width']},height={$data['parameters']['window_height']},toolbar={$data['parameters']['toolbar']},status={$data['parameters']['status']},menubar={$data['parameters']['menubar']},scrollbars={$data['parameters']['scrollbars']},location=no,resizable={$data['parameters']['resizable']}";
function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	}
	return null;
}
function SetCookie (name, value) {
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}
function DeleteCookie (name) {
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie (name);
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}
var exp = new Date();
exp.setTime(exp.getTime() + (expDays));
function amt(){
	var count = GetCookie("$cookie_name");
	if(count == null) {
		SetCookie("$cookie_name","1");
		return 1;
	} else {
		var newcount = parseInt(count) + 1;
		DeleteCookie("$cookie_name");
		SetCookie("$cookie_name",newcount,exp);
		return count;
	}
}
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}
function checkCount() {
	var count = GetCookie("$cookie_name");
	if (count == null) {
		count=1;
		SetCookie("$cookie_name", count, exp);
		var newPopup=window.open("","PopupWindow",windowprops);
		var popContent='<HTML><HEAD><TITLE>Advertisement</TITLE></HEAD><BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0>';
		popContent+= '<a target="_blank" href="{$link_label}"><img src="{$mosConfig_live_site}/images/stories/ad_agency/{$data["advertiser_id"]}/{$data["image_url"]}" height="{$data["height"]}" width="{$data["width"]}" border=0></a>';
		popContent+='</BODY></HTML>';
		newPopup.document.write(popContent);
		newPopup.document.close();
		newPopup.blur();
	} else {
		count++;
		SetCookie("$cookie_name", count, exp);
	}
}
{$showon}
</script>
EOD;
					}

					break;
				case 'html':
						$crt=1;
						$string='href="';
						$fisier=$data['parameters']["html"];
						$pos1=strpos($fisier,$string);
						while ($pos1) {
							$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
							$pos2=strpos($fisier,'"');
							$url=substr($fisier,0,$pos2);
							$data['parameters']['linktrack'][$crt] = $url;
							$crt++;
							$pos1=strpos($fisier,$string);
						}
					$html_content = "";
					$data['parameters']["html"] = preg_replace("/href((\s+)?)=((\s+)?)\"\b((http(s?):\/\/)|(www\.)|(ftp:\/\/))([\w\.]+)([-~\/\w+\.-?]+)\b/i","href=\"ad_url",$data['parameters']['html']);
					$LINES = explode("\n", $data['parameters']["html"]);
					foreach ($LINES as $i=>$v) {
						$v = trim($v);
						$html_content .= "popContent+='".$v."\\r\\n';\r\n";
					}

					$preview_content = "";
					$LINES = explode("\n", addslashes($data['parameters']["html"]));
					foreach ($LINES as $i=>$v) {
						$v = trim($v);
						$preview_content .= "popContent+='".$v."\\r\\n';\r\n";
					}

					$data['parameters']['preview_script'] = "var newPopup=window.open('','PopupWindow','width=".$data['parameters']['window_width'].",height=".$data['parameters']['window_height'].",toolbar=".$data['parameters']['toolbar'].",status=".$data['parameters']['status'].",menubar=".$data['parameters']['menubar'].",scrollbars=".$data['parameters']['scrollbars'].",location=no,resizable=".$data['parameters']['resizable']."');
					var popContent='<HTML><HEAD><TITLE>Advertisement</TITLE></HEAD><BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0>'; ".$preview_content." popContent+='</BODY></HTML>';
					newPopup.document.write(popContent); newPopup.document.close(); newPopup.focus(); ";

					if ($data['parameters']['window_type'] == 'popup') {
$script = <<<EOD
<script language="javascript">
var expDays = {$expday};
var windowprops = "width={$data['parameters']['window_width']},height={$data['parameters']['window_height']},toolbar={$data['parameters']['toolbar']},status={$data['parameters']['status']},menubar={$data['parameters']['menubar']},scrollbars={$data['parameters']['scrollbars']},location=no,resizable={$data['parameters']['resizable']}";
function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	}
	return null;
}
function SetCookie (name, value) {
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}
function DeleteCookie (name) {
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie (name);
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}
var exp = new Date();
exp.setTime(exp.getTime() + (expDays));
function amt(){
	var count = GetCookie("$cookie_name");
	if(count == null) {
		SetCookie("$cookie_name","1");
		return 1;
	} else {
		var newcount = parseInt(count) + 1;
		DeleteCookie("$cookie_name");
		SetCookie("$cookie_name",newcount,exp);
		return count;
	}
}
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}
function checkCount() {
	var count = GetCookie("$cookie_name");
	if (count == null) {
		count=1;
		SetCookie("$cookie_name", count, exp);
		var newPopup=window.open("","PopupWindow",windowprops);
		var popContent='<HTML><HEAD><TITLE>Advertisement</TITLE></HEAD><BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0>';

		{$html_content}

		popContent+='</BODY></HTML>';
		newPopup.document.write(popContent);
		newPopup.document.close();
		newPopup.focus();
	} else {
		count++;
		SetCookie("$cookie_name", count, exp);
	}
}
{$showon}
</script>
EOD;
					}
					if ($data['parameters']['window_type'] == 'popunder') {
$script = <<<EOD
<script language="javascript">
var expDays = {$expday};
var windowprops = "width={$data['parameters']['window_width']},height={$data['parameters']['window_height']},toolbar={$data['parameters']['toolbar']},status={$data['parameters']['status']},menubar={$data['parameters']['menubar']},scrollbars={$data['parameters']['scrollbars']},location=no,resizable={$data['parameters']['resizable']}";
function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	}
	return null;
}
function SetCookie (name, value) {
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
}
function DeleteCookie (name) {
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie (name);
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}
var exp = new Date();
exp.setTime(exp.getTime() + (expDays));
function amt(){
	var count = GetCookie("$cookie_name");
	if(count == null) {
		SetCookie("$cookie_name","1");
		return 1;
	} else {
		var newcount = parseInt(count) + 1;
		DeleteCookie("$cookie_name");
		SetCookie("$cookie_name",newcount,exp);
		return count;
	}
}
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}
function checkCount() {
	var count = GetCookie("$cookie_name");
	if (count == null) {
		count=1;
		SetCookie("$cookie_name", count, exp);
		var newPopup=window.open("","PopupWindow",windowprops);
		var popContent='<HTML><HEAD><TITLE>Advertisement</TITLE></HEAD><BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0>';

		{$html_content}

		popContent+='</BODY></HTML>';
		newPopup.document.write(popContent);
		newPopup.document.close();
		newPopup.blur();
	} else {
		count++;
		SetCookie("$cookie_name", count, exp);
	}
}
{$showon}
</script>
EOD;
					}
					break;
				default:
					break;
			}

	$content = $script;

	if ($data['parameters']['popup_type']!="image") {
		$data['width'] = $data['parameters']['window_width'];
		$data['height'] = $data['parameters']['window_height'];
	}



		// Check settings for auto-approve ads [begin]
		$advertiser = $this->getCurrentAdvertiser();
		if(isset($advertiser->apr_ads)&&($advertiser->apr_ads!='G')){
			$approved = $advertiser->apr_ads;
		} else {
			$sql = "SHOW COLUMNS FROM `#__ad_agency_banners` WHERE field = 'approved'";
			$db->setQuery($sql);
			$res = $db->loadObject();
			$approved = $res->Default;
		}
		if($approved == 'N') { $approved = 'P'; }
		$data['approved'] = $approved;

        // if the ad is not new then ->
		if(isset($data['id'])&&($data['id']>0)){
			$ex_ad = $this->getAdById($data['id']);
            $ex_ad->parameters = @unserialize($ex_ad->parameters);
			// if the ad was approved or declined and the new status would be 'pending', make sure changes occured to it!
			if(($ex_ad->approved != 'P')&&($data['approved'] == 'P')){
				// if no change has been made, let the status be as it was
				if(($ex_ad->title == $data['title'])&&($ex_ad->description == $data['description'])&&($ex_ad->parameters['popup_type'] == $data['parameters']['popup_type'])) {
                	if(($data['parameters']['popup_type'] == 'html')&&($ex_ad->parameters['html'] == $data['parameters']['html'])) {
                        $data['approved'] = $ex_ad->approved;
                    } elseif(($data['parameters']['popup_type'] == 'image')&&($data['target_url'] == $ex_ad->target_url)&&($data["image_url"] == $ex_ad->image_url)) {
                    	$data['approved'] = $ex_ad->approved;
                    } elseif(($data['parameters']['popup_type'] == 'webpage')&&($ex_ad->parameters['page_url'] == $data['parameters']['page_url'])) {
                    	$data['approved'] = $ex_ad->approved;
                    }
                    else{
                    	//it was changed, and sent email to administrator
                        $notify = "0";
                        $changed = "changed";
                    }
				}
                else{
					//it was changed, and sent email to administrator
					$notify = "0";
					$changed = "changed";
				}
			}
		}

		// Check settings for auto-approve ads [end]

    	//echo "<pre>";var_dump($data);die();

		$data['parameters'] = serialize($data['parameters']);

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

		if (isset($notify) && ($notify!=0)) $where = $notify;
			else $where = mysql_insert_id();
		if ($where==0) {
			$ask = "SELECT `id` FROM `#__ad_agency_banners` ORDER BY `id` DESC LIMIT 1 ";
			$db->setQuery( $ask );
			$where = $db->loadResult();
		}

		if (!isset($data['id']) || $data['id']==0) $data['id'] = mysql_insert_id();
		$idi = $data['id'];
		if ($idi==0) {
			$ask = "SELECT `id` FROM `#__ad_agency_banners` ORDER BY `id` DESC LIMIT 1 ";
			$db->setQuery( $ask );
			$idi = $db->loadResult();
		}
		if(isset($idi)) {
			$this->store_geo($idi);
		}
		require_once(JPATH_BASE . "/administrator/components/com_adagency/helpers/jomsocial.php");
		JomSocialTargeting::save($idi);		

        $campLimInfo = $adsModel->getCampLimInfo();

		if(isset($adv_cmp)){
			$query = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id=".intval($idi)." AND relative_weighting = 100";
            $sqlz[] = $query;
			$db->setQuery($query);
			$db->query();
			foreach ($adv_cmp as $val) {
				if ( ($val) && (
                    ( !isset($campLimInfo[$val])) || ( $campLimInfo[$val]['adslim'] > $campLimInfo[$val]['occurences'] )
                ) ) {
                	$query = "INSERT INTO `#__ad_agency_campaign_banner`
                        (`campaign_id` ,`banner_id` ,`relative_weighting` ,`thumb`, `zone`)
                        VALUES ('".intval($val)."', '".intval($idi)."', '100', NULL, ".intval($data['czones'][$val]).");";
					//$query = "INSERT INTO #__ad_agency_campaign_banner VALUES ({$val},{$idi},100)";
					$db->setQuery($query);
					$db->query();
                    $sqlz[] = $query;
				}
			}
		}

        //var_dump($content);die('123');

		$sql = "UPDATE #__ad_agency_banners SET `ad_code` = '".$db->getEscaped($content)."' WHERE `id`='".intval($where)."'";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		$db->query();

		// Send email to administrator
		if ($notify=='0') {
$sql = "SELECT a.user_id, a.company, a.telephone as phone, u.name, u.username, u.email FROM #__ad_agency_advertis AS a LEFT OUTER JOIN #__users as u on u.id=a.user_id	WHERE a.aid=".intval($advid)." GROUP BY a.aid";
			$db->setQuery($sql);
			$user = $db->loadObject();
			if(isset($user)&&($user!=NULL))
			{
				
                $subject = $changed == "new" ? $configs->sbnewad : $configs->sbadchanged;
                $message = $changed == "new" ? $configs->bodynewad : $configs->boadchanged;
                
				if(!isset($user->company)||($user->company=="")){$user->company = "N/A";}
				if(!isset($user->phone)||($user->phone=="")){$user->phone = "N/A";}

				$current_ad = $this->getAdById($where);
				if(isset($current_ad->approved)&&($current_ad->approved == "Y")) { $status = JText::_("NEWADAPPROVED");} else { $status = JText::_("ADAG_PENDING");}

				$epreview = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&no_html=1&adid=".$where."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".$where."</a>";
				$eapprove = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=approve&cid=".$where."&key=".$data['key']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=approve&cid=".$where."&key=".$data['key']."</a>";
				$edecline = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=decline&cid=".$where."&key=".$data['key']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=decline&cid=".$where."&key=".$data['key']."</a>";

				$subject =str_replace('{name}',$user->name,$subject);
				$subject =str_replace('{login}',$user->username,$subject);
				$subject =str_replace('{email}',$user->email,$subject);
				$subject =str_replace('{banner}',$data['title'],$subject);
				$subject =str_replace('{company}',$user->company,$subject);
				$subject =str_replace('{phone}',$user->phone,$subject);
				$subject =str_replace('{approval_status}',$status,$subject);
				$subject =str_replace('{banner_preview_url}',$epreview,$subject);
				$subject =str_replace('{approve_banner_url}',$eapprove,$subject);
				$subject =str_replace('{decline_banner_url}',$edecline,$subject);

				$message =str_replace('{name}',$user->name,$message);
				$message =str_replace('{login}',$user->username,$message);
				$message =str_replace('{email}',$user->email,$message);
				$message =str_replace('{banner}',$data['title'],$message);
				$message =str_replace('{company}',$user->company,$message);
				$message =str_replace('{phone}',$user->phone,$message);
				$message =str_replace('{approval_status}',$status,$message);
				$message =str_replace('{banner_preview_url}',$epreview,$message);
				$message =str_replace('{approve_banner_url}',$eapprove,$message);
				$message =str_replace('{decline_banner_url}',$edecline,$message);

				JUtility::sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject, $message, 1 );
			}		}
		// END - Send email to administrator

		$sql = "SELECT approved FROM #__ad_agency_banners WHERE id = ".intval($idi);
		$db->setQuery($sql);
		$approved = $db->loadResult();

		$isWizzard = $this->isWizzard();

        $item_id = JRequest::getInt('Itemid','0','post');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		$link = "index.php?option=com_adagency&controller=adagencyAds&p=1".$Itemid;
		$msg = JText::_('AD_BANNERSAVED');
		global $mainframe;
		if($isWizzard && $approved=='P'){
			$mainframe->redirect($link,$msg);
		}

		//echo "<pre>";var_dump($sqlz);echo "</pre>";die('-END-');
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

	function isWizzard(){
		$db = & JFactory::getDBO();
		$sql = "SELECT `show` FROM #__ad_agency_settings ORDER BY id LIMIT 1";
		$db->setQuery($sql);
		$shown = $db->loadResult();
		$shown = explode(";",$shown);
		$isWizzard = false;
		foreach($shown as $element){
			if($element == "wizzard") { $isWizzard = true; }
		}
		return $isWizzard;
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

	function getpopuplistAdvertisers () {
		if (empty ($this->_package)) {
			$db =& JFactory::getDBO();
			$sql = "SELECT a.aid, a.company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY a.company ASC";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$this->_package = $db->loadObjectList();

		}
		return $this->_package;

	}
};
?>
