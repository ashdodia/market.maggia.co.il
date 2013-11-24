<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */

function VmvendorBuildRoute(&$query) 
{
	$segments = array();
	$db		  = & JFactory::getDBO();
	if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();

	if(isset($query['view']))
	{
		if(empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}
		
		if($query['view'] == 'vendorprofile' || $query['view'] == 'dashboard' || $query['view'] == 'editprofile'  || $query['view'] == 'editproduct' || $query['view'] == 'addproduct'  || $query['view'] == 'askvendor'  || $query['view'] == 'mailcustomer'   || $query['view'] == 'edittax'  || $query['view'] == 'catsuggest') {
			$segments[] = $query['view'];
		}

		unset($query['view']);
	}
	
	
	
	if(isset($query['userid']))
	{			
		//$segments[] = $query['userid'];

		 
		 $sqlQuery = "SELECT vvl.`vendor_store_name` 
		FROM `#__virtuemart_vendors_".VMLANG."` AS vvl
		 LEFT JOIN `#__virtuemart_vmusers` vvu ON vvu.`virtuemart_vendor_id` = vvl.`virtuemart_vendor_id` 
		 WHERE vvu.`virtuemart_user_id` ='".$query['userid']."' ";
		$db->setQuery($sqlQuery);				
		$segments[] = urlencode($query['userid'].'-'.$db->loadResult());		
		unset($query['userid']);
	}
	
	return $segments;
}

function VmvendorParseRoute($segments)
{
	$vars = array();
	$db	= & JFactory::getDBO();
	if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
	
	// Count route segments
	$count = count($segments);	
	
	if ( $count ) {
	
		if($segments[0] == 'vendorprofile') {
			$vendor_store_name = urldecode($segments[$count-1]);
			$strpos = strpos( $vendor_store_name , ':');
			$vendor_stotre_name = substr($vendor_store_name, $strpos + 1);
			$vars['view'] = 'vendorprofile';
			$sqlQuery = "SELECT vvu.`virtuemart_user_id` 
			FROM `#__virtuemart_vmusers` vvu
			LEFT JOIN `#__virtuemart_vendors_".VMLANG."` vvl ON vvl.`virtuemart_vendor_id` = vvu.`virtuemart_vendor_id`
			WHERE vvl.`vendor_store_name`='".$vendor_stotre_name."' LIMIT 1  ";
			$db->setQuery($sqlQuery);
			//$vars['userid'] = $segments[$count-1];
			$vars['userid'] = $db->loadResult();
			//$vars['task'] = $segments[$count-2];
			return $vars;
		}
		elseif($segments[0] == 'editprofile') {
			$vars['view'] = 'editprofile';
			
			return $vars;
		}
		elseif($segments[0] == 'addproduct') {
			$vars['view'] = 'addproduct';
			
			return $vars;
		}
		elseif($segments[0] == 'editproduct') {
			$vars['view'] = 'editproduct';
			
			return $vars;
		}
		elseif($segments[0] == 'dashboard') {
			$vars['view'] = 'dashboard';
			
			return $vars;
		}
		elseif($segments[0] == 'askvendor') {
			$vars['view'] = 'askvendor';
			
			return $vars;
		}
		elseif($segments[0] == 'mailcustomer') {
			$vars['view'] = 'mailcustomer';
			
			return $vars;
		}
		elseif($segments[0] == 'edittax') {
			$vars['view'] = 'edittax';
			
			return $vars;
		}
		elseif($segments[0] == 'catsuggest') {
			$vars['view'] = 'catsuggest';
			
			return $vars;
		}
	
		
		
	}
}
?>