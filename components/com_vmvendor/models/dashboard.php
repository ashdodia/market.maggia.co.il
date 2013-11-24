<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


class VmvendorModelDashboard extends JModelItem
{
	/**
	 * @var string msg
	 */
	//protected $msg;
 
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	public function getMysales() 
	{
		$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$naming 		= $cparams->getValue('naming', 'username');
		$profileman 	= $cparams->getValue('profileman');
		$db = &JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$app = JFactory::getApplication();
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_vmvendor.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$q = "SELECT DISTINCT(voi.virtuemart_order_item_id) , voi.virtuemart_product_id , voi.order_item_sku , voi.order_item_name , voi.product_quantity, voi.product_item_price , voi.order_status, 
		vpc.virtuemart_category_id ,
		vp.virtuemart_vendor_id , 
		vo.virtuemart_user_id , vo.order_number , vo.order_currency , vo.customer_note , vo.created_on ,
		vu.address_type , vu.name , vu.company , vu.title , vu.last_name , vu.first_name , vu.middle_name , vu.address_1 , vu.address_2 , vu.phone_1 , vu.phone_2 , vu.city ,  vu.zip , 
		u.email ,
		vc.country_name ,
		vs.state_name 
		FROM #__virtuemart_order_items voi
		LEFT JOIN `#__virtuemart_product_categories` vpc ON vpc.virtuemart_product_id = voi.virtuemart_product_id 
		LEFT JOIN #__virtuemart_products vp ON vp.virtuemart_product_id = voi.virtuemart_product_id 
		LEFT JOIN #__virtuemart_vmusers vv ON vv.virtuemart_vendor_id = vp.virtuemart_vendor_id 
		LEFT JOIN #__virtuemart_orders vo ON vo.virtuemart_order_id = voi.virtuemart_order_id 
		LEFT JOIN #__virtuemart_userinfos vu ON vu.virtuemart_user_id = vo.virtuemart_user_id 
		LEFT JOIN #__users u ON u.id = vo.virtuemart_user_id 
		LEFT JOIN #__virtuemart_countries vc ON vc.virtuemart_country_id = vu.virtuemart_country_id 
		LEFT JOIN #__virtuemart_states vs ON vs.virtuemart_state_id = vu.virtuemart_state_id 
		WHERE vv.virtuemart_user_id = '".$user->id."' 
		AND vo.order_status='C' 
		GROUP BY voi.virtuemart_order_item_id 
		ORDER BY voi.virtuemart_order_item_id DESC ";
		$db->setQuery($q);
		//$this->mysales = $db->loadObjectList();
		
		$total = @$this->_getListCount($q);
		$mysales = $this->_getList($q, $limitstart, $limit);
		return array($mysales, $total, $limit, $limitstart);
		
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
	
	/*public function getColumnchart() 
	{
		$user 		= & JFactory::getUser();
		$db = &JFactory::getDBO();
		$start_date = JRequest::getVar('start_date');
		if(!$start_date)
			$start_date= date('Y-m').'-01 00:00:00';
		$end_date = JRequest::getVar('end_date');
		if(!$end_date)
			$end_date= date('Y-m-d H:i:s');
		$time_unit = JRequest::getVar('time_unit');
		if(!$time_unit)
			$time_unit = 'days';
		$subject = JRequest::getVar('subject');
		if(!$subject)
			$subject = 'revenue';
			
			
			
			
		if($time_unit == 'days')
			$orderby = 'SUBSTR( voi.created_on , 0, 10)';
		elseif($time_unit == 'months')
			$orderby = 'SUBSTR( voi.created_on , 0, 7)';
		elseif($time_unit == 'years')
			$orderby = 'SUBSTR( voi.created_on , 0, 4)';
		
		echo $q = "SELECT voi.virtuemart_order_item_id , voi.virtuemart_product_id ,  voi.product_quantity, voi.product_item_price ,
		vp.virtuemart_vendor_id , 
		vo.virtuemart_user_id , vo.order_number , vo.order_currency , vo.customer_note , vo.created_on ,
		vu.city ,  vu.zip , 
		vc.country_name ,
		vs.state_name 
		FROM #__virtuemart_order_items voi 
		LEFT JOIN #__virtuemart_products vp ON vp.virtuemart_product_id = voi.virtuemart_product_id 
		LEFT JOIN #__virtuemart_vmusers vv ON vv.virtuemart_vendor_id = vp.virtuemart_vendor_id 
		LEFT JOIN #__virtuemart_orders vo ON vo.virtuemart_order_id = voi.virtuemart_order_id 
		LEFT JOIN #__virtuemart_userinfos vu ON vu.virtuemart_user_id = vo.virtuemart_user_id 
		LEFT JOIN #__users u ON u.id = vo.virtuemart_user_id 
		LEFT JOIN #__virtuemart_countries vc ON vc.virtuemart_country_id = vu.virtuemart_country_id 
		LEFT JOIN #__virtuemart_states vs ON vs.virtuemart_state_id = vu.virtuemart_state_id 
		WHERE vv.virtuemart_user_id = '".$user->id."' AND vo.order_status='C' 
		AND voi.created_on >= '".$start_date."' 
		AND voi.created_on <= '".$end_date."' 
		GROUP BY ".$orderby." 
		ORDER BY voi.virtuemart_order_item_id DESC 
		 ";
		$db->setQuery($q);
		
		
		$this->column_chart = $db->loadObjectList();
		return $this->column_chart;
	}*/
	
	
	public function getMyreviews() 
	{
		$db = &JFactory::getDBO();
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$naming 		= $cparams->getValue('naming', 'username');
		
		$user 		= & JFactory::getUser();
		$app = JFactory::getApplication();
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_vmvendor.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$q ="SELECT vrr.virtuemart_rating_review_id , vrr.virtuemart_product_id, vrr.comment , vrr.review_rating , vrr.created_on ,
		vrr.published , 
		u.id , u.".$naming." , 
		vpc.virtuemart_category_id , 
		vp.product_sku ,
		vpl.product_name
		 FROM `#__virtuemart_rating_reviews` vrr 
		 
		 LEFT JOIN #__users u ON u.id = vrr.created_by 
		 LEFT JOIN `#__virtuemart_product_categories` vpc ON vpc.virtuemart_product_id = vrr.virtuemart_product_id 
		 LEFT JOIN #__virtuemart_products vp ON vp.virtuemart_product_id = vrr.virtuemart_product_id 
		 LEFT JOIN #__virtuemart_products_".VMLANG." vpl ON vpl.virtuemart_product_id = vrr.virtuemart_product_id 
		 LEFT JOIN #__virtuemart_vendors vv ON vv.virtuemart_vendor_id = vp.virtuemart_vendor_id 
		 WHERE vv.created_by='".$user->id."'
		 ORDER BY vrr.published ASC , vrr.virtuemart_rating_review_id DESC " ;		
		$db->setQuery($q);
		// LEFT JOIN `#__virtuemart_ratings` vr ON vr.virtuemart_rating_id = vrr.virtuemart_rating_review_id 
		//$this->reviews = $db->loadObjectList();
		// AND vrr.published='1' AND vp.published='1' 
		$total_reviews = @$this->_getListCount($q);
		$myreviews = $this->_getList($q, $limitstart, $limit);
		return array($myreviews, $total_reviews, $limit, $limitstart);
		
		
		//return $this->myreviews;
	}
	
	
	
	
	
	public function getMyTaxes() 
	{
		$db = &JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$q = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vendors` WHERE `created_by`='".$user->id."' "; 
		$db->setQuery($q);
		$my_vendorid = $db->loadresult();
		$q ="SELECT vc.`virtuemart_calc_id` , vc.`virtuemart_vendor_id`,  vc.`calc_name` , vc.`calc_descr` ,
		 vc.`calc_kind` , vc.`calc_value_mathop` , vc.`calc_value` , vc.`calc_currency` ,  vc.`ordering`  ,  vc.`shared` 
			FROM `#__virtuemart_calcs` vc 
			WHERE vc.`published`='1' 
			AND (vc.`shared` ='1' OR  vc.`virtuemart_vendor_id`='".$my_vendorid."' ) 
			AND vc.`calc_vendor_published` ='1' 
			AND (vc.`publish_up`='0000-00-00 00:00:00' OR vc.`publish_up` <= NOW() )  
			AND (vc.`publish_down`='0000-00-00 00:00:00' OR vc.`publish_down` >= NOW() ) 	
			ORDER BY vc.`shared` DESC , vc.`ordering` ASC , vc.`virtuemart_calc_id` ASC ";
		$db->setQuery($q);
		$this->mytaxes = $db->loadObjectList();
		return $this->mytaxes;

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