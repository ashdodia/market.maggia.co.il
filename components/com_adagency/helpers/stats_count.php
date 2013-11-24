<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
	function checkbot($user_agent){
	
		if($user_agent == "") {return 1;}
		  
		$bots_array = array("AdsBot-Google", "googlebot", "FeedFetcher-Google", "DotBot", "Bloglines", "Charlotte", "Quihoobot", "WebAlta", "LinkWalker", "sogou", "Baiduspider", "MSNbot-media", "BSpider", "DNAbot", "becomebot", "legs", "Nutch", "Spiderman", "SurveyBot", "BBot", "Netcraft", "Exabot", "bot", "robot", "Speedy Spider", "spider", "crawl", "Teoma", "ia_archiver", "froogle", "archiver", "curl", "python", "nambu", "twitt", "perl", "sphere", "PEAR", "java", "wordpress", "radian", "yandex", "eventbox", "monitor", "mechanize", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "www.galaxy.com", "Scooter", "ScoutJet", "Slurp", "MSNBot", "blogscope", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz", "spider", "TechnoratiSnoop" , "blogpulse", "jobo", "facebookexternalhit"); 

			
		foreach($bots_array as $bot){
			if(strpos(strtolower($user_agent),strtolower($bot)) !== false) { return 1; }
		}
		  
		return 0;	
	}

	function geo($channel,$files) {
		$database = &JFactory::getDBO();
		if($channel === NULL) return 1;
		//if(is_array($channel)) { array_unique($channel); }
		if(file_exists(JPATH_BASE."/".$files)) {
			//echo "The MaxMind city file exists.<hr />";							
			if (!function_exists('json_encode')) {
				require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');
			}
			require_once(JPATH_BASE."/components/com_adagency/helpers/geoip.inc");
			require_once(JPATH_BASE."/components/com_adagency/helpers/geoipcity.inc");
			require_once(JPATH_BASE."/components/com_adagency/helpers/geoipregionvars.php");

			$gi = geoip_open(JPATH_BASE."/".$files, GEOIP_STANDARD);
			$record = geoip_record_by_addr($gi, iJoomlaGetRealIpAddr());
			geoip_close($gi);
		
			//echo "<b>This ad has a targeting channel</b><hr />";
			//$sql = "SELECT `type`,`logical`,`option`,`data` FROM #__ad_agency_channel_set WHERE channel_id = '".$channel."'";
			//$database->setQuery($sql);
			$channel_rules = $channel;//$database->loadObjectList();
			if(isset($channel_rules)) {
				$counter = 1;
				foreach($channel_rules as $element) {
					if(!is_array($element->data)) { $element->data = json_decode($element->data); }
					if(($element->type == 'country')&&(isset($record->country_code))) {
						if($element->option == 'isnot') { 
							if(@in_array($record->country_code, $element->data)) { $conditions_array[$counter]['result'] = 0; }
							else { $conditions_array[$counter]['result'] = 1; }
						} else {
							if(@in_array($record->country_code, $element->data)) { $conditions_array[$counter]['result'] = 1; }
							else { $conditions_array[$counter]['result'] = 0; }
						}
					} elseif (($element->type == 'continent')&&(isset($record->continent_code))) {
						if($element->option == 'isnot') { 
							if(@in_array($record->continent_code, $element->data)) { $conditions_array[$counter]['result'] = 0; }
							else { $conditions_array[$counter]['result'] = 1; }
						} else {
							if(@in_array($record->continent_code, $element->data)) { $conditions_array[$counter]['result'] = 1; }
							else { $conditions_array[$counter]['result'] = 0; }
						}									
					} elseif (($element->type == 'region')&&(isset($record->region))) {
						if($element->option == 'isnot') { 
							if($element->data[0] == $record->country_code){ 
								$element->data[0] = NULL;
								if(@in_array($record->region, $element->data)) { $conditions_array[$counter]['result'] = 0; }
								else { $conditions_array[$counter]['result'] = 1; }
							} else { $conditions_array[$counter]['result'] = 1; }
						} else {
							if($element->data[0] != $record->country_code) {
								$conditions_array[$counter]['result'] = 0;
							} else {
								$element->data[0] = NULL;
								if(@in_array($record->region, $element->data)) { $conditions_array[$counter]['result'] = 1; }
								else { $conditions_array[$counter]['result'] = 0; }
							}
						}									
					} elseif (($element->type == 'city')&&(isset($record->city))) {
						for($i=1;$i<=count($element->data)-1;$i++){
							$ttemp[] = $element->data[$i];
						}
						
						if($element->option == '==') { 
							if(($record->country_code == $element->data[0])&&(@in_array($record->city,$ttemp))) 
								{ $conditions_array[$counter]['result'] = 1; }
							else { $conditions_array[$counter]['result'] = 0; }
						} elseif($element->option == '!=') {
							if(($record->country_code == $element->data[0])&&(@in_array($record->city,$ttemp))) 
								{ $conditions_array[$counter]['result'] = 0; }
							else { $conditions_array[$counter]['result'] = 1; }
						}									
					} elseif (($element->type == 'latitude')&&(isset($record->latitude))&&(isset($record->longitude))) {
						if($element->option == '==') { 
							if(($record->latitude > $element->data->a)&&(($record->latitude < $element->data->b))
							&&($record->longitude > $element->data->c)&&(($record->longitude < $element->data->d))) { 
								$conditions_array[$counter]['result'] = 1; 
							} else { $conditions_array[$counter]['result'] = 0; }
						} else {
							if(($record->latitude > $element->data->a)&&(($record->latitude < $element->data->b))
							&&($record->longitude > $element->data->c)&&(($record->longitude < $element->data->d))) { 
								$conditions_array[$counter]['result'] = 0; 
							} else { $conditions_array[$counter]['result'] = 1; }
						}
					} elseif ($element->type == 'dma') {
						if(isset($record->dma_code)&&($record->dma_code != NULL)) {
							if($element->option == 'isnot') { 
								if(@in_array($record->dma_code, $element->data)) { $conditions_array[$counter]['result'] = 0; }
								else { $conditions_array[$counter]['result'] = 1; }
							} else {
								if(@in_array($record->dma_code, $element->data)) { $conditions_array[$counter]['result'] = 1; }
								else { $conditions_array[$counter]['result'] = 0; }
							}
						} else {
							if($element->option == 'isnot') { return 1; } else { return 0; }
						}	
					} elseif ($element->type == 'usarea') {
						if (isset($record->area_code) && ($record->area_code != NULL)) {
							if($element->option == '==') { 
								if($record->area_code == $element->data[0]) { $conditions_array[$counter]['result'] = 1; }
								else { $conditions_array[$counter]['result'] = 0; }
							} elseif($element->option == '!=') {
								if($record->area_code == $element->data[0]) { $conditions_array[$counter]['result'] = 0; }
								else { $conditions_array[$counter]['result'] = 1; }
							} elseif($element->option == '=~') {
								if(intval(strpos('_'.$record->area_code, $element->data[0]))>0) {
									$conditions_array[$counter]['result'] = 1;
								} else { $conditions_array[$counter]['result'] = 0; } 
							} elseif($element->option == '!~') {
								if(intval(strpos('_'.$record->area_code, $element->data[0]))>0) {
									$conditions_array[$counter]['result'] = 0;
								} else { $conditions_array[$counter]['result'] = 1; } 
							}
						} else {
							if (($element->option == '==')||($element->option == '=~')) { $conditions_array[$counter]['result'] = 0;}
						}	
					} elseif ($element->type == 'postalcode') {
						if(isset($record->postal_code)){
							$temp_zip = explode(',',$element->data[0]);
							if($element->option == '==') { 
								if(@in_array($record->postal_code,$temp_zip)) { $conditions_array[$counter]['result'] = 1; }
								else { $conditions_array[$counter]['result'] = 0; }
							} elseif($element->option == '!=') {
								if(@!in_array($record->postal_code,$temp_zip)) { $conditions_array[$counter]['result'] = 0; }
								else { $conditions_array[$counter]['result'] = 1; }
							}
						} else {
							if (($element->option == '==')||($element->option == '=~')) { $conditions_array[$counter]['result'] = 0;}
						}	
					}
					
					if(isset($conditions_array[$counter]['result'])) { $conditions_array[$counter]['logical'] = $element->logical; }
					$counter++;
				}
				$final_decision = 1;
				if(isset($conditions_array)) {
					foreach($conditions_array as $element) {
						// To make final decision about displaying here
						if($element['logical'] == 'AND') {
							$final_decision = $final_decision && $element['result'];
						} else {
							$final_decision = $final_decision || $element['result'];
						}
					}
				}
			}
		}

	//if(!$final_decision) continue;	
	return $final_decision;
	}

	function impression ($adv,$time) {
		global $impressions;
		$database = & JFactory :: getDBO();
		$timp = date("Y:m:d",$time); // H:i:s
		$sql = "SELECT `id` FROM #__ad_agency_campaign WHERE `aid`={$adv} AND `approved`='Y' AND `exp_notice`!=1 AND `status`=1 AND ((`type` = 'cpm' AND `quantity` >0) OR (`type` = 'pc' AND `quantity` >0) OR (`type` = 'fr' AND `validity` > '".$timp."'))";
		$database->setQuery($sql);		
		$campaigns = $database->loadObjectList();
		$i=0;
		
		foreach ($campaigns as $cmps) {
			$database->setQuery("SELECT sum(case type when 'impressions' then how_many else 0 end) as t FROM #__ad_agency_stat WHERE `campaign_id`={$cmps->id} AND `type`='impressions' AND `entry_date` > '{$timp}'");
			$impressions[$i] = $database->loadResult();
			$i++;
		}
		
		return $impressions;
	}

	function clicks ($adv,$time) {
		global $impressions;
		$database = & JFactory :: getDBO();
		$timp = date("Y:m:d",$time); //H:i:s
		$sql = "SELECT `id` FROM #__ad_agency_campaign WHERE `aid`={$adv} AND `approved`='Y' AND `exp_notice`!=1 AND `status`=1 AND ((`type` = 'cpm' AND `quantity` >0) OR (`type` = 'pc' AND `quantity` >0) OR (`type` = 'fr' AND `validity` > '".$timp."'))";
		$database->setQuery($sql);	
		$campaigns = $database->loadObjectList();
		$i=0;
		
		foreach ($campaigns as $cmps) {
				$database->setQuery("SELECT sum(case type when 'click' then how_many else 0 end) as t FROM #__ad_agency_stat WHERE `campaign_id`={$cmps->id} AND `type`='click' AND `entry_date` > '{$timp}'");
				$impressions[$i] = $database->loadResult();
				$i++;
		}	
		return $impressions;
	}

	function sendreport($id, $clicks, $impression, $lastreport) {
		global $adver_id, $totals, $subject;
		$mosConfig_absolute_path =JPATH_BASE; 
		$database = & JFactory :: getDBO();
		$db = & JFactory :: getDBO();
		if (!class_exists('TableadagencyConfig')) require_once($mosConfig_absolute_path."/components/com_adagency/tables/adagencyconfig.php");
		if (!class_exists('adagencyModeladagencyConfig')) require_once($mosConfig_absolute_path."/components/com_adagency/models/adagencyconfig.php");
		if (!class_exists('TableadagencyAds')) require_once($mosConfig_absolute_path."/components/com_adagency/models/adagencyconfig.php");
		$configs = new adagencyModeladagencyConfig();
		$configs = $configs->getConfigs();
		$sql = "SELECT u.name, u.email FROM #__users AS u 
			LEFT JOIN #__ad_agency_advertis AS a
			ON u.id = a.user_id
			WHERE u.id=".$id." AND u.block =0 AND a.approved = 'Y'";
		$database->setQuery($sql);
		$user = $database->loadObjectList();
		// If the user is disabled, don't send any emails
		if(!isset($user[0]->email)) { return false; }
		
		$database->setQuery("SELECT `aid`,`lastreport`,`weekreport`,`monthreport` FROM #__ad_agency_advertis WHERE `user_id`=$id");
		$adver = $database->loadObjectList();
		$adver_id=$adver[0]->aid;
		$timesql = date("Y-m-d h-i-s");
		$database->setQuery("SELECT count(id) FROM #__ad_agency_campaign WHERE `aid`={$adver_id} AND `approved`='Y' AND `exp_notice`!=1 AND ((`type` = 'cpm' AND `quantity` >0) OR (`type` = 'pc' AND `quantity` >0) OR (`type` = 'fr' AND `validity` > '".$timesql."'))");
		$totals = $database->loadResult();
		$database->setQuery("SELECT `id`,`type`,`quantity`,`validity` FROM #__ad_agency_campaign WHERE `aid`={$adver_id} AND `approved`='Y' AND `exp_notice`!=1 AND ((`type` = 'cpm' AND `quantity` >0) OR (`type` = 'pc' AND `quantity` >0) OR (`type` = 'fr' AND `validity` > '".$timesql."'))");
		$campaigns = $database->loadObjectList();
		$name 		= $user[0]->name;
		$email 		= $user[0]->email;
		$subject 	= html_entity_decode($subject, ENT_QUOTES);
		$subject=$configs->sbrep;
		$message=$configs->bodyrep;
		$time = '['.modAdAgencyZoneHelper::formatime(date("Y-m-d",$lastreport)).'] - ['.modAdAgencyZoneHelper::formatime(date("Y-m-d")).']';
		if ($totals==1) { 
			$idcmp=$campaigns[0]->id;

			// check if campaign has banners 
			$sql = "SELECT count(campaign_id) FROM `#__ad_agency_campaign_banner` WHERE `campaign_id`=$idcmp";
			$db->setQuery($sql);
			$banners_count_current_campaign = $db->loadResult();
			if($banners_count_current_campaign == 0) return false;
			// check if campaign has banners - end
		
			$database->setQuery("SELECT `name` FROM #__ad_agency_campaign WHERE `id`=$idcmp");
			$cmptext = $database->loadResult();
			if (!$clicks[0]) $clicks[0]=0;
			if (!$impression[0]) $impression[0]=0;
			if($clicks[0] == 0 && $impression[0] == 0) {
				return false;
			}			
			$message =str_replace('{name}',$name,$message);
			$message =str_replace('{daterange}',$time,$message);
			$message =str_replace('{clicks}',$clicks[0],$message);
			$message =str_replace('{impressions}',$impression[0],$message);
			$message =str_replace('{used_for_more_campaigns}',' ',$message);
			$message =str_replace('{campaign}',$cmptext,$message);	
		} else if ($totals > 1){
			if (!$clicks[0]) $clicks[0]=0;
			if (!$impression[0]) $impression[0]=0;
			if(($clicks[0] == 0)&&($impression[0]==0)) { $first_zero = true; } else { $first_zero = false; }			
			$message =str_replace('{name}',$name,$message);
			$message =str_replace('{daterange}',$time,$message);
			$message =str_replace('{clicks}',$clicks[0],$message);
			$message =str_replace('{impressions}',$impression[0],$message);		
			$cmp_id=0;
			foreach ($campaigns as $cmps) {
				// check if campaign has banners 
				$sql = "SELECT count(campaign_id) FROM `#__ad_agency_campaign_banner` WHERE `campaign_id`=$cmps->id";
				$db->setQuery($sql);
				$banners_count_current_campaign = $db->loadResult();
				if($banners_count_current_campaign == 0) continue;
				// check if campaign has banners - end
							
				$database->setQuery("SELECT `name` FROM #__ad_agency_campaign WHERE `approved`='Y' AND `id`=$cmps->id");
				$cmptext[$cmp_id] = $database->loadObjectList();
				$cmp_id++;
			}
			$morecmp = '';
			$temp=$cmp_id-1;
			$tmp=1;
			while ($temp) {
				if (!$clicks[$tmp]) $clicks[$tmp]=0;
				if (!$impression[$tmp]) $impression[$tmp]=0;
				if(($clicks[$tmp] != 0) && ($impression[$tmp] != 0)) {
					$morecmp.="<br />Campaign Name: ".$cmptext[$tmp][0]->name."<br /><br />";
					$morecmp.=$clicks[$tmp]." - Clicks total<br />";
					$morecmp.=$impression[$tmp]." - Impressions total<br />";
				}
				$tmp++;
				$temp--;
			}
			if(($first_zero == true)&&($morecmp == '')) { return false; }			
			$message =str_replace('{campaign}',$cmptext[0][0]->name,$message);	
			$message =str_replace('{used_for_more_campaigns}',$morecmp,$message);
		}	
		$message = html_entity_decode($message, ENT_QUOTES);
		$newtime=time();
		if ($totals!=0) {
		if ($lastreport==$adver[0]->lastreport) {
			$database->setQuery("UPDATE #__ad_agency_advertis SET `lastreport`='{$newtime}' WHERE `user_id`=$id ");
			$database->query();
		} else if ($lastreport==$adver[0]->weekreport) {
			$database->setQuery("UPDATE #__ad_agency_advertis SET `weekreport`='{$newtime}' WHERE `user_id`=$id ");
			$database->query();
		} else if ($lastreport==$adver[0]->monthreport) {
			$database->setQuery("UPDATE #__ad_agency_advertis SET `monthreport`='{$newtime}' WHERE `user_id`=$id ");
			$database->query();
		}				

			JUtility::sendMail( $configs->fromemail, $configs->fromname, $email, $subject, $message, 1 );
		}	
	}
	
	function exp_notices($id, $toUser = true) {
		global $subject, $adver_id, $message;
		$mosConfig_absolute_path =JPATH_BASE; 
		$database = & JFactory :: getDBO();
		if (!class_exists('TableadagencyConfig')) require_once($mosConfig_absolute_path."/components/com_adagency/tables/adagencyconfig.php");
		if (!class_exists('adagencyModeladagencyConfig')) require_once($mosConfig_absolute_path."/components/com_adagency/models/adagencyconfig.php");
		$configs = new adagencyModeladagencyConfig();
		$configs = $configs->getConfigs();
		$database->setQuery("SELECT `name`,`username`,`email` FROM #__users WHERE `id`=$id");
		$user = $database->loadObject();
		$database->setQuery("SELECT `aid`,`company` FROM #__ad_agency_advertis WHERE `user_id`=$id");
		$advertiser_current = $database->loadObject();
		$adver_id = $advertiser_current->aid;
		$timesql = date("Y-m-d h-i-s");
		$sql = "SELECT `id`,`name`,`start_date`,`type`,`quantity`,`validity`,`exp_notice`,`otid` FROM #__ad_agency_campaign WHERE `aid`={$adver_id} AND `exp_notice`!=1 AND ((`type` = 'cpm' AND `quantity` <1) OR (`type` = 'pc' AND `quantity` <1) OR (`type` = 'fr' AND `validity` < '".$timesql."'))";
		$database->setQuery($sql);
		$campaigns = $database->loadObjectList();
		if(isset($user->name)){ $name = $user->name; $email = $user->email; } else { $name = NULL; $email = NULL; }
		if($toUser){
			// Variables for the emails sent to the advertiser
			$subject = $configs->sbcmpex;
			$subject = html_entity_decode($subject, ENT_QUOTES);
			$message = $configs->bodycmpex;
			$message = str_replace('{name}',$name,$message);
			$subject = str_replace('{name}',$name,$subject);
			$message = str_replace('{username}',$user->username,$message);
			$subject = str_replace('{username}',$user->username,$subject);	
			$message = str_replace('{company}',$advertiser_current->company,$message);
			$subject = str_replace('{company}',$advertiser_current->company,$subject);	
			$message = str_replace('{packages_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyPackages",$message);
			$subject = str_replace('{packages_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyPackages",$subject);
			$message = str_replace('{expire_date}', date("Y-m-d"),$message);
			$subject = str_replace('{expire_date}', date("Y-m-d"),$subject);
		}
		// Variables for the emails sent to the admin
		$adm_subject = $configs->sbcmpexpadm;
		$adm_subject = html_entity_decode($subject, ENT_QUOTES);
		$adm_message = $configs->bodycmpexpadm;
		$adm_message = str_replace('{name}',$name,$adm_message);
		$adm_subject = str_replace('{name}',$name,$adm_subject);
		$adm_message = str_replace('{username}',$user->username,$adm_message);
		$adm_subject = str_replace('{username}',$user->username,$adm_subject);	
		$adm_message = str_replace('{company}',$advertiser_current->company,$adm_message);
		$adm_subject = str_replace('{company}',$advertiser_current->company,$adm_subject);
		$adm_message = str_replace('{packages_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyPackages",$adm_message);
		$adm_subject = str_replace('{packages_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyPackages",$adm_subject);
		$adm_message = str_replace('{expire_date}', date("Y-m-d"),$adm_message);
		$adm_subject = str_replace('{expire_date}', date("Y-m-d"),$adm_subject);
		
		
		foreach ($campaigns as $cmps) { 
			if (($cmps->type=="cpm"  || $cmps->type=="pc") && $cmps->quantity < 1) {
				if($toUser){
					$message2adv = $message; $subject2adv = $subject;
					$message2adv = str_replace('(campaign_renew_URL}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&ren_id=".$cmps->otid,$message2adv);
					$subject2adv = str_replace('(campaign_renew_URL}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&ren_id=".$cmps->otid,$subject2adv);		
					$message2adv = str_replace('{campaign}',$cmps->name,$message2adv);
					$subject2adv = str_replace('{campaign}',$cmps->name,$subject2adv);
					JUtility::sendMail( $configs->fromemail, $configs->fromname, $email, $subject2adv, $message2adv, 1 );
				}
				// Send mail to admin here
					$message2adm = $adm_message; $subject2adm = $adm_subject;
					$message2adm = str_replace('{campaign}',$cmps->name,$message2adm);
					$subject2adm = str_replace('{campaign}',$cmps->name,$subject2adm);
					JUtility::sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject2adm, $message2adm, 1 );
				// end 
								
				$database->setQuery("UPDATE #__ad_agency_campaign SET `exp_notice`='1' WHERE `id`={$cmps->id}");
				$database->query();
			} else if ($cmps->type=="fr") {
				$start_datetime = explode(" ", $cmps->validity, 2);
				$first_date_ex = explode("-",$start_datetime[0]);
				$first_time_ex = explode(":",$start_datetime[1]);
				$dif=0;
				$i=0;
				if ($first_date_ex[0]==date("Y")) $i++;
				if ($first_date_ex[1]==date("m")) $i++;
				if ($first_date_ex[2]==date("d")) $i++;
				if ($first_time_ex[0]==date("H")) $i++;
				if ($first_time_ex[1]==date("i")) $i++;
				if($toUser){
					$message2adv = $message; $subject2adv = $subject;
					$message2adv = str_replace('{campaign}',$cmps->name,$message2adv);
					$subject2adv = str_replace('{campaign}',$cmps->name,$subject2adv);
					$message2adv = str_replace('(campaign_renew_URL}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&ren_id=".$cmps->otid,$message2adv);
					$subject2adv = str_replace('(campaign_renew_URL}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&ren_id=".$cmps->otid,$subject2adv);
					JUtility::sendMail( $configs->fromemail, $configs->fromname, $email, $subject2adv, $message2adv, 1 );
				}
				// Send mail to admin here
					$message2adm = $adm_message; $subject2adm = $adm_subject;
					$message2adm = str_replace('{campaign}',$cmps->name,$message2adm);
					$subject2adm = str_replace('{campaign}',$cmps->name,$subject2adm);
					JUtility::sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject2adm, $message2adm, 1 );
				// end 
				
				$database->setQuery("UPDATE #__ad_agency_campaign SET `exp_notice`='1' WHERE `id`={$cmps->id}");
				$database->query();
			}
		}	
	}
		/////sending email report///////
		
	function getIn($bnrs){
		$ids = array();
		if(isset($bnrs)){
			foreach($bnrs as $element){
				$ids[] = $element->banner_id;
			}
		}
		$ids = array_unique($ids);
		if(count($ids)>0) {$in = ",".implode(',',$ids);} else { $in =""; }
		if($in == ",") { return NULL; } else { return $in; }
	}
	
	function getBanners($bnrs,$db){
		$in = getIn($bnrs);			
		$sql = "SELECT * FROM #__ad_agency_banners WHERE id IN (0".$in.")";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		
		return $res;
	}
	
	function loadBannerById($id, $elements){
		if(isset($elements)){
			foreach($elements as $element){
				if($element->id == $id) {
					return $element;
				}
			}
		}
		return NULL;
	}

	function loadChannelById($id, $elements){
		$res = array();
		if(isset($elements)){
			foreach($elements as $element){
				//var_dump($element);die();
				if($element->id == $id) {
					$res[] = $element;
				}
			}
		}
		if(count($res)>0) { return $res; } else { return NULL; }
	}
	
	function getManyByIds($banner_id,$campaign_id,$elements){
		if(isset($elements)){
			foreach($elements as $element){
				if(($element->banner_id == $banner_id)&&($element->campaign_id == $campaign_id)){
					return $element->how_many;				
				}
			}
		}
		return 0;
	}
?>