<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
class VmvendorModelAskvendor extends JModelItem
{
	public function getVendorcontacts()
	{
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$naming 					= $cparams->getValue('naming');
		$db = &JFactory::getDBO();
		$q = "SELECT `".$naming."` , `email` FROM `#__users` WHERE `id`='".JRequest::getVar('vendoruserid')."' ";
		$db->setQuery($q);
		$this->vendorcontacts = $db->loadRow();
		return $this->vendorcontacts;
	}
	public function getProductname()
	{
		$db = &JFactory::getDBO();
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$q = "SELECT vpl.`product_name` 
		FROM `#__virtuemart_products_".VMLANG."` vpl 
		WHERE `virtuemart_product_id` ='".JRequest::getVar('productid')."' ";
		$db->setQuery($q);
		$productname = $db->loadResult();
		$this->productname = $productname;
		return $this->productname;
	}
}
?>