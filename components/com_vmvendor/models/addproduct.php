<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


class VmvendorModelAddproduct extends JModelItem
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
	public function getPriceformat() 
	{
		$db = &JFactory::getDBO();
		$q ="SELECT curr.*  
		FROM `#__virtuemart_currencies` AS curr 
		LEFT JOIN `#__virtuemart_vendors` AS vend ON vend.`vendor_currency` = curr.`virtuemart_currency_id`  
		WHERE vend.`virtuemart_vendor_id` = '1' ";
		$db->setQuery($q);
		$price_format 	= $db->loadRow();
		$this->_price_format = $price_format;
		return $this->_price_format;
	}
	
	public function getCorecustomfields() 
	{
		$db = &JFactory::getDBO();
		$q ="SELECT `virtuemart_custom_id` , `custom_parent_id` , `virtuemart_vendor_id` , `custom_jplugin_id` , `custom_title` , `custom_tip` , `custom_value`, `custom_field_desc` , `field_type` , `is_list` , `shared`  
		FROM `#__virtuemart_customs`
		WHERE `custom_jplugin_id`='0' 
		AND `admin_only`='0' 
		AND `published`='1' 
		AND `custom_element`!='' 
		ORDER BY `ordering` ASC , `virtuemart_custom_id` ASC ";
		$db->setQuery($q);
		$core_custom_fields	= $db->loadObjectList();
		$this->_core_custom_fields = $core_custom_fields;
		return $this->_core_custom_fields;
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
	
	public function getManufacturers() 
	{
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$db = &JFactory::getDBO();
		$q = "SELECT vm.virtuemart_manufacturer_id ,
		vml.mf_name , vml.mf_desc
		FROM #__virtuemart_manufacturers vm 
		LEFT JOIN #__virtuemart_manufacturers_".VMLANG." vml ON vm.virtuemart_manufacturer_id = vml.virtuemart_manufacturer_id
		WHERE vm.published='1' ORDER BY mf_name ASC ";
		$db->setQuery($q);
		$manufacturers = $db->loadObjectList();
		return $manufacturers;
	}
	
	
}
?>