<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user 			= &JFactory::getUser();
$db 			= &JFactory::getDBO();
$doc 			= &JFactory::getDocument();
$juri 			= JURI::base();
$app 			= JFactory::getApplication();
if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
$cparams 					=& JComponentHelper::getParams('com_vmvendor');
$tax_mode 			= $cparams->getValue('tax_mode',0);



$taxid = JRequest::getVar('taxid');

$tax_id 			= $this->taxdata[0];
$tax_name 			= $this->taxdata[1];
$tax_descr 			= $this->taxdata[2];
$tax_kind 			= $this->taxdata[3];
$tax_mathop 		= $this->taxdata[4];
$tax_value 			= $this->taxdata[5];
$tax_currency 		= $this->taxdata[6];
$tax_publish_up 	= $this->taxdata[7];
$tax_publish_down 	= $this->taxdata[8];

$tax_cats			= $this->tax_cats;
$vendor_shoppergroups = $this->vendor_shoppergroups;
//var_dump($vendor_shoppergroups);

$virtuemart_vendor_id = $this->virtuemart_vendor_id;

if($tax_mode==1){

	if ($taxid !='' && $tax_name !='')
		echo '<h1>'.JText::_('COM_VMVENDOR_VMVENEDITTAX_FORM_TITLE_EDITION').'</h1>';
	else
		echo '<h1>'.JText::_('COM_VMVENDOR_VMVENEDITTAX_FORM_TITLE_CREATION').'</h1>';
	
	if (!class_exists( 'VmConfig' ))
		require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	
	echo '<div>'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXEDITION_NOTICE').'<br /><br /></div>';
	
	if($profileman == 2){
		require_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'window.php' );
		CWindow::load();
		$tooltip_class = 'jomTips';		
	}
	else{
		JHTML::_('behavior.tooltip');
		$tooltip_class = 'hasTip';
	}
	
	$doc->addStylesheet($juri.'components/com_vmvendor/assets/js/jquery.multiselect.css');
	$doc->addStylesheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css');
	$load_jquery	= $cparams->getValue('load_jquery', 1);
	//$jquery_url	= $cparams->getValue('jquery_url','https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js');
	$jquery_url	= $cparams->getValue('jquery_url','http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js');
	//if($load_jquery)
		$doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js');
		
	$doc->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js');
	
	
	
	$doc->addScript($juri.'components/com_vmvendor/assets/js/jquery.multiselect.php?all='.JText::_('COM_VMVENDOR_VMVENEDITTAX_FORM_CATS_ALL').'&uncheck_all='.JText::_('COM_VMVENDOR_VMVENEDITTAX_FORM_CATS_UNCHECKALL').'&select_cats='.JText::_('COM_VMVENDOR_VMVENEDITTAX_FORM_SELECTCATS').'&selected='.JText::_('COM_VMVENDOR_VMVENEDITTAX_FORM_TITLE_SELECTEDCATS'));
	//$doc->addScriptdeclaration($jquery_multiselect_js);
	
	
	$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/bootstrap.min.css');
	$doc->addScript($juri.'components/com_vmvendor/assets/js/bootstrap.min.js');
		
		
	$forbidcatids 	= $cparams->getValue('forbidcatids');	
	
	$price_format	= $this->price_format;
	$symbol 		= $price_format[7];
	$currency_id	= $price_format[0];
	$currency 		= $price_format[4];
				
	
	$vmitemid 		= $cparams->getValue('vmitemid', '103');
	$autopublish 	= $cparams->getValue('autopublish', 1);
	
	$emailnotify_addition 	= $cparams->getValue('emailnotify_addition', 0);
	$to 			= $cparams->getValue('to');
	
	
	
	
	$minimum_price	= $cparams->getValue('minimum_price');
	
	
	
	
	
	//$form_taxid			= JRequest::getVar('form_taxname');
	
	echo '<script type="text/javascript">function validateForm(it){
	var warning = "'.JText::_('COM_VMVENDOR_VMVENADD_JS_FIXTHIS').' \n";
	var same = warning;
	if (it.calc_name.value=="")
	{
		warning += " * '.JText::_('COM_VMVENDOR_TAXNAME_REQUIRED').' \n";
		it.calc_name.style.backgroundColor = \'#ff9999\';
	}
	if (it.taxproductcats.value=="")
	{
		warning += " * '.JText::_('COM_VMVENDOR_TAXPRODUCTCATS').' \n";
		it.multiselect_button.style.backgroundColor = \'#ff9999\';
	}
	
	if (it.calc_mathop.value=="")
	{
		warning += " * '.JText::_('COM_VMVENDOR_TAXMATHOP_REQUIRED').' \n";
		it.calc_mathop.style.backgroundColor = \'#ff9999\';
	}';
	//if(!$wysiwyg_prod) // not checking description if wysiwyg is on
	
	echo 'if (it.calc_value.value=="" || isNaN ( it.calc_value.value ) )
	{
		warning += " * '.JText::_('COM_VMVENDOR_TAXVALUE_REQUIRED').' \n";
		it.calc_value.style.backgroundColor = \'#ff9999\';
	}';
	/*echo 'if (it.formdesc.value=="")
	{
		warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_DESCREQUIRED').' \n";
		it.formdesc.style.backgroundColor = \'#ff9999\';
	}';*/
	
				
		echo '
		if (warning == same)
		{
			
			return true;
		}
		else
		{
			alert(warning);
			return false;
		}
	}
	</script>';
	
	
	
				echo  '<form name="addtax" onsubmit="return validateForm(this);" method="post"  >';
				echo  '<table class="table table-striped table-condensed">';
				if ($taxid !='' && $tax_name !=''){
				echo  '<tr >';
				echo  '<td>'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXID').'</td>';
				echo  '<td>';
				if 	($tax_id!=''){
					echo $tax_id;
					echo '<input type="hidden" name="calc_id" value="'.$tax_id.'" />';
				}
				else
					echo JText::_('COM_VMVENDOR_EDITTAX_FORM_NEWTAX');
	
				echo  '</td>';
				echo  '</tr>';
				}
				
				echo  '<tr>';
				echo  '<td>'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXKIND').'</td>';
				echo  '<td>';
				if 	($tax_kind!='')
					echo $tax_kind;
				else
					echo 'VatTax';
	
				echo  '</td>';
				echo  '</tr>';
				
				echo  '<tr>';
				echo  '<td>'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXNAME').'</td>';
				echo  '<td>';
				echo '<input type="text" id="calc_name"  name="calc_name" onkeyup="this.style.backgroundColor = \'\'" class="form-control" ';
				if 	($tax_name!='')
					echo 'value="'.$tax_name.'" ';
				echo ' />';
				echo  '</td>';
				echo  '</tr>';
				
				
				echo  '<tr>';
				echo  '<td>'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXDESCR').'</td>';
				echo  '<td>';
				echo '<textarea id="calc_descr"  name="calc_descr" class="form-control" >';
				if 	($tax_descr!='')
					echo $tax_descr;
				echo '</textarea>';
				echo  '</td>';
				echo  '</tr>';
				
				echo  '<tr>';
	echo  '<td>';
	echo JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXPRODUCTCATS');
	echo  '</td>';
	echo  '<td>';
	
	
	//////////////////////// Category select field
	echo '<select  multiple="multiple" size="5" id="taxproductcats" name="taxproductcats[]" onchange="document.getElementById(\'multiselect_button\').style.backgroundColor = \'\'" class="form-control" >';
	//echo '<option value="0">'.JText::_('COM_VMVENDOR_VMVENEDIT_FORM_CHOOSEMULTIPLECAT').'</option>';
	
	
		
		
	function traverse_tree_down($class,$category_id, $level,$forbidcatids, $tax_cats , $tax_id , $virtuemart_vendor_id)
	{
		$db 						= &JFactory::getDBO();	
		$banned_cats = explode(',',$forbidcatids);
		$level++;
		$q = "SELECT * FROM `#__virtuemart_categories_".VMLANG."` AS vmcl, `#__virtuemart_category_categories` AS vmcc,   `#__virtuemart_categories` AS vmc
			WHERE vmcc.`category_parent_id` = '".$category_id."' 
			AND vmcl.`virtuemart_category_id` = `category_child_id` 
			AND vmc.`virtuemart_category_id` = vmcl.`virtuemart_category_id` 
			AND vmc.`published`='1' 
			AND (vmc.`virtuemart_vendor_id`='1' OR vmc.`virtuemart_vendor_id` ='".$virtuemart_vendor_id."' OR vmc.`shared`='1' ) ";
		foreach($banned_cats as $banned_cat){
			$q .= "AND vmc.`virtuemart_category_id` !='".$banned_cat."' ";
		}
			
		$q .= "	ORDER BY vmc.`ordering` ASC ";
		$db->setQuery($q);
		$cats = $db->loadObjectList();
		foreach($cats as $cat)
		{
	
			
		
		
		echo '<option value="'.$cat->virtuemart_category_id.'" ';
			
			if($tax_id!='' && is_array( $tax_cats)){
				if( in_array(  $cat->virtuemart_category_id , $tax_cats ) )
					echo 'selected="selected" ';
			}
			echo '>';
			$parent =0;
			for ($i=1; $i<$level; $i++)
			{
				echo ' . ';
			}
			if($level >1)
				echo '  |_ ';
			echo $cat->category_name.'</option>';
			traverse_tree_down($class, $cat->category_child_id, $level,$forbidcatids, $tax_cats, $tax_id , $virtuemart_vendor_id);
		}
	}
	$traverse = traverse_tree_down('',0,0,$forbidcatids,  $tax_cats , $tax_id , $virtuemart_vendor_id);
	echo '</select>';
	echo  '</td>';
	echo  '</tr>';
	
	$doc->addScriptdeclaration('$(document).ready(function(){
	   $("#taxproductcats").multiselect();
	});');
				
				
				
				
				echo  '<tr>';
				echo  '<td>'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXMATHOP').'</td>';
				echo  '<td>';
				echo '<select id="calc_mathop"  name="calc_mathop"  onchange="this.style.backgroundColor = \'\'" class="form-control">';
				echo '<option value="">'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXSELECTMATHOP').'</option>';
				echo '<option value="+%" ';
					if($tax_mathop =='+%')
						echo 'selected' ;
				echo '>+%</option>';
				echo '<option value="-%" ';
				if($tax_mathop =='-%')
						echo 'selected' ;
				echo '>-%</option>';
				echo '<option value="+" ';
				if($tax_mathop =='+')
						echo 'selected' ;
				echo '>+</option>';
				echo '<option value="-" ';
				if($tax_mathop =='-')
						echo 'selected' ;
				echo '>-</option>';
				echo '</textarea>';
				echo  '</td>';
				echo  '</tr>';
				
				echo  '<tr>';
				echo  '<td>';
				echo JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXVALUE');
				echo  '</td>';
				echo  '<td>';
				echo ' <input type="text" id="calc_value"  name="calc_value" onkeyup="this.style.backgroundColor = \'\'" class="form-control" ';
				if 	($tax_value!='')
					echo 'value="'.$tax_value.'" ';
				echo ' />';
				
				echo  '</td>';
	
				echo  '</tr>';
				
				
				$tax_shoppergroups = array();
				//echo count($vendor_shoppergroups);
				for($i=0; $i < count($vendor_shoppergroups) ; $i++ ){
					//if($i>0)
						array_push($tax_shoppergroups , $vendor_shoppergroups[$i] ); 
				}
				//echo count($tax_shoppergroups);
				//var_dump($tax_shoppergroups);
				echo ' <input type="hidden" id="calc_shoppergroups"  name="calc_shoppergroups" value="'.implode(',',$tax_shoppergroups).'" />';
				echo ' <input type="hidden" id="calc_currency"  name="calc_currency" value="'.$tax_currency.'" />';
				echo ' <input type="hidden" id="calc_vendor_id"  name="calc_vendor_id" value="'.$virtuemart_vendor_id.'" />';
				
				
				
				
				
				
				
				
				
				echo  '<tr align="left" class="sectiontableheader">';
				echo  '<td><input type="button" name="cancel" id="cancelbutton" value="'.JText::_('COM_VMVENDOR_VMVENADD_BTTN_CANCEL').'" onclick="history.go(-1)" class="btn btn-default"></td>';
				echo  '<td style="text-align:right;">';
				if ($taxid !='' && $tax_name !=''){
					echo '<input type="submit" value="'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXEDITBUTTON').'" class="btn btn-primary" />';
					echo '<input type="hidden" name="task" value="edittax" />';
				}
				else{
					echo '<input type="submit" value="'.JText::_('COM_VMVENDOR_EDITTAX_FORM_TAXADDBUTTON').'" class="btn btn-lg btn-primary" />';
					echo '<input type="hidden" name="task" value="addtax" />';
				}
				
				
				echo  '</td>';
				echo  '</tr>';
				
				
				
				echo  '</table>';
				echo '<input type="hidden" name="option" value="com_vmvendor" />
						<input type="hidden" name="controller" value="edittax" />';
				
				
				echo  '</form>';
				echo '<div style="clear:both;"> </div>';
		
}
else{
	echo JText::_('COM_VMVENDOR_EDITTAX_TAX_MODE_DISABLED');
	echo '<input type="button" name="cancel" id="cancelbutton" value="'.JText::_('COM_VMVENDOR_VMVENADD_BTTN_CANCEL').'" onclick="history.go(-1)">';
}
?>