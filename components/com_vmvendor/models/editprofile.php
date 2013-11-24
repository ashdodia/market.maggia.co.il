<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


class VmvendorModelEditprofile extends JModelItem
{
	/**
	 * @var string msg
	 */
	//protected $msg;
 
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
/*	public function getCoords() 
	{
		$db = &JFactory::getDBO();
		$q = "SELECT metakey FROM #__content WHERE id=".JRequest::getVar('a_id');
		$db->setQuery($q);
		$coords = $db->loadResult();
		$this->_coords = $coords;
		return $this->_coords;
	}*/
	
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
		
		$q ="SELECT  vvl.vendor_store_desc , vvl.vendor_terms_of_service , vvl.vendor_legal_info , vvl.vendor_store_name , vvl.vendor_phone , vvl.vendor_url ,
		vv.virtuemart_vendor_id 
		FROM #__virtuemart_vendors_".VMLANG." vvl 
		LEFT JOIN #__virtuemart_vmusers vv ON vv.virtuemart_vendor_id = vvl.virtuemart_vendor_id 
		WHERE vv.virtuemart_user_id='".$userid."' " ;	
		$db->setQuery($q);
		$this->vendor_data = $db->loadRow();
		return $this->vendor_data;
	}
	
	public function getVendorthumb() 
	{
		$user 			= &JFactory::getUser();
		$userid = JRequest::getVar('userid');
		if(!$userid)
			$userid = $user->id;
		$db = &JFactory::getDBO();
		
		$q ="SELECT vm.file_url_thumb
		FROM #__virtuemart_medias vm 
		LEFT JOIN #__virtuemart_vendor_medias vvm ON vvm.virtuemart_media_id = vm.virtuemart_media_id 
		LEFT JOIN #__virtuemart_vmusers vv ON vv.virtuemart_vendor_id =vvm.virtuemart_vendor_id 
		WHERE vv.virtuemart_user_id = '".$userid."' " ;	
		$db->setQuery($q);
		$this->vendor_thumb = $db->loadResult();
		return $this->vendor_thumb;
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