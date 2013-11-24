<?php

define( '_JEXEC', 1 );
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "components")),0,-1));
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

$banner_id = JRequest::getInt('banner_id');
$advertiser_id = JRequest::getInt('advertiser_id');
$campaign_id = JRequest::getInt('campaign_id');
$type = JRequest::getVar('type');

$sql = "SELECT limit_ip FROM #__ad_agency_settings LIMIT 1";
$database->setQuery($sql);
$limit_ip = $database->loadResult();

//reduce table size
$time_interval = date("Y-m-d");

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

$the_ipp = ip2long($ip);

$sql = "SELECT how_many FROM #__ad_agency_stat WHERE substring(entry_date,1,10)='".$time_interval."' and ip_address='".$the_ipp."' AND campaign_id='".$campaign_id."' AND banner_id='".$banner_id."' AND `type`='impressions' limit 1";

$database->setQuery($sql);
if (!$database->query()) {
	echo $database->stderr();
	return;
}		
				
				
if ($database->getNumRows()) {
	$how_many = $database->loadResult();
	$how_many++; 
	if($how_many <= $limit_ip) {
		$class_helper = new adagencyAdminHelper;
		$ip_is_private = $class_helper->ip_is_private($the_ipp);
		if(!$ip_is_private){
			$sql = "UPDATE #__ad_agency_stat SET `how_many` = '".$how_many."' WHERE substring(entry_date,1,10)='".$time_interval."' AND ip_address='".$the_ipp."' AND campaign_id='".$campaign_id."' AND banner_id='".$banner_id."' AND `type`='impressions' limit 1";
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
		}
	}
} else { 
		$how_many = 1;
		// insert is made only if from the same IP during current day are no records
		// v.1.5.3 update - user_agent in no longer kept as a record
		$class_helper = new adagencyAdminHelper;
		$ip_is_private = $class_helper->ip_is_private($the_ipp);
		if(!$ip_is_private){
			$sql = "INSERT INTO #__ad_agency_stat SET entry_date=NOW(), ip_address='".$the_ipp."', advertiser_id='".$advertiser_id."', campaign_id='".$campaign_id."', banner_id='".$banner_id."', `type`='impressions', `how_many`='1'";
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
		}
}	

if ('cpm'==$type) {
	if($how_many <= $limit_ip) {
		$sql = "UPDATE #__ad_agency_campaign SET quantity = quantity-1 WHERE quantity > 0 AND id=".$campaign_id;
		$database->setQuery($sql);
		if (!$database->query()) {
			echo $database->stderr();
			return;
		}
	}

	$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".$campaign_id;
	$database->setQuery($sql);
	if (!$database->query()) {
		echo $database->stderr();
		return;
	}
	$quantity = $database->loadResult();
	
	if(($quantity == 0)&&($type!='fr')){
		$nowdatetime = date("Y-m-d H:i:s");
		$sql = "UPDATE #__ad_agency_campaign SET validity = '".$nowdatetime."' WHERE id=".$campaign_id;
		$database->setQuery($sql);
		if (!$database->query()) {
			echo $database->stderr();
			return;
		}
	}			
}

?>