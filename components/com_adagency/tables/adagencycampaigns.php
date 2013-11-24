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

class TableadagencyCampaigns extends JTable {
	var $id = null;
	var $aid = null;
	var $name = null;
	var $notes = null;
	var $default = null;
	var $start_date = null;
	var $type = null;
	var $quantity = null;
	var $validity = null;
	var $cost = null;
	var $otid = null;
	var $approved = null;
	var $status = null;
	var $exp_notice = null;
	var $key = null; 
    var $params = null;
	var $renewcmp = null;

	function TableadagencyCampaigns (&$db) {
		parent::__construct('#__ad_agency_campaign', 'id', $db);
	}

};


?>