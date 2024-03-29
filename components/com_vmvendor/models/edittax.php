<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


class VmvendorModelEdittax extends JModelItem
{
	/**
	 * @var string msg
	 */
	//protected $msg;
 
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	/*public function getCoords() 
	{
		$db = &JFactory::getDBO();
		$q = "SELECT metakey FROM #__content WHERE id=".JRequest::getVar('a_id');
		$db->setQuery($q);
		$coords = $db->loadResult();
		$this->_coords = $coords;
		return $this->_coords;
	}*/
	public function getThistaxdata() 
	{
		$taxid = JRequest::getVar('taxid');
		$db = &JFactory::getDBO();
		$user = &JFactory::getUser();
		
		$q = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vendors` WHERE `created_by`='".$user->id."' "; 
		$db->setQuery($q);
		$my_vendorid = $db->loadResult();
		
		
		$q ="SELECT  `virtuemart_calc_id`, `calc_name` , `calc_descr` , `calc_kind` , `calc_value_mathop` , `calc_value` , `calc_currency` , `publish_up` , `publish_down` 
		FROM `#__virtuemart_calcs` 		
		WHERE `published`='1' 
		AND `shared` ='0'
		AND `virtuemart_vendor_id`='".$my_vendorid."' 
		AND `calc_vendor_published` ='1' 
		AND (`publish_up`='0000-00-00 00:00:00' OR `publish_up` <= NOW() )  
		AND (`publish_down`='0000-00-00 00:00:00' OR `publish_down` >= NOW() ) 
		AND `virtuemart_calc_id` ='".$taxid."' ";
		$db->setQuery($q);
		$taxdata 	= $db->loadRow();
		$this->taxdata = $taxdata;
		return $this->taxdata;
	}
	
	
	public function getThistaxcats() 
	{
		$taxid = JRequest::getVar('taxid');
		$db = &JFactory::getDBO();
		$q ="SELECT  virtuemart_category_id FROM `#__virtuemart_calc_categories`  WHERE virtuemart_calc_id ='".$taxid."' ";
		$db->setQuery($q);
		$taxcats 	= $db->loadObjectList();
		$taxcats_ids = array();
		foreach ($taxcats as $taxcat){
			array_push($taxcats_ids ,  $taxcat->virtuemart_category_id);	
		}	
		$this->tax_cats = $taxcats_ids;
		return $this->tax_cats;
	}
	
	
	public function getVendorshoppergroups() 
	{  // shared shoppergroups or vendor's own
		$db = &JFactory::getDBO();
		$user = &JFactory::getUser();
		
		$q = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vendors` WHERE `created_by`='".$user->id."' "; 
		$db->setQuery($q);
		$my_vendorid = $db->loadResult();
		
		$db = &JFactory::getDBO();
		$q ="SELECT  virtuemart_shoppergroup_id ,  shopper_group_name 
		FROM `#__virtuemart_shoppergroups` 
		WHERE ( shared ='1' OR virtuemart_vendor_id='".$my_vendorid."' ) 
		AND published ='1'  ";
		$db->setQuery($q);
		$shoppergroups 	= $db->loadObjectList();
		$shoppergroups_ids = array();
		foreach ($shoppergroups as $shoppergroup){
			array_push( $shoppergroups_ids ,  $shoppergroup->virtuemart_shoppergroup_id);	
		}	
		$this->vendor_shoppergroups = $shoppergroups_ids;
		//$this->vendor_shoppergroups = $shoppergroups;
		//var_dump($this->vendor_shoppergroups);
		return 	 $this->vendor_shoppergroups;
	}
	
	
		public function getVendorid() 
	{
		$db = &JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$q = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id` = '".$user->id."' ";
		$db->setQuery($q);
		$virtuemart_vendor_id = $db->loadResult();
		$this->_virtuemart_vendor_id = $virtuemart_vendor_id;
		return $this->_virtuemart_vendor_id;
	}
}



?>