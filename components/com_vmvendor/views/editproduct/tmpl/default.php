<?php
/*
 * @component VMVendor view editproduct
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

$product_vendor_id		= $this->product_data[0];
$product_sku 			= $this->product_data[1];
$product_weight			= $this->product_data[2];
$product_weightunit		= $this->product_data[3];
$product_in_stock		= $this->product_data[4];
$product_s_desc			= $this->product_data[5];
$product_desc			= $this->product_data[6];
$product_name			= $this->product_data[7];
$virtuemart_category_id = $this->product_data[8];
$product_price 			= $this->product_data[9];

$virtuemart_vendor_id = $this->virtuemart_vendor_id;


if($product_vendor_id == $virtuemart_vendor_id){
	echo '<h1>'.JText::_('COM_VMVENDOR_VMVENEDIT_FORM_TITLE').'</h1>';
	if (!class_exists( 'VmConfig' ))
		require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	
		
	
	
	
		
	$cparams 					=& JComponentHelper::getParams('com_vmvendor');
	$profileman 				= $cparams->getValue('profileman');
	if($profileman == 2){
		require_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'window.php' );
		CWindow::load();
		$tooltip_class = 'jomNameTips';		
	}
	else{
		JHTML::_('behavior.tooltip');
		$tooltip_class = 'hasTip';
	}
	$naming 				= $cparams->getValue('naming', 'username');
	
	
	
		
		
		
	$forbidcatids 	= $cparams->getValue('forbidcatids');	
	
	$price_format	= $this->price_format;
	$symbol 		= $price_format[7];
	$currency_id	= $price_format[0];
	$currency 		= $price_format[4];
	$currency_decimal_place = $price_format[8];
				
	$termsurl 		= $cparams->getValue('termsurl');
	$vendordefcat 	= $cparams->getValue('vendordefcat', '6');
	$populatemf 	= $cparams->getValue('populatemf', 0);
	$mfdefcat 		= $cparams->getValue('mfdefcat', '1');
	$vmitemid 		= $cparams->getValue('vmitemid', '103');
	$profileitemid 	= $cparams->getValue('profileitemid', '2');
	$autopublish 	= $cparams->getValue('autopublish', 1);
	$enablerss 		= $cparams->getValue('enablerss', 1);
	$rsslimit 		= $cparams->getValue('rsslimit', 10);
	$shoutbox 		= $cparams->getValue('shoutbox', 0);
	$shoutboxlink 	= $cparams->getValue('shoutboxlink', 0);
	$emailnotify 	= $cparams->getValue('emailnotify', 0);
	$autoaddapp 	= $cparams->getValue('autoaddapp', 1);
	$to 			= $cparams->getValue('to');
	$maxfilesize 	= $cparams->getValue('maxfilesize', '4000000');//4 000 000 bytes   =  4M
	$max_imagefields= $cparams->getValue('max_imagefields', 4);
	$max_filefields	= $cparams->getValue('max_filefields', 4);
	$maximgside 	= $cparams->getValue('maximgside', '600');
	$thumbqual 		= $cparams->getValue('thumbqual', 70);
	$enable_sdesc 	= $cparams->getValue('enable_sdesc', 1);
	$wysiwyg_prod 		= $cparams->getValue('wysiwyg_prod', 0);
	$enablefiles 	= $cparams->getValue('enablefiles', 1);
	$enableweight 	= $cparams->getValue('enableweight', 1);
	$weightunits 	= $cparams->getValue('weightunits');
	$enableprice	= $cparams->getValue('enableprice', 1);
	$enablestock	= $cparams->getValue('enablestock', 1);
	$enablemanufield = $cparams->getValue('enablemanufield', 1);
	
	$filemandatory 	= $cparams->getValue('filemandatory', 1);
	$allowedexts 	= $cparams->getValue('allowedexts', 'zip,mp3');
	$minimum_price	= $cparams->getValue('minimum_price', '5');
	$sepext 		= explode( "," , $allowedexts );
	$countext 		= count($sepext);
	$load_jquery	= $cparams->getValue('load_jquery', 1);
	$jquery_url	= $cparams->getValue('jquery_url','https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js');
	if($load_jquery)
		$doc->addScript($jquery_url);
	$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/bootstrap.min.css');
	$doc->addScript($juri.'components/com_vmvendor/assets/js/bootstrap.min.js');
	
	$enable_corecustomfields	= $cparams->getValue('enable_corecustomfields', 1);
	$enable_vm2tags			= $cparams->getValue('enable_vm2tags', 0);
	$tagslimit				= $cparams->getValue('tagslimit', '5');
	$enable_vm2geolocator	= $cparams->getValue('enable_vm2geolocator', 0);
	$enable_vm2sounds		= $cparams->getValue('enable_vm2sounds', 0);
	$enable_vm2dropdownfield		= $cparams->getValue('enable_vm2dropdownfield', 0);
	
	$image_path 	=  VmConfig::get('media_product_path');
	$safepath 		= VmConfig::get( 'forSale_path' );
	
	
	
	$thumb_path = $image_path.'resized/';
	
	$formfile='';
	
	$formname 			= JRequest::getVar('formname');
	$formdesc		 	= JRequest::getVar('formdesc');
	$form_s_desc		 	= JRequest::getVar('form_s_desc');
	if($enableprice)
		$formprice 			= JRequest::getVar('formprice');
	else
		$formprice			= 0;
	$formcat 			= JRequest::getVar('formcat');
	$file1				= JRequest::getVar('file1', null, 'files', 'array');
	$file1name			= $file1['name'];
	
	if($enablefiles && $safepath=='')
		JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_SAFEPATHREQUIRED') );
	if(VmConfig::get('multix','none')!='admin')
		JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_MULTIVENDORREQUIRED') );
	
	
		
		
	
	echo '<script type="text/javascript">function validateForm(it){
	var warning = "'.JText::_('COM_VMVENDOR_VMVENADD_JS_FIXTHIS').' \n";
	var same = warning;
	if (it.formcat.value=="0")
	{
		warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_CATREQUIRED').' \n";
		it.formcat.style.backgroundColor = \'#ff9999\';
	}
	if (it.formname.value=="")
	{
		warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_NAMEREQUIRED').' \n";
		it.formname.style.backgroundColor = \'#ff9999\';
	}';
	//if(!$wysiwyg_prod) // not checking description if wysiwyg is on
	if ($enable_sdesc){
		echo 'if (it.form_s_desc.value=="")
		{
			warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_DESCREQUIRED').' \n";
			it.form_s_desc.style.backgroundColor = \'#ff9999\';
		}';
	}
	/*echo 'if (it.formdesc.value=="")
	{
		warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_DESCREQUIRED').' \n";
		it.formdesc.style.backgroundColor = \'#ff9999\';
	}';*/
	if($enableprice){
		echo 'if (it.formprice.value=="")
		{
			warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_PRICEREQUIRED').' \n";
			it.formprice.style.backgroundColor = \'#ff9999\';
		}
		if (isNaN (it.formprice.value))
		{
			warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_UNVALIDPRICE').' \n";
			it.formprice.style.backgroundColor = \'#ff9999\';
		}';	
	}
	
	if($enablestock){
		echo ' if (it.formstock.value=="" || isNaN (it.formstock.value))
			{
				warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_STOCKREQUIRED').' \n";
				document.getElementById("formstock").style.backgroundColor = \'#ff9999\';
				
			} ';	
		
	}
	
	if($enableweight){
		echo ' if (it.formweight.value=="" || isNaN (it.formweight.value))
			{
				warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_WEIGHTREQUIRED').' \n";
				document.getElementById("formweight").style.backgroundColor = \'#ff9999\';
				
			} ';	
			echo ' if (it.formweightunit.value=="" )
			{
				warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_WEIGHTUNITREQUIRED').' \n";
				document.getElementById("formweightunit").style.backgroundColor = \'#ff9999\';
				
			} ';	
		
	}
	
	if($enablefiles)
	{ 				
		for( $i=1 ; $i<= $max_filefields ; $i++ )
		{
			echo  ' if(  document.getElementById("fileinput'.$i.'") )
			{
				var thisfile = it.file'.$i.';';
			echo 'if( thisfile.value!="" ';
							for ( $j=0 ; $j < $countext ; $j++ )
							{
								echo  ' && ';
								echo  ' thisfile.value.indexOf(".'.$sepext[$j].'") == -1';
							}
			echo  ')
			{ 
				warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_FILEMISSING').' \n"; 
				thisfile.style.backgroundColor = \'#ff9999\';
			}';
			
			echo  '}';
		}
		
	
		
		
		
	}
	for( $i=1 ; $i<= $max_imagefields ; $i++)
	{
		echo  'if(document.getElementById("imginput'.$i.'"))
		{
			var thisimage = it.image'.$i.';
			if ( thisimage.value!="" &&  thisimage.value.indexOf(".jpg") == -1  &&  thisimage.value.indexOf(".gif") == -1 &&  thisimage.value.indexOf(".png") == -1 
									&&  thisimage.value.indexOf(".JPG") == -1  &&  thisimage.value.indexOf(".GIF") == -1 &&  thisimage.value.indexOf(".PNG") == -1)
			{ 
				warning += " * '.JText::_('COM_VMVENDOR_IMAGETYPENOT').' \n";
				thisimage.style.backgroundColor = \'#ff9999\';
				
	
			}
		}';
	}
	
		if($enable_vm2geolocator){
			echo  ' if( document.getElementById("latitude").value=="" || document.getElementById("longitude").value=="" )
			{		
					warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_FORM_MISSINGCOORDS').' \n";
					document.getElementById("latitude").style.backgroundColor = \'#ff9999\';
					document.getElementById("longitude").style.backgroundColor = \'#ff9999\';			
				}';
		}
				
		if($enable_vm2dropdownfield ){
			$q = "SELECT virtuemart_custom_id , custom_title , custom_tip,  `custom_params` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2dropdownfield' AND `published`='1' ";
			$db->setQuery($q);
			$vm2drops = $db->loadObjectList();
			$i = 1;
			if($enable_vm2dropdownfield ==2){ // mandatory
				foreach($vm2drops as $vm2drop){
					echo  ' if( document.getElementById("vm2dropdownfield'.$i.'").value==""  )
				{		
						warning += " * '.JText::_('COM_VMVENDOR_DROPFIELD_REQUIRED').' '.$vm2drop->custom_title.' \n";
						document.getElementById("vm2dropdownfield'.$i.'").style.backgroundColor = \'#ff9999\';		
					}';
					$i++;
				}
			}
		}
				
		echo 'if (it.formterms.checked==false)
		{
			warning += " * '.JText::_('COM_VMVENDOR_VMVENADD_JS_ACCEPTTERMS').' \n";
			document.getElementById("checkboxtd").style.backgroundColor = \'#ff9999\';
			
		}
		if (warning == same)
		{
			it.loading.style.display = "";
			return true;
		}
		else
		{
			alert(warning);
			return false;
		}
	}
	</script>';
	
				//$sku = $user->id.".".date('ymd.His');
				echo  '<form name="add" enctype="multipart/form-data" onsubmit="return validateForm(this);" method="post" class="form-inline" >';
				echo  '<table class="table table-striped table-condensed">';
				echo  '<tr >';
				echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_PRODUCTINFO').'</td>';
				echo  '<td><b>*</b> '.JText::_('COM_VMVENDOR_VMVENADD_MANDATORYFIELDS').'</td>';
				echo  '</tr>';
				
				echo  '<tr  >';
				echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_NAME').' <b>*</b></td>';
				echo  '<td>';
				echo  '<input type="text" name="formname" id="formname" size="50" onkeyup="this.style.backgroundColor = \'\'" value="'.$product_name.'" class="form-control" />';
				echo '</td>';
				echo  '</tr>';
				
				
				
				if($show_sku){
					echo  '<tr >';
					echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_SKU').'</td>';
					echo  "<td>".$product_sku."</td>";
					echo  '</tr>';
				}
				echo '<INPUT type="hidden" value="'.$product_sku.'" name="formsku">
				<input type="hidden" name="productid" value="'.JRequest::getVar('productid','', 'post').'" />';
				
				
				
				echo  '<tr style="text-align:left;" >';
				echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CAT').'</td>';
				echo  "<td>";
				//////////////////////// Category select field
				echo '<select id="formcat" name="formcat" onchange="this.style.backgroundColor = \'\'" class="form-control">
				<option value="0">'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CHOOSECAT').'</option>';
				function traverse_tree_down( $class , $category_id , $level ,  $forbidcatids , $virtuemart_category_id , $virtuemart_vendor_id )
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
						if($cat->virtuemart_category_id == $virtuemart_category_id)
							echo ' selected="selected" ';
						echo '>';
						$parent =0;
						for ($i=1; $i<$level; $i++)
						{
							echo ' . ';
						}
						if($level >1)
							echo '  |_ ';
						echo $cat->category_name.'</option>';
						traverse_tree_down($class , $cat->category_child_id , $level , $forbidcatids , $virtuemart_category_id  , $virtuemart_vendor_id );
					}
				}
				$traverse = traverse_tree_down('' , 0 , 0 , $forbidcatids , $virtuemart_category_id , $virtuemart_vendor_id );
				echo '</select>';
	
				///////////////////////////////// end Category select field
				echo  "</td>";
				echo  '</tr>';
				
				if($enablemanufield){
					echo  '<tr >';
					echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_MANUFACTURER').'</td>';
					echo  '<td>';
					
					echo '<div class="form-group"><select id="formmanufacturer" name="formmanufacturer" class="form-control" onchange="this.style.backgroundColor = \'\'"><option value="0">'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CHOOSEMANUFACTURER').'</option>';
					$manufacturers = VmvendorModelEditproduct::getManufacturers();
					$manufacturerid = VmvendorModelEditproduct::getProductManufacturer();
					foreach($manufacturers as $manufacturer){
						echo '<option value="'.$manufacturer->virtuemart_manufacturer_id.'" title="'.$manufacturer->mf_desc.'" ';
						if($manufacturerid==$manufacturer->virtuemart_manufacturer_id)
							echo ' selected="selected" ';
						echo '>'.$manufacturer->mf_name.'</option>';	
					}
					echo  '</td>';
					echo  '</tr>';
					
					
				}
				
				if ($enable_sdesc){
					echo  '<tr  >';
					echo  '<td >'.JText::_('COM_VMVENDOR_VMVENADD_FORM_SDESC').' <b>*</b>';
					echo '<br /><B><SPAN id=myCounter>'. ( 255 - strlen($product_s_desc) ).' </SPAN></B> '.JText::_('COM_VMVENDOR_VMVENADD_FORM_CHARSREMAINING');
					echo '</td>';
		
					$counterscript ='maxL= 255;
					var bName = navigator.appName;
					function taLimit(taObj) {
						if (taObj.value.length==maxL) return false;
						return true;
					}
					
					function taCount(taObj,Cnt) { 
						objCnt=createObject(Cnt);
						objVal=taObj.value;
						if (objVal.length>maxL) objVal=objVal.substring(0,maxL);
						if (objCnt) {
							if(bName == "Netscape"){	
								objCnt.textContent=maxL-objVal.length;}
							else{objCnt.innerText=maxL-objVal.length;}
						}
						return true;
					}
					
					function createObject(objId) {
						if (document.getElementById) return document.getElementById(objId);
						else if (document.layers) return eval("document." + objId);
						else if (document.all) return eval("document.all." + objId);
						else return eval("document." + objId);
					}';
					$doc->addScriptDeclaration($counterscript);
					
					echo  '<td><textarea name="form_s_desc" id="form_s_desc" cols="45" rows="5" onkeyup="this.style.backgroundColor = \'\';return taCount(this,\'myCounter\');"   onKeyPress="return taLimit(this)" class="form-control" >';
					echo $product_s_desc;
					echo  '</textarea></td>';
	
					echo  '</tr>';
				}
				
				echo  '<tr  >';
				echo  '<td >'.JText::_('COM_VMVENDOR_VMVENADD_FORM_DESC').' <b>*</b></td>';
				if($wysiwyg_prod){
					jimport( 'joomla.html.editor' );
					$editor = &JFactory::getEditor();
					$editorhtml = $editor->display("formdesc", $product_desc, "100%;", '200', '5', '30', false);
					//  ; required after %
	
					//$editorhtml = JEditor::display( 'editor',  '' , 'description', '100%;', '150', '5', '30' );
					echo  '<td>'.$editorhtml.'</td>';
				}else{
					echo  '<td><textarea name="formdesc" id="formdesc" cols="45" rows="5" onkeyup="this.style.backgroundColor = \'\'" class="form-control">';
					echo $product_desc;
					echo  '</textarea></td>';
				}
				echo  '</tr>';
				
				if($enableweight){
					echo  '<tr  >';
					echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT').' <b>*</b></td>';
					echo  '<td>';
					echo '<div class="form-group">';
					echo '<input type="text" class="form-control" ';
					if($product_weight)
						echo 'value="'.$product_weight.'" ';
					echo 'name="formweight" id="formweight" onkeyup="this.style.backgroundColor = \'\';" />';
					echo '</div>';
					if(count($weightunits)<1){
						if($product_weightunit){			
							echo JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT_'.$product_weightunit);
							echo '<input type="hidden" id="formweightunit" name="formweightunit" value="'.$product_weightunit.'" />';
						}
						else{
							echo '<br />'.JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT_UNITNOTDEFINED');
						}
						
					}
					elseif( ( count($weightunits)==1 && !$product_weightunit ) OR ( count($weightunits)==1 && $product_weightunit == $weightunits[0]) ){
							echo JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT_'.$weightunits[0]);
							echo '<input type="hidden" id="formweightunit" name="formweightunit" value="'.$weightunits[0].'" class="form-control"/>';
					}
					elseif( count($weightunits)>1  OR !in_array($product_weightunit,$weightunits) ){
						echo ' <div class="form-group">';
						echo '<select id="formweightunit" name="formweightunit" onchange="this.style.backgroundColor = \'\';" class="form-control" >';
						echo '<option value="" >'.JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT_SELECTUNIT').'</option>';
						foreach($weightunits as $weightunit){
							echo '<option value="'.$weightunit.'" ';
							if($product_weightunit == $weightunit)
								echo ' selected="selected" ';
							echo '>';
							echo JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT_'.$weightunit);
							echo '</option>';
						}
						if (!in_array($product_weightunit,$weightunits) && $product_weightunit!=''){
							echo '<option value="'.$product_weightunit.'" ';
							echo ' selected="selected" ';
							echo '>';
							echo JText::_('COM_VMVENDOR_VMVENADD_FORM_WEIGHT_'.$product_weightunit);
							echo '</option>';	
							
						}
							
						echo '</select>';
						echo '</div>';
					}
					
					echo '</div>';
					echo  '</td>';
					echo  '</tr>';
				}
				
				if($enablestock){
					echo  '<tr  >';
					echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_STOCK').' <b>*</b></td>';
					echo  '<td><div class="form-group"><input type="text" size="6" name="formstock" id="formstock" value="'.$product_in_stock.'" onkeyup="this.style.backgroundColor = \'\';this.value=this.value.replace(/\D/,\'\')" class="form-control" /></div></td>';
					echo  '</tr>';
					
				}
				
				
				if($enableprice){
					echo  '<tr >';
					echo  '<td>'.JText::_('COM_VMVENDOR_VMVENADD_FORM_PRICE').' <b>*</b></td>';
					echo  '<td><div class="form-group"><input type="text" name="formprice" id="formprice" value="'.number_format($product_price , $currency_decimal_place ).'" onkeyup="this.style.backgroundColor = \'\'" class="form-control"/></div>
					<div class="form-group"><label for="formprice">'.$symbol.' ( '.$currency.' )</label></div>
					<INPUT type="hidden" value="'.$currency.'" name="currency">
					<INPUT type="hidden" value="'.$product_price.'" name="oldprice">
					</td>';
					echo  '</tr>';
				}
				
				
				
				
				echo  '<tr>';
				echo  '<td colspan="2">';		
				echo  '</td>';
				echo  '</tr>';
				
				
				
				echo  '<tr  >';
				echo  '<td colspan="2">'.JText::_('COM_VMVENDOR_VMVENADD_FORM_FILES').'</td>';
				echo  '</tr>';
				echo  '<tr  >';
				
				if($enablefiles){////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// get upload_max_filesize from php.ini and 
					$umfs = ini_get('upload_max_filesize');
			// get it in bytes...
					$umfs = trim($umfs);
					$last = strtolower($umfs[strlen($umfs)-1]);
					switch($last){
						case 'g':
						$umfs *= 1024;
						case 'm':
						$umfs *= 1024;
						case 'k':
						$umfs *= 1024;
					}
			//if smaller than $maxfilesize replace $maxfilesize
					if ($umfs < $maxfilesize)
						$maxfilesize = $umfs;
					$maxfilesizemega = $maxfilesize/(1024*1024);
					$maxfilesizemega = round($maxfilesizemega,1)."MB";
				
					echo  '<td>';
					echo  JText::_('COM_VMVENDOR_VMVENADD_FORM_FILE').' ';
					if($filemandatory)////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						echo  '<b>*</b>';
					echo '<span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="('.$allowedexts.') '.JText::_('COM_VMVENDOR_VMVENADD_FORM_MAX').': '.$maxfilesizemega.'"></span>';
					echo  '</td>';
					
					
				
				
					
					
					
					
					
					echo  "<input type='hidden' name='MAX_FILE_SIZE' value='".$maxfilesize."' />";
					echo  '<td>';
					
					
					$m = 1;
				foreach($this->product_files as $product_file){
					echo '<div id="fileinput'.($m ).'_edit" class="fileclonedInput well well-sm">';
					
					
					echo '<img src="'.$juri.'components/com_vmvendor/assets/img/ext/file-'.JFile::getExt($product_file->file_title).'.png" height="20" title="'.$product_file->file_title.'" style="vertical-align:middle;padding:2px;" /> '.$product_file->file_title.'';
					echo ' <input type="file" name="file'. $m .'" id="file'. $m .'" onchange="this.style.backgroundColor = \'\'" class="form-control" style="margin-bottom:4px;width:310px;" /> ';
					echo '<div class="file_removal" style="float:right;width:50px;">';
					if($m>1 OR !$filemandatory){
						echo '<div title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CHECKTOREMOVEFILE').'" class="'.$tooltip_class.'">';
						echo '<input type="checkbox" name="delfile'. $m .'" id="delfile'. $m .'" class="checkbox ">';
						
						echo '<span class="glyphicon glyphicon-trash"></span>';
						echo '</div>';
					}
					else{
						echo '<div  >';
						echo '<input type="checkbox"  class="checkbox '.$tooltip_class.'" title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CANTDELETEFIRSTFILE').'" disabled >';
						echo '<span class="glyphicon glyphicon-trash"></span>';
						echo '</div>';
					}
					echo '<input type="hidden" name="filemedia_id'. $m .'" id="filemedia_id'. $m .'" value="'.$product_file->virtuemart_media_id.'" >';
					echo '</div>';
					
					echo '</div>';
					//echo '<div style="clear:both;"></div>';
					$m++;
				}
					$file_ajax = "var jq = jQuery.noConflict();
					jq(document).ready(function() {
				jq('#fileAdd').click(function() {
					var num     = jq('.fileclonedInput').length;
					var newNum  = new Number(num + 1);
	 
					var newElem = jq('#fileinput' + num).clone().attr('id', 'fileinput' + newNum);
	 
					newElem.children(':first').attr('id', 'file' + newNum).attr('name', 'file' + newNum);
					jq('#fileinput' + num).after(newElem);
					jq('#fileDel').attr('disabled',false);
	 
					if (newNum == ".$max_filefields .")
					   jq('#fileAdd').attr('disabled',true);
				});
	 
				jq('#fileDel').click(function() {
					var num = jq('.fileclonedInput').length;
	 
					jq('#fileinput' + num).remove();
					jq('#fileAdd').attr('disabled',false);
	 
					if (num-1 == $m)
						jq('#fileDel').attr('disabled',true);
				});
	 
				jq('#fileDel').attr('disabled',true);
			});";
				$doc->addScriptDeclaration($file_ajax);
				echo '<div style="display:none;">'; //trick to have hidden image fields
	
				echo '</div>';
				if($max_filefields>1 && $m < $max_filefields){
					echo '<div style="float:right;width:60px"> <span class="glyphicon glyphicon-cloud-upload"></span>
					<input type="button" id="fileAdd" value="+" class="btn btn-xs btn-default"/>
					<input type="button" id="fileDel" value="-" class="btn btn-xs btn-default"/>
					</div>';
				}
				if($max_filefields>1 && $m <= $max_filefields){
					echo '<div id="fileinput'.$m.'" style="margin-bottom:4px;width:300px;" class="fileclonedInput">';
					echo ' <input type="file" name="file'.$m.'" id="file'.$m.'" onchange="this.style.backgroundColor = \'\'" class="form-control" />';
					echo '</div>';
				}
					echo '</td>';
					echo  '</tr>';
				}
				echo  '<tr  >';
				echo  '<td>';
				echo  JText::_('COM_VMVENDOR_VMVENADD_FORM_IMAGE');
				echo ' <span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="(png,gif,jpg) '.JText::_('COM_VMVENDOR_VMVENADD_FORM_MAXSIDE').' '.$maximgside.'px"></span>';
				echo '</td>';
				echo  '<td>';
				$k = 1;
				
				
				foreach($this->product_images as $product_image){
					echo '<div id="imginput'.($k ).'_edit" class="imgclonedInput well well-sm" >';
					
					
					echo '<img src="'.$juri.$product_image->file_url_thumb.'" height="25" title="'.$product_image->file_title.'" style="vertical-align:middle;padding:2px;"/>';
					echo ' <input type="file" name="image'. $k .'" id="image'. $k .'" onchange="this.style.backgroundColor = \'\'" style="margin-bottom:4px;width:310px;" class="form-control" />';
					echo '<div class="file_removal" style="float:right;width:50px;">';
					if($k>1){
						echo '<div title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CHECKTOREMOVEIMAGE').'" class="'.$tooltip_class.'">';
						echo '<input type="checkbox" name="delimg'. $k .'" id="delimg'. $k .'" >';
						
						echo '<span class="glyphicon glyphicon-trash"></span>';
						echo '</div>';
					}
					else{
						echo '<div  class="'.$tooltip_class.'" title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_CANTDELETEFIRSTIMG').'">';
						echo '<input type="checkbox"   disabled >';
						echo '<span class="glyphicon glyphicon-trash"></span>';
						echo '</div>';
					}
					echo '<input type="hidden" name="media_id'. $k .'" id="media_id'. $k .'" value="'.$product_image->virtuemart_media_id.'" >';
					echo '</div>';
					
					echo '</div>';
					$k++;
				}
				
				
				$img_ajax = "var jq = jQuery.noConflict();
				jq(document).ready(function() {
				jq('#imgAdd').click(function() {
					var num     = (jq('.imgclonedInput').length ) ;
					var newNum  = new Number(num + 1 );	
					var newElem = jq('#imginput' + num).clone().attr('id', 'imginput' + newNum);
					newElem.children(':first').attr('id', 'image' + newNum).attr('name', 'image' + newNum);
					jq('#imginput' + num).after(newElem);
					jq('#imgDel').attr('disabled',false);
					if (newNum == $max_imagefields )
						jq('#imgAdd').attr('disabled',true);
				});
	 
				jq('#imgDel').click(function() {
					var num = jq('.imgclonedInput').length;
					jq('#imginput' + num).remove();
					jq('#imgAdd').attr('disabled',false);	 
					if (num-1 == $k )
						jq('#imgDel').attr('disabled',true);
				});
	 
				jq('#imgDel').attr('disabled',true);
			});";
				$doc->addScriptDeclaration($img_ajax);
				echo '<div style="display:none;">'; //trick to have hidden image fields
	
				echo '</div>';
				if($max_imagefields>1 && $k < $max_imagefields){
					echo '<div style="float:right;width:60px"> <span class="glyphicon glyphicon-camera"></span>
					<input type="button" id="imgAdd" value="+" class="btn btn-xs btn-default"/>
					<input type="button" id="imgDel" value="-" class="btn btn-xs btn-default"/>
					</div>';
				}
				
				if($max_imagefields>1 && $k <= $max_imagefields){
					echo '<div id="imginput'.($k ).'" style="margin-bottom:4px;width:310px;" class="imgclonedInput" >';
					echo ' <input type="file" name="image'.($k ).'" id="image'.($k ).'" onchange="this.style.backgroundColor = \'\'" class="form-control"/> ';
					echo '<div style="clear:both" > </div>';
					echo '</div>';
				}
				echo '</td>';
				echo  '</tr>';
				
				
				
				if($enable_vm2tags OR $enable_vm2geolocator OR $enable_vm2sounds  OR $enable_vm2dropdownfields){
					echo  '<tr>';
					echo  '<td colspan="2">';
					echo  '</td>';
					echo  '</tr>';
					echo  '<tr >';
					echo '<td colspan="2">';
					echo JText::_('COM_VMVENDOR_VMVENADD_CUSTOMFIELDS');
					echo  '</td>';				
					echo  '</tr>';
				}

				if($enable_vm2tags){
					//$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js');
					$doc->addScript($juri.'components/com_vmvendor/assets/js/jquery.tagsinput.min.js');
					$doc->addScript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js');
					
					$doc->addStylesheet($juri.'components/com_vmvendor/assets/js/jquery.tagsinput.css');
					$doc->addStylesheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/start/jquery-ui.css');
					$tag_script = "var jq = jQuery.noConflict();
									function onAddTag(tag) {
										alert(\"Added a tag: \" + tag);
									}
									function onRemoveTag(tag) {
										alert(\"Removed a tag: \" + tag);
									}
									
									function onChangeTag(input,tag) {
										alert(\"Changed a tag: \" + tag);
									}
									jq(function() {
										jq('#formtags').tagsInput({width:'auto'});
	
								// Uncomment this line to see the callback functions in action
								//			jq('input.tags').tagsInput({onAddTag:onAddTag,onRemoveTag:onRemoveTag,onChange: onChangeTag});		
								
								// Uncomment this line to see an input with no interface for adding new tags.
								//			jq('input.tags').tagsInput({interactive:false});
			});";
			
					$doc->addScriptDeclaration($tag_script);
					echo  '<tr  class="sectiontableentry1">';
					echo '<td>';
					echo JText::_('COM_VMVENDOR_VMVENADD_FORM_TAGS').' <span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_TAGSLIMIT').'"></span>';
					echo  '</td>';
					
					echo '<td >';
					if($this->product_tags!=''){
						$obj = json_decode($this->product_tags);
						$tags = $obj->{'product_tags'};	
					}
					else
						$tags ='';
					echo '<input type="text" size="50" name="formtags" id="formtags"  value="'.$tags.'" class="form-control tags" />';
					
					echo '</td>';
					echo  '</tr>';	
				}
				
				
				
				if($enable_vm2geolocator){
					// get the vm2geolocator custom plugin parameters
					
					function get_between($input, $start, $end) { 
					  $substr = substr($input, strlen($start)+strpos($input, $start), (strlen($input) - strpos($input, $end))*(-1)); 
					  return $substr; 
					}
					$fe_map_width = get_between($vm2geo_params, 'fe_map_width="', '"|fe_map_height');
					
					$q ="SELECT `custom_param` FROM `#__virtuemart_product_customfields` WHERE `virtuemart_product_id`='".JRequest::getVar('productid')."' AND `custom_value`='vm2geolocator' ";
					$db->setQuery($q);
					$custom_param = $db->loadResult();
					if ($custom_param!=''){
						$be_lat =  get_between($custom_param, '{"latitude":"', '","longitude');
						$be_lng =  get_between($custom_param, 'longitude":"', '","zoom');
						$be_zoom =  get_between($custom_param, 'zoom":"', '","maptype');
						$be_maptype =  get_between($custom_param, 'maptype":"', '"}');
					}
					else{
						$custom_param='';
						$q = "SELECT `custom_params` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2geolocator' AND `published`='1' ";
						$db->setQuery($q);
						$vm2geo_params= $db->loadResult();
						$be_lat =  get_between($vm2geo_params, 'default_lat="', '"|default_lng');
						$be_lng =  get_between($vm2geo_params, 'default_lng="', '"|default_zoom');
						$be_zoom =  get_between($vm2geo_params, 'default_zoom="', '"|default_maptype');
						$be_maptype	= get_between($vm2geo_params, 'default_maptype="', '"|stylez');	
					}
					
					
	
					$doc->addScript( "http://maps.googleapis.com/maps/api/js?sensor=true&libraries=places");
					
					$mapscript ="function add_Event(obj_, evType_, fn_){ 
							if (obj_.addEventListener)
								obj_.addEventListener(evType_, fn_, false); 
							else
								obj_.attachEvent('on'+evType_, fn_);  
						};
						function initializemap(){
							directionsDisplay = new google.maps.DirectionsRenderer();
							var latlng = new google.maps.LatLng(".$be_lat.",".$be_lng.");
							var myOptions = {
								zoom: ".$be_zoom.",
								center: latlng,
								mapTypeId: google.maps.MapTypeId.".$be_maptype.",
								scrollwheel: false,
								navigationControl: true,
								scaleControl: true,
								mapTypeControl: true,
								overviewMapControl:true,
								streetViewControl: true
							}
	
							var map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);					
							var input = document.getElementById('searchTextField');
							var autocomplete = new google.maps.places.Autocomplete(input);
								autocomplete.bindTo('bounds', map);
								var place_infowindow = new google.maps.InfoWindow();
								var place_marker = new google.maps.Marker({
								  map: map
								});
								google.maps.event.addListener(autocomplete, 'place_changed', function() {
								   place_infowindow.close();
								  var place = autocomplete.getPlace();
								  if (place.geometry.viewport) {
									map.fitBounds(place.geometry.viewport);
								  } else {
									map.setCenter(place.geometry.location);
									map.setZoom(17);  // Why 17? Because it looks good.
								  }				
								  var image = new google.maps.MarkerImage(
									  place.icon,
									  new google.maps.Size(71, 71),
									  new google.maps.Point(0, 0),
									  new google.maps.Point(17, 34),
									  new google.maps.Size(35, 35));
								  place_marker.setIcon(image);
								  place_marker.setPosition(place.geometry.location);
								  var address = '';
								  if (place.address_components) {
									address = [(place.address_components[0] &&
												place.address_components[0].short_name || ''),
											   (place.address_components[1] &&
												place.address_components[1].short_name || ''),
											   (place.address_components[2] &&
												place.address_components[2].short_name || '')
											  ].join(' ');
								  }
								  place_infowindow.setContent('<div><img src=\"' + place.icon + '\" width=\"24\" height=\"24\"/> <strong>' + place.name + '</strong><br>' + address + '<br />".JText::_('COM_VMVENDOR_VMVENADD_FORM_PLACEFOUND')."');
								  place_infowindow.open(map, place_marker);
								});
								var product = new google.maps.LatLng(".$be_lat.",".$be_lng.");
								var marker = new google.maps.Marker({	";
								
								
								if($custom_param!='')
								$mapscript .="position: product,";
								
								$mapscript .="map: map,
									clickable: false,					
									title:'".JText::_('VMCUSTOM_VM2GEOLOCATOR_PRODUCTLOCATION')."'
								});
								google.maps.event.addListener(map, 'click', function(event) {
									place_infowindow.close();												 
									var PointTmp2 = event.latLng;
									marker.setPosition(PointTmp2);
									document.getElementById('latitude').value = PointTmp2.lat();
									document.getElementById('longitude').value = PointTmp2.lng();
									document.getElementById('latitude').style.backgroundColor = '';
									document.getElementById('longitude').style.backgroundColor = '';
								});	
								google.maps.event.addListener(map, 'zoom_changed', function(event) {
								document.getElementById('zoom').value = map.getZoom();
								});
								google.maps.event.addListener(map, 'maptypeid_changed', function(event) {
									var mapTypeID = map.getMapTypeId();
									document.getElementById('maptype').value = mapTypeID.toUpperCase();
								});
						}
							function initgmap() {
							//if (arguments.callee.done) GUnload();";
							if($custom_param!=''){
								$mapscript .="document.getElementById('latitude').value ='".$be_lat."';
											document.getElementById('longitude').value ='".$be_lng."';";
							}
							else{
								$be_lat='';
								$be_lng='';
							}
							$mapscript .="arguments.callee.done = true;
								initializemap();
							};
							add_Event(window, 'load', initgmap);";
							$doc->addScriptDeclaration($mapscript);
	
					echo  '<tr  class="geolocator" style="background-color:#f7f7f7;">';
					echo '<td>';
					echo JText::_('COM_VMVENDOR_VMVENADD_FORM_LOCATION').' <span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_LOCATIONDESC').'"></span>';
					echo  '</td>';
					
					echo '<td >';
					echo '<div id="map_canvas" style="height:300px;">#dev<div>
					<div style="clear:both;position:absolute;"></div>';
					echo  '</td>';
					echo  '<tr  class="geolocator" style="background-color:#f7f7f7;">';
					echo '<td>';
					echo '</td>';
					echo '<td>';
					
					
					echo '<div style="padding-bottom:3px;"><input id="searchTextField" type="text" size="50" placeholder="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_PLACE_SEARCH').'" class="form-control" /></div>';
					
					echo '<div class=" form-group col-lg-3"><input title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_LAT').'" type="text" value="'.$be_lat.'" size="10" name="latitude" id="latitude" readonly class="form-control" /></div> ';
	
					echo '<div class=" form-group col-lg-3"><input title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_LNG').'" type="text" value="'.$be_lng.'" size="10" name="longitude" id="longitude" readonly class="form-control" /></div> ';
	
					echo '<div class=" form-group col-lg-2"><input title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_ZOOM').'" type="text" value="'.$be_zoom.'" size="2" name="zoom" id="zoom" readonly class="form-control" /></div> ';
	
					echo '<div class=" form-group col-lg-3"><input title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_MAPTYPE').'" type="text" value="'.$be_maptype.'" size="10" name="maptype" id="maptype" readonly class="form-control"/></div>';
					echo '<div class="form-group"><a href="javascript:initgmap();" class="btn btn-sm btn-default" '.$tooltip_class.'t" title="'.JText::_('COM_VMVENDOR_VMVENADD_FORM_RESET').'"> <span class="glyphicon glyphicon-refresh"></span></a></div>';
					echo '</td>';
					echo  '</tr>';
					
					
				}
				
				
				if($enable_vm2dropdownfield ){
					function jsonRemoveUnicodeSequences($struct) {
					   return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
					}
					$i = 1;
	
					
					
					foreach($vm2drops as $vm2drop){					
						$q = "SELECT custom_param FROM #__virtuemart_product_customfields WHERE virtuemart_product_id='".JRequest::getVar('productid')."' AND virtuemart_custom_id='".$vm2drop->virtuemart_custom_id."' ";
						$db->setQuery($q);
						$custom_param = $db->loadResult();
						$obj = json_decode($custom_param);
						$custom_param = $obj->{'options'};
	
						$list_options = substr($vm2drop->custom_params , 9);
						$list_options = substr($list_options , 0 , -2);
						$seps = explode(',' , $list_options);
						echo  '<tr  class="sectiontableentry1">';
						echo '<td>';
						echo $vm2drop->custom_title.' <span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="'.$vm2drop->custom_tip.'">';
						echo  '</td>';	
						echo '<td >';
						echo '<select id="vm2dropdownfield'.$i.'" name="vm2dropdownfield'.$i.'" onchange="this.style.backgroundColor = \'\';" class="form-control">';
						echo '<option value="">'.JText::_('COM_VMVENDOR_SELECT_OPTION').'</option>';
						for($f = 0 ; $f < count($seps) ; $f++){
							$seps_f = $seps[$f ];
							$seps_f= jsonRemoveUnicodeSequences($seps_f);
							$seps_f = substr($seps_f , 1);
							$seps_f = substr($seps_f , 0 , -1);
							$seps_f = stripslashes($seps_f);
							echo '<option value="'.$seps_f.'" ';
							if($custom_param == $seps_f)
								echo ' selected="selected" ';
							echo ' >'.$seps_f.'</option>';			
						}
						echo '<select>';
						echo '<input type="hidden" name="hiddendropfieldid_'.$i.'" value="'.$vm2drop->virtuemart_custom_id.'" />';
						echo '</td>';
						echo  '</tr>';	
						$i++;
					}	
				}
	
				
				////////////////////////////// Core Custom fields support Hasardous place as Virtuemart shared and multivendor custom fields is not totally done yet.	
				
				if($enable_corecustomfields){
					$i = 0;
					foreach ($this->core_custom_fields as $core_custom_field){
						$i++;
						echo  '<tr >';
						echo '<td>';
						//echo 'Under dev: ';
						echo $core_custom_field->custom_title;
						
						if($core_custom_field->custom_tip !='' OR $core_custom_field->custom_field_desc!='' )
							echo ' <span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="'.$core_custom_field->custom_tip.'"></span>';
						echo  '</td>';
						
						echo '<td >';
						
						switch($core_custom_field->field_type){
							case "S":  //string
								echo '<input name="corecustomfield_'.$i.'" type="text" value="'.$core_custom_field->value.'" size="50" class="form-control" />';
							break;
							case "I": // integer
								echo '<input name="corecustomfield_'.$i.'" type="text" value="'.$core_custom_field->value.'" size="50"  class="form-control" />';
							break;
							case "B": // bolean
								echo '<div class="radio-inline"><input name="corecustomfield_'.$i.'" id="corecustomfield_'.$i.'_0" type="radio"   value="0" ';
								if($core_custom_field->value =='0' )
									echo ' checked="checked" ';
								echo  '/><label for="corecustomfield_'.$i.'_0">'.JText::_('JNo').'</label></div> 
								<div class="radio-inline"><input name="corecustomfield_'.$i.'" id="corecustomfield_'.$i.'_1" type="radio"    value="1"';
								if($core_custom_field->value =='1' )
									echo ' checked="checked" ';
								echo ' /> <label for="corecustomfield_'.$i.'_1">'.JText::_('JYes').'</label></div>';
							break;
							case "D": // date
						
								echo JHTML::calendar('','corecustomfield_'.$i ,'corecustomfield_'.$i,'%Y-%m-%d');
							break;
							case "T": // time
								echo '<input class="form-control" name="corecustomfield_'.$i.'" type="text" value="'.$core_custom_field->value.'" size="50"  />';
							break;
							case "M": // image
								
							break;
							case "V": // cart variant
								if(!$core_custom_field->is_list)
									echo '<input name="corecustomfield_'.$i.'" type="text" value="'.$core_custom_field->value.'" size="50" class="form-control" />';
								else{
									$exploded_cartvar = explode(';',$core_custom_field->custom_value);
									if(count($exploded_cartvar)>1){
										echo '<select name="corecustomfield_'.$i.'" id="corecustomfield_'.$i.'" class="form-control" >';
										echo '<option value="">'.JText::_('COM_VMVENDOR_SELECT_OPTION').'</option>';
										for($i = 0; $i<count($exploded_cartvar);$i++){
											echo '<option value="'.$exploded_cartvar[$i].'"';
											if($exploded_cartvar[$i] == $core_custom_field->value)
												echo ' selected ';
											echo '>'.$exploded_cartvar[$i].'</option>';	
										}
										echo '</select>';
									}
								}
									
							break;
							case "A": // generic Child variant
								
							break;
							case "X": // editor
								jimport( 'joomla.html.editor' );
								$editor = &JFactory::getEditor();
								$editor_customfield_html = $editor->display("corecustomfield_".$i , $core_custom_field->value , "100%;", '200', '5', '30', false);
								echo  $editor_customfield_html;
								
							break;
							
							case "Y": // textarea
								echo '<textarea name="corecustomfield_'.$i.'" class="form-control">';
								echo $core_custom_field->value;
								echo '</textarea>';
								
							break;
						}
						echo '</td>';
						echo  '</tr>';
					}
				}
				
				
					echo  '<tr  class="sectiontableentry2"><td>';
				//(new): click on string checks the checkbox
				echo  '</td><td ><div id="checkboxtd" class="checkbox inline" style="float:left;width:40%"><input type="checkbox" name="formterms" id="formterms" onchange="document.getElementById(\'checkboxtd\').style.backgroundColor = \'\';"/> <label for="formterms">'.JText::_('COM_VMVENDOR_VMVENADD_FORM_IAGREE').' '; 
				if($termsurl !=NULL)
					echo  '<a href="'.$termsurl.'" target="_blank" >'.JText::_('COM_VMVENDOR_VMVENADD_FORM_TERMS').'</a>';
				else
					echo  JText::_('COM_VMVENDOR_VMVENADD_FORM_TERMS');	
				echo  ' <b>*</b></label></div>';
				
				if($profileman==2 && $autopublish){
					echo '<div style="float:left;width:60%;text-align:right" class="checkbox inline">';	
					echo '<input type="checkbox" name="announceupdate" id="announceupdate"  /> <label for="announceupdate" >'.JText::_('COM_VMVENDOR_VMVENADD_ANNOUNCEUPDATE').'</label>';
					echo '</div>';
				}
				
				
				echo '</td></tr>';
				
				
				
				echo  '<tr >';
				echo  '<td></td>';
				echo  '<td>';
				if ($user->id !=0){
					
					
					echo '<button type="submit" name="update" id="button" value="'.JText::_('COM_VMVENDOR_VMVENADD_BTTN_UPDATE').'" class="btn btn-primary">'.JText::_('COM_VMVENDOR_VMVENADD_BTTN_UPDATE').'</button>';
					
					
					echo ' <input type="button" name="cancel" id="cancelbutton" value="'.JText::_('COM_VMVENDOR_VMVENADD_BTTN_CANCEL').'" onclick="history.go(-1)" class="btn btn-lg btn-default"/> ';
					echo  ' <img src="'.$juri.'components/com_vmvendor/assets/img/loader.gif" alt="" width="200" height="19" border="0" name="loading" id="loading"  align="absmiddle" style="display: none;" />';
				}
				else
					JError::raiseWarning( 100, '<font color="red"><b>'.JText::_('COM_VMVENDOR_VMVENADD_ONLYLOGGEDIN').'</b></font>');
				echo '</td>';
				echo  '</tr>';
				echo  '</table>';
				echo '<input type="hidden" name="option" value="com_vmvendor" />
							<input type="hidden" name="controller" value="updateproduct" />
							<input type="hidden" name="task" value="updateproduct" />';
				echo  '</form>';
				echo '<div style="clear:both;"> </div>';
}
else
	JError::raiseWarning( 100, '<font color="red"><b>'.JText::_('COM_VMVENDOR_VMVENADD_NOTYOURPRODUCT!').'</b></font>');
?>