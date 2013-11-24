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
jimport ('joomla.application.component.controller');

class adagencyController extends JController {
	var $_customer = null;
	function __construct() {
		parent::__construct();
		$this->checkCampaignsExpiration();
	}

	function display () {
		parent::display();	
	}

	function setclick($msg = '') {
	}
	
	function checkCampaignsExpiration(){
		$db =& JFactory::getDBO();
		$date_today = date("Y-m-d");
		
		$sql = "select `last_check_date` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$last_check_date = $db->loadResult(); 
		if($last_check_date != $date_today." 00:00:00"){
			$sql = "select id from #__ad_agency_campaign where `validity` like '".$date_today."%'";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadResultArray();
			if(isset($result) && is_array($result) && count($result) > 0){
				$sql = "update #__ad_agency_campaign set `activities` = concat(activities, 'Expired - ".date("Y-m-d H:i:s")."', ';') where id in (".implode(",", $result).")";
				$db->setQuery($sql);
				$db->query();
			}
			
			$sql = "update #__ad_agency_settings set `last_check_date`='".$date_today." 00:00:00'";
			$db->setQuery($sql);
			$db->query();
		}
	}
};
?>

