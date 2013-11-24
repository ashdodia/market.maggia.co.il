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

jimport ("joomla.application.component.view");

class adagencyViewadagencyCampaigns extends JView {

	function display ($tpl =  null ) {
		$task2 = JRequest::getVar('task2', '', 'get');
		$tasks = JRequest::getVar('tasks', '', 'get');
		$database =& JFactory::getDBO();
		$advertiser = $this->get('CurrentAdvertiser');
		$aid = $advertiser->aid;
        $itemid  = & $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');
        $itemid_ads  = & $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid_pkg  = & $this->getModel("adagencyConfig")->getItemid('adagencypackage');

		require_once (JPATH_SITE."/administrator/components/com_adagency/plugin_handler.php");
		global $plugin_handler;
		$plugin_handler = new HandleAdAgencyPlugins;
		$flag = 0;
		$flag = $plugin_handler->interceptPaymentResponse($tasks);

		if ($flag!=1) {
			$nrads = $this->get('BannerCount');
			//checking for unavailable packs
			$rezultat = $this->get('BuyedAvailablePacksForAid');
			$params = $this->get('TimeFormat');

			if(isset($aid)&&($aid!=NULL)) {
				$dFormat = $advertiser->fax;
				if(isset($dFormat)&&($dFormat!=NULL)){
					$params = $dFormat;
				} else {
					$params = "-1";
				}
			}

			$camps =& $this->get('listCampaigns');

            $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
            $this->assign("itemid_cpn", $itemid_cpn);

            $this->assignRef("itemid", $itemid);
            $this->assignRef("itemid_ads", $itemid_ads);
            $this->assignRef("itemid_pkg", $itemid_pkg);
			$this->assignRef("advertiser", $advertiser);
			$this->assignRef("params", $params);
			$this->assignRef('camps', $camps);
			$this->assignRef('task2', $task2);
			$this->assignRef('rezultat', $rezultat);
			$this->assignRef('nrads', $nrads);
			parent::display($tpl);
		}
	}

	function approve( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$imgP = "components/com_adagency/images/pending.gif";
		if($row->approved=='Y') {
			$img = 'images/'.$imgY;
			$task = "pending";
			$alt = JText::_('Approve');
			$action = JText::_('ADAG_CHTPEN');
		} elseif ($row->approved=='N') {
			$img = 'images/'.$imgX;
			$task = "approve";
			$alt = JText::_('Unapprove');
			$action = JText::_('Approve item');
		} elseif ($row->approved=='P') {
			$img = $imgP;
			$task = "unapprove";
			$alt = JText::_("ADAG_PENDING");
			$action = "Unnapprove Item";
		} else {return false;}

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}

	function time_difference($start_datetime, $end_datetime){
		// Splits the dates into parts, to be reformatted for mktime.
		if ((isset($start_datetime))&&(isset($end_datetime))) {
		$start_datetime = explode(" ", $start_datetime, 2);
		$end_datetime = explode(" ", $end_datetime, 2);

		$first_date_ex = explode("-",$start_datetime[0]);
		if(isset($start_datetime[1])) {	$first_time_ex = explode(":",$start_datetime[1]); }
		$second_date_ex = explode("-",$end_datetime[0]);
		$second_time_ex = explode(":",$end_datetime[1]);

		// makes the dates and times into unix timestamps.
		$first_unix  = @mktime($first_time_ex[0], $first_time_ex[1], $first_time_ex[2], $first_date_ex[1], $first_date_ex[2], $first_date_ex[0]);
		$second_unix  = @mktime($second_time_ex[0], $second_time_ex[1], $second_time_ex[2], $second_date_ex[1], $second_date_ex[2], $second_date_ex[0]);

		// Gets the difference between the two unix timestamps.
		$timediff = $second_unix-$first_unix;

		// Works out the days, hours, mins and secs.
		$days=intval($timediff/86400);
		$remain=$timediff%86400;
		$hours=intval($remain/3600);
		$remain=$remain%3600;
		$mins=intval($remain/60);
		$secs=$remain%60;

		// Returns a pre-formatted string. Can be chagned to an array.
		$ARR = array();
		$ARR['days'] = $days;
		$ARR['hours'] = $hours;
		$ARR['mins'] = $mins;

		return $ARR;
		}
	}


	function editForm($tpl = null) {
		$db =& JFactory::getDBO();
		$database =& JFactory::getDBO();
		$data = JRequest::get('post');
		$camp =& $this->get('Campaign');
		$isNew = ($camp->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		$pid_pst = JRequest::getInt('pid');
		$task = $isNew?"new":"edit";
		$configs =& $this->_models['adagencyconfig']->getConfigs();
		$advertiser =& $this->get('CurrentAdvertiser');
		$JApp =& JFactory::getApplication();
		$jnow = JFactory::getDate();
		$jnow->setOffset($JApp->getCfg('offset'));
		$itemid  = & $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');

		if(isset($advertiser->aid)&&($advertiser->aid>0)) {
			$count_total_banners = adagencyModeladagencyCampaigns::getCountBannersPerAdv($advertiser->aid);
		} else { $count_total_banners = 0; }

		$configs->params = @unserialize($configs->params);
		$configs->payment = @unserialize($configs->payment);
		if(isset($configs->params['timeformat'])){
			//$configs->params = $configs->params['timeformat'];
		} else { $configs->params = "-1"; }
		$advertiser_id = (int)$advertiser->aid;
		$permission = adagencyModeladagencyCampaigns::getPermForAdv($camp->id,$advertiser_id);
		if($permission == false) {
			global $mainframe;
			$mainframe->redirect("index.php?option=com_adagency");
		}

		$currencydef = $configs->currencydef;
		if ($isNew) {
			$rows = adagencyModeladagencyCampaigns::getlistPackages();
			foreach($rows as $element){
				if($element->cost != '0.00') {
					$element->description.=' - '.JText::_('ADAG_C_'.trim($currencydef)).$element->cost.' '.$currencydef;
				} else {
					$element->description.=' - '.JText::_('VIEWPACKAGEFREE');
				}
			}
		} else {
			$rows = adagencyModeladagencyCampaigns::getAllPacks();
		}
        if (!is_array($rows)) { $rows = array(); }

		$javascript = ' onchange="submitbutton(\'refresh\')" ';
		$packagess[] = JHTML::_('select.option',  "0", JText::_('ADAG_SEL_PACK'), 'tid', 'description' );
	    $packagess 	= array_merge( $packagess, $rows );

		if(isset($data['otid'])){
			$pid_pst = $data['otid'];
		}
		elseif(JRequest::getInt('ren_id','0','get') != 0){
			$pid_pst = JRequest::getInt('ren_id','0','get');
		}
		
		$remove_action = JRequest::getVar("remove_action", "");
		if($remove_action == ""){
			$lists['package']  =  JHTML::_( 'select.genericlist', $packagess, 'otid', 'class="inputbox" size="1"'.$javascript,'tid', 'description', $pid_pst);
		}
		else{
			foreach($packagess as $key=>$element){
				if($element->tid == $pid_pst){
					$lists['package'] = $element->description;
					break;
				}
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			if ($row->tid == $camp->otid) {
				$package_row = $row;

			}
		}
		$get_jreq_id=JRequest::getInt('id');
		if ($advertiser_id > 0) $camp->aid = $advertiser_id;
		$creat=false;
		if (!$camp->aid) {
			if (isset($get_jreq_id) && $get_jreq_id!=0) {
				$camp =& $this->get('Campaign');
				$camp->aid = $advertiser_id;
			}
			else {
				$camp->aid = 0;
				$camp->start_date = $jnow->toMySQL(true);
				//$camp->start_date = date( 'Y-m-d H:i:s', time() );
			}
			$creat=true;
		} else $advertiser_id = $camp->aid;

		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );
	    $advertisersloaded = adagencyModeladagencyCampaigns::getcmplistAdvertisers();
        if (!is_array($advertisersloaded)) { $advertisersloaded = array(); }
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'aid', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
	    $lists['approved'] 		= JHTML::_('select.booleanlist',  'approved', '', $camp->approved );

		if (!isset($package_row)) {
			$package_row->type='';
			$types2 = NULL;
		} else {
			$package_row->allzones = & $this->_models['adagencycampaigns']->getZonesForPack($package_row->tid);
			if(is_array($package_row->allzones)){
				foreach($package_row->allzones as $element){
					$b_with_size = false;
					$element->adparams = @unserialize($element->adparams);
					$types = array("'a'","'b'");
					if(isset($element->adparams)&&is_array($element->adparams)){
						foreach($element->adparams as $key=>$value) {
							if($key == 'affiliate') { $types[] = "'Advanced'"; $b_with_size = true; }
							if($key == 'textad') { $types[] = "'TextLink'"; }
							if($key == 'standard') { $types[] = "'Standard'"; $b_with_size = true; }
							if($key == 'flash') { $types[] = "'Flash'"; $b_with_size = true; }
							if($key == 'popup') { $types[] = "'Popup'"; }
							if($key == 'transition') { $types[] = "'Transition'"; }
							if($key == 'floating') { $types[] = "'Floating'"; }
						}
						if($b_with_size == true) {
							if(isset($element->adparams['width'])&&($element->adparams['width'] != '')) {
								$with_size = " AND b.width ='".$element->adparams['width']."' AND b.height='".$element->adparams['height']."'";
							} else {
								$with_size = NULL;
							}
						} else {
							$with_size = NULL;
						}

						$types2[] = "(b.media_type IN (".implode(',',$types).")".$with_size.")";
					}
				}
				$types2 = " AND (".implode(" OR ",$types2).")";
			}
		}

		if ($package_row->type == "cpm") {
			$package_row->details = $package_row->quantity ."&nbsp;".JText::_("AD_CAMP_IMP");
		}
		if ($package_row->type == "pc") {
			$package_row->details = $package_row->quantity . "&nbsp;".JText::_("AD_CAMP_CLK");
		}
		if ($package_row->type == "fr") {
			$tmp_validity = explode("|", $package_row->validity, 2);
			$package_row->details = $tmp_validity[0] . " " . JText::_("ADAG_".strtoupper($tmp_validity[1]."s"));

			//$now_datetime = date("Y-m-d H:i:s");
			$now_datetime = $jnow->toMySQL(true);
			if ($now_datetime > $camp->start_date) {
				//CONTINUE
			}
			else {
				$now_datetime = $camp->start_date;
			}

			if ($now_datetime > $camp->validity) {
				$camp->expired = true;
			}
			else {
				$camp->expired = false;

				//get time difference as days, hours, mins
				$camp->time_left = adagencyViewadagencyCampaigns::time_difference($now_datetime, $camp->validity);
			}
		}

		if ($creat) {
			if(isset($package_row->tid)){
				$sql = "SELECT b.id, b.title, b.media_type, b.parameters, b.width, b.height , b.approved, '0' as relative_weighting FROM #__ad_agency_banners AS b WHERE b.approved <>'N' AND b.advertiser_id=".intval($camp->aid)." ".$types2;
				//echo "<pre>";var_dump($sql);die();
				$database->setQuery($sql);
				$ban_row = $database->loadObjectList();
			} else {
				$ban_row = NULL;
			}
		} else {
			if(isset($package_row->tid)){
				$sql = "SELECT id, title, media_type, parameters, width, height , approved, '0' AS relative_weighting
                        FROM #__ad_agency_banners AS b WHERE b.advertiser_id=".intval($camp->aid)." ".$types2;
				//echo "<pre>";var_dump($sql);die();
				$database->setQuery($sql);
				$ban_row = $database->loadObjectList();
			} else {
				$ban_row = NULL;
			}
		}

		if(isset($ban_row)&&isset($package_row->allzones)){
			$ban_row = & $this->_models['adagencycampaigns']->updateMediaType($ban_row);
			$ban_row = & $this->_models['adagencycampaigns']->updateZoneList($ban_row,$package_row->allzones,$camp->id);
		}

		for ($i=0, $n=count( $ban_row ); $i < $n; $i++) {
			$ban_row[$i]->parameters = unserialize($ban_row[$i]->parameters);
			if ($ban_row[$i]->media_type == "Popup" && $ban_row[$i]->parameters['popup_type'] == "webpage") {
				$ban_row[$i]->display = "no";
			}
			elseif ($ban_row[$i]->media_type == "Transition" || $ban_row[$i]->media_type == "Floating" || $ban_row[$i]->media_type == "Advanced" || ($ban_row[$i]->media_type == "Popup" && $ban_row[$i]->parameters['popup_type'] == "HTML")) {
				if (@preg_match("/ad_url/", $ban_row[$i]->parameters['ad_code'])) {
					$ban_row[$i]->display = "yes";
				}
				else {
					$ban_row[$i]->display = "no";
				}
			}
			else {
				$ban_row[$i]->display = "yes";
			}
			unset($ban_row[$i]->parameters);
		}

		if (!$creat) {
			//SHOW STATS REPORT
            $camp->id = (int) $camp->id;
			$sql="SELECT sum(case s.type when 'impressions' then s.how_many else 0 end) as impressions, sum(case s.type when 'click' then s.how_many else 0 end) as click, TRUNCATE((sum(case s.type when 'click' then s.how_many else 0 end)/(sum(case s.type when 'impressions' then s.how_many else 0 end)/100)),2) as click_rate
				FROM #__ad_agency_stat as s
				WHERE s.advertiser_id = ".intval($camp->aid)." AND s.campaign_id = ".intval($camp->id); //s.entry_date BETWEEN '".$camp->start_date."' AND '".$jnow->toMySQL(true)."' AND

			$database->setQuery($sql);
			$stats=$database->loadAssocList();
			$stats = $stats[0];

			//SHOW CAMPAIGN DURATION
			$now_datetime = $jnow->toMySQL(true);
			//date("Y-m-d H:i:s");

			if ($camp->validity == "0000-00-00 00:00:00" || $now_datetime < $camp->validity || $camp->default == "Y") {
				//CONTINUE
			}
			else {
				$now_datetime = $camp->validity;
			}

			//get time difference as days, hours, mins

			$duration_stats = adagencyViewadagencyCampaigns::time_difference($camp->start_date, $now_datetime);
            if (!is_array($duration_stats)) { $duration_stats = array(); }
            if (!is_array($stats)) { $stats = array(); }
			if (isset($duration_stats)) $stats = array_merge($stats, $duration_stats);
		}

		if (!isset($stats)) $stats='';

		if(isset($advertiser_id)&&($advertiser_id!=NULL)) {
			$dFormat = $advertiser->fax;
			if(isset($dFormat)&&($dFormat!=NULL)){
				$configs->params['timeformat'] = $dFormat;
			}
			else{
				$configs->params = "-1";
			}
		}
		$pstatus = & $this->_models['adagencycampaigns']->getApprSts($advertiser_id);

        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        $this->assign("itemid_cpn", $itemid_cpn);

        if (isset($camp->params)) {
            $camp->params = @unserialize( $camp->params );
        }

        $this->assign("itemid", $itemid);
		$this->assign("pstatus", $pstatus);
		$this->assign("camp", $camp);
		$this->assign("stats", $stats);
		$this->assign("package_row", $package_row);
		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("task", $task);
		$this->assign("text", $text);
		$this->assign("ban_row", $ban_row);
		$this->assign("count_total_banners", $count_total_banners);
		
		parent::display($tpl);
	}

    function changecb($tpl = null) {
        $camp_id = JRequest::getInt('id', 0, 'get');
        $camp = $this->getModel("adagencyCampaigns")->getCmpById( $camp_id );
        $banners = $this->getModel("adagencyCampaigns")->getCampBanners( $camp_id );
        $this->assign("camp", $camp);
        $this->assign("banners", $banners);
		parent::display($tpl);
    }

    function closeboxcb($tpl = null, $vars) {
        $this->assign("vars", $vars);
        parent::display($tpl);
    }
	
	function getOrderDetails($campaign_id, $package_id){
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_order where `card_number`='".intval($campaign_id).";".intval($package_id)."'";
		$db->setquery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function promoValid(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_promocodes where published=1 and codestart <= ".time()." and (codeend = 0 OR codeend >= ".time().")";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		
		$sql = "select `showpromocode` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$showpromocode = $db->loadResult();
		if($showpromocode == 0 && $result > 0){//show promo code
			$result = 1;
		}
		else{
			$result = 0;
		}
		
		$cid = JRequest::getVar("cid", "0");
		if(intval($cid) != "0"){
			$result = 0;
		}
		
		return $result;
	}
}

?>
