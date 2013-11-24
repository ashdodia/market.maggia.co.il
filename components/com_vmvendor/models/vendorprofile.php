<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class VmvendorModelVendorprofile extends JModelItem
{
	/**
	 * @var string msg
	 */
	//protected $msg;
 
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	public function getMyproducts() 
	{
		//$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$db = &JFactory::getDBO();
		$user 			= &JFactory::getUser();
		$userid = JRequest::getVar('userid');
		if(!$userid  )
			$userid = $user->id;
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		
		
		$app = JFactory::getApplication();
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_vmvendor.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$q = "SELECT DISTINCT(vp.`virtuemart_product_id`) , vp.`product_sku` , vp.`product_in_stock` , 
		vpl.`product_s_desc` , vpl.`product_name` ,  
		vpc.`virtuemart_category_id` ,
		vcl.`category_name` ,
		vpp.`product_price` 
		FROM `#__virtuemart_products` vp 
		JOIN `#__virtuemart_products_".VMLANG."` vpl ON vpl.`virtuemart_product_id` = vp.`virtuemart_product_id`
		JOIN `#__virtuemart_vmusers` vv ON vv.`virtuemart_vendor_id` = vp.`virtuemart_vendor_id` 
		JOIN `#__virtuemart_product_prices` vpp ON vpp.`virtuemart_product_id` = vp.`virtuemart_product_id` 
		JOIN `#__virtuemart_product_categories` vpc ON vpc.`virtuemart_product_id` = vp.`virtuemart_product_id` 
		JOIN `#__virtuemart_categories_".VMLANG."` vcl ON vcl.`virtuemart_category_id` = vpc.`virtuemart_category_id` 		
		WHERE vv.`virtuemart_user_id` = '".$userid."' 
		AND vp.`virtuemart_vendor_id`!='0' 
		AND vp.`published`='1' 
		AND vp.`product_parent_id` ='0' 
		GROUP BY vp.`virtuemart_product_id` 
		ORDER BY vp.`virtuemart_product_id` DESC ";
		$db->setQuery($q);
		//$this->mysales = $db->loadObjectList();
		
		$total = @$this->_getListCount($q);
		$myproducts = $this->_getList($q, $limitstart, $limit);
		return array($myproducts, $total, $limit, $limitstart);
		
		//return $this->mysales;
	}
	
	
	public function getCurrency() 
	{
		$db = &JFactory::getDBO();
		$q ="SELECT vc.`currency_symbol` , vc.`currency_positive_style` , vc.`currency_decimal_place` , vc.`currency_decimal_symbol` , vc.`currency_thousands` 
		FROM `#__virtuemart_currencies` vc 
		LEFT JOIN `#__virtuemart_vendors` vv ON vv.`vendor_currency` = vc.`virtuemart_currency_id` 
		WHERE vv.`virtuemart_vendor_id` ='1' " ;		
		$db->setQuery($q);
		$this->main_currency = $db->loadRow();
		return $this->main_currency;
	}
	
	public function getVendordata() 
	{
		$user 			= &JFactory::getUser();
		$userid = JRequest::getVar('userid');
		if(!$userid)
			$userid = $user->id;
		$db = &JFactory::getDBO();
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		
		$q ="SELECT  vvl.`vendor_store_desc` , vvl.`vendor_terms_of_service` , vvl.`vendor_legal_info` , vvl.`vendor_store_name` , vvl.`vendor_phone` , vvl.`vendor_url` ,
		vv.`virtuemart_vendor_id` 
		FROM `#__virtuemart_vendors_".VMLANG."` vvl 
		LEFT JOIN `#__virtuemart_vmusers` vv ON vv.`virtuemart_vendor_id` = vvl.`virtuemart_vendor_id` 
		WHERE vv.`virtuemart_user_id`='".$userid."' " ;	
		$db->setQuery($q);
		$this->vendor_data = $db->loadRow();
		return $this->vendor_data;
	}
	
	public function getUserthumb() 
	{
		$db = &JFactory::getDBO();
		$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$profileman 	= $cparams->getValue('profileman');
		$naming 	= $cparams->getValue('naming');
		$user 			= &JFactory::getUser();
		$userid = JRequest::getVar('userid');
		if(!$userid)
			$userid = $user->id;
		$q = "SELECT u.`".$naming."` , ";
		if($profileman==1)
			$q .=" c.`avatar` ";
		elseif($profileman==2)
			$q .=" c.`thumb` AS avatar ";
		elseif($profileman==3)
			$q .=" aup.`avatar` , referreid ";
		
		$q .=" FROM `#__users` u ";
		if($profileman==1)
			$q .=" LEFT JOIN `#__comprofiler` c ON c.`user_id` = u.`id`";
		elseif($profileman==2)
			$q .=" LEFT JOIN `#__community_users` c ON c.`userid` = u.`id`";
		elseif($profileman==3)
			$q .=" LEFT JOIN `#__alpha_userpoints` aup ON aup.`userid` = u.`id`";
		$q .=" WHERE u.`id` = '".$userid."' ";
		$db->setQuery($q);
		$this->user_thumb = $db->loadRow();
		return $this->user_thumb;
	}
	
	public function getJSProfileallowed($profiletypes_ids) 
	{
		$db = &JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$profiletypes_mode 	= $cparams->getValue('profiletypes_mode');
		
		$allowed = 0;
		if($profiletypes_mode==1)
			$q = "SELECT profile_id FROM #__community_users WHERE userid='".$user->id."' ";
		if($profiletypes_mode==2)
			$q = "SELECT profiletype FROM #__xipt_users WHERE userid='".$user->id."' ";
		$db->setQuery($q);
		$user_profile_id = $db->loadResult();
		$allowedprofiles_array = array();
		if(strpos( $profiletypes_ids , ',' ) ){
			$exploded = explode( ',' , $profiletypes_ids);
			$count = count($exploded);
			for($i= 0 ; $i < $count ; $i++ ){
				$allowedprofiles_array[] = $exploded[$i];	
			}		  
		}
		else
			$allowedprofiles_array[] = $profiletypes_ids;
		if(  in_array ($user_profile_id, $allowedprofiles_array ) )
			$allowed	= 1 ;
		return $allowed;
	}
	
}



?>