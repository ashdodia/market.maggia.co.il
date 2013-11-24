<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
class VmvendorModelCatsuggest extends JModelItem
{
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
	
	function traverse_tree_down($class,$category_id, $level,$forbidcatids,$virtuemart_vendor_id )
	{
		$db 						= &JFactory::getDBO();	
		$banned_cats = explode(',',$forbidcatids);
		$level++;
		$q = "SELECT * FROM `#__virtuemart_categories_".VMLANG."` AS vmcl, `#__virtuemart_category_categories` AS vmcc,   `#__virtuemart_categories` AS vmc
			WHERE vmcc.`category_parent_id` = '".$category_id."' 
			AND vmcl.`virtuemart_category_id` = `category_child_id` 
			AND vmc.`virtuemart_category_id` = vmcl.`virtuemart_category_id` 
			AND vmc.`published`='1' ";
		foreach($banned_cats as $banned_cat){
			$q .= "AND vmc.`virtuemart_category_id` !='".$banned_cat."' ";
		}
			
		$q .= "	ORDER BY vmc.`ordering` ASC ";
		$db->setQuery($q);
		$cats = $db->loadObjectList();
		foreach($cats as $cat)
		{
			echo '<option value="'.$cat->virtuemart_category_id.'" >';
			$parent =0;
			for ($i=1; $i<$level; $i++)
			{
				echo ' . ';
			}
			if($level >1)
				echo '  |_ ';
			echo $cat->category_name.'</option>';
			VmvendorModelCatsuggest::traverse_tree_down($class, $cat->category_child_id, $level,$forbidcatids,$virtuemart_vendor_id );
		}
	}
}
?>