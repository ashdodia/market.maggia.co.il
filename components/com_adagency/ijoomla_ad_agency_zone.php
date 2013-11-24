<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/
define( '_JEXEC', 1 );
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "components")),0,-1));
//if (!isset($_SERVER["HTTP_REFERER"])) exit("Direct access not allowed.");
//$mosConfig_absolute_path =substr(JPATH_BASE, 0, strpos(JPATH_BASE, "/components")); 
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'methods.php');
require_once ( JPATH_BASE .DS.'configuration.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database'.DS.'mysql.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'folder.php');
require_once ( JPATH_BASE .DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'helper.php');
$config = new JConfig();

$options = array ("host" => $config->host, "user" => $config->user, "password" => $config->password, "database" => $config->db,"prefix" => $config->dbprefix);
class JoomlaDatabase extends JDatabaseMySQL{
	public function __construct($options){
		parent::__construct($options);
	}
}
$database = new JoomlaDatabase($options);

//include("tables/adagencyads.php");

	function checkGeo($record,$database,$channel_id){
		$sql = "SELECT `type`,`logical`,`option`,`data` FROM #__ad_agency_channel_set WHERE channel_id = '".intval($channel_id)."' ORDER BY id ASC";
		$database->setQuery($sql);
		$channel_rules = $database->loadObjectList();
		if(isset($channel_rules)) {
			$counter = 1;
			foreach($channel_rules as $element) {
				$element->data = json_decode($element->data);
				if(($element->type == 'country')&&(isset($record->country_code))) {
					if($element->option == 'isnot') {
						if(in_array($record->country_code, $element->data)) { $conditions_array[$counter]['result'] = 0; }
						else { $conditions_array[$counter]['result'] = 1; }
					} else {
						if(in_array($record->country_code, $element->data)) { $conditions_array[$counter]['result'] = 1; }
						else { $conditions_array[$counter]['result'] = 0; }
					}
				} elseif (($element->type == 'continent')&&(isset($record->continent_code))) {
					if($element->option == 'isnot') {
						if(in_array($record->continent_code, $element->data)) { $conditions_array[$counter]['result'] = 0; }
						else { $conditions_array[$counter]['result'] = 1; }
					} else {
						if(in_array($record->continent_code, $element->data)) { $conditions_array[$counter]['result'] = 1; }
						else { $conditions_array[$counter]['result'] = 0; }
					}
				} elseif (($element->type == 'region')&&(isset($record->region))) {
					if($element->option == 'isnot') {
						if($element->data[0] == $record->country_code){
							$element->data[0] = NULL;
							//unset($element->data[0]);
							if(in_array($record->region, $element->data)) { $conditions_array[$counter]['result'] = 0; }
							else { $conditions_array[$counter]['result'] = 1; }
						} else { $conditions_array[$counter]['result'] = 1; }
					} else {
						if($element->data[0] != $record->country_code) {
							$conditions_array[$counter]['result'] = 0;
						} else {
							$element->data[0] = NULL;
							//unset($element->data[0]);
							if(in_array($record->region, $element->data)) { $conditions_array[$counter]['result'] = 1; }
							else { $conditions_array[$counter]['result'] = 0; }
						}
					}
				} elseif (($element->type == 'city')&&(isset($record->city))) {
					for($i=1;$i<=count($element->data)-1;$i++){
						$ttemp[] = $element->data[$i];
					}
					if($element->option == '==') {
						if(($record->country_code == $element->data[0])&&(in_array($record->city,$ttemp)))
							{ $conditions_array[$counter]['result'] = 1; }
						else { $conditions_array[$counter]['result'] = 0; }
					} elseif($element->option == '!=') {
						if(($record->country_code == $element->data[0])&&(in_array($record->city,$ttemp)))
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
				} elseif (($element->type == 'dma')&&(isset($record->dma_code))) {
					if($element->option == 'isnot') {
						if(in_array($record->dma_code, $element->data)) { $conditions_array[$counter]['result'] = 0; }
						else { $conditions_array[$counter]['result'] = 1; }
					} else {
						if(in_array($record->dma_code, $element->data)) { $conditions_array[$counter]['result'] = 1; }
						else { $conditions_array[$counter]['result'] = 0; }
					}
				} elseif (($element->type == 'usarea')&&(isset($record->area_code))) {
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
				} elseif (($element->type == 'postalcode')&&(isset($record->postal_code))) {
					$temp_zip = explode(',',$element->data[0]);
					if($element->option == '==') {
						if(in_array($record->postal_code,$temp_zip)) { $conditions_array[$counter]['result'] = 1; }
						else { $conditions_array[$counter]['result'] = 0; }
					} elseif($element->option == '!=') {
						if(!in_array($record->postal_code,$temp_zip)) { $conditions_array[$counter]['result'] = 0; }
						else { $conditions_array[$counter]['result'] = 1; }
					}								}
				if(isset($conditions_array[$counter]['result'])) { $conditions_array[$counter]['logical'] = $element->logical; }
				$counter++;
			}
			$final_decision = 1;
			if(isset($conditions_array))
			foreach($conditions_array as $element) {
				// To make final decision about displaying here
				if($element['logical'] == 'AND') {
					$final_decision = $final_decision && $element['result'];
				} else {
					$final_decision = $final_decision || $element['result'];
				}
			}
		}
		return $final_decision;
	}

	function checkbot($user_agent){

		//if no user agent is supplied then assume it's a bot
		if($user_agent == "") {return 1;}

		$bots_array = array("AdsBot-Google", "googlebot", "FeedFetcher-Google", "DotBot", "Bloglines", "Charlotte", "Quihoobot", "WebAlta", "LinkWalker", "sogou", "Baiduspider", "MSNbot-media", "BSpider", "DNAbot", "becomebot", "legs", "Nutch", "Spiderman", "SurveyBot", "BBot", "Netcraft", "Exabot", "bot", "robot", "Speedy Spider", "spider", "crawl", "Teoma", "ia_archiver", "froogle", "archiver", "curl", "python", "nambu", "twitt", "perl", "sphere", "PEAR", "java", "wordpress", "radian", "yandex", "eventbox", "monitor", "mechanize", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "www.galaxy.com", "Scooter", "ScoutJet", "Slurp", "MSNBot", "blogscope", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz", "spider", "TechnoratiSnoop" , "blogpulse", "jobo", "facebookexternalhit");

		foreach($bots_array as $bot){
			if(strpos(strtolower($user_agent),strtolower($bot)) !== false) { return 1; }
		}

		return 0;
	}

$mosConfig_absolute_path =JPATH_BASE;
$live_site     = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$mosConfig_live_site = substr($live_site, 0, strpos($live_site, "/components"));
$real_ip = iJoomlaGetRealIpAddr();


$jnow = JFactory::getDate();
if(isset($config->offset)) {
	$jnow->setOffset($config->offset);
}

//get the image folder
$sqla = "SELECT `imgfolder` FROM #__ad_agency_settings LIMIT 1";
$database->setQuery($sqla);
$database->query();
$imgfolder = $database->loadResult();
$ad_agency_folder=$imgfolder;
//end image folder

global $abm_image_root_folder;
//get the zone id
$ZoneID = (isset($_GET["zid"])) ? abs(intval($_GET["zid"])) : 0;
//end

//get zone info + advertise here
$sql = "SELECT * FROM #__ad_agency_zone WHERE zoneid='".intval($ZoneID)."'";
$database->setQuery($sql);
$rotator_info = $database->loadObject();
$zoneSettings = $rotator_info;

$zoneSettings->adparams = @unserialize($zoneSettings->adparams);
$zoneSettings->textadparams = @unserialize($zoneSettings->textadparams);

	// v.1.5.3 - adding "advertise here" link - start
	$target = '';
	if($rotator_info->link_taketo == 0)
		$link = $mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyPackages';
	elseif($rotator_info->link_taketo == 1)
		$link = $mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=0';
	elseif($rotator_info->link_taketo == 3)
		$link = $mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyAdvertisers&task=overview';
	else
		{
			$link = $rotator_info->taketo_url;
			$target = 'target="_blank"';
		}

	$adv_here_top = ''; $adv_here_bottom = '';
	if($rotator_info->show_adv_link == 1)
		{
			$adv_here_top = '';
			$adv_here_bottom = '<div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>Advertise Here</a></div>';
		}
	elseif($rotator_info->show_adv_link == 2)
		{
			$adv_here_top = '<div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>Advertise Here</a></div>';
			$adv_here_bottom = '';
		}
	elseif($rotator_info->show_adv_link == 3)
		{
			$adv_here_top = '<div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>Advertise Here</a></div>';
			$adv_here_bottom = '<div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>Advertise Here</a></div>';
		}
//end
//if (!preg_match("/googlebot/", strtolower($_SERVER['HTTP_USER_AGENT'])) && !preg_match("/msnbot/", strtolower($_SERVER['HTTP_USER_AGENT'])) && !preg_match("/slurp/", strtolower($_SERVER['HTTP_USER_AGENT'])) && !preg_match("/gigabot/", strtolower($_SERVER['HTTP_USER_AGENT']))) {
if(isset($_SERVER['HTTP_USER_AGENT'])&&checkbot($_SERVER['HTTP_USER_AGENT'])!=1){
	$valid_c = array();
	array_push( $valid_c, 0 );
	$database->setQuery( "SELECT id FROM #__ad_agency_banners WHERE media_type='Transition' OR media_type='Floating'" );
	$ids = $database->loadResultArray();
	if ( $database->getErrorMsg() ) {
		die( 'SQL error' );
	}
	foreach ( $ids as $sid ){
		$cookie_name = "spl".md5( $sid );
		if ( isset ( $_COOKIE[$cookie_name] ) ){
			array_push( $valid_c, $sid );
		}
	}
	$valid_cookie = implode(",",$valid_c);
	$sql = "SELECT banners FROM #__ad_agency_zone WHERE zoneid=".intval($ZoneID);
	$database->setQuery($sql);
	$bannernr = $database->loadResult();
	$sql_r = "SELECT banners_cols FROM #__ad_agency_zone WHERE zoneid=".intval($ZoneID);
	$database->setQuery($sql_r);
	$banner_cols = $database->loadResult();

	$sql_rr = "SELECT cellpadding FROM #__ad_agency_zone WHERE zoneid=".intval($ZoneID);
	$database->setQuery($sql_rr);
	$cellpadding = $database->loadResult();

	$sql="SELECT defaultad as banner_id FROM #__ad_agency_zone WHERE zoneid=".intval($ZoneID)." LIMIT 1";
	$database->setQuery($sql);
	$defaultad=$database->loadObjectList();

	$sql = "SELECT geoparams FROM #__ad_agency_settings LIMIT 1";
	$database->setQuery($sql);
	$res1 = @unserialize($database->loadResult());
	if(isset($res1['allowgeo'])||isset($res1['allowgeoexisting'])){
		require_once("../../components/com_adagency/helpers/geoip.inc");
		require_once("../../components/com_adagency/helpers/geoipcity.inc");
		require_once("../../components/com_adagency/helpers/geoipregionvars.php");
		$allow_geo = true;
		$sql = "SELECT cityloc FROM #__ad_agency_settings LIMIT 1";
		$database->setQuery($sql);
		$files = $database->loadResult();
		//echo "<pre>";var_dump($files);echo "<hr /></pre>";

		if(file_exists("../../".$files)) {
			//echo "The MaxMind city file exists.<hr />";
			if (!function_exists('json_encode')) {
				require_once('../../administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');
			}
			$gi = geoip_open('../../'.$files, GEOIP_STANDARD);
			$record = geoip_record_by_addr($gi, $real_ip);
			//echo "<pre>";var_dump($record);die();
			geoip_close($gi);
		}
	} else { $allow_geo = false;}

	$banner_total=$bannernr*$banner_cols;
	$bannernr=$banner_total;
	$bannernr = ($bannernr > 0) ? $bannernr : 1;
	$dateok = $jnow->toMySQL(true);//date("Y-m-d H:i:s");
	//$sql = "SELECT t0.*, b.zone, b.approved, campaign_id, banner_id, FLOOR(RAND() * relative_weighting) as rw FROM #__ad_agency_campaign_banner LEFT OUTER JOIN #__ad_agency_banners as b ON b.id=banner_id LEFT JOIN #__ad_agency_campaign as t0 ON campaign_id=t0.id WHERE b.zone='".$ZoneID."' AND b.approved='Y' AND ((t0.approved='Y') AND (t0.approved='Y' AND ('{$dateok}' > t0.start_date) AND ((t0.type IN ('cpm','pc') AND t0.quantity>0) OR (t0.type='fr' AND NOW() < t0.validity)))) AND t0.status='1' AND banner_id NOT IN (".$valid_cookie.") ORDER BY rw DESC LIMIT 30 ";
	$sql = "SELECT t0.*, b.zone, b.approved, cb.campaign_id, cb.banner_id, FLOOR(RAND() * relative_weighting) AS rw
			FROM #__ad_agency_campaign_banner AS cb
			LEFT OUTER JOIN #__ad_agency_banners AS b
			ON b.id = cb.banner_id
			LEFT JOIN #__ad_agency_advertis AS a
			ON b.advertiser_id = a.aid
			LEFT JOIN #__ad_agency_campaign AS t0
			ON campaign_id = t0.id
			LEFT JOIN #__ad_agency_order_type AS p
			ON p.tid = t0.otid
			WHERE a.approved = 'Y' AND cb.zone = '".intval($ZoneID)."' AND b.approved='Y' AND ((t0.approved='Y') AND (t0.approved='Y' AND ('{$dateok}' > t0.start_date) AND ((t0.type IN ('cpm','pc') AND t0.quantity>0) OR (t0.type='fr' AND NOW() < t0.validity)))) AND t0.status='1' AND banner_id NOT IN (".$valid_cookie.") ORDER BY rw DESC LIMIT 30 ";
	//LIMIT $banner_total";
	//if(isset($_GET['testme'])) { echo $sql."<hr />";die(); }

	$dfa_bool=false;
	$counter2 = 1;
	$database->setQuery( $sql );
	if(!$result = $database->query()) {
		echo $database->stderr();
		return;
	}

	if(isset($adv_here_top)&&($adv_here_top!='')) {$output_adv_top='<td valign="top" colspan="'.$banner_cols.'">'.$adv_here_top.'</td></tr><tr>';} else {$output_adv_top='';}
	if(isset($adv_here_bottom)&&($adv_here_bottom!='')) {$output_adv_bottom='<tr><td valign="top" colspan="'.$banner_cols.'">'.$adv_here_bottom.'</td></tr>';} else {$output_adv_bottom='';}

	$numrows=$database->getNumRows();
	//if ($numrows) {
	if (($numrows)||(isset($defaultad[0]->banner_id)&&($defaultad[0]->banner_id!=0))) {
		//$banners = $database->loadObjectList();
	if($numrows){
		$banners = diversify($database->loadObjectList());
		$banner_total = count($banners);
		//echo "<pre>";var_dump($banners);die();
	} else { $banners=$defaultad; $dfa_bool=true; $banner_total = 1;}
						echo <<<eohtml
						document.write('<div class="remote_ads"><table align="center" class="table_remote_ads" border="0" cellspacing="{$cellpadding}" cellpadding="{$cellpadding}"><tr>{$output_adv_top}');
eohtml;
		$i=0; $geocount = 0;
		while (($i <= $banner_total-1)&&($geocount <= $bannernr-1)){
			//echo "<font color='red'>".$i."</font><hr />";
			$banner_id = $banners[$i]->banner_id;

			if ($banner_id) { 
				$sqll = "select * from #__ad_agency_banners where id=".intval($banner_id);
				$database->setQuery($sqll);
				$banner_roww = $database->loadObjectList();
				$banner_row = $banner_roww["0"];
				//$banner_row = new TableadagencyAds($database);
				//$banner_row->load( $banner_id );
				if(isset($record)&&(!$dfa_bool)&&($allow_geo)&&($banner_row->channel_id != NULL)&&(intval($banner_row->channel_id) >0)) {
					$geoCheck = checkGeo($record,$database,$banner_row->channel_id);
					//echo "| Id:".$banner_id."<pre>";var_dump($var);echo "</pre>|<br />";
				} else {
					$geoCheck = true;
				}
				//var_dump($geoCheck);echo "<br />";
				if(!$geoCheck) {
					if(($i == $banner_total-1)&&($geocount == 0)&&(isset($defaultad[0]->banner_id))) {
						$banner_row = new TableadagencyAds($database);
						$banner_row->load( $defaultad[0]->banner_id );
						$dfa_bool = true;
					} else {
						$i++; continue;
					}
				}
				//da
				//echo $banner_id."<br />";
					echo <<<eohtml
						document.write('<td valign="top">');
eohtml;
				$banner_row->parameters = unserialize($banner_row->parameters);
				// update statistic and campaigns information

				if (isset($banners[$i]->type)&&('cpm'==$banners[$i]->type)) {
					$sql = "UPDATE #__ad_agency_campaign SET quantity = quantity-1 WHERE id=".intval($banners[$i]->id);
					$database->setQuery($sql);
					if (!$database->query()) {
						echo $database->stderr();
						return;
					}

					$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".intval($banners[$i]->id);
					$database->setQuery($sql);
					if (!$database->query()) {
						echo $database->stderr();
						return;
					}
					$quantity = $database->loadResult();
					if ($quantity == 0) {
						$nowdatetime = $jnow->toMySQL(true);//date("Y-m-d H:i:s");
						$sql = "UPDATE #__ad_agency_campaign SET validity = '".trim($nowdatetime)."' WHERE id=".intval($banners[$i]->id);
						$database->setQuery($sql);
						if (!$database->query()) {
							echo $database->stderr();
							return;
						}
					}

				}

				if($dfa_bool) {
					if(isset($banner_row->target_url)) {
						$link_dfa = $banner_row->target_url;
					} elseif(isset($banner_row->parameters['linktrack'])) {
						$link_dfa = $banner_row->parameters['linktrack'];
					} else { $link_dfa = JURI::root(); }
					$banners[$i]->aid = $banner_row->advertiser_id;
				} else {
					//reduce table size
					$time_interval = date("Y-m-d");
					$sql = "SELECT how_many FROM #__ad_agency_stat WHERE substring(entry_date,1,10)='".$time_interval."' and ip_address='".ip2long($real_ip)."' AND campaign_id=".intval($banners[$i]->id)." AND banner_id=".intval($banner_row->id)." AND `type`='impressions' limit 1";
					$database->setQuery($sql);
					if (!$database->query()) {
						echo $database->stderr();
						return;
					}


					if ($database->getNumRows()) {
						$how_many = $database->loadResult();
						$how_many++;
						$sql = "SELECT limit_ip FROM #__ad_agency_settings LIMIT 1";
						$database->setQuery($sql);
						$limit_to = $database->loadResult();
						if($how_many <= $limit_to){
							$class_helper = new adagencyAdminHelper;
							$ip_is_private = $class_helper->ip_is_private($real_ip);
							if(!$ip_is_private){
								$sql = "UPDATE #__ad_agency_stat SET `how_many` = '".intval($how_many)."' WHERE substring(entry_date,1,10)='".trim($time_interval)."' AND ip_address='".ip2long($real_ip)."' AND campaign_id=".intval($banners[$i]->id)." AND banner_id=".intval($banner_row->id)." AND `type`='impressions' limit 1";
								$database->setQuery($sql);
								if (!$database->query()) {
									echo $database->stderr();
									return;
								}
							}
						}
					}
					else
					{
						// insert is made only if from the same IP during current day are no records
						$class_helper = new adagencyAdminHelper;
						$ip_is_private = $class_helper->ip_is_private($real_ip);
						if(!$ip_is_private){
							$sql = "INSERT INTO #__ad_agency_stat SET entry_date=NOW(), ip_address='".ip2long($real_ip)."', advertiser_id=".intval($banners[$i]->aid).", campaign_id=".intval($banners[$i]->id).", banner_id=".intval($banner_row->id).", `type`='impressions', `how_many`='1'";
							//	echo $sql;
							$database->setQuery($sql);
							if (!$database->query()) {
								echo $database->stderr();
								return;
							}
						}
					}
					//end
				} // end not dfa

				if(isset($link_dfa)) { $track_link = $link_dfa; } else { $track_link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;	}

				switch ($banner_row->media_type) {
					case 'Standard':

						$imageurl=$mosConfig_live_site .'/images/stories/'.$imgfolder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url;
						if(!isset($link_dfa)){
							$link=$mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
							$link = str_replace('//index.php','/index.php',$link);
						} else {
							$link = $link_dfa;
						}

						//border and color
						if (isset($banner_row->parameters['border']) && ($banner_row->parameters['border']>0)) {
							$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
						}
						else {
							$table_style="border: none;";
						}
						$bg_color="";
						if (isset($banner_row->parameters['bg_color']) && ($banner_row->parameters['bg_color']!="")) {
							$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
						}

						//td padding
						$padding="";
						if (isset($banner_row->parameters['padding']) && ($banner_row->parameters['padding']>0)) {			
							$padding="padding: ".$banner_row->parameters['padding']."px;";
						}
						$banner_row->parameters['alt_text']=addslashes($banner_row->parameters['alt_text']);
						$banner_row->parameters['alt_text']=str_replace('\n','<br/>',$banner_row->parameters['alt_text']);
						echo <<<eohtml
						document.write('<div class="standard_ad"><table align="{$banner_row->parameters['align']}" width="{$banner_row->width}" cellspacing="0" cellpadding="0" style="{$table_style} {$bg_color}"><tr><td style="{$padding}"><a href="{$link}" target="{$banner_row->parameters['target_window']}"><img  src="{$imageurl}" border="0" title="{$banner_row->parameters['alt_text']}" alt="{$banner_row->parameters['alt_text']}"></a></td></tr></table></div>');
eohtml;

						break;

					case 'TextLink':
						$img_siz = NULL;
						$banner_row->parameters['img_alt']=addslashes($banner_row->parameters['img_alt']);
						$banner_row->parameters['img_alt']=str_replace('\n','<br/>',$banner_row->parameters['img_alt']);

						$thumb = NULL;
						$sql = "SELECT thumb FROM #__ad_agency_campaign_banner WHERE campaign_id = ".intval($banners[$i]->id)." AND banner_id = ".intval($banner_row->id)." LIMIT 1";
						$database->setQuery($sql);
						$thumb = $database->loadResult();
						if(($thumb != NULL)&&(strlen($thumb)>4)) {
							$banner_row->image_url = $thumb;
						}

						if (isset($banner_row->image_url)&&($banner_row->image_url!='')) {$txtimageurl=$mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url;}
						$img_style = '';$br = '';
						if(isset($txtimageurl)&&($txtimageurl != NULL)) {
							if(isset($zoneSettings->textadparams['ia'])) {
								$img_siz = @getimagesize($mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url);
								if(isset($img_siz[0])) { $img_siz[0]+=10; $img_siz = $img_siz[0]."px;";}
								if($zoneSettings->textadparams['ia'] == 'l') { $img_style = "float:left; padding: 5px;";$img_siz = 'margin-left:'.$img_siz.'px;';}
								elseif($zoneSettings->textadparams['ia'] == 'r') { $img_style = "float:right; padding: 5px;";$img_siz = 'margin-right:'.$img_siz.'px;';}
								else { $br = "<br />";}
							}
							$imagetxtcode='<img class="standard_adv_img" src="'. $txtimageurl .'" border="0" style="'.$img_style.'" title="'.$banner_row->parameters['img_alt'].'" alt="'.$banner_row->parameters['img_alt'].'" />';
						} else {$imagetxtcode=''; $br = "<br />";}
						if(isset($zoneSettings->textadparams["wrap_img"])&&($zoneSettings->textadparams["wrap_img"] == '1')) {$img_siz = NULL;}
						//if(isset($txtimageurl)) {$imagetxtcode='<img class="standard_adv_img" src="'. $txtimageurl .'" border="0" alt="'.$banner_row->parameters['img_alt'].'" />'; } else {$imagetxtcode='';}
						if(isset($zoneSettings->adparams['width'])&&($zoneSettings->adparams['width'] != '')) { $banner_row->width = $zoneSettings->adparams['width']; }
						if(isset($zoneSettings->adparams['height'])&&($zoneSettings->adparams['height']) != '') { $banner_row->height = $zoneSettings->adparams['height']; }

						if(!isset($link_dfa)){
							$link = $mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
							$link = str_replace('//index.php','/index.php',$link);
						} else {
							$link = $link_dfa;
						}
						//border and color

						if ($banner_row->parameters['border']>0) {
							$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
						}
						else {
							$table_style="border: none;";
						}
						$bg_color="";
						if ($banner_row->parameters['bg_color']!="") {
							$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
						}

						if(!isset($banner_row->parameters['font_family_b'])) { $banner_row->parameters['font_family_b'] = 'Arial'; }
						if(!isset($banner_row->parameters['font_size_b'])) { $banner_row->parameters['font_size_b'] = 12; }
						if(!isset($banner_row->parameters['font_weight_b'])) { $banner_row->parameters['font_weight_b'] = 'normal'; }

						if(!isset($banner_row->parameters['font_family_a'])) { $banner_row->parameters['font_family_a'] = 'Arial'; }
						if(!isset($banner_row->parameters['font_size_a'])) { $banner_row->parameters['font_size_a'] = 10; }
						if(!isset($banner_row->parameters['font_weight_a'])) { $banner_row->parameters['font_weight_a'] = 'normal'; }

						if(!isset($banner_row->parameters['title_color'])) { $banner_row->parameters['title_color'] = '#0066CC'; }
						if(!isset($banner_row->parameters['body_color'])) { $banner_row->parameters['body_color'] = '#000000'; }
						if(!isset($banner_row->parameters['action_color'])) { $banner_row->parameters['action_color'] = '#0066CC'; }

						//td padding
						$padding="";
						if ($banner_row->parameters['padding']>0) {
							$padding="padding: ".$banner_row->parameters['padding']."px;";
						}
						$order   = array("\r\n", "\n", "\r");$replace = '<br />';
						$banner_row->parameters['alt_text_t']=str_replace($order,$replace,$banner_row->parameters['alt_text_t']);
						$banner_row->parameters['alt_text_a']=str_replace($order,$replace,$banner_row->parameters['alt_text_a']);
						$banner_row->parameters['alt_text']=str_replace($order,$replace,$banner_row->parameters['alt_text']);
						$banner_row->parameters['alt_text_t']=addslashes($banner_row->parameters['alt_text_t']);
						$banner_row->parameters['alt_text']=addslashes($banner_row->parameters['alt_text']);
						$banner_row->parameters['alt_text_a']=addslashes($banner_row->parameters['alt_text_a']);
						$width = ($banner_row->width > 0) ? 'width:'.$banner_row->width.'px;':'';
						$height = ($banner_row->height > 0) ? 'height:'.$banner_row->height.'px;':'';
						$overflow = "overflow:hidden;";
						
						if ( !isset($zoneSettings->adparams['width']) || !isset($zoneSettings->adparams['height']) 
							|| empty($zoneSettings->adparams['width'])  || empty($zoneSettings->adparams['height']) ) {
							$width = NULL; $height = NULL;
						}						

						if($zoneSettings->ignorestyle == '1'){
							$banner_row->parameters['font_family'] = NULL;$banner_row->parameters['font_family_b'] = NULL;$banner_row->parameters['font_family_a'] = NULL;
							$banner_row->parameters['title_color'] = NULL;$banner_row->parameters['action_color'] = NULL;$banner_row->parameters['body_color'] = NULL;
							$banner_row->parameters['font_size'] = NULL; $banner_row->parameters['font_size_a'] = NULL; $banner_row->parameters['font_size_b'] = NULL;
							$banner_row->parameters['font_weight'] = NULL; $banner_row->parameters['font_weight_a'] = NULL; $banner_row->parameters['font_weight_b'] = NULL;
						}

						//echo "<pre>";var_dump($zoneSettings);die();
						echo <<<eohtml
						document.write('<div align="{$banner_row->parameters['align']}" style="{$overflow} {$width} {$height} {$table_style} {$bg_color} {$padding}"><a href="{$link}" style="font-family: {$banner_row->parameters['font_family']}; font-size: {$banner_row->parameters['font_size']}px; font-weight: {$banner_row->parameters['font_weight']}; color: {$banner_row->parameters['title_color']};" target="{$banner_row->parameters['target_window']}">{$banner_row->parameters['alt_text_t']}</a><br /><a href="{$link}" target="{$banner_row->parameters['target_window']}">{$imagetxtcode}</a><div style="{$img_siz} font-family: {$banner_row->parameters['font_family_b']}; font-size: {$banner_row->parameters['font_size_b']}px; font-weight: {$banner_row->parameters['font_weight_b']}; color: {$banner_row->parameters['body_color']};">{$banner_row->parameters['alt_text']}</div><a href="{$link}" style="font-family: {$banner_row->parameters['font_family_a']}; font-size: {$banner_row->parameters['font_size_a']}px; font-weight: {$banner_row->parameters['font_weight_a']}; color: {$banner_row->parameters['action_color']};" target="{$banner_row->parameters['target_window']}">{$banner_row->parameters['alt_text_a']}</a></div>');
eohtml;
						$txtimageurl = NULL;
						break;

					case 'Flash':
						if(!isset($link_dfa)){
							$link=$mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
							$link = str_replace('//index.php','/index.php',$link);
						} else {
							$link = $link_dfa;
						}

						if(isset($zoneSettings->adparams['width']) && ($zoneSettings->adparams['width'] != '')) { $banner_row->width = $zoneSettings->adparams['width']; }
						if(isset($zoneSettings->adparams['height']) && ($zoneSettings->adparams['height'] != '')) { $banner_row->height = $zoneSettings->adparams['height']; }

						//border and color
						if ($banner_row->parameters['border']>0) {
							$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
						}
						else {
							$table_style="border: none;";
						}
						$bg_color="";
						if ($banner_row->parameters['bg_color']!="") {
							$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
						}

						//td padding
						$padding="";
						if ($banner_row->parameters['padding']>0) {
							$padding="padding: ".$banner_row->parameters['padding']."px;";
						}

						$onevent = "onclick";

                        if(isset($banner_row->parameters['target_window'])&&($banner_row->parameters['target_window'] == '_self')) {
							$js_open = 'document.location.href=\''.urldecode($link).'\';';
                            $flash_target = '_self';
						} else {
                            $flash_target = '_blank';
							$js_open = 'javascript:window.open(\''.urldecode($link).'\')';
						}
						if(isset($_SERVER['HTTP_USER_AGENT'])&&(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")>1)){
							$onevent = "onmousedown";
						}

						///changes in adding flash objects
                        $JURI_root = $mosConfig_live_site . '/';
						$flashurl = $mosConfig_live_site .'/images/stories/'.$imgfolder.'/'.$banners[$i]->aid.'/'. $banner_row->swf_url;
						$adflash = '<EMBED SRC="'.$flashurl.'" width=' . $banner_row->width . ' height='.$banner_row->height.' QUALITY="high" wmode="transparent" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>';
						echo <<<eohtml
						document.write('<div class="flash_ad" align="{$banner_row->parameters['align']}"><table width="{$banner_row->width}" height="{$banner_row->height}" style="{$table_style} {$bg_color}" >');
eohtml;
                        // {$onevent}="javascript:window.open(\'{$link}\')"
 						echo <<<eohtml
						document.write('<tr><td style="{$padding}" width="100%" height="100%">');
eohtml;
						echo <<<eohtml
						document.write('<table style="position:absolute; background-color:transparent;cursor: pointer;" cellpadding="0" cellspacing="0" width="{$banner_row->width}" height="{$banner_row->height}"><tr><td valign="top"><a href="{$link}" target="{$flash_target}"><img alt="" src="{$JURI_root}components/com_adagency/images/trans.gif" style="float: left; border: 0; width: {$banner_row->width}px; height: {$banner_row->height}px;" /></a></td></tr></table>{$adflash}</td></tr></table></div>');
eohtml;
					break;

				case 'Advanced':
				if(!isset($link_dfa)){
					$link = $mosConfig_live_site.'/index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
					$link = str_replace('//index.php','/index.php',$link);
				} else {
					$link = $link_dfa;
				}
				if(isset($zoneSettings->adparams['width'])&&($zoneSettings->adparams['width'] != NULL)) { $tWidth = $zoneSettings->adparams['width']+5; } else { $tWidth = NULL; }
				if(isset($zoneSettings->adparams['height'])&&($zoneSettings->adparams['height'] != NULL)) { $tHeight = $zoneSettings->adparams['height']+5; } else { $tHeight = NULL; }

				$banner_row->ad_code=str_replace("\r",'',$banner_row->ad_code);
				$banner_row->ad_code=str_replace("\n",'<br />',$banner_row->ad_code);
				$banner_row->ad_code=addslashes($banner_row->ad_code);
				if (preg_match('/ad_url/',$banner_row->ad_code)) {
					$banner_row->ad_code = str_replace('ad_url',$link.'" target="'.$banner_row->parameters['target_window'].'"',$banner_row->ad_code);
					echo <<<eohtml
					document.write('<div class="affiliate_ad" style="width:{$tWidth}px;height:{$tHeight}px;overflow:hidden;"><table align="center"><tr><td>{$banner_row->ad_code}</td></tr></table></div>');
eohtml;
} else {
			echo <<<eohtml
						document.write('<div class="affiliate_ad" style="width:{$tWidth}px;height:{$tHeight}px;overflow:hidden;"><table align="center"><tr><td><a href="{$link}" target="{$banner_row->parameters['target_window']}">{$banner_row->ad_code}</a></td></tr></table></div>');
eohtml;
		}			break;
					case 'Popup':
						$popupcode=str_replace('ad_url/','ad_url',$banner_row->ad_code);
						$popupcode=str_replace("\r\n",'',$popupcode);
						$popupcode=str_replace("\n",'',$popupcode);
						//html
						if ($banner_row->parameters["window_type"]=='popup') {
							$crt=1;
							$adcontent="";
							$string='ad_url"';
							$fisier=$popupcode;
							$pos1=strpos($fisier,$string);
							$poz=0;
							while ($pos1) {
								$link=$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid.'&lid='.$crt;
								$cont=substr($fisier,$poz,$pos1+strlen($string));
								$adcontent.=str_replace('ad_url"',$link.'" target="_blank"',$cont);
								$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
								$crt++;
								$pos1=strpos($fisier,$string);
							}

							$adcontent.=$fisier;
							$adcode = str_replace('color="','color="#',$adcontent);
							$adcode=str_replace('"images/','"'.$mosConfig_live_site.'/images/',$adcode);
							$adcode=addslashes($adcode);
							echo <<<eohtml
document.write('$adcode');
eohtml;
						}
						//end
						else {
							$popup=str_replace('ad_url',$banner_row->target_url,$popupcode);
							echo <<<eohtml
 document.write('$popup');
eohtml;
						}
						break;
					case 'Floating':
						$crt=1;
						$adcontent="";
						$string='ad_url"';
						$banner_row->ad_code = stripslashes($banner_row->ad_code);
						$fisier=str_replace('ad_url/','ad_url',$banner_row->ad_code);
						$pos1=strpos($fisier,$string);
						$poz=0;
						while ($pos1) {
							$link=$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid.'&lid='.$crt;
							$cont=substr($fisier,$poz,$pos1+strlen($string));
							$adcontent.=str_replace('ad_url"',$link.'" target="_blank"',$cont);
							$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
							$crt++;
							$pos1=strpos($fisier,$string);
						}
		      				$cookie_value = ".";
						  	$frequency = $banner_row->frequency;
							$cookie_name = "spl".md5( $banner_row->id );
					if ($frequency == "1" || $frequency == "7" || $frequency == "30" || $frequency == "365") {											$floatingcode = '<script type="text/javascript">
						function SetCookie(cookieName,cookieValue,nDays) {
							var today = new Date();
							var expire = new Date();
							if (nDays==null || nDays==0) nDays=1;
							expire.setTime(today.getTime() + 3600000*24*nDays);
							document.cookie = cookieName+"="+escape(cookieValue)
							+ "; path=/; expires="+expire.toGMTString();
						}

						</script>
						<script type="text/javascript">SetCookie("$cookie_name", "$cookie_value","$frequency");</script>';
						$floatingcode=str_replace("\r\n",'\\r\\n',$floatingcode);
						$floatingcode=str_replace("\n",'\\r\\n',$floatingcode);
						echo <<<eohtml
						document.write('$floatingcode');
eohtml;
						  }

						$adcontent.=addslashes($fisier);
						$adcode = str_replace('color="','color="#',$adcontent);
						$adcode=str_replace("initbox();",'\\r\\n',$adcode);
						$src='src="'.$mosConfig_live_site.'/images';
						$adcode=str_replace('src="images',$src,$adcode);
						$adcode=str_replace("var xxx;","initbox();",$adcode);
					    $adcode=str_replace("\r\n",'\\r\\n',$adcode);

						echo <<<eohtml
						document.write('$adcode');
eohtml;
						break;
					case 'Transition':
						$crt=1;
						$adcontent="";
						$string='ad_url"';
						$banner_row->ad_code = stripslashes($banner_row->ad_code);
						$fisier=str_replace('ad_url/','ad_url',$banner_row->ad_code);
						$pos1=strpos($fisier,$string);
						$poz=0;
						while ($pos1) {
							$link=$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid.'&lid='.$crt;
							$cont=substr($fisier,$poz,$pos1+strlen($string));
							$adcontent.=str_replace('ad_url"',$link.'" target="_blank"',$cont);
							$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
							$crt++;
							$pos1=strpos($fisier,$string);
						}

		      				$cookie_value = ".";
						  	$frequency = $banner_row->frequency;
							$cookie_name = "spl".md5( $banner_row->id );
					if ($frequency == "1" || $frequency == "7" || $frequency == "30" || $frequency == "365") {										$transitioncode = '<script type="text/javascript">
						function SetCookie(cookieName,cookieValue,nDays) {
							var today = new Date();
							var expire = new Date();
							if (nDays==null || nDays==0) nDays=1;
							expire.setTime(today.getTime() + 3600000*24*nDays);
							document.cookie = cookieName+"="+escape(cookieValue)
							+ "; path=/; expires="+expire.toGMTString();
						}

						</script>
						<script type="text/javascript">SetCookie("$cookie_name", "$cookie_value","$frequency");</script>';
						$transitioncode=str_replace("\r\n",'\\r\\n',$transitioncode);
						$transitioncode=str_replace("\n",'\\r\\n',$transitioncode);
						echo <<<eohtml
						document.write('$transitioncode');
eohtml;
						  }
						$adcontent.=$fisier;
						$adcode = str_replace('color="','color="#',$adcontent);
						$src='src="'.$mosConfig_live_site.'/images';
						$adcode=str_replace('src="images',$src,$adcode);
					    $adcode=str_replace("\r\n",'\\r\\n',$adcode);

						echo <<<eohtml
						document.write('$adcode');
eohtml;
						break;
				}
			}
			if($counter2==$banner_cols) {
				$variable='</td></tr><tr>';
				$counter2=1;
			} else {
				$variable='</td>';
				$counter2++;
			}

			$i++; $geocount++;
			echo <<<eohtml
						document.write('{$variable}');
eohtml;
		}

		echo <<<eohtml
						document.write('</td></tr>{$output_adv_bottom}</table></div>');
eohtml;
	} else {
		if(isset($adv_here_bottom)) {$advtt=$adv_here_bottom;} elseif (isset($adv_here_top)){$advtt=$adv_here_top;}
				echo <<<eohtml
						document.write('{$advtt}');
eohtml;
	}
}

function iJoomlaGetRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
		$ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
	// check if isset REMOTE_ADDR and != empty
    elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
    {
    	$ip = $_SERVER['REMOTE_ADDR'];
	// you're probably on localhost
    } else {
		$ip = "127.0.0.1";
	}
    return $ip;
}

function diversify($sss){
	if(!is_array($sss)) { return $sss; }
	$ary = array();$numring = 0;
	foreach($sss as $key=>$value){
		$ary[$numring] = $value->aid; $numring++;
	}

	$len = count($ary);
	$pos = array();
	if($len>=2){
		for($q=0;$q<=$len-1;$q++){
			$pos[$q] = $q;
		}
		for($i=1;$i<=$len-2;$i++){
			//$haystack = array_slice($ary,$i+1,$len-$i-1,true);
			$haystack = array_slice($ary,0,$i,true);
			//echo "<pre>";var_dump($haystack);die();
			if(in_array($ary[$i],$haystack)){
				$found = false;
				for($j=$i+1;$j<=$len-1;$j++){
					if((!in_array($ary[$j],$haystack))&&(!$found)){
						$aux = $ary[$i];
						$ary[$i] = $ary[$j];
						$ary[$j] = $aux;

						$aux2 = $pos[$i];
						$pos[$i] = $pos[$j];
						$pos[$j] = $aux2;

						$found = true;
						break;
					}
				}
			}
		}
		for($i=1;$i<=$len-2;$i++){
				if($ary[$i-1] == $ary[$i]) {
					$found = false;
					for($j=$i+1;$j<=$len-1;$j++){
						if((!$found)&&($ary[$i] != $ary[$j])) {
							$found = true;
							$aux = $ary[$j];
							$ary[$j] = $ary[$i];
							$ary[$i] = $aux;

							$aux2 = $pos[$i];
							$pos[$i] = $pos[$j];
							$pos[$j] = $aux2;
							break;
						}
					}
				}
			}
		} else {
			return $sss;
		}

	if(isset($pos)&&is_array($pos)){
		foreach($pos as $key=>$val){
			$pos2[$key] = $sss[$val];
		}
	}
	//echo "<pre>";var_dump($pos);echo "<hr />";var_dump($ary);die();
	//return $ary;
	return $pos2;
}
?>
