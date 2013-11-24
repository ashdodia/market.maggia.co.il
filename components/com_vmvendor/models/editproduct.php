<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


class VmvendorModelEditproduct extends JModelItem
{
	/**
	 * @var string msg
	 */
	//protected $msg;
 
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */

	public function getProductdata() 
	{
		$db = &JFactory::getDBO();
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$virtuemart_product_id		= JRequest::getVar('productid','','get');		
		$q = "SELECT vp.`virtuemart_vendor_id` , vp.`product_sku` , vp.`product_weight` , vp.`product_weight_uom` , vp.`product_in_stock` , 
		vpl.`product_s_desc` , vpl.`product_desc` , vpl.`product_name` , 
		vpc.`virtuemart_category_id` , 
		vpp.`product_price` 
		FROM `#__virtuemart_products` vp 
		LEFT JOIN `#__virtuemart_products_".VMLANG."` vpl ON vpl.`virtuemart_product_id` = vp.`virtuemart_product_id`  
		LEFT JOIN `#__virtuemart_product_categories` vpc ON vpc.`virtuemart_product_id` = vpl.`virtuemart_product_id` 
		LEFT JOIN `#__virtuemart_product_prices` vpp ON vpp.`virtuemart_product_id` = vpl.`virtuemart_product_id` 
		WHERE vp.`virtuemart_product_id` =".$virtuemart_product_id." ";
		$db->setQuery($q);
		$product_data = $db->loadRow();
		$this->_product_data = $product_data;
		return $this->_product_data;
	}

	public function getProductimages() 
	{
		$db = &JFactory::getDBO();
		$virtuemart_product_id		= JRequest::getVar('productid','','get');			
		$q = "SELECT vm.`file_title` , vm.`virtuemart_media_id`, vm.`file_url` , vm.`file_url_thumb` 
		FROM `#__virtuemart_medias` vm 
		LEFT JOIN `#__virtuemart_product_medias` vpm on vpm.`virtuemart_media_id` = vm.`virtuemart_media_id`  
		WHERE vpm.`virtuemart_product_id` =".$virtuemart_product_id." 
		AND vm.`file_mimetype` LIKE 'image/%' 
		AND vm.`file_is_product_image`='1'
		ORDER BY vm.`virtuemart_media_id` ASC";
		$db->setQuery($q);
		$product_images = $db->loadObjectList();
		$this->_product_images = $product_images;
		return $this->_product_images;
	}


	public function getProductfiles() 
	{
		$db = &JFactory::getDBO();
		$virtuemart_product_id		= JRequest::getVar('productid','','get');
		// Get price first to know where to look for files
		$q ="SELECT `product_price` FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_id`='".$virtuemart_product_id."' ";
		$db->setQuery($q);
		$product_price = $db->loadResult();
		
		if($product_price >0){ // product is not free, file is stored in the st42_download customfield table
			$q = "SELECT vm.`file_title` , vm.`virtuemart_media_id`, vm.`file_url`  
			FROM `#__virtuemart_medias` vm 
			LEFT JOIN `#__virtuemart_product_customfields` vpc ON vpc.`custom_param` LIKE CONCAT('%' , CONCAT('\"media_id\":\"' , vm.`virtuemart_media_id` , '\"') , '%' )
			WHERE vpc.`virtuemart_product_id` =".$virtuemart_product_id." 
			AND ( vm.`file_is_downloadable`='1' OR vm.`file_is_forSale`='1' ) 
			ORDER BY vm.`virtuemart_media_id` ASC";
			$db->setQuery($q);
			$product_files = $db->loadObjectList();
		}
		else{ // product is free, file is stored in the products media table
			$q = "SELECT vm.`file_title` , vm.`virtuemart_media_id`, vm.`file_url`  
			FROM `#__virtuemart_medias` vm 
			LEFT JOIN `#__virtuemart_product_medias` vpm on vpm.`virtuemart_media_id` = vm.`virtuemart_media_id`  
			WHERE vpm.`virtuemart_product_id` =".$virtuemart_product_id." 
			AND ( vm.`file_is_downloadable`='1' OR vm.`file_is_forSale`='1' ) 
			ORDER BY vm.`virtuemart_media_id` ASC";
			$db->setQuery($q);
			$product_files = $db->loadObjectList();
		}
		
		$this->_product_files = $product_files;
		return $this->_product_files;
	}
	
	
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
	
	
	public function getProductTags() 
	{
		$db = &JFactory::getDBO();
		$q ="SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2tags' ";
		$db->setQuery($q);
		$virtuemart_customfield_id = $db->loadResult();			
		$virtuemart_product_id		= JRequest::getVar('productid','','get'); 
		$q = " SELECT `custom_param` 
		FROM `#__virtuemart_product_customfields` 
		WHERE `custom_value`='vm2tags' 
		AND `virtuemart_product_id`='".$virtuemart_product_id."'  
		AND `virtuemart_custom_id` ='".$virtuemart_customfield_id."' ";
		$db->setQuery($q);
		$product_tags = $db->loadResult();	
		$this->_product_tags = $product_tags;
		return $this->_product_tags;
	}
	
	
	public function getProductLocation()  // not used
	{
		$db = &JFactory::getDBO();
		$q ="SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2geolocator' ";
		$db->setQuery($q);
		$virtuemart_customfield_id = $db->loadResult();			
		$virtuemart_product_id		= JRequest::getVar('productid','','get');
		
		$q = " SELECT `custom_param` 
		FROM `#__virtuemart_product_customfields` 
		WHERE `custom_value`='vm2tags' 
		AND `virtuemart_product_id`='".$virtuemart_product_id."'  
		AND `virtuemart_custom_id` ='".$virtuemart_customfield_id."' ";
		$db->setQuery($q);
		$product_location = $db->loadResult();	
		$this->_product_location = $product_location;
		return $this->_product_location;
	}
	
	public function getCorecustomfields() 
	{
		$virtuemart_product_id		= JRequest::getVar('productid','','get'); 
		$db = &JFactory::getDBO();
		$q ="SELECT vc.`virtuemart_custom_id` , vc.`custom_parent_id` , vc.`virtuemart_vendor_id` , vc.`custom_jplugin_id` , vc.`custom_title` , vc.`custom_tip` , vc.`custom_value`, vc.`custom_field_desc` , vc.`field_type` , vc.`is_list` , vc.`shared` ,
		vpc.`custom_value` AS value 
		FROM `#__virtuemart_customs` vc 
		LEFT JOIN #__virtuemart_product_customfields vpc ON vc.`virtuemart_custom_id` = vpc.`virtuemart_custom_id` AND vpc.`virtuemart_product_id` ='".$virtuemart_product_id."'
		WHERE vc.`custom_jplugin_id`='0' 
		AND vc.`admin_only`='0' 
		AND vc.`published`='1' 
		AND vc.`custom_element`!='' 	
		ORDER BY vc.`ordering` ASC , vc.`virtuemart_custom_id` ASC ";
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
	public function getProductManufacturer() 
	{
		$virtuemart_product_id		= JRequest::getVar('productid','','get');
		$db = &JFactory::getDBO();
		$q = "SELECT virtuemart_manufacturer_id FROM #__virtuemart_product_manufacturers  WHERE virtuemart_product_id='".$virtuemart_product_id."' ";
		$db->setQuery($q);
		$manufacturerid = $db->loadResult();
		return $manufacturerid;
	}
	
}
?>