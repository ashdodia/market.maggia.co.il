<?php

/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
defined ("_JEXEC") or die ("Go away.");

class TableadagencyAdvertiser extends JTable {
	var $aid = null;
	var $user_id = null;
	var $company = null;
	var $description = null;
	var $website = null;
	var $address = null;
	var $country = null;
	var $city = null;
	var $state = null;
	var $zip = null;
	var $telephone = null;
	var $fax = null;
	var $logo = null;
	var $email_daily_report = null;
	var $email_weekly_report = null;
	var $email_month_report = null;
	var $email_campaign_expiration = null;
	var $approved = null;
	var $lastreport = null;
	var $weekreport = null;
	var $monthreport = null;
	var $paywith = null;
	var $show = null;
	var $mandatory = null;
	var $key = null;

	function TableadagencyAdvertiser (&$db) {
		parent::__construct('#__ad_agency_advertis', 'aid', $db);
	}

	function load ($id = 0) {
		$db =& JFactory::getDBO();
		$sql = "select aid from #__ad_agency_advertis where user_id='".$id."'";
		//echo  $sql;die();
		$db->setQuery($sql);
		$realid = $db->loadResult();
		$post_cid = JRequest::getInt('cid');
		if (isset($post_cid)) $realid = $id;
		parent::load($realid);
	}

	function store(){ 
		$db = JFactory::getDBO(); 
		parent::store();
		return true;
	}

};


?>