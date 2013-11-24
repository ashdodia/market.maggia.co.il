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

class adagencyModeladagencyStandard extends JModel {
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
            $sql = "SELECT * FROM #__ad_agency_order_type";
            $this->_total = $this->_getListCount($sql);
            $this->_packages = $this->_getList($sql);
        }
        return $this->_packages;
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
            ON z.zoneid = pz.zone_id,
			#__modules m
            WHERE c.aid = ".intval($adv_id)." AND z.adparams LIKE '%standard%'
			AND m.id=pz.zone_id
			AND m.published=1
        ";
        if ($distinct == 1) { $sqls .= "GROUP BY c.id"; }
        // echo $sqls;die();
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
        // echo "<pre>";var_dump($camps);die();
        return $camps;
    }

    function processCampZones($camps){
        $zones = array();
        if ( isset($camps) && (is_array($camps)) ) {
            foreach ($camps as $camp) {
                if(!isset($zones[$camp->id])) {
                    $zones[$camp->id] = $this->getAllZonesForCampByCampId($camps, $camp->id);
                }
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
        $sql = "SELECT DISTINCT cb.campaign_id FROM #__ad_agency_banners AS b
                LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb
                ON cb.banner_id=b.id
                WHERE b.advertiser_id=".intval($advertiser_id)."
                AND b.id=".intval($adid);
        $db->setQuery($sql);
        $these_campaigns=$db->loadResultArray();
        return $these_campaigns;
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
		global $mainframe;
        $item =& $this->getTable('adagencyAds');
        $configs = $this->getInstance("adagencyConfig", "adagencyModel");
        $configs = $configs->getConfigs();
        $adsModel = $this->getInstance("adagencyAds", "adagencyModel");
        $notify = JRequest::getInt('id');
		$changed = "new";
        $advid = $this->getCurrentAdvertiser();
        if(isset($advid->aid)) { $advid = $advid->aid; } else { $advid = 0;}
        $database =& JFactory::getDBO();
        $db =& JFactory::getDBO();
        $data = JRequest::get('post');
        $selected_camps = array();
        if(isset($data['adv_cmp'])&&(is_array($data['adv_cmp']))){
            foreach ($data['adv_cmp'] as $val) {
                if ($val) {
                    if(strpos($val,"|yes|")>0){
                        $val = intval(str_replace("|yes|","",$val));
                        $selected_camps[] = $val;
                    }
                }
            }
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
        
		if($approved == 'N'){
			$approved = 'P';
		}
        
		$data['approved'] = $approved;
        // Check settings for auto-approve ads [end]

        if($data['id']==0){
            $data['created'] = date("Y-m-d");
            $data['key'] = md5(rand(1000,9999));
        } else {
            $data['key'] = NULL;
        }

        // if the ad is not new then ->
        if(isset($data['id'])&&($data['id']>0)){
            $ex_ad = $this->getAdById($data['id']);
            $ex_ad->parameters = @unserialize($ex_ad->parameters);
            // if the ad was approved or declined and the new status would be 'pending', make sure changes occured to it!
            if(($ex_ad->approved != 'P')&&($data['approved'] == 'P')){
                // if no change has been made, let the status be as it was
                if(($ex_ad->title == $data['title'])&&($ex_ad->description == $data['description'])&&($ex_ad->target_url == $data['target_url'])&&($ex_ad->width == $data['width'])&&($ex_ad->height == $data['height'])&&($ex_ad->image_url == $data['image_url'])) {
                    //&&($ex_ad->parameters['alt_text'] == $data['parameters']['alt_text'])
                    $data['approved'] = $ex_ad->approved;
                }
				else{
					//it was changed, and sent email to administrator
					$notify = "0";
					$changed = "changed";
				}
            }
        }

        $data['parameters']['alt_text'] = $data['target_url'];
        $data['parameters'] = @serialize($data['parameters']);
        if(!isset($data['adv_cmp'])){$data['adv_cmp'] = NULL;}
        $adv_cmp=$data['adv_cmp'];
		
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
        if (!isset($data['id']) || $data['id']==0) $data['id'] = @mysql_insert_id();
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
		
        $query = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id=".intval($idi);
        $db->setQuery($query);
        $db->query();

        $campLimInfo = $adsModel->getCampLimInfo();
        //echo "<pre>";var_dump($campLimInfo);die();

        if(isset($selected_camps)){
            foreach ($selected_camps as $val) {
                if ( ($val) && (
                    ( !isset($campLimInfo[$val])) || ( $campLimInfo[$val]['adslim'] > $campLimInfo[$val]['occurences'] )
                ) ) {
                    $query = "INSERT INTO `#__ad_agency_campaign_banner`
                                (`campaign_id` ,`banner_id` ,`relative_weighting` ,`thumb`, `zone`)
                                VALUES
                                ('".intval($val)."', '".intval($idi)."', '100', NULL, ".intval($data['czones'][$val]).");";
                    //$query = "INSERT INTO #__ad_agency_campaign_banner VALUES ({$val},{$idi},100)";
                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
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

                $current_ad = $this->getAdById($idi);

                if(isset($current_ad->approved)&&($current_ad->approved == "Y")) {
                    $status = JText::_("NEWADAPPROVED");
                } else {
                    $status = JText::_("ADAG_PENDING");
                }

                $epreview = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&no_html=1&adid=".$idi."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".$idi."</a>";
                $eapprove = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=approve&cid=".$idi."&key=".$data['key']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=approve&cid=".$idi."&key=".$data['key']."</a>";
                $edecline = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=decline&cid=".$idi."&key=".$data['key']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=decline&cid=".$idi."&key=".$data['key']."</a>";

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
            }
        }

        $sql = "SELECT approved FROM #__ad_agency_banners WHERE id = ".intval($idi);
        $db->setQuery($sql);
        $approved = $db->loadResult();

        $isWizzard = $this->isWizzard();
        $item_id = JRequest::getInt('Itemid','0','post');
        if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

        $link = "index.php?option=com_adagency&controller=adagencyAds&p=1".$Itemid;
        $msg = JText::_('AD_BANNERSAVED');

        if($isWizzard && $approved=='P'){
            $mainframe->redirect($link,$msg);
        }

        // END - Send email to administrator
        return true;
    }

    function getstandardlistAdvertisers () {
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

};
?>
