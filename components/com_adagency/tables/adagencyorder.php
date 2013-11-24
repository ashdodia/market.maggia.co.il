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

class TableadagencyOrder extends JTable {
	var $oid = null;
	var $tid = null;
	var $aid = null;
	var $type = null;
	var $quantity = null;
	var $cost = null;
	var $order_date = null;
	var $payment_type = null;
	var $card_number = null;
	var $expiration = null;
	var $card_name = null;
	var $notes = null;
	var $status = null;
	var $pack_id = null;


	function TableadagencyOrder (&$db) {
		parent::__construct('#__ad_agency_order', 'oid', $db);
	}

};


?>