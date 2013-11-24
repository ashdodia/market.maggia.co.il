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

class TableadagencyAds extends JTable {
	var $id = null;
	var $advertiser_id = null;
	var $title = null;
	var $description = null;
	var $media_type = null;
	var $image_url = null;
	var $swf_url = null;
	var $target_url = null;
	var $width = null;
	var $height = null;
	var $ad_code = null;
	var $use_ad_code_in_netscape = null;
	var $ad_code_netscape = null;
	var $parameters = null;
	var $approved = null;
	var $zone = null;
	var $frequency = null;
	var $created = null;
	var $key = null;
	var $channel_id = null;
	
	function TableadagencyAds (&$db) {
		parent::__construct('#__ad_agency_banners', 'id', $db);
	}

	function load($id = 0) {
		parent::load($id);
		
	}
	
};


?>