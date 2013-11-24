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

class adagencyViewadagencyReports extends JView {
	
	function formatime2($time,$option = 1){
		$date_time = explode(" ",$time);
		$date_time[0] = str_replace("/","-",$date_time[0]);
		$tdate = explode("-",$date_time[0]); 		
		if (($option == 1)||($option == 2)||($option == 7)||($option == 8)) { 
			$aux=$tdate[0];
			$tdate[0]=$tdate[2];
			$tdate[2]=$aux;
		}
		elseif (($option == 3)||($option == 4)||($option == 9)||($option == 10)) { 
			//mm-dd-yyyy
			$aux=$tdate[0];
			$tdate[0]=$tdate[2];
			$tdate[2]=$tdate[1];
			$tdate[1]=$aux;	
		}
		$output = NULL;
		if(!isset($date_time[1])) {$date_time[1] = NULL;}
		$output = $tdate[0]."-".$tdate[1]."-".$tdate[2]." ".$date_time[1];			
		return trim($output);
	}
	
	function display ($tpl =  null ) {
		$data = JRequest::get('post');
        $configsModel = $this->getModel("adagencyConfig");        
        $itemid = $configsModel->getItemid('adagencyreports');
        
		if(!isset($data['adag_datepicker'])) {$data['adag_datepicker'] = 0;}
		
		if(isset($data['tfa'])){
			if(($data['tfa']==1)||($data['tfa']==2)||($data['tfa']==7)||($data['tfa']==8)){
				$data['start_date']=$this->formatime2($data['start_date'],1);	
				$data['end_date']=$this->formatime2($data['end_date'],1);	
			} elseif(($data['tfa']==3)||($data['tfa']==4)||($data['tfa']==9)||($data['tfa']==10)){
				$data['start_date']=$this->formatime2($data['start_date'],3);	
				$data['end_date']=$this->formatime2($data['end_date'],3);	
			} else {
				$data['start_date']=$this->formatime2($data['start_date'],6);
				$data['end_date']=$this->formatime2($data['end_date'],6);	
			} 
			if(isset($data['start_date'])&&(isset($data['end_date']))&&($data['end_date']!='')&&($data['start_date']!='')) {
			$_SESSION['rep_start_date2'] = $data['start_date'];	
			$_SESSION['rep_end_date2'] = $data['end_date'];	
			}
		}
		
				
		if(!isset($_SESSION['rep_start_date2'])) {$_SESSION['rep_start_date2'] = NULL;}
		if(!isset($_SESSION['rep_end_date2'])) {$_SESSION['rep_end_date2'] = NULL;}		
		$this->assign("start_date", $_SESSION['rep_start_date2']);
		$this->assign("end_date", $_SESSION['rep_end_date2']);	
		
		$my = & JFactory::getUser();
		$cid = JRequest::getVar('cid', '', 'post');
		$type = JRequest::getVar('type', '', 'post');
		$task = JRequest::getVar('task', '', 'post');
		$chkAdvertiser=intval(JRequest::getVar('chkAdvertiser', '', 'post','0'));
		$chkCampaign=intval(JRequest::getVar('chkCampaign', '', 'post','0'));
		$chkBanner=intval(JRequest::getVar('chkBanner', '', 'post','0'));
		$chkDay=intval(JRequest::getVar('chkDay', '', 'post','0'));
		
		$database =& JFactory::getDBO();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."'";
		$database->setQuery($sql);
		$aid = (int)$database->loadResult();
		$javascript = 'onchange="document.adminForm.submit();"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('ADSELADVERTISER'), 'aid', 'company' );	
	    $advertisersloaded = adagencyModeladagencyReports::getreportsAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['aid']  =  JHTML::_( 'select.genericlist', $advertisers, 'aid', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $aid);
	    $campaigns[] 	= JHTML::_('select.option',  "0", JText::_('ADAG_ALL_CAMP'), 'id', 'name' );
	    if ($aid) { 
			$sql	= "SELECT id, name FROM #__ad_agency_campaign WHERE aid='".intval($aid)."' ORDER BY name ASC";
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
			$campaigns 	= array_merge( $campaigns, $database->loadObjectList() );
		}
	    $javascripts='onchange="document.adminForm.submit();"';
		$lists['cid']  =  JHTML::_( 'select.genericlist', $campaigns, 'cid', 'class="inputbox" size="1"'.$javascript,'id', 'name', $cid);
		
		$javascripts='onchange="document.adminForm.submit();"';
		$types[] 	=  JHTML::_( 'select.option', 'Summary', JText::_('ADAG_SUMMARY'), 'value', 'option' );
		$types[] 	=  JHTML::_( 'select.option', 'Click Detail',JText::_('ADAG_CLICK_DETAIL'), 'value', 'option' );
		$lists['type']  =  JHTML::_( 'select.genericlist', $types, 'type', 'class="inputbox" size="1"'.$javascripts,'value', 'option', $type);
		
		$start_now_year=JRequest::getVar( 'start_year',date("Y"), 'post');
		$stop_now_year=JRequest::getVar(  'stop_year', date("Y"), 'post');
		$start_now_month=JRequest::getVar(  'start_month', date("m"), 'post');
		$stop_now_month=JRequest::getVar(  'stop_month', date("m"), 'post');
		$start_now_day=JRequest::getVar(  'start_day', date("d"), 'post');
		$stop_now_day=JRequest::getVar(  'stop_day', date("d"), 'post');
			
		// year listbox
		$array = array();
		for ($i=1999; $i<2020; $i++) {
			$array[]=JHTML::_( 'select.option', $i, $i, 'value', 'option');
		}
		$lists['start_year']=JHTML::_( 'select.genericlist',$array, 'start_year','class="inputbox"','value', 'option', $start_now_year);
		$lists['stop_year']=JHTML::_( 'select.genericlist', $array, 'stop_year','class="inputbox"','value', 'option', $stop_now_year);
		
		$months[] = JHTML::_('select.option',  "1", JText::_('ADJAN'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "2", JText::_('ADFEB'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "3", JText::_('ADMAR'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "4", JText::_('ADAPR'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "5", JText::_('ADMAY'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "6", JText::_('ADJUN'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "7", JText::_('ADJUL'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "8", JText::_('ADAUG'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "9", JText::_('ADSEP'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "10", JText::_('ADOCT'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "11", JText::_('ADNOV'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "12", JText::_('ADDEC'), 'value', 'option' );	
		
		$lists['start_month']=JHTML::_( 'select.genericlist',$months, 'start_month', 'class="inputbox"','value', 'option', $start_now_month );
		$lists['stop_month']=JHTML::_( 'select.genericlist',$months, 'stop_month', 'class="inputbox"','value', 'option', $stop_now_month );
	    
		// day listbox
		$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31",);
		$array=array();
		foreach ( $days as $val) {
			$array[]=JHTML::_( 'select.option',$val, $val, 'value', 'option');
		}
		$lists['start_day']=JHTML::_( 'select.genericlist',$array, 'start_day','class="inputbox"','value', 'option', $start_now_day);
		$lists['stop_day']=JHTML::_( 'select.genericlist',$array, 'stop_day','class="inputbox"','value', 'option', $stop_now_day);
		
		$this->assign("lists", $lists);
		$this->assign("task", $task);
	    
			$filds_out=array();
	if ('creat' == $task) { 
		switch ($type) {
			case "Summary":
				if ( $aid ) $where[] = "s.advertiser_id = ".intval($aid);
				if ( $cid ) $where[] = "s.campaign_id = ".intval($cid);
				if ( $chkAdvertiser ) {
					$group[]="s.advertiser_id";
					$filds_out[]=JText::_('REPADVERTISER');
					$select[]="a.company";
					$join[]="LEFT JOIN #__ad_agency_advertis AS a ON a.aid=s.advertiser_id";
				}
				if ( $chkCampaign ) {
					$group[]="s.campaign_id";
					$filds_out[]=JText::_('REPCAMPAIGN');
					$select[]="c.name";
					$join[]="LEFT JOIN #__ad_agency_campaign AS c ON c.id=s.campaign_id";
				}
				if ( $chkBanner ) {
					$group[]="s.banner_id";
					$filds_out[]=JText::_('REPBANNER');
					$select[]="b.title";
					$join[]="LEFT JOIN #__ad_agency_banners AS b ON b.id=s.banner_id";
				}
				if ( $chkDay ) {
					$group[]="DAYOFYEAR(s.entry_date)";
					$filds_out[]=JText::_('ADAG_DATE');
					$select[]="s.entry_date";
				}
				
				$filds_out[]=JText::_("VIEWADIMPRESSIONS");
				$filds_out[]=JText::_("VIEWADCLICKS");
				$filds_out[]=JText::_("AD_CLICK_RATE");
				$select[]="sum(case s.type when 'impressions' then s.how_many else 0 end) as impressions";
				$select[]="sum(case s.type when 'click' then s.how_many else 0 end) as click";
				$select[]="TRUNCATE((sum(case s.type when 'click' then s.how_many else 0 end)/(sum(case s.type when 'impressions' then s.how_many else 0 end)/100)),2) as click_rate";	
				
				//date/time if the report is for one day
				
				if (($start_now_year == $stop_now_year) AND ($start_now_month == $stop_now_month) AND ($start_now_day == $stop_now_day)) {
				global $stop_now_day1;
				$stop_now_day1 = $stop_now_day + 1;
				//$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				$where[]="s.entry_date > '".$data['start_date']." 00:00:00' AND s.entry_date < '".$data['end_date']." 23:59:59'";
				 } else {
				 $where[]="s.entry_date > '".$data['start_date']." 00:00:00' AND s.entry_date < '".$data['end_date']." 23:59:59'";
				//$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				}
				
				// concat SELECT
				if ( isset( $select ) ) {
					$select = implode( ', ', $select );
				} else {
					$select = '';
				}
				
				// concat WHERE
				if ( isset( $where ) ) {
					$where = "\n WHERE ". implode( ' AND ', $where );
				} else {
					$where = '';
				}

				// concat JOIN 
				if ( isset( $join ) ) {
					$join = implode( ' ', $join );
				} else {
					$join = '';
				}

				// concat GROUP

				if ( isset( $group ) ) {
					$group = 'GROUP BY '.implode( ', ', $group );
				} else {
					$group = '';
				}

				$sql="SELECT $select
						FROM #__ad_agency_stat as s
						$join
						$where ".trim($group);
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
				$data_row=$database->loadAssocList();
			break;

			case "Click Detail":
				$filds_out=array(JText::_('REPCAMPAIGN'), JText::_('ADAG_DATETIME'), JText::_('ADAG_IPADDR'), JText::_('ADAG_NUMBER'));
				$filds_select=array('campaign_id', 'entry_date', 'ip_address', 'how_many');				
				if ( $aid ) $where[] = "s.advertiser_id = ".intval($aid);
				if ( $cid ) $where[] = "s.campaign_id = ".intval($cid);
				if (($start_now_year == $stop_now_year) AND ($start_now_month == $stop_now_month) AND ($start_now_day == $stop_now_day)) $stop_now_day = $stop_now_day;
				//$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				$where[]="s.entry_date > '".trim($data['start_date'])." 00:00:00' AND s.entry_date < '".trim($data['end_date'])." 23:59:59'";
				$where[]="s.type='click'";
				if ( isset( $where ) ) {
					$where = "\n WHERE ". implode( ' AND ', $where );
				} else {
					$where = '';
				}
				$sql="SELECT c.name, s.entry_date, s.ip_address, s.how_many 
						FROM #__ad_agency_stat as s
						LEFT JOIN #__ad_agency_campaign AS c ON c.id=s.campaign_id
						$where";				
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
				$data_row=$database->loadAssocList();
			break;
		}
	}
		if (!isset($data_row)) $data_row='';
						
		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$database->setQuery($query);
		$params = $database->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "10"; }
		
		if(isset($aid)&&($aid!=NULL)) {
			$sql = "SELECT fax FROM #__ad_agency_advertis WHERE aid = '".intval($aid)."'";
			$database->setQuery($sql);
			$dFormat = $database->loadResult();
			if(isset($dFormat)&&($dFormat!=NULL)){
				$params = $dFormat;
			}
		}
		
		$itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        
		$this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assign("params", $params);
		$this->assign("adag_datepicker", $data['adag_datepicker']);			
		$this->assign("filds_out", $filds_out);		
		$this->assign("data_row", $data_row);
		$this->assign("type", $type);
		parent::display($tpl);
	}
	
	function emptyrep ($tpl =  null ) {
		JToolBarHelper::title(JText::_('Reports'), 'generic.png');		
		JToolBarHelper::addNewX('creat','Run Report');
		JToolBarHelper::addNewX('emptyrep','Empty');
		
		$aid = JRequest::getVar('aid', '', 'post');
		$cid = JRequest::getVar('cid', '', 'post');
		$type = JRequest::getVar('type', '', 'post');
		$task = JRequest::getVar('task', '', 'post');
		
		$chkAdvertiser=intval(JRequest::getVar('chkAdvertiser', '', 'post','0'));
		$chkCampaign=intval(JRequest::getVar('chkCampaign', '', 'post','0'));
		$chkBanner=intval(JRequest::getVar('chkBanner', '', 'post','0'));
		$chkDay=intval(JRequest::getVar('chkDay', '', 'post','0'));
		
		$database =& JFactory::getDBO();
		$orders =& $this->get('listPackages');
		$this->assignRef('packages', $orders);
		$pagination = & $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);
		
		$javascript = 'onchange="document.adminForm.submit();"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('ADSELADVERTISER'), 'aid', 'company' );	
	    $advertisersloaded = adagencyAdminModeladagencyReports::getreportsAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['aid']  =  JHTML::_( 'select.genericlist', $advertisers, 'aid', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $aid);
		
	    $campaigns[] 	= JHTML::_('select.option',  "0", JText::_('ADSELCAMPAIGN'), 'id', 'name' );
	    if ($aid) { 
			$sql	= "SELECT id, name FROM #__ad_agency_campaign WHERE aid='".intval($aid)."' ORDER BY name ASC";
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
			$campaigns 	= array_merge( $campaigns, $database->loadObjectList() );
		}
	    $javascripts='onchange="return changetype();"';
		$lists['cid']  =  JHTML::_( 'select.genericlist', $campaigns, 'cid', 'class="inputbox" size="1"'.$javascript,'id', 'name', $cid);
		
		$javascripts='onchange="return changetype();"';
		$types[] 	=  JHTML::_( 'select.option', 'Summary', JText::_('ADAG_SUMMARY'), 'value', 'option' );
		$types[] 	=  JHTML::_( 'select.option', 'Click Detail',JText::_('ADAG_CLICK_DETAIL'), 'value', 'option' );
		$lists['type']  =  JHTML::_( 'select.genericlist', $types, 'type', 'class="inputbox" size="1"'.$javascripts,'value', 'option', $type);
		
		$start_now_year=JRequest::getVar( 'start_year',date("Y"), 'post');
		$stop_now_year=JRequest::getVar(  'stop_year', date("Y"), 'post');
		$start_now_month=JRequest::getVar(  'start_month', date("m"), 'post');
		$stop_now_month=JRequest::getVar(  'stop_month', date("m"), 'post');
		$start_now_day=JRequest::getVar(  'start_day', date("d"), 'post');
		$stop_now_day=JRequest::getVar(  'stop_day', date("d"), 'post');
			
		// year listbox
		$array = array();
		for ($i=1999; $i<2020; $i++) {
			$array[]=JHTML::_( 'select.option', $i, $i, 'value', 'option');
		}
		$lists['start_year']=JHTML::_( 'select.genericlist',$array, 'start_year','class="inputbox"','value', 'option', $start_now_year);
		$lists['stop_year']=JHTML::_( 'select.genericlist', $array, 'stop_year','class="inputbox"','value', 'option', $stop_now_year);
		
		$months[] = JHTML::_('select.option',  "1", JText::_('ADJAN'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "2", JText::_('ADFEB'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "3", JText::_('ADMAR'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "4", JText::_('ADAPR'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "5", JText::_('ADMAY'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "6", JText::_('ADJUN'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "7", JText::_('ADJUL'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "8", JText::_('ADAUG'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "9", JText::_('ADSEP'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "10", JText::_('ADOCT'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "11", JText::_('ADNOV'), 'value', 'option' );	
		$months[] = JHTML::_('select.option',  "12", JText::_('ADDEC'), 'value', 'option' );	
		
		$lists['start_month']=JHTML::_( 'select.genericlist',$months, 'start_month', 'class="inputbox"','value', 'option', $start_now_month );
		$lists['stop_month']=JHTML::_( 'select.genericlist',$months, 'stop_month', 'class="inputbox"','value', 'option', $stop_now_month );
	    
		// day listbox
		$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31",);
		$array=array();
		foreach ( $days as $val) {
			$array[]=JHTML::_( 'select.option',$val, $val, 'value', 'option');
		}
		$lists['start_day']=JHTML::_( 'select.genericlist',$array, 'start_day','class="inputbox"','value', 'option', $start_now_day);
		$lists['stop_day']=JHTML::_( 'select.genericlist',$array, 'stop_day','class="inputbox"','value', 'option', $stop_now_day);
		
		$this->assign("lists", $lists);
		$this->assign("task", $task);
	    
			$filds_out=array();
	if ('creat' == $task) { 
		switch ($type) {
			case "Summary":
				if ( $aid ) $where[] = "s.advertiser_id = $aid";
				if ( $cid ) $where[] = "s.campaign_id = $cid";
				if ( $chkAdvertiser ) {
					$group[]="s.advertiser_id";
					$filds_out[]=JText::_('REPADVERTISER');
					$select[]="a.company";
					$join[]="LEFT JOIN #__ad_agency_advertis AS a ON a.aid=s.advertiser_id";
				}
				if ( $chkCampaign ) {
					$group[]="s.campaign_id";
					$filds_out[]=JText::_('REPCAMPAIGN');
					$select[]="c.name";
					$join[]="LEFT JOIN #__ad_agency_campaign AS c ON c.id=s.campaign_id";
				}
				if ( $chkBanner ) {
					$group[]="s.banner_id";
					$filds_out[]=JText::_('REPBANNER');
					$select[]="b.title";
					$join[]="LEFT JOIN #__ad_agency_banners AS b ON b.id=s.banner_id";
				}
				if ( $chkDay ) {
					$group[]="DAYOFYEAR(s.entry_date)";
					$filds_out[]=JText::_('ADAG_DATE');
					$select[]="s.entry_date";
				}
				
				$filds_out[]=JText::_("VIEWADIMPRESSIONS");
				$filds_out[]=JText::_("VIEWADCLICKS");
				$filds_out[]=JText::_("AD_CLICK_RATE");
				$select[]="sum(case s.type when 'impressions' then s.how_many else 0 end) as impressions";
				$select[]="sum(case s.type when 'click' then s.how_many else 0 end) as click";
				$select[]="TRUNCATE((sum(case s.type when 'click' then s.how_many else 0 end)/(sum(case s.type when 'impressions' then s.how_many else 0 end)/100)),2) as click_rate";	
				
				//date/time if the report is for one day
				
				if (($start_now_year == $stop_now_year) AND ($start_now_month == $stop_now_month) AND ($start_now_day == $stop_now_day)) {
				global $stop_now_day1;
				$stop_now_day1 = $stop_now_day + 1;
				$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				 } else {
				$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				}
				
				// concat SELECT
				if ( isset( $select ) ) {
					$select = implode( ', ', $select );
				} else {
					$select = '';
				}
				
				// concat WHERE
				if ( isset( $where ) ) {
					$where = "\n WHERE ". implode( ' AND ', $where );
				} else {
					$where = '';
				}

				// concat JOIN 
				if ( isset( $join ) ) {
					$join = implode( ' ', $join );
				} else {
					$join = '';
				}

				// concat GROUP

				if ( isset( $group ) ) {
					$group = 'GROUP BY '.implode( ', ', $group );
				} else {
					$group = '';
				}

				$sql="SELECT $select
						FROM #__ad_agency_stat as s
						$join
						$where $group";
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
				$data_row=$database->loadAssocList();
			break;

			case "Click Detail":
				$filds_out=array(JText::_('REPCAMPAIGN'), JText::_('ADAG_DATETIME'), JText::_('ADAG_IPADDR'), JText::_('ADAG_NUMBER'));
				$filds_select=array('campaign_id', 'entry_date', 'ip_address', 'how_many');
				if ( $aid ) $where[] = "s.advertiser_id = ".intval($aid);
				if ( $cid ) $where[] = "s.campaign_id = ".intval($cid);
				if (($start_now_year == $stop_now_year) AND ($start_now_month == $stop_now_month) AND ($start_now_day == $stop_now_day)) $stop_now_day = $stop_now_day;
				$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				$where[]="s.type='click'";
				if ( isset( $where ) ) {
					$where = "\n WHERE ". implode( ' AND ', $where );
				} else {
					$where = '';
				}
				$sql = "DELETE FROM #__ad_agency_stat $where ";
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
				$data_row=$database->loadAssocList();
			break;
		}
	}
		if (!isset($data_row)) $data_row='';
		$this->assign("filds_out", $filds_out);
		$this->assign("data_row", $data_row);
		parent::display($tpl);
	}
	
	function editForm($tpl = null) { 
		global $mainframe;
		
		$data = JRequest::get('post');
		$db =& JFactory::getDBO();
		$ad =& $this->get('ad'); 
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
		if (!$advertiser_id) $advertiser_id = $ad->advertiser_id;
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('VIEWTREEADDADCODE').":<small>[".$text."]</small>");
		if ($isNew) {
			JToolBarHelper::cancel();
			JToolBarHelper::save('save', 'Save');

		} else {
			JToolBarHelper::cancel ('cancel', 'Close');
			JToolBarHelper::save('save', 'Save');

		}
		$this->assign("ad", $ad);

		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );	
	    $advertisersloaded = adagencyAdminModeladagencyAdcode::getadcodelistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
			
	    $lists['approved'] 		= JHTML::_('select.booleanlist',  'approved', '', $ad->approved );
	    		
		// Window option
		$window[] 	= JHTML::_('select.option', '_blank', JText::_('open in new window'), 'value', 'option' );
		$window[] 	= JHTML::_('select.option', '_self', JText::_('open in the same window'), 'value', 'option' );
		$lists['window'] = JHTML::_( 'select.genericlist', $window, 'parameters[target_window]', 'class="inputbox" size="1"  id="show_hide_box"','value', 'option', $ad->parameters['target_window']);
				
		//Show Zone select
		$sql	= "SELECT id, title FROM #__modules WHERE module='mod_ijoomla_adagency_zone' ORDER BY title ASC";
		$db->setQuery($sql);
		if (!$db->query()) {
			mosErrorAlert( $db->getErrorMsg() );
			return;
		}
		$zone[] 	= JHTML::_('select.option',  "0", JText::_('select zone'), 'id', 'title' );	
		$zone 	= array_merge( $zone, $db->loadObjectList() );
		$lists['zone_id'] = JHTML::_( 'select.genericlist', $zone, 'zone', 'class="inputbox" size="1"','id', 'title', $ad->zone);
		
		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
		$sqls = "SELECT `id`,`name` FROM #__ad_agency_campaign WHERE `aid`=".intval($adv_id);
		$db->setQuery($sqls);
			if (!$db->query()) {
			mosErrorAlert( $db->getErrorMsg() );
			return;
			}
			$camps = $db->loadObjectList();
		} else $camps='';
		
				
		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "-1"; }
		
		$this->assign("params", $params);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);

		parent::display($tpl);
	}
}
?>