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

class TableadagencyPackage extends JTable {
	var $tid = null;
	var $description = null;
	var $quantity = null;
	var $type = null;
	var $cost = null;
	var $validity = null;
	var $visibility = null;
	var $sid = null;
	var $zones = null;
	var $pack_description = null;

	function TableadagencyPackage (&$db) {
		parent::__construct('#__ad_agency_order_type', 'tid', $db);
	}

};


?>