<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * Vmvendord Component Controller
 */
class VmvendorController extends JController
{
	function display() {
        parent::display();
    }	
	
	
	public function mailcustomer()  {
		$user 			= &JFactory::getUser();
		$db 			= &JFactory::getDBO();
		$doc 			= &JFactory::getDocument();
		$juri 			= JURI::base();
		$app 			= JFactory::getApplication();
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$profileman 			= $cparams->getValue('profileman');
		$naming 				= $cparams->getValue('naming', 'username');	
		$customercontactform	= $cparams->getValue('customercontactform', '1'); //1=email 	11=email+admin    2=jomsocial pms
		$emailto		= JRequest::getVar('emailto');
		if(JRequest::getVar('formname')!='' && JRequest::getVar('formemail')!='' &&  JRequest::getVar('formsubject')!='' && JRequest::getVar('formmessage')!='' ){
			$product_url = JRequest::getVar('formhref');
			$message =& JFactory::getMailer(); 
			$config =& JFactory::getConfig();
			$mailfrom = JRequest::getVar('formemail');
			$fromname = JRequest::getVar('formname');
			$subject = JRequest::getVar('formsubject');
			$body = JRequest::getVar('formmessage').",\r\n\r\n";
			$body .= urldecode($product_url);
			$mailerror = JText::_('COM_VMVENDOR_ASKVENDOR_EMAILFAILED');
			$message->addRecipient($emailto); 
			$message->addBCC( $mailfrom );
			if($customercontactform=='11')
				$message->addBCC( $config->getValue( 'config.mailfrom' ) );   // site admin
			$message->setSubject($subject);
			$message->setBody($body);
			$sender = array( $mailfrom, $fromname );
			$message->setSender($sender);
			$sent = $message->send();
			if ($sent != 1) 
				echo  $mailerror;
			else{
				echo JText::_('COM_VMVENDOR_ASKVENDOR_SENT');	//Email sent successfully
			}
			$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_ASKVENDOR_SENT' );
			$app->enqueueMessage( $message );
		}
		else
			JError::raiseWarning('100' ,  JText::_('COM_VMVENDOR_ASKVENDOR_EMAILFAILED')  );
		$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid'));
	}
	
	
	
	
	
	public function addproduct() {
		$user 			= &JFactory::getUser();
		$db 			= &JFactory::getDBO();
		$doc 			= &JFactory::getDocument();
		$juri 			= JURI::base();
		$app 			= JFactory::getApplication();
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$image_path 	=  VmConfig::get('media_product_path');
		$safepath		=	VmConfig::get( 'forSale_path' );		
		$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$multilang_mode = $cparams->getValue('multilang_mode', 0);

		
		if($multilang_mode >0){
			$active_languages	=	VmConfig::get( 'active_languages' ); //en-GB
		}
		
		$profileman 	= $cparams->getValue('profileman');
		$naming 		= $cparams->getValue('naming', 'username');				
		$vmitemid 		= $cparams->getValue('vmitemid', '103');
		$profileitemid 	= $cparams->getValue('profileitemid', '2');
		$autopublish 	= $cparams->getValue('autopublish', 1);
		$enablerss 		= $cparams->getValue('enablerss', 1);
		$rsslimit 		= $cparams->getValue('rsslimit', 10);
		$emailnotify_addition 	= $cparams->getValue('emailnotify_addition', 0);
		$to 			= $cparams->getValue('to');
		$maxfilesize 	= $cparams->getValue('maxfilesize', '4000000');//4 000 000 bytes   =  4M
		$max_imagefields= $cparams->getValue('max_imagefields', 4);
		$max_filefields	= $cparams->getValue('max_filefields', 4);
		$maximgside 	= $cparams->getValue('maximgside', '600');
		$thumbqual 		= $cparams->getValue('thumbqual', 70);
		$enable_sdesc 		= $cparams->getValue('enable_sdesc', 1);
		$wysiwyg_prod 		= $cparams->getValue('wysiwyg_prod', 0);
		$enablefiles 	= $cparams->getValue('enablefiles', 1);
		$enableweight 	= $cparams->getValue('enableweight', 1);
		$weightunits 	= $cparams->getValue('weightunits');
		//$allowpublicfiles	=  $cparams->getValue('allowpublicfiles', 0);
		$enableprice	= $cparams->getValue('enableprice', 1);
		$enablestock	= $cparams->getValue('enablestock', 1);
		$cat_suggest 	= $cparams->getValue('cat_suggest',1);
		
		$imagemandatory = $cparams->getValue('imagemandatory', 0);
		$filemandatory 	= $cparams->getValue('filemandatory', 1);
		$allowedexts 	= $cparams->getValue('allowedexts', 'zip,mp3');
		$minimum_price	= $cparams->getValue('minimum_price');
		$sepext 		= explode( "," , $allowedexts );
		$countext 		= count($sepext);
		$stream			= $cparams->getValue('stream', 0);
		$maxspeed		= $cparams->getValue('maxspeed', '3000');
		$maxtime		= $cparams->getValue('maxtime', '365');
		$acy_listid		= $cparams->getValue('acy_listid');
			
		$enable_corecustomfields	= $cparams->getValue('enable_corecustomfields', 1);
		$enable_vm2tags			= $cparams->getValue('enable_vm2tags', 0);	
		$tagslimit				= $cparams->getValue('tagslimit', '5');
		$enable_vm2geolocator	= $cparams->getValue('enable_vm2geolocator', 0);
		$enable_vm2dropdownfield= $cparams->getValue('enable_vm2dropdownfield', 0);
		$enable_vm2sounds		= $cparams->getValue('enable_vm2sounds', 0);
		
		$thumb_path = $image_path.'resized/';
		
		$formfile='';
		
		$formname 			= JRequest::getVar('formname');
		
		$formdesc		 	= JRequest::getVar('formdesc');
		if($wysiwyg_prod)
			$formdesc     		= JRequest::getVar('formdesc', '', 'post', 'string', JREQUEST_ALLOWRAW);
		//if($enable_sdesc)
			$form_s_desc		 = JRequest::getVar('form_s_desc');
		/*else{
			$length = 250; 
			if (strlen($formdesc) <= $length) {
			   $form_s_desc	=  $formdesc; //do nothing
			} else {
			   $form_s_desc = substr( $formdesc ,0,strpos($formdesc ,' ',$length));
			   $form_s_desc .= '...';
			}
		}*/
		
			
			
		if($enableprice)
			$formprice 			= JRequest::getVar('formprice');
		else
			$formprice 			= 0;
		$formweight 			= JRequest::getVar('formweight');
		$formweightunit			= JRequest::getVar('formweightunit');
		
		$formcat 			= JRequest::getVar('formcat');
		$formmanufacturer	= JRequest::getVar('formmanufacturer');
		
		$formtags			= JRequest::getVar('formtags');
		$latitude			= JRequest::getVar('latitude');
		$longitude			= JRequest::getVar('longitude');
		$zoom				= JRequest::getVar('zoom');
		$maptype			= JRequest::getVar('maptype');
		$file1				= JRequest::getVar('file1', null, 'files', 'array');
		$file1name			= $file1['name'];
		$get_img			= JRequest::getVar('get_img');

		if($imagemandatory){	
			$first_image = JRequest::getVar('image1', null, 'files', 'array');
			if($first_image){
				$first_image_name = JFile::makeSafe($first_image['name']);						
				if( $first_image['name']!=''){
					$infosImg = getimagesize($first_image['tmp_name']);	
					//if ( (substr($first_image['type'],0,5) != 'image' || $infosImg[0] > $maximgside || $infosImg[1] > $maximgside )){
					if ( (substr($first_image['type'],0,5) != 'image' )){
						//image not valid
						JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_IMGEXTNOT') );
						$app->redirect('index.php?option=com_vmvendor&view=addproduct&Itemid='.JRequest::getVar('Itemid'));	
					}							
				}						
			}	
		}

		if( $formcat 
		   && $formname 
		   && ($formdesc || $form_s_desc) 
		   && ( ($enableweight && $formweight!='' && $formweightunit!='') OR !$enableweight  )
		   && ( ($enableprice && $formprice!='') OR !$enableprice  )
		   && $user->id > 0 
		   && ( ($enablefiles && $filemandatory  && $file1name!='' )  OR !$filemandatory  OR !$enablefiles     ) )
		{ // We add the product
			jimport('joomla.filesystem.file');
			$formsku 			= JRequest::getVar('formsku');
			$q ="SELECT COUNT(*) FROM `#__virtuemart_products` WHERE `product_sku`='".$formsku."' ";
			$db->setQuery($q);
			$checknotyetadded = $db->loadResult();
			if ($checknotyetadded <1){
				$formcurrency 		= JRequest::getVar('formcurrency');
				if($enablestock)
					$formstock 			= JRequest::getVar('formstock');
				else
					$formstock = '1';
					
					
					
			// we check here if vendor id has not been reset by 0 when editing an address
			$q = "SELECT `virtuemart_vendor_id` , `user_is_vendor` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id` = '".$user->id."' ";
			$db->setQuery($q);
			$row = $db->loadRow();
			$virtuemart_vendor_id = $row[0];
			$user_is_vendor		 = $row[1];
			if($virtuemart_vendor_id =='0' && $user_is_vendor='1' ){
				$uslug = str_replace( " ", "-",strtolower($user->$naming) );
				$uslug = str_replace( "'", "-",$uslug );
				$uslug = $user->id."-".$uslug ;			
				$q ="SELECT `virtuemart_vendor_id` 
				FROM `#__virtuemart_vendors_".VMLANG."`  
				WHERE `slug`='".$uslug."' ";
				$db->setQuery($q);
				$vvi= $db->loadResult();
				$q ="UPDATE `#__virtuemart_vmusers`  
				SET `virtuemart_vendor_id`='".$vvi."' 
				WHERE `virtuemart_user_id`='".$user->id."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));					
			}
			// look for main vendor currency
			$q ="SELECT `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id` ='1' " ;
			$db->setQuery($q);
			$currency_id = $db->loadResult();

			//////////////// Check if the vendor has ben created
				if(!$virtuemart_vendor_id OR $virtuemart_vendor_id=='0'){  ////////////////// We create the vendor and create the vmuser or update if allready exists as a customer 
					$q = "INSERT INTO `#__virtuemart_vendors` 
					( `vendor_name` , `vendor_currency` , `vendor_accepted_currencies` , `vendor_params` , `created_on` , `created_by` , `modified_on` , `modified_by` , `locked_on` , `locked_by` ) 
					VALUES 
					('".$db->getEscaped($user->$naming)."' , '".$currency_id."' , '' , 'vendor_min_pov=0|vendor_min_poq=1|vendor_freeshipment=0|vendor_address_format=\"\"|vendor_date_format=\"\"|' , '".date('Y-m-d H:i:s')."' , '".$user->id."' , '".date('Y-m-d H:i:s')."' , '".$user->id."' , '0000-00-00 00:00:00' , '0') ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));		
					$vendorid = $db->insertid();		
					$q = "INSERT INTO `#__virtuemart_vendors_".VMLANG."`  
					( `virtuemart_vendor_id` , `vendor_store_desc` , `vendor_terms_of_service` , `vendor_legal_info` , `vendor_store_name` , `vendor_phone` , `vendor_url` , `slug` ) 
					VALUES 
					('".$vendorid."' , '' , '' , '' , '".$db->getEscaped($user->$naming)."' , '' , '' , '".strtolower(str_replace(' ','-', $db->getEscaped($user->id.'-'.$user->$naming)))."') ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					if($multilang_mode >0){ //					
						for($i = 0 ; $i < count( $active_languages ) ; $i++){
							if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
								$q = "INSERT INTO `#__virtuemart_vendors_".strtolower( str_replace('-' , '_' , $active_languages[$i]) ) ."`  
								( `virtuemart_vendor_id` , `vendor_store_desc` , `vendor_terms_of_service` , `vendor_legal_info` , `vendor_store_name` , `vendor_phone` , `vendor_url` , `slug` ) 
								VALUES 
								('".$vendorid."' , '' , '' , '' , '".$db->getEscaped($user->$naming)."' , '' , '' , '".strtolower(str_replace(' ','-', $db->getEscaped($user->id.'-'.$user->$naming)))."') ";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
								
							}
							
						}
					}
						
					if($virtuemart_vendor_id==='0'){  // vmuser allready (has allready created a customer profile)
						$q = "UPDATE `#__virtuemart_vmusers`  
						 SET `virtuemart_vendor_id` ='".$vendorid."', 
						 `user_is_vendor` = '1' 
						 WHERE `virtuemart_user_id` ='".$user->id."' ";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
						$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_USERUPDATEDVENDOR');
					}
					elseif(!$virtuemart_vendor_id){ //vmuser does not exist, we create it (without a customer_number...)
						$q = "INSERT INTO `#__virtuemart_vmusers` 
						( `virtuemart_user_id` , `virtuemart_vendor_id` , `user_is_vendor` , `customer_number` , `perms` )
						VALUES
						('".$user->id."' , '".$vendorid."' , '1', '', 'shopper')";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
						$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_VENDORCREATED');
					}
					$virtuemart_vendor_id = $vendorid;
					
					if($acy_listid!=''){ // we subscribe the member to the vendor mailing list
						// is new vendor allready subscribed? to that list!?
						$q = "SELECT COUNT(acyl.`subid`)  
						FROM `#__acymailing_listsub` acyl 
						LEFT JOIN `#__acymailing_subscriber` acys ON acys.`subid` = acyl.`subid` 
						WHERE acys.`userid` = '".$user->id."' 
						AND acyl.`listid` ='".$acy_listid."' ";
						$db->setQuery($q);
						$is_subscribed = $db->loadResult();
						
						if( $is_subscribed < 1 ){
							$q = "SELECT COUNT(*) FROM `#__acymailing_subscriber` WHERE `userid`='".$user->id."' ";
							$db->setQuery($q);
							$is_subscriber = $db->loadresult();
							if( $is_subscriber <1 ){					
								$q ="INSERT INTO `#__acymailing_subscriber` 
								(`email` , `userid`, `name` , `created` , `confirmed`, `enabled`, `accept`, `html` )
								VALUES 
								('".$user->email."','".$user->id."','".$user->name."','".time()."','1','1','1','1' ) ";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
								$subid = $db->insertid();
							}
							else{
								$q ="SELECT `subid` FROM `#__acymailing_subscriber` WHERE `userid` ='".$user->id."' ";	
								$db->setQuery($q);
								$subid = $db->loadResult();
							}
							$q ="INSERT INTO `#__acymailing_listsub` 
								(`listid` , `subid` , `subdate` , `status`) 
								VALUES 
								('".$acy_listid."' , '".$subid."' , '".time()."' , '1' ) ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));	
						}
					}
					
					if($profileman==2){ // auto add the jomsocial profile applciation
						$q = "SELECT COUNT(*) FROM `#__community_apps` WHERE `apps`='vmvendor' AND `userid`='".$user->id."'  " ;
						$db->setQuery($q);
						$app_added = $db->loadResult();
						if($app_added <1){
							$q ="INSERT INTO `#__community_apps` 
							( `userid` , `apps` , `ordering` , `position` ) 
							VALUES
							('".$user->id."' , 'vmvendor' , '0' , 'content' )"; 	
							 $db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));		
						}	
					}
					
					
					
			
					$app->enqueueMessage( $message );
				}//////////////// Vendor is created
				
				
				
				
				$q = "INSERT INTO `#__virtuemart_products` 
					( `virtuemart_vendor_id` , `product_parent_id` , `product_sku` , ";
				if($enableweight)
					$q .= " `product_weight` , `product_weight_uom` , ";
				$q .=" `product_in_stock` , `product_ordered` ,  `published` , `created_on` , `created_by` ) 
					VALUES 
					( '".$virtuemart_vendor_id."' , '0' , '".$formsku."' , ";
				if($enableweight)
					$q .= " '".$formweight."' , '".$formweightunit."' , ";			 
				$q .= " '".$formstock."' , '0' , '".$autopublish."' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
				
				
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$virtuemart_product_id = $db->insertid();
					
					// now we have the $virtuemart_product_id
				
				
				///////////////// 3rd party plugins insertion
				if($enable_vm2tags && $formtags!=''){
					$q ="SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2tags' AND `published`='1'";
					$db->setQuery($q);
					$virtuemart_custom_id = $db->loadResult();
					
					$septags = explode(',' ,$formtags); 
					$i=0;
					$limited_tags='';
					foreach ( $septags as $septag ){
						$i++;
						if ( $i <= $tagslimit && strlen($septag)>=2 && strlen($septag)<=20 ){	
							if( $i > 1)
								$limited_tags .=',';
							$limited_tags .= $septag ;			
						}				
					}
					$tags_array =array('product_tags' => $limited_tags);
					$limited_tags = json_encode($tags_array);
					$q = "INSERT INTO `#__virtuemart_product_customfields` 
					( `virtuemart_product_id` , `virtuemart_custom_id` , `custom_value` , `custom_param` , `published` , `created_on` , `created_by`  ) 
					VALUES 
					('".$virtuemart_product_id."' , '".$virtuemart_custom_id."' , 'vm2tags' , '".$db->getEscaped($limited_tags)."' , '1',  '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					
					if(count($septags) > $tagslimit){
						JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.$tagslimit.'</b> '.JText::_('COM_VMVENDOR_VMVENADD_FIRSTTAGSONLY').'');	
						
					}	
				}
				
				if($enable_vm2geolocator && $latitude!='' && $longitude!=''){
					
					$q="SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element` = 'vm2geolocator' AND `published`='1' ";
					$db->setQuery($q);
					$virtuemart_custom_id = $db->loadResult();
					
					if($virtuemart_custom_id!=''){
						$q="INSERT INTO `#__virtuemart_product_customfields` 
					(`virtuemart_product_id`,`virtuemart_custom_id`,`custom_value`,`custom_param`,`published`,`created_on`,`created_by`)
					VALUES 
					('".$virtuemart_product_id."','".$virtuemart_custom_id."','vm2geolocator','{\"latitude\":\"".$latitude."\",\"longitude\":\"".$longitude."\",\"zoom\":\"".$zoom."\",\"maptype\":\"".$maptype."\"}','1','".date('Y-m-d H:i:s')."','".$user->id."')";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
				}
				if($enable_vm2dropdownfield){
					$q = "SELECT virtuemart_custom_id FROM `#__virtuemart_customs` WHERE `custom_element`='vm2dropdownfield' AND `published`='1' ";
					$db->setQuery($q);
					$vm2drops = $db->loadObjectList();
					$i = 1;
					foreach($vm2drops as $vm2drop){
						if(JRequest::getVar('vm2dropdownfield'.$i ,'','post') !=''){
							$q = "INSERT INTO #__virtuemart_product_customfields 
							(`virtuemart_product_id`,`virtuemart_custom_id`,`custom_value`,`custom_param`,`published`,`created_on`,`created_by`)
							VALUES 
						('".$virtuemart_product_id."','".$vm2drop->virtuemart_custom_id."','vm2dropdownfield','{\"options\":\"". $db->getEscaped( JRequest::getVar('vm2dropdownfield'.$i ,'','post') )."\"}','1','".date('Y-m-d H:i:s')."','".$user->id."')";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}
						$i++;
					}	
				}
		
				////////////////////////
				
				
				
				
				
				
			
				$admitted = 1;
				if($enablefiles && $filemandatory)
					$admitted = 0;
				
				if($enablefiles){
					for ($i=1; $i <= $max_filefields ;$i++){ ////////////// images
						$fileisvalid = 0;
						$file = JRequest::getVar('file'.$i, null, 'files', 'array');
						if($file){
							$filename = JFile::makeSafe($file['name']);
							$ext =  JFile::getExt($filename);
							$formfilesize 	= $file['size'];
							$form_mime 		= $file['type'];					
							for ( $j=0 ; $j < $countext ; $j++ ){
								if ($sepext[$j] == $ext)
									$fileisvalid = 1; // file has an allowed extention					
							}
							
							if($filename!=''){							
								if	(!$fileisvalid)
									JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> <font color="red"><b>'.JText::_('COM_VMVENDOR_FILEEXTNOT').'</b></font>');
								if($formfilesize > $maxfilesize OR $formfilesize==0){
									$fileisvalid = 0;
									JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> <font color="red"><b>'.JText::_('COM_VMVENDOR_MAXFILESIZEX').'</b></font> '.$formsku ."_".$filename );
								}
							}
							else
								$fileisvalid = 0;
								
							$file_is_downloadable = 0;
							$file_is_forSale = 1;
							$target_filepath = $safepath .  $formsku ."_".$filename;							
							if( $formprice == '0'){
								$file_is_downloadable = 1;
								$file_is_forSale = 0;
								$target_filepath = $image_path. $formsku ."_".$filename;
							}
								
							if($fileisvalid){
								if( JFile::upload($file['tmp_name'] , $target_filepath ) )
									$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_FILEUPLOADRENAME_SUCCESS').' '. $formsku.'_'. $filename);
								else{
									JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> <font color="red"><b>'.JText::_('COM_VMVENDOR_FILEUPLOAD_ERROR').'</b></font>');
									//$fileisvalid = 0;	
								}
							}
							
							if($fileisvalid){	
								
								$q = "INSERT INTO `#__virtuemart_medias` 
									( `virtuemart_vendor_id` , `file_title` , `file_mimetype` , `file_type` , `file_url` , `file_url_thumb` , `file_is_downloadable` , `file_is_forSale` , `published` , `created_on` , `created_by` )
									VALUES
									(  '".$virtuemart_vendor_id."' , '".$formsku.'_'.$filename."' , '".$form_mime."' , 'product' , '".addslashes($target_filepath)."' , '' , '".$file_is_downloadable."', '".$file_is_forSale."', '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";																///addslashes($target_filepath)
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
								$virtuemart_media_id = $db->insertid();
								$forsalefiles_plugin = '1';
								if( $formprice >0){ // product is not free, file is For sale and has to be added as a ST42_download custom plugin entry
									if($forsalefiles_plugin == '1'){  // Istraxx plugin is used
										$q = "SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='st42_download' ";
										$db->setQuery($q);
										$virtuemart_custom_id = $db->loadresult();
										
										
										$q = "INSERT INTO `#__virtuemart_product_customfields` 
										( `virtuemart_product_id` , `virtuemart_custom_id` , `custom_value` ,  `custom_param` , `published` , `created_on` , `created_by` )
										VALUES 
										( '".$virtuemart_product_id."' , '".$virtuemart_custom_id."' , 'st42_download' ,  '{\"media_id\":\"".$virtuemart_media_id."\",\"stream\":\"".$stream."\",\"maxspeed\":\"".$maxspeed."\",\"maxtime\":\"".$maxtime."\"}' , '0' , '".date('Y-m-d H:i:s')."' , '".$user->id."'  )";
										$db->setQuery($q);
										if (!$db->query()) die($db->stderr(true));
									}
									elseif($forsalefiles_plugin == '2'){  // Digitoll plugin is used
										$q = "SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='digitolldownloads' ";
										$db->setQuery($q);
										$virtuemart_custom_id = $db->loadresult();
										
										
										$q = "INSERT INTO `#__virtuemart_product_customfields` 
										( `virtuemart_product_id` , `virtuemart_custom_id` , `custom_value` , `custom_price` , `custom_param` , `published` , `created_on` , `created_by` )
										VALUES 
										( '".$virtuemart_product_id."' , '".$virtuemart_custom_id."' , 'digitolldownloads' , '0' , '' , '0' , '".date('Y-m-d H:i:s')."' , '".$user->id."'  )";
										$db->setQuery($q);
										if (!$db->query()) die($db->stderr(true));
										/// todo!
									
									
									
									}
								}
								else{  ////////// Product is free, file is shown on product details
									$q = "INSERT INTO `#__virtuemart_product_medias` 
									( `virtuemart_product_id` , `virtuemart_media_id` )
									VALUES
									(  '".$virtuemart_product_id."' , '".$virtuemart_media_id."'  )";
									$db->setQuery($q);
									if (!$db->query()) die($db->stderr(true));
								}
								$admitted = 1;
							}
							elseif($i==1 && $filemandatory){ // files are mandatory and first file field is not valid . EXIT PRODUCT ADDITION!
								$admitted = 0;	
									
							}
						}
					}
				}
				else
					$admitted = 1;
					
	
					
				if($admitted == 0){// we unstorez the product id from the DB
					$q ="DELETE FROM `#__virtuemart_products` WHERE `virtuemart_product_id`='".$virtuemart_vendor_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
				}
			
				
				if($formdesc=='') $formdesc = $form_s_desc;
				if($form_s_desc=='') $form_s_desc = $formdesc;
			
				if($admitted == 1){
					// we format the short description, cut at 255 char and replace last word by '...'
					if (strlen($form_s_desc) > 255){ 
						$form_s_desc = substr($form_s_desc,0,251);
						$splitted = split(" ",$form_s_desc);
						$keys = array_keys($splitted);
						$lastKey = end($splitted);
						$countlastkey = strlen($lastKey);
						$form_s_desc = substr_replace($form_s_desc.' ','...',-($countlastkey+1),-1);
					}
					
					$q = "INSERT INTO `#__virtuemart_products_".VMLANG."` 
					( `virtuemart_product_id` , `product_s_desc` , `product_desc` , `product_name` , `slug`   ) 
					VALUES 
					( '".$virtuemart_product_id."' , '".$db->getEscaped($form_s_desc)."' , '".$db->getEscaped($formdesc)."' , '".$db->getEscaped($formname)."' , '".$virtuemart_product_id.'-'.strtolower(str_replace(' ','-', $db->getEscaped($formname))).$virtuemart_product_id."' )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					
					if($multilang_mode >0){ 					
						for($i = 0 ; $i < count( $active_languages ) ; $i++){
							//$app->enqueueMessage($active_languages[$i]); //en-GB
							if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
								$q = "INSERT INTO `#__virtuemart_products_".strtolower( str_replace('-' , '_' , $active_languages[$i]) ) ."` 
								( `virtuemart_product_id` , `product_s_desc` , `product_desc` , `product_name` , `slug`   ) 
								VALUES 
								( '".$virtuemart_product_id."' , '".$db->getEscaped($form_s_desc)."' , '".$db->getEscaped($formdesc)."' , '".$db->getEscaped($formname)."' , '".strtolower(str_replace(' ','-', $db->getEscaped($formname))).$virtuemart_product_id."' )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
								
							}
							
						}
					}
					
					
					
					$q = "INSERT INTO `#__virtuemart_product_categories` 
					( `virtuemart_product_id` , `virtuemart_category_id`   ) 
					VALUES 
					( '".$virtuemart_product_id."' , '".$formcat."'  )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					
					
					if($formmanufacturer){
						$q= "INSERT INTO #__virtuemart_product_manufacturers 
						(virtuemart_product_id , virtuemart_manufacturer_id) 
						VALUES ('".$virtuemart_product_id."' , '".$formmanufacturer."') ";	
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
					
					
					
					if( $formprice < $minimum_price ){
						$formprice = $minimum_price;
						JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_PRICECHANGED').' '.$minimum_price);
					}
						
					$q = "INSERT INTO `#__virtuemart_product_prices` 
					( `virtuemart_product_id` , `virtuemart_shoppergroup_id` , `product_price` ,  `product_currency` , `created_on` , `created_by` ) 
					VALUES 
					( '".$virtuemart_product_id."' , '' , '".$formprice."' , '".$currency_id."' , '".date('Y-m-d H:i:s')."' , '".$user->id."')";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));	
						
					for ($i=1; $i <= $max_imagefields ;$i++){ ////////////// images
						//if($_FILES['image'.$i]['tmp_name']!=''){
						$imgisvalid = 1;
						$image = JRequest::getVar('image'.$i, null, 'files', 'array');
						$image['name'] = JFile::makeSafe($image['name']);
						if($image['name']!=''){
							$infosImg = getimagesize($image['tmp_name']);							
							//if ( (substr($image['type'],0,5) != 'image' || $infosImg[0] > $maximgside || $infosImg[1] > $maximgside )){
							if ( (substr($image['type'],0,5) != 'image'  )){
								JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> <font color="red"><b>'.JText::_('COM_VMVENDOR_VMVENADD_IMGEXTNOT').'</b></font>');
								$imgisvalid = 0;
							}
		
							$product_image = strtolower($formsku ."_".$image['name']);														
							$target_imagepath = JPATH_BASE . DS . $image_path . $product_image;
							if($imgisvalid){
								if( JFile::upload( $image['tmp_name'] , $target_imagepath )  ){
									$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOADRENAME_SUCCESS').' '.$product_image);
								}
								else
									JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_ERROR') );
							}
									
												// we store thumb
							$ext = JFile::getExt( $image['name'] ) ; 
							$ext = strtolower($ext);
							$ext = str_replace('jpeg','jpg',$ext); 
										//SWITCHES THE IMAGE CREATE FUNCTION BASED ON FILE EXTENSION
							switch(strtolower($ext)) {
								case 'jpg':
									$source = imagecreatefromjpeg($target_imagepath);
									$large_source = imagecreatefromjpeg($target_imagepath);
								break;
								case 'png':
									$source = imagecreatefrompng($target_imagepath);
									$large_source = imagecreatefrompng($target_imagepath);
								break;
								case 'gif':
									$source = imagecreatefromgif($target_imagepath);
									$large_source = imagecreatefromgif($target_imagepath);
								break;
								default:
									//JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_INVALID') );
									$imgisvalid = 0;
								break;
							} 
							if($product_image!='' && $imgisvalid ){		
								list($width,  $height) = getimagesize($target_imagepath); 
								/*if($width>=$height){ 
									$resizedH = ( VmConfig::get('img_width') * $height) / $width;
									$thumb = imagecreatetruecolor( VmConfig::get('img_width') ,  $resizedH);
									imagecopyresampled( $thumb,  $source,  0,  0,  0,  0,  VmConfig::get('img_width') ,  $resizedH,  $width,  $height );
								}
								else{
									$resizedW = ( VmConfig::get('img_height') * $width) / $height;
									$thumb = imagecreatetruecolor($resizedW,  VmConfig::get('img_height') );
									imagecopyresampled($thumb ,  $source ,  0 ,  0 ,  0 ,  0 ,  $resizedW,  VmConfig::get('img_height') ,  $width,  $height );
								}*/
		
								 if($width>$maximgside){
									$resizedH = ( $maximgside * $height) / $width;
									if($ext=='gif')
										$largeone = imagecreate( $maximgside ,  $resizedH);
									else
										$largeone = imagecreatetruecolor( $maximgside ,  $resizedH);
									imagealphablending( $largeone, false);
									imagesavealpha( $largeone,true);
									$transparent = imagecolorallocatealpha($largeone, 255, 255, 255, 127);
									imagefilledrectangle($largeone, 0, 0, $maximgside, $resizedH, $transparent);
									imagecopyresampled( $largeone,  $large_source,  0,  0,  0,  0,  $maximgside ,  $resizedH,  $width,  $height );
                                  }
                                else{
                                     $largeone = $target_imagepath;
                               }
								
								
								switch($ext) {
									case 'jpg':
										imagejpeg($largeone, JPATH_BASE . DS . $image_path .DS .$product_image,  $thumbqual);
									break;
									case 'jpeg':
										imagejpeg($largeone, JPATH_BASE . DS . $image_path .DS .$product_image,  $thumbqual);
									break;
									case 'png':
										imagepng($largeone, JPATH_BASE . DS . $image_path .DS .$product_image);
									break;
									case 'gif':
										imagegif($largeone, JPATH_BASE . DS . $image_path .DS .$product_image);
									break;
								} 
								imagedestroy($largeone);
								
								
								
								//list($width,  $height) = getimagesize($target_imagepath); 
								if($width>=$height){ 
									$resizedH = ( VmConfig::get('img_width') * $height) / $width;
									if($ext=='gif')
										$thumb = imagecreate( VmConfig::get('img_width') ,  $resizedH);
									else
										$thumb = imagecreatetruecolor( VmConfig::get('img_width') ,  $resizedH);
									imagealphablending( $thumb, false);
									imagesavealpha( $thumb,true);
									$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
									imagefilledrectangle($thumb, 0, 0, VmConfig::get('img_width') , $resizedH, $transparent);
									imagecopyresampled( $thumb,  $source,  0,  0,  0,  0,  VmConfig::get('img_width') ,  $resizedH,  $width,  $height );
								}
								else{
									$resizedW = ( VmConfig::get('img_height') * $width) / $height;
									if($ext=='gif')
										$thumb = imagecreate($resizedW,  VmConfig::get('img_height') );
									else
										$thumb = imagecreatetruecolor($resizedW,  VmConfig::get('img_height') );
									imagealphablending( $thumb, false);
									imagesavealpha( $thumb,true);
									$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
									imagefilledrectangle($thumb, 0, 0, $resizedW , VmConfig::get('img_height'), $transparent);
									imagecopyresampled($thumb ,  $source ,  0 ,  0 ,  0 ,  0 ,  $resizedW,  VmConfig::get('img_height') ,  $width,  $height );
								}
								switch($ext) {
									case 'jpg':
										imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image,  $thumbqual);
									break;
									case 'jpeg':
										imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image,  $thumbqual);
									break;
									case 'png':
										imagepng($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image);
									break;
									case 'gif':
										imagegif($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image);
									break;
								} 
								imagedestroy($thumb);
							
							
								$q = "INSERT INTO `#__virtuemart_medias` 
								( `virtuemart_vendor_id` , `file_title` , `file_mimetype` , `file_type` , `file_url` , `file_url_thumb` , `file_is_product_image` , `published` , `created_on` , `created_by` )
								VALUES
								(  '".$virtuemart_vendor_id."' , '".JFile::makeSafe($product_image)."' , '".$image['type']."' , 'product' , '".$image_path.JFile::makeSafe($product_image)."' , '".$thumb_path.JFile::makeSafe($product_image)."' , '1', '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
								$virtuemart_media_id = $db->insertid();
								$q = "INSERT INTO `#__virtuemart_product_medias` 
								( `virtuemart_product_id` , `virtuemart_media_id` , `ordering`)
								VALUES
								(  '".$virtuemart_product_id."' , '".$virtuemart_media_id."' ,'1' )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));	
							}
						}
					}
					
					
					
					
					if($get_img!=''){ /////////////////////////////////////// Only used if img url sent via get ot post from a 3rd party extension			
						$parts = explode('/', $get_img);
 						$imagefile = end($parts);
						$product_image = $formsku ."_".$imagefile;														
						$target_imagepath = JPATH_BASE . DS . $image_path . $product_image;
						copy( $get_img , $target_imagepath );
						
						
						
						
						$ext = JFile::getExt( $imagefile ) ; 
						$ext = strtolower($ext);
										//SWITCHES THE IMAGE CREATE FUNCTION BASED ON FILE EXTENSION
							switch($ext) {
								case 'jpg':
									$source = imagecreatefromjpeg($target_imagepath);
								break;
								case 'png':
									$source = imagecreatefrompng($target_imagepath);
								break;
								case 'gif':
									$source = imagecreatefromgif($target_imagepath);
								break;
								default:
									//JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_INVALID') );
									$imgisvalid = 0;
								break;
						}
						list($width,  $height) = getimagesize($target_imagepath); 
						if($width>=$height){ 
									$resizedH = ( VmConfig::get('img_width') * $height) / $width;
									$thumb = imagecreatetruecolor( VmConfig::get('img_width') ,  $resizedH);

									imagecopyresampled( $thumb,  $source,  0,  0,  0,  0,  VmConfig::get('img_width') ,  $resizedH,  $width,  $height );
						}
						else{
									$resizedW = ( VmConfig::get('img_height') * $width) / $height;
									$thumb = imagecreatetruecolor($resizedW,  VmConfig::get('img_height') );
									imagecopyresampled($thumb ,  $source ,  0 ,  0 ,  0 ,  0 ,  $resizedW,  VmConfig::get('img_height') ,  $width,  $height );
						}
						imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image,  $thumbqual);
						imagedestroy($thumb);
						$file_mimetype = 'image/'.str_replace('jpg','jpeg',$ext);
						$q = "INSERT INTO `#__virtuemart_medias` 
								( `virtuemart_vendor_id` , `file_title` , `file_mimetype` , `file_type` , `file_url` , `file_url_thumb` , `file_is_product_image` , `published` , `created_on` , `created_by` )
								VALUES
								(  '".$virtuemart_vendor_id."' , '".strtolower(JFile::makeSafe($product_image))."' , '".$file_mimetype."' , 'product' , '".strtolower($image_path.JFile::makeSafe($product_image))."' , '".strtolower($thumb_path.JFile::makeSafe($product_image))."' , '1', '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
								$virtuemart_media_id = $db->insertid();
								$q = "INSERT INTO `#__virtuemart_product_medias` 
								( `virtuemart_product_id` , `virtuemart_media_id` )
								VALUES
								(  '".$virtuemart_product_id."' , '".$virtuemart_media_id."'  )";
								$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));		
					}
					
					
					
					////////////////////////////// Core Custom fields support
					
					if($enable_corecustomfields){
						$q ="SELECT `virtuemart_custom_id` , `custom_parent_id` , `virtuemart_vendor_id` , `custom_jplugin_id` , `custom_title` , `custom_tip` , `custom_value`, `custom_field_desc` , `field_type` , `is_list` , `shared`  
						FROM `#__virtuemart_customs`
						WHERE `custom_jplugin_id`='0' 
						AND `admin_only`='0' 
						AND `published`='1' 
						AND `custom_element`!='' 
						ORDER BY `ordering` ASC , `virtuemart_custom_id` ASC ";
						$db->setQuery($q);
						$core_custom_fields	= $db->loadObjectList();
						$i = 0;
						foreach ($core_custom_fields as $core_custom_field){
							$i++;
							$q ="INSERT INTO #__virtuemart_product_customfields 
							( virtuemart_product_id , virtuemart_custom_id , custom_value , published , created_on , created_by , ordering )
							VALUES
							(  '".$virtuemart_product_id."' , '".$core_custom_field->virtuemart_custom_id."' , '".$db->getEscaped( JRequest::getVar( 'corecustomfield_'.$i ) )."' , '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' , '".$i."'  )";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
								
							}
						}
					}
				
					if ($enablerss){							
						if(!file_exists(JPATH_BASE."/media/vmvendorss"))
							mkdir(JPATH_BASE."/media/vmvendorss", 0777);	
						$xml_path = JPATH_BASE.'/media/vmvendorss/'.$user->id.'.rss';
						
						$q = "SELECT vm.`file_url_thumb` FROM `#__virtuemart_medias` vm 
						LEFT JOIN `#__virtuemart_vendor_medias` vvm ON vvm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
						WHERE vvm.`virtuemart_vendor_id`='".$virtuemart_vendor_id."'";
						$db->setQuery($q);
						$avatar = $db->loadResult();
						$feed_img = $juri.$avatar;
						if($avatar == NULL)
							$feed_img = $juri.'components/com_vmvendor/assets/img/noimage.gif';
								
						
						
						$profile_url =JRoute::_('index.php?option=com_vmvendor&view=vendorprofile&userid='.$user->id);
						$rss_xml = '<?xml version="1.0" encoding="utf-8" ?>';
						$rss_xml .='<rss version="2.0" xmlns:blogChannel="http://'.$_SERVER["SERVER_NAME"].$profile_url.'">';
						$rss_xml .='<channel>';
						$rss_xml .='<title>'.ucfirst($user->username).' - '.JText::_('COM_VMVENDOR_RSS_CAT').'</title>';
						$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].$profile_url.'</link>';
						$rss_xml .='<description>My online catalogue at '.$juri.'</description>';
						$rss_xml .='<generator>Nordmograph.com - VMVendor user catalogue RSS feed for Virtuemart and Joomla</generator>';
						$rss_xml .='<lastBuildDate>'.date("D, d M Y H:i:s O").'</lastBuildDate>';
						$rss_xml .='<language>'.substr($doc->getLanguage(),0,2).'</language>';
						$rss_xml .='<image>';
						$rss_xml .='<url>'.$feed_img.'</url>';
						$rss_xml .='<title>'.ucfirst($user->username).' - '.JText::_('COM_VMVENDOR_RSS_CAT').'</title>';
						$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].$profile_url.'</link>';
						$rss_xml .='</image>';
						$q = "SELECT p.`virtuemart_product_id` , p.`virtuemart_vendor_id` ,p.`created_on` , 
						pl.`product_s_desc` , pl.`product_name` , 
			
						vu.`virtuemart_user_id` , 
						vc.`virtuemart_category_id`
						FROM `#__virtuemart_products` p 
						LEFT JOIN `#__virtuemart_products_".VMLANG."` pl ON pl.`virtuemart_product_id` = p.`virtuemart_product_id` 
						LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_product_id` = p.`virtuemart_product_id`
		
						LEFT JOIN `#__virtuemart_vmusers` AS vu ON vu.`virtuemart_vendor_id` = p.`virtuemart_vendor_id`
						LEFT JOIN `#__virtuemart_product_categories` vc ON vc.`virtuemart_product_id` = p.`virtuemart_product_id` 
						WHERE vu.`virtuemart_user_id` ='".$user->id."' 
						
						AND p.`published`='1' 
						ORDER BY p.`virtuemart_product_id` DESC LIMIT ".$rsslimit;
						$db->setQuery($q);				
						$products = $db->loadObjectList();				
						foreach($products as $product){
							$q = "SELECT vm.`file_url_thumb` 
							FROM `#__virtuemart_medias` vm 
							LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
							WHERE vpm.`virtuemart_product_id` = '".$product->virtuemart_product_id."' 
							AND vm.`file_is_product_image`='1' ";
							$db->setQuery($q);				
							$product_file_url_thumb = $db->loadResult();	
							
							$rss_xml .='<item>';
							$rss_xml .='<title>'.$product->product_name.'</title>';
							$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id.'&Itemid='.$vmitemid).'</link>';
							$feeditem_img = $juri. $product_file_url_thumb;
							if(!$product_file_url_thumb)
								$feeditem_img = $juri.'components/com_virtuemart/assets/images/vmgeneral/'. VmConfig::get('no_image_set');
							$rss_xml .='<description><img src="'.$feeditem_img.'" width="'.VmConfig::get('img_width').'" height="'.VmConfig::get('img_height').'"/>'.$product->product_s_desc.'</description>';
							$rss_xml .='<pubDate>'.$product->created_on.'</pubDate>';
							$rss_xml .='</item>';
						}
						$rss_xml .='</channel>';
						$rss_xml .='</rss>';		
						$feed = fopen($xml_path, "w");	
						fwrite($feed,$rss_xml);
						fclose($feed);
						$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_RSSUPDATED').' <a target="_blank" href="'.$juri.'media/vmvendorss/'.$user->id.'.rss"><img  alt="rss" width="14" height="14" align="absmiddle" src="'.$juri.'components/com_vmvendor/assets/img/rss.png" title="'.JText::_('COM_VMVENDOR_RSS_SHAREFEED').'" /></a>');
											
					}
				
				
					
					$product_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$virtuemart_product_id.'&virtuemart_category_id='.$formcat.'&Itemid='.$vmitemid);
								
								
					
					// Jomsocial activity feed
					if($profileman==2 && $autopublish){
						$jspath = JPATH_ROOT . DS . 'components' . DS . 'com_community';
						include_once($jspath. DS . 'libraries' . DS . 'core.php');//activity stream  - added a blog
						CFactory::load('libraries', 'activities');          
						$act = new stdClass();
						$act->cmd    = 'wall.write';
						$act->actor    = $user->id;
						$act->target    = 0; // no target
						$act->title    = JText::_('COM_VMVENDOR_JOMSOCIAL_HASJUSTADDED').' <a href="'.$product_url.'">'.stripslashes( ucfirst($formname) ).'</a>' ;
						$output = '';
						if($enablerss)
							$output .='<div><a href="'.$juri.'media/vmvendorss/'.$user->id.'.rss" ><img src="'.$juri.'components/com_vmvendor/assets/img/rss.png" alt="rss" width="14" height="14" /> '.JText::_('COM_VMVENDOR_VENDORRSS').'</a></div>';
						$act->content    = $output;
						$act->app    = 'vmvendor.productaddition';
						$act->cid    = 0;
						$act->comment_id	= CActivities::COMMENT_SELF;
						$act->comment_type	= 'vmvendor.productaddition';
						$act->like_id		= CActivities::LIKE_SELF;		
						$act->like_type		= 'vmvendor.productaddition';
						CActivityStream::add($act);
					}
					
					// AUP rule if on product addition (mostly never used) - Usefull if VM used as a catlogue without vendor remuneration
					$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
					if ( file_exists($api_AUP) ){
						require_once ($api_AUP);

						$user = JFactory::getUser();
						$referencekey = $formsku;	
						$informationdata =  JText::_('COM_VMVENDOR_PRODUCTADDED').': '.stripslashes(ucfirst($formname) );
						$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $user->id );
						AlphaUserPointsHelper::newpoints( 'plgaup_vmvendor_addproduct', $aupid , $referencekey , $informationdata , '', '' ,'','');				
					}
					
					
					
					
					// Email Notification when new product added
					if( ($emailnotify_addition && $to!= NULL ) || !$autopublish){
						$subject = JText::_('COM_VMVENDOR_EMAIL_HELLO')." ".$juri." ".JText::_('COM_VMVENDOR_EMAIL_BYUSER')." ".$user->$naming;	
						$mailurl= $juri.'index.php?option=com_vmvendor&view=vendorprofile&userid='.$user->id.'&Itemid='.$profileitemid;
						$body = JText::_('COM_VMVENDOR_EMAIL_YOUCAN')." <a href='".$juri."index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=".$virtuemart_product_id."&virtuemart_category_id=".$formcat."&Itemid=".$vmitemid."' >"
							.JText::_('COM_VMVENDOR_EMAIL_HERE')."</a>."
							.JText::_('COM_VMVENDOR_EMAIL_SUBMITTEDBY')." <a href='".$mailurl."'>".$user->$naming."</a>. ";
						
						if(!$autopublish)
							$body .=JText::_('COM_VMVENDOR_EMAIL_BUTFIRSTREVIEW').' <a href="'.$juri.'administrator/index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$virtuemart_product_id.'&product_parent_id=0">'.JText::_('COM_VMVENDOR_EMAIL_SHOPADMIN').'</a>.';												
						$mailerror = "<img src='".$juri."components/com_vmvendor/assets/img/bad.png' width='16' height='16' alt='' align='absmiddle' /> <font color='red'><b>".JText::_('COM_VMVENDOR_EMAIL_FAIL')."</b></font>";		
						$message =& JFactory::getMailer();
						$message->addRecipient( $to );
						$message->setSubject( $subject );
						$message->setBody($body);
						$config =& JFactory::getConfig();
						$sender = array( 
							$config->getValue( 'config.mailfrom' ),
							$config->getValue( 'config.fromname' )
						);
						$message->isHTML(true);
						$message->Encoding = 'base64';
						$message->setSender( $sender );
						$message->addRecipient( $config->getValue( 'config.mailfrom' ) );
						$sent = $message->send();
						if ($sent != 1) 
							echo  $mailerror;
					}
				}
				if($autopublish){
					$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_PRODUCTADDED'));
					if($get_img=='')
						$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/file.png" width="16" height="16" alt="RSS" align="absmiddle" /> <a href="'.JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$virtuemart_product_id.'&virtuemart_category_id='.$formcat.'&Itemid='.$vmitemid).'">'.stripslashes(ucfirst($formname) ).'</a>');	
				}
				else
					JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/clock.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_TOBEREVIEWD') );
			}
			if (isset($_POST['formsku']) && $user->id == 0) // user has been inactive for too long and session time out
					JError::raiseWarning( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/bad.png" width="16" height="16" alt="" align="absmiddle" /> <font color="red"><b>'.JText::_('COM_VMVENDOR_UPLOAD_TIMEOUT').'</b></font>');
			if($get_img!='')
				$app->redirect($product_url);	
			else
				$app->redirect('index.php?option=com_vmvendor&view=addproduct&Itemid='.JRequest::getVar('Itemid'));	
		}
	
	
	
	public function addcat() // suggests or adds a new category
	{
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();		
		$juri 					= JURI::base();
		$db						=& JFactory::getDBO();	
		$model      			= &$this->getModel ( 'catsuggest' );
		$view       			= $this->getView  ( 'catsuggest','html' );
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();	
		
		$cparams 		=& JComponentHelper::getParams('com_vmvendor');
		$multilang_mode = $cparams->getValue('multilang_mode', 0);
		if($multilang_mode >0){
			$active_languages	=	VmConfig::get( 'active_languages' ); //en-GB
		}
		
		$cat_suggest 			= $cparams->getValue('cat_suggest',1);
		$naming 				= $cparams->getValue('naming','username');
		$to 				= $cparams->getValue('to');
		$cat_name				=	JRequest::getVar('cat_name', '' , 'post');
		$cat_descr				=	JRequest::getVar('cat_descr', '' , 'post');
		$cat_parent				=	JRequest::getVar('cat_parent', '' , 'post');
		$cat_published			=	JRequest::getVar('cat_published', '' , 'post');		
		if($cat_parent!='0'){
			$q = "SELECT `category_name` FROM `#__virtuemart_categories_".VMLANG."` WHERE `virtuemart_category_id` ='".$cat_parent."' ";
			$db->setQuery($q);
			$parent_name = $db->loadResult();
		}
		else
			$parent_name = JText::_('COM_VMVENDOR_CATSUGGEST_ROOT');
		
		if($cat_suggest >1 && $cat_name !='' && $cat_parent!='' && $user->id > 0 ){
				$q = "INSERT INTO `#__virtuemart_categories`  
				( `virtuemart_vendor_id`   ,  `limit_list_step` , `limit_list_initial` , `hits` , `ordering` , `shared` , `published` , `created_on` , `created_by` , `modified_on` , `modified_by` , `locked_on` , `locked_by` )
				VALUES 
				('1','0','10','0','0','0','".$cat_published."' , '".date('Y-m-d H:i:s')."' , '".$user->id."' , '0000-00-00 00:00:00' , '' , '0000-00-00 00:00:00' , '') ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$cat_id = $db->insertid();
				$cat_slug				= strtolower( str_replace(' ','-' , $cat_name) ).'-'.$cat_id;
				$q ="INSERT INTO  `#__virtuemart_categories_".VMLANG."`  
				( `virtuemart_category_id` , `category_name` , `category_description` ,  `customtitle` , `slug` ) 
				VALUES 
				('".$cat_id."','".ucfirst( $db->getEscaped($cat_name) )."','".ucfirst( $db->getEscaped($cat_descr))."','','". $db->getEscaped($cat_slug) ."') ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));		
				
				if($multilang_mode >0){ 					
					for($i = 0 ; $i < count( $active_languages ) ; $i++){
						$app->enqueueMessage($active_languages[$i]);
						if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
							$q ="INSERT INTO  `#__virtuemart_categories_".strtolower( str_replace('-' , '_' , $active_languages[$i]) )."`  
							( `virtuemart_category_id` , `category_name` , `category_description` , `metadesc` , `metakey` , `customtitle` , `slug` ) 
							VALUES 
							('".$cat_id."','".ucfirst( $db->getEscaped($cat_name) )."','".ucfirst( $db->getEscaped($cat_descr))."','','','','". $db->getEscaped($cat_slug) ."') ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
								
						}
							
					}
				}
				
				
						
				$q ="INSERT INTO  `#__virtuemart_category_categories`   
				( `category_parent_id` , `category_child_id` , `ordering` ) 
				VALUES 
				('".$cat_parent."','".$cat_id."','0') ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
		}		
		// Email Notification when new cat suggested/added
			if( $to!= NULL ) {
				$subject = JText::_('COM_VMVENDOR_CATSUGGEST_EMAILNOTIFICATION_SUGGESTION_SUBJECT')." ".$user->$naming;	
				$mailurl= $juri.'administrator/index.php?option=com_virtuemart&view=category';
				if($cat_id!='')
					$mailurl .= '&task=edit&cid='.$cat_id;					
				$body = JText::_('COM_VMVENDOR_CATSUGGEST_EMAILNOTIFICATION_SUGGESTION_BODY');
				$body .= '<br /><br />'.JText::_('COM_VMVENDOR_CATSUGGEST_CATNAME').': '.ucfirst($cat_name);
				$body .= '<br />'.JText::_('COM_VMVENDOR_CATSUGGEST_CATDESCR').': '.ucfirst($cat_descr);
				$body .= '<br />'.JText::_('COM_VMVENDOR_CATSUGGEST_CATPARENT').': '.ucfirst($parent_name);
				if($cat_parent!='0')
					$body .= ' (ID:'.$cat_parent.')';
				$body .= '<br />URL: <a href="'.$mailurl.'">'.$mailurl.'</a>';
				$mailerror = "<img src='".$juri."components/com_vmvendor/assets/img/bad.png' width='16' height='16' alt='' align='absmiddle' /> <font color='red'><b>".JText::_('COM_VMVENDOR_EMAIL_FAIL')."</b></font>";		
				$message =& JFactory::getMailer();
				$message->addRecipient( $to );
				$message->setSubject( $subject );
				$message->setBody($body);
				$config =& JFactory::getConfig();
				$sender = array( 
					$config->getValue( 'config.mailfrom' ),
					$config->getValue( 'config.fromname' )
				);
				$message->isHTML(true);
				$message->Encoding = 'base64';
				$message->setSender( $sender );
				$message->addRecipient( $config->getValue( 'config.mailfrom' ) );
				$sent = $message->send();
				if ($sent != 1) 
					echo  $mailerror;
			}
		if($cat_suggest==3)
			$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_CATSUGGEST_THANKS_PUBLISHED' );
		else
			$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_CATSUGGEST_THANKS_UNDERMODERATION' );					
		$app->enqueueMessage( $message );
		$app->redirect('index.php?option=com_vmvendor&view=addproduct&Itemid='.JRequest::getVar('Itemid'));		
	}
		
		
	public function addtax() {
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();		
		$juri 					= JURI::base();
		$db						=& JFactory::getDBO();	
		$model      			= &$this->getModel ( 'edittax' );
		$view       			= $this->getView  ( 'edittax','html' );	
		$cparams 				=& JComponentHelper::getParams( 'com_vmvendor' );
		$tax_mode 			= $cparams->getValue('tax_mode',0);

		
		
		
		$tax_name						=	JRequest::getVar('calc_name', '' , 'post');
		$tax_descr						=	JRequest::getVar('calc_descr', '' , 'post');
		$tax_mathop						=	JRequest::getVar('calc_mathop', '' , 'post');
		$tax_value						=	JRequest::getVar('calc_value', '' , 'post');
		
		$tax_vendor_id						=	JRequest::getVar('calc_vendor_id', '' , 'post');
		$tax_cats						=	JRequest::getVar('taxproductcats', '' , 'post');
		$tax_shoppergroups					=	explode( ',', JRequest::getVar('calc_shoppergroups', '' , 'post') );
		
		// get currency
		$q ="SELECT `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id` ='".$tax_vendor_id."' " ;
		$db->setQuery($q);
		$currency_id = $db->loadResult();
		
		
		$q = "INSERT INTO `#__virtuemart_calcs` 
		( `calc_jplugin_id` , `virtuemart_vendor_id` , `calc_name` , `calc_descr` , `calc_kind` , `calc_value_mathop` , `calc_value` , `calc_currency` , `calc_shopper_published` , `calc_vendor_published` , `publish_up` , `publish_down` , `for_override` , `calc_params` , `ordering` , `shared` , `published` , `created_on` , `created_by` )
		VALUES
		( '0' , '".$tax_vendor_id."' , '".$db->getEscaped($tax_name)."' ,'".$db->getEscaped($tax_descr)."' , 'VatTax' , '".$tax_mathop."' , '".$db->getEscaped($tax_value)."' , '".$currency_id."' , '1' , '1' , '".date('Y-m-d H:i:s')."' , '0000-00-00 00:00:00' , '0' , '' , '0' , '0' , '1' , '".date('Y-m-d H:i:s')."' ,'".$user->id."'  )";
		$db->setQuery($q);
		if (!$db->query()) die($db->stderr(true));
		$virtuemart_calc_id = $db->insertid();
		// Procdct categories addition
		for($i = 0; $i <= count($tax_cats) ; $i++ ){
			if($tax_cats[$i]>0){
				$q ="INSERT INTO `#__virtuemart_calc_categories` 
			( `virtuemart_calc_id` , `virtuemart_category_id` )
			VALUES
			('".$virtuemart_calc_id."','".$tax_cats[$i] ."') ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}
		
		}
		
		// Shopper groups addition
		
		for($i = 0; $i <= count($tax_shoppergroups) ; $i++ ){
			if($tax_shoppergroups[$i]>0){
				$q ="INSERT INTO `#__virtuemart_calc_shoppergroups` 
			( `virtuemart_calc_id` , `virtuemart_shoppergroup_id` )
			VALUES
			('".$virtuemart_calc_id."','".$tax_shoppergroups[$i] ."') ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}		
		}
		
		
		$message .= '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXADDED_SUCCESS' );
		$app->enqueueMessage( $message );
		$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid'));
	}
	
	
	
	
	public function edittax()
	{
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();
		$db							=& JFactory::getDBO();	
		$model      				= &$this->getModel ( 'edittax' );
		$view       				= $this->getView  ( 'edittax','html' );
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$tax_mode 			= $cparams->getValue('tax_mode',0);
		
		$tax_id							=	JRequest::getVar('calc_id', '' , 'post');
		$tax_name						=	JRequest::getVar('calc_name', '' , 'post');
		$tax_descr						=	JRequest::getVar('calc_descr', '' , 'post');
		$tax_mathop						=	JRequest::getVar('calc_mathop', '' , 'post');
		$tax_value						=	JRequest::getVar('calc_value', '' , 'post');
		$tax_currency						=	JRequest::getVar('calc_currency', '' , 'post');
		$tax_vendor_id						=	JRequest::getVar('calc_vendor_id', '' , 'post');
		$tax_cats						=	JRequest::getVar('taxproductcats', '' , 'post');
		$tax_shoppergroups					=	explode( ',', JRequest::getVar('calc_shoppergroups', '' , 'post') );
		$message .= 'count tax_cats: '.count($tax_cats);
		$message .= '<br />count tax_shoppergroups: '.count($tax_shoppergroups);
		
		 $q = "UPDATE `#__virtuemart_calcs` SET 
		`virtuemart_vendor_id` ='".$tax_vendor_id."' , 
		`calc_name` ='".$db->getEscaped($tax_name)."' , 
		`calc_descr` = '".$db->getEscaped($tax_descr)."' ,
		`calc_value_mathop` = '".$tax_mathop."' , 
		`calc_value` = '".$db->getEscaped($tax_value)."' ,
		`calc_currency` ='".$tax_currency."' , 
		`publish_down` = '0000-00-00 00:00:00' , 
		`shared` ='0' ,
		`modified_on` ='".date('Y-m-d H:i:s')."' ,
		`modified_by`='".$user->id."' 
		WHERE `virtuemart_calc_id` ='".$tax_id."' AND `published`='1' AND `shared`='0' AND `calc_kind` ='VatTax' AND (`created_by`='0' OR `created_by`='".$user->id."') ";
		$db->setQuery($q);
		if (!$db->query()) die($db->stderr(true));
		// categories
		// check if any cat is being removed, we check in the DB if any entry is not in the cats array and if so, we delete these
		$q ="SELECT `virtuemart_category_id` FROM `#__virtuemart_calc_categories` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
		$db->setQuery($q);
		$cat_ids = $db->loadObjectList();
		foreach($cat_ids as $cat_id){
			if( !in_array( $cat_id->virtuemart_category_id , $tax_cats ) ){
				$message .= '<br />' . $q =" DELETE FROM `#__virtuemart_calc_categories`  WHERE `virtuemart_calc_id` ='".$tax_id."' AND `virtuemart_category_id` ='".$cat_id->virtuemart_category_id."' ";	
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}
		}
		for($i = 0; $i <= count($tax_cats) ; $i++ ){
			$q ="SELECT COUNT(*) FROM `#__virtuemart_calc_categories`  WHERE `virtuemart_calc_id` ='".$tax_id."' AND `virtuemart_category_id`='".$tax_cats[$i]."' ";
			$db->setQuery($q);
			$is_cat_yet = $db->loadResult();
			if ($is_cat_yet > 0){  // it's allready in , do nothing.
			}
			elseif($tax_cats[$i] !='' ){ // we add the cat
				
					$message .= '<br />' .  $q ="INSERT INTO `#__virtuemart_calc_categories` 
				( `virtuemart_calc_id` , `virtuemart_category_id` )
				VALUES
				('".$tax_id."' , '".$tax_cats[$i]."' )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
				
			}	
		}
		
		// shoppergroups
		// check if any shoppergroup is being removed, we check in the DB if any entry is not in the cats array and if so, we delete these
		$q ="SELECT `virtuemart_shoppergroup_id` FROM `#__virtuemart_calc_shoppergroups` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
		$db->setQuery($q);
		$shoppergroup_ids = $db->loadObjectList();
		foreach($shoppergroup_ids as $shoppergroup_id){
			if( !in_array( $shoppergroup_id->virtuemart_shoppergroup_id , $tax_shoppergroups ) ){
				$message .= '<br />' . $q =" DELETE FROM `#__virtuemart_calc_shoppergroups`  WHERE `virtuemart_calc_id` ='".$tax_id."' AND `virtuemart_shoppergroup_id` ='".$shoppergroup_id->virtuemart_shoppergroup_id."' ";	
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}
		}
		for($j = 0; $j <= count($tax_shoppergroups) ; $j++ ){
			$message .= '<br />' .$q ="SELECT COUNT(*) FROM `#__virtuemart_calc_shoppergroups`  WHERE `virtuemart_calc_id` ='".$tax_id."' AND `virtuemart_shoppergroup_id`='".$tax_shoppergroups[$j]."' ";
			$db->setQuery($q);
			$is_shoppergroup_yet = $db->loadResult();
			if ($is_shoppergroup_yet > 0){  // it's allready in , do nothing.
			}
			elseif($tax_shoppergroups[$j] !='' ){ // we add the cat
				
					$message .= '<br />' .  $q ="INSERT INTO `#__virtuemart_calc_shoppergroups` 
				( `virtuemart_calc_id` , `virtuemart_shoppergroup_id` )
				VALUES
				('".$tax_id."' , '".$tax_shoppergroups[$j]."' )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
				
			}	
		}
		
		
		$message .= '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXEDITED_SUCCESS' );
		$app->enqueueMessage( $message );
		$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid'));
	}
	
	public function deletetax(){
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();
		$db							=& JFactory::getDBO();	
		$model      				= &$this->getModel ( 'dashboard' );
		$view       				= $this->getView  ( 'dashboard','html' );
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$tax_mode 			= $cparams->getValue('tax_mode',0);
		
		$tax_id							=	JRequest::getVar('delete_taxid', '' , 'post');
		$tax_userid							=	JRequest::getVar('userid', '' , 'post');
		
		if($tax_userid = $user->id && $tax_mode){  // user is tax owner and tax management is enabled
			$q = "DELETE FROM `#__virtuemart_calcs` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$q = "DELETE FROM `#__virtuemart_calc_categories` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$q = "DELETE FROM `#__virtuemart_calc_countries` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$q = "DELETE FROM `#__virtuemart_calc_shoppergroups` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$q = "DELETE FROM `#__virtuemart_calc_states` WHERE `virtuemart_calc_id` ='".$tax_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$message .= '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXDELETED_SUCCESS' );
			$app->enqueueMessage( $message );
			$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid'));
			
		}
		
		
	}
	
	
	function publishreview(){
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();
		$db							=& JFactory::getDBO();	
		$model      				= &$this->getModel ( 'dashboard' );
		$view       				= $this->getView  ( 'dashboard','html' );
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$manage_reviews 			= $cparams->getValue('manage_reviews',1);
		
		$review_id					= JRequest::getVar('review_id', '' , 'post');
		if($manage_reviews){
			$q ="UPDATE `#__virtuemart_ratings` SET `published` ='1' WHERE `virtuemart_rating_id`='".$review_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			$q ="UPDATE `#__virtuemart_rating_reviews` SET `published` ='1' WHERE `virtuemart_rating_review_id`='".$review_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$q = "SELECT `virtuemart_product_id` FROM  `#__virtuemart_rating_reviews` WHERE `virtuemart_rating_review_id`='".$review_id."' ";
			$db->setQuery($q);
			$virtuemart_product_id = $db->loadResult();
			// recount ratings
			$q = "SELECT review_rates  
			 FROM `#__virtuemart_rating_reviews`  
			 WHERE published='1' AND virtuemart_product_id = '".$virtuemart_product_id."' ";
			 $db->setQuery($q);
			 $published_reviews_ratings = $db->loadObjectList();
			 $published_review_total = 0;
			 $published_review_count = 0;
			 foreach($published_reviews_ratings as $published_reviews_rating){
				 $published_review_total = $published_review_total + $published_reviews_rating->review_rates;
				 $published_review_count++;
			 }
			 $q= "UPDATE `#__virtuemart_ratings`  SET rates='".$published_review_total."' , ratingcount	='".$published_review_count."' , rating='".($published_review_total/$published_review_count)."' 
			 WHERE virtuemart_product_id='".$virtuemart_product_id."' ";
			 $db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			 
			 $message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_DASHBOARD_REVIEWS_PUBLISHEDSUCCESS' );
			$app->enqueueMessage( $message );
		}
		$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid') );
	}
	function deletereview(){
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();
		$db							=& JFactory::getDBO();	
		$model      				= &$this->getModel ( 'dashboard' );
		$view       				= $this->getView  ( 'dashboard','html' );
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$manage_reviews 			= $cparams->getValue('manage_reviews',1);
		
		$review_id					= JRequest::getVar('review_id', '' , 'post');
		//$created_on					= JRequest::getVar('created_on', '' , 'post');
		if($manage_reviews){
			$q = "SELECT `virtuemart_product_id` FROM  `#__virtuemart_rating_reviews` WHERE `virtuemart_rating_review_id`='".$review_id."' ";
			$db->setQuery($q);
			$virtuemart_product_id = $db->loadResult();
			
			
			
			$q = "DELETE FROM `#__virtuemart_rating_reviews` WHERE `virtuemart_rating_review_id`='".$review_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			$q = "DELETE FROM `#__virtuemart_rating_votes` WHERE `virtuemart_rating_vote_id`='".$review_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			
			// recount ratings
			$qc = "SELECT review_rates , published  
			 FROM `#__virtuemart_rating_reviews`  
			 WHERE virtuemart_product_id = '".$virtuemart_product_id."' ";
			 $db->setQuery($qc);
			 $reviews_ratings = $db->loadObjectList();
			 $published_review_total = 0;
			 $published_review_count = 0;
			 $unpublished_review_count= 0;
			 foreach($reviews_ratings as $reviews_rating){
				 if($reviews_rating->published){
				 	$published_review_total = $published_review_total + $reviews_rating->review_rates;
				 	$published_review_count++;
				 }
				 else
				 	$unpublished_review_count++;
			 }
			 if($published_review_count + $unpublished_review_count <1){
				 $q = "DELETE FROM `#__virtuemart_ratings` WHERE `virtuemart_product_id`='".$virtuemart_product_id."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			 }
			 else{		 	
			 	$q= "UPDATE `#__virtuemart_ratings`  SET rates='".$published_review_total."' , ratingcount	='".$published_review_count."' , rating='".($published_review_total/$published_review_count)."' 
			 	WHERE virtuemart_product_id='".$virtuemart_product_id."' ";
			 	$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			 }
			
			$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_DASHBOARD_REVIEWS_DELETEDSUCCESS' );
			$app->enqueueMessage( $message );
		}
		$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid') );
	}
	
	
	function updateorderstatus(){
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();
		$db							=& JFactory::getDBO();	
		$model      				= &$this->getModel ( 'dashboard' );
		$view       				= $this->getView  ( 'dashboard','html' );
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$allow_orderstatuschange 	= $cparams->getValue('allow_orderstatuschange',1);
		$update_notifies_admin		= $cparams->getValue('update_notifies_admin',1);
		$update_notifies_customer	= $cparams->getValue('update_notifies_customer',1);
		
		$saleordernumber			= JRequest::getVar('saleordernumber', '' , 'post');		
		$orderitemid				= JRequest::getVar('orderitemid', '' , 'post');
		$neworderstatus				= JRequest::getVar('neworderstatus', '' , 'post');
		if($allow_orderstatuschange && $user->id !='0' ){
			$q = "UPDATE `#__virtuemart_order_items` SET `order_status`='".$neworderstatus."' WHERE `virtuemart_order_item_id`='".$orderitemid."' ";
			$db->setQuery($q);
			if (!$db->query()) 
				die($db->stderr(true));
			else{
				$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_DASHBOARD_STATUSUPDATED' );
				$app->enqueueMessage( $message );
				
				if ( $neworderstatus=='X'){ // we cancel the AUP payment.
					$q = "SELECT `id` FROM `#__alpha_userpoints_rules` WHERE plugin_function='plgaup_vm_points2vendor' " ;
					$db->setQuery( $q );
					$ruleid = $db->loadResult();
				
					$q = "SELECT `referreid` , `points` FROM `#__alpha_userpoints_details` WHERE `keyreference` ='".$orderitemid."|OderItemID' AND `rule`='".$ruleid."' ";
					$db->setQuery( $q );
					$pointsdata = $db->loadRow();
					$referreid = $pointsdata[0];
					$points2deduct = $pointsdata[1];
					$referencekey = $orderitemid. "|CanceledVendorPoints";
					$informationdata= JText::_('COM_VMVENDOR_EMAIL_STATUSCANCELED_INFODATA').' '.$saleordernumber;
					$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
					if ( file_exists($api_AUP))
					{
						require_once ($api_AUP);
						AlphaUserPointsHelper::newpoints( 'plgaup_cancel_vendorpoints',$referreid, $referencekey , $informationdata , -$points2deduct );
					} 
					
					
					
					
				}
				
				
				
				if ($update_notifies_admin OR $update_notifies_customer OR $neworderstatus=='X'){  // if status is 'canceled', notify admin even if notifications disabled
				
				
					// email admin	
					$mailer =& JFactory::getMailer();
					$config =& JFactory::getConfig();
					$sender = array( 
						$config->getValue( 'config.mailfrom' ),
						$config->getValue( 'config.fromname' )
					);
					$mailer->setSender( $sender );
					if($update_notifies_admin OR $neworderstatus=='X')
						$mailer->addRecipient( $config->getValue( 'config.mailfrom' ) );
					if($to!='')
						$mailer->addRecipient( $to );
					if($update_notifies_customer){
						$q = "SELECT u.`email`
						FROM `#__users` u 
						JOIN `#__virtuemart_orders` vo ON u.`id` = vo.`virtuemart_user_id` 
						JOIN `#__virtuemart_order_items` voi ON vo.`virtuemart_order_id` = voi.`virtuemart_order_id` 
						WHERE voi.`virtuemart_order_item_id` = '".$orderitemid."' ";
						$db->setQuery($q);
						$customer_email = $db->loadResult();
						$mailer->addRecipient( $customer_email );
					}
					$subject = JText::_('COM_VMVENDOR_EMAIL_STATUSUPDATE_SUBJECT');
					
					$body = ucfirst($user->username).' '.JText::_('COM_VMVENDOR_EMAIL_STATUSUPDATE_BODY').' '.$neworderstatus.'. ';	
					$body .= JText::_('COM_VMVENDOR_EMAIL_STATUSUPDATE_ORDERID').': '.$saleordernumber;	

					
					$mailerror = "<img src='".$juri."components/com_vmvendor/assets/img/bad.png' width='16' height='16' alt='' align='absmiddle' /> <font color='red'><b>".JText::_('COM_VMVENDOR_EMAIL_FAIL')."</b></font>";		
					
					$mailer->setSubject( $subject );
					$mailer->isHTML(true);
					$mailer->Encoding = 'base64';
					$mailer->setBody($body);
					
					$send =& $mailer->Send();
					if ($send != 1) 
						echo  $mailerror;
				}	
			}
		}
		$app->redirect('index.php?option=com_vmvendor&view=dashboard&Itemid='.JRequest::getVar('Itemid') );
	}
	
	
	function deleteproduct(){
		$app 					= JFactory::getApplication();
		$doc 					= &JFactory::getDocument();
		$user 					= & JFactory::getUser();		
		$juri 					= JURI::base();
		$db						=& JFactory::getDBO();	
		$model      			= &$this->getModel ( 'vendorprofile' );
		$view       			= $this->getView  ( 'vendorprofile','html' );
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$cparams 				=& JComponentHelper::getParams('com_vmvendor');
		$multilang_mode 		= $cparams->getValue('multilang_mode', 0);
		if($multilang_mode >0){
			$active_languages	=	VmConfig::get( 'active_languages' ); //en-GB
		}
		$allow_deletion 		= $cparams->getValue('allow_deletion');
		$enablerss 				= $cparams->getValue('enablerss', 1);
		$rsslimit				= $cparams->getValue('rsslimit');
		$profileman 			= $cparams->getValue('profileman');
		$profileitemid 			= $cparams->getValue('profileitemid');
		$vmitemid				= $cparams->getValue('vmitemid');
		$price					= JRequest::getVar('price', '' , 'post');
		$userid					= JRequest::getVar('userid', '' , 'post');
		jimport('joomla.filesystem.file');
		
		if($allow_deletion >0 && JRequest::getVar('delete_productid', '' , 'post')    && $user->id >0 && $userid==$user->id   ) {
			$q = " SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id`='".$user->id."' " ;
			$db->setQuery($q);
			$virtuemart_vendor_id = $db->loadResult();
			$delete_productid = JRequest::getVar('delete_productid');	
			if($profileman==2){ // we need the product name to delete the activity stream from url
				$q ="SELECT `product_name` FROM `#__virtuemart_products_".VMLANG."`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				$delete_productname = $db->loadResult();
			}
			switch($allow_deletion){
				case 1: // unpublish
					$q ="UPDATE `#__virtuemart_products` SET `published`='0' WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_PROFILE_PRODUCTUNPUBLISHED' );
					$app->enqueueMessage( $message );
				break;				
				case 2: // Delete if no sale
				$q ="SELECT count(*) FROM `#__virtuemart_order_items` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				$yetsold = $db->loadResult();
				if($yetsold<1)
				{
					$q = "DELETE FROM `#__virtuemart_products` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));					
					$q = "DELETE FROM `#__virtuemart_products_".VMLANG."` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					if($multilang_mode >0){ 					
						for($i = 0 ; $i < count( $active_languages ) ; $i++){
							$app->enqueueMessage($active_languages[$i]); //en-GB
							if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
								$q = "DELETE FROM `#__virtuemart_products_".strtolower( str_replace('-' , '_' , $active_languages[$i]) ) ."` WHERE `virtuemart_product_id`='".$delete_productid."' ";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
							}
						}
					}
					
					
										
					$q = "DELETE FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));															
					$q = "DELETE FROM `#__virtuemart_product_manufacturers` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					////// Delete medias
					$q ="SELECT `virtuemart_media_id` FROM `#__virtuemart_product_medias` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					$mediastodel = $db->loadObjectList();
					foreach($mediastodel as $mediatodel ){
						$q = "SELECT `file_url` , `file_url_thumb` FROM `#__virtuemart_medias` WHERE `virtuemart_media_id` ='".$mediatodel->virtuemart_media_id."' ";
						$db->setQuery($q);
						$files_url = $db->loadRow();
						$image_url = $files_url[0];
						$thumb_url = $files_url[1];
						if($image_url!='')
							JFile::delete($image_url);
						if($thumb_url!='')
							JFile::delete($thumb_url);
						$q ="DELETE FROM `#__virtuemart_medias` WHERE `virtuemart_media_id` ='".$mediatodel->virtuemart_media_id."' ";	
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
					if($price>0){
						$q ="SELECT `custom_param` FROM `#__virtuemart_product_customfields` WHERE `virtuemart_product_id`='".$delete_productid."' ";	
						$db->setQuery($q);
						$custom_params = $db->loadObjectList();
						foreach($custom_params as $custom_param){
							$virtuemart_media_id = 	str_replace('{"media_id":"' , '' , $custom_param->custom_param);
							$strlen = strlen($virtuemart_media_id);
							$strpos = strpos($virtuemart_media_id , '"');
							$virtuemart_media_id = substr($virtuemart_media_id , 0 , -($strlen - $strpos) );
							$q ="SELECT `file_url` FROM `#__virtuemart_medias` WHERE  `virtuemart_media_id` ='".$virtuemart_media_id."' " ;
							$db->setQuery($q);
							$customfiled_file_url = $db->loadResult();
							if($customfiled_file_url!='')
								JFile::delete($customfiled_file_url);
							$q ="DELETE FROM `#__virtuemart_medias` WHERE `virtuemart_media_id` ='".$virtuemart_media_id."'  ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}	
					}
					$q ="DELETE FROM `#__virtuemart_product_medias` WHERE `virtuemart_product_id` ='".$delete_productid."' ";	
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q = "DELETE FROM `#__virtuemart_product_customfields` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));					
					$q = "DELETE FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q = "DELETE FROM `#__virtuemart_product_relations`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q = "DELETE FROM `#__virtuemart_product_shoppergroups`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q = "DELETE FROM `#__virtuemart_ratings`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q = "DELETE FROM `#__virtuemart_rating_reviews`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q = "DELETE FROM `#__virtuemart_rating_votes`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_PROFILE_PRODUCTDELETED' );
					$app->enqueueMessage( $message );
					
				}
				else{					
					$q ="UPDATE `#__virtuemart_products` SET `published`='0' WHERE `virtuemart_product_id`='".$delete_productid."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$message='<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_PROFILE_PRODUCTUNPUBLISHEDBECAUSE' );
					JFactory::getApplication()->enqueueMessage( $message );
				}
				break;
				
				case 3: // Delete !
				$q = "DELETE FROM `#__virtuemart_products` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));					
				$q = "DELETE FROM `#__virtuemart_products_".VMLANG."` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));	
				if($multilang_mode >0){ 					
					for($i = 0 ; $i < count( $active_languages ) ; $i++){
						$app->enqueueMessage($active_languages[$i]); //en-GB
						if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
							$q = "DELETE FROM `#__virtuemart_products_".strtolower( str_replace('-' , '_' , $active_languages[$i]) ) ."` WHERE `virtuemart_product_id`='".$delete_productid."' ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));	
						}	
					}
				}				
				$q = "DELETE FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));									
				$q = "DELETE FROM `#__virtuemart_product_manufacturers` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));					
				////// Delete medias
				$q ="SELECT `virtuemart_media_id` FROM `#__virtuemart_product_medias` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				$mediastodel = $db->loadObjectList();
				foreach($mediastodel as $mediatodel ){
					$q = "SELECT `file_url` , `file_url_thumb` FROM `#__virtuemart_medias` WHERE `virtuemart_media_id` ='".$mediatodel->virtuemart_media_id."' ";
					$db->setQuery($q);
					$files_url = $db->loadRow();
					$image_url = $files_url[0];
					$thumb_url = $files_url[1];
					if($image_url!='')
						JFile::delete($image_url);
					if($thumb_url!='')
						JFile::delete($thumb_url);
					$q ="DELETE FROM `#__virtuemart_medias` WHERE `virtuemart_media_id` ='".$mediatodel->virtuemart_media_id."' ";	
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));	
				}					
				if($price>0){
					$q ="SELECT `custom_param` FROM `#__virtuemart_product_customfields` WHERE `virtuemart_product_id`='".$delete_productid."' ";	
					$db->setQuery($q);
					$custom_params = $db->loadObjectList();
					foreach($custom_params as $custom_param){
						$virtuemart_media_id = 	str_replace('{"media_id":"' , '' , $custom_param->custom_param);
						$strlen = strlen($virtuemart_media_id);
						$strpos = strpos($virtuemart_media_id , '"');
						$virtuemart_media_id = substr($virtuemart_media_id , 0 , -($strlen - $strpos) );
						$q ="SELECT `file_url` FROM `#__virtuemart_medias` WHERE  `virtuemart_media_id` ='".$virtuemart_media_id."' " ;
						$db->setQuery($q);
						$customfiled_file_url = $db->loadResult();
						if($customfiled_file_url!='')
							JFile::delete($customfiled_file_url);
						$q ="DELETE FROM `#__virtuemart_medias` WHERE `virtuemart_media_id` ='".$virtuemart_media_id."'  ";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}	
				}
				$q ="DELETE FROM `#__virtuemart_product_medias` WHERE `virtuemart_product_id` ='".$delete_productid."' ";	
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));				
				$q = "DELETE FROM `#__virtuemart_product_customfields` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));										
				$q = "DELETE FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));	
				$q = "DELETE FROM `#__virtuemart_product_relations`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$q = "DELETE FROM `#__virtuemart_product_shoppergroups`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$q = "DELETE FROM `#__virtuemart_ratings`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$q = "DELETE FROM `#__virtuemart_rating_reviews`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$q = "DELETE FROM `#__virtuemart_rating_votes`  WHERE `virtuemart_product_id`='".$delete_productid."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$message = '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" alt="ok" width="16" height="16" /> '.JText::_( 'COM_VMVENDOR_PROFILE_PRODUCTDELETED' );
				$app->enqueueMessage( $message );			
				break;
			}			
			if ($enablerss){							
				if(!file_exists(JPATH_BASE."/media/vmvendorss"))
					mkdir(JPATH_BASE."/media/vmvendorss", 0777);	
				$xml_path = JPATH_BASE.'/media/vmvendorss/'.$user->id.'.rss';				
				$q = "SELECT vm.file_url_thumb FROM #__virtuemart_medias vm 
				LEFT JOIN #__virtuemart_vendor_medias vvm ON vvm.virtuemart_media_id = vm.virtuemart_media_id 
				WHERE vvm.virtuemart_vendor_id='".$virtuemart_vendor_id."'";
				$db->setQuery($q);
				$avatar = $db->loadResult();
				$feed_img = $juri.$avatar;
				if($avatar == NULL)
					$feed_img = $juri.'components/com_vmvendor/assets/img/noimage.gif';
				$profile_url =JRoute::_('index.php?option=com_vmvendor&view=vendorprofile&userid='.$user->id);				
				$rss_xml = '<?xml version="1.0" encoding="utf-8" ?>';
				$rss_xml .='<rss version="2.0" xmlns:blogChannel="http://'.$_SERVER["SERVER_NAME"].$profile_url.'">';
				$rss_xml .='<channel>';
				$rss_xml .='<title>'.ucfirst($user->username).' - '.JText::_('COM_VMVENDOR_RSS_CAT').'</title>';
				$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].$profile_url.'</link>';
				$rss_xml .='<description>My online catalogue at '.$juri.'</description>';
				$rss_xml .='<generator>Nordmograph.com - VMVendor user catalogue RSS feed for Virtuemart and Joomla</generator>';
				$rss_xml .='<lastBuildDate>'.date("D, d M Y H:i:s O").'</lastBuildDate>';
				$rss_xml .='<language>'.substr($doc->getLanguage(),0,2).'</language>';
				$rss_xml .='<image>';
				$rss_xml .='<url>'.$feed_img.'</url>';
				$rss_xml .='<title>'.ucfirst($user->username).' - '.JText::_('COM_VMVENDOR_RSS_CAT').'</title>';
				$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].$profile_url.'</link>';
				$rss_xml .='</image>';
				$q = "SELECT p.`virtuemart_product_id` , p.`virtuemart_vendor_id` ,p.`created_on` , 
				pl.`product_s_desc` , pl.`product_name` , 
	
				vu.`virtuemart_user_id` , 
				vc.`virtuemart_category_id`
				FROM `#__virtuemart_products` p 
				LEFT JOIN `#__virtuemart_products_".VMLANG."` pl ON pl.`virtuemart_product_id` = p.`virtuemart_product_id` 
				LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_product_id` = p.`virtuemart_product_id`

				LEFT JOIN `#__virtuemart_vmusers` AS vu ON vu.`virtuemart_vendor_id` = p.`virtuemart_vendor_id`
				LEFT JOIN `#__virtuemart_product_categories` vc ON vc.`virtuemart_product_id` = p.`virtuemart_product_id` 
				WHERE vu.`virtuemart_user_id` ='".$user->id."' 
				
				AND p.`published`='1' 
				ORDER BY p.`virtuemart_product_id` DESC LIMIT ".$rsslimit;
				$db->setQuery($q);				
				$products = $db->loadObjectList();				
				foreach($products as $product){
					$q = "SELECT vm.`file_url_thumb` 
					FROM `#__virtuemart_medias` vm 
					LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
					WHERE vpm.`virtuemart_product_id` = '".$product->virtuemart_product_id."' 
					AND vm.`file_is_product_image`='1' ";
					$db->setQuery($q);				
					$product_file_url_thumb = $db->loadResult();
					
					$rss_xml .='<item>';
					$rss_xml .='<title>'.$product->product_name.'</title>';
					$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id.'&Itemid='.$vmitemid).'</link>';
					$feeditem_img = $juri. $product_file_url_thumb;
					if(!$product_file_url_thumb)
						$feeditem_img = $juri.'components/com_virtuemart/assets/images/vmgeneral/'. VmConfig::get('no_image_set');
					$rss_xml .='<description><img src="'.$feeditem_img.'" width="'.VmConfig::get('img_width').'" height="'.VmConfig::get('img_height').'"/>'.$product->product_s_desc.'</description>';
					$rss_xml .='<pubDate>'.$product->created_on.'</pubDate>';
					$rss_xml .='</item>';
				}
				$rss_xml .='</channel>';
				$rss_xml .='</rss>';		
				$feed = fopen($xml_path, "w");	
				fwrite($feed,$rss_xml);
				fclose($feed);								
			}
			if($profileman ==2){ // delete product related Jomsocial activity stream
				$q = "DELETE FROM `#__community_activities` 


				WHERE `actor`='".$user->id."' 
				AND `title` LIKE '%".ucfirst($delete_productname)."%' " ;
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}
			return $this->display ();
		}		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function updateprofile(){
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$dblang = VMLANG;
		$image_path 	=  VmConfig::get('media_vendor_path');
		$thumb_path = $image_path.'resized/';
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();		
		$juri 					= JURI::base();
		$db						=& JFactory::getDBO();	
		$model      			= &$this->getModel ( 'vendorprofile' );
		$view       			= $this->getView  ( 'vendorprofile','html' );
		
		$cparams 				=& JComponentHelper::getParams( 'com_vmvendor' );
		$multilang_mode 		= $cparams->getValue('multilang_mode', 0);
		if($multilang_mode >0){
			//$active_languages	=	VmConfig::get( 'active_languages' );
			$lang =& JFactory::getLanguage(); 
			$dblang = strtolower( str_replace('-' , '_' , $lang->getTag() ) );
		}
		
		$profileman 			= $cparams->getValue( 'profileman' );
		$maximgside 			= $cparams->getValue('maximgside', '800');
		$thumbqual 				= $cparams->getValue('thumbqual', 90);
		$wysiwyg_prof			= $cparams->getValue('wysiwyg_prof', 1);
		$vendor_title						=	JRequest::getVar('vendor_title', '' , 'post');
		$vendor_telephone					=	JRequest::getVar('vendor_telephone', '' , 'post');
		$vendor_url							=	JRequest::getVar('vendor_url', '' , 'post');
		$vendor_store_desc					=	JRequest::getVar('vendor_store_desc', '' , 'post');
		$vendor_terms_of_service			=	JRequest::getVar('vendor_terms_of_service', '' , 'post');
		$vendor_legal_info					=	JRequest::getVar('vendor_legal_info', '' , 'post');
		if($wysiwyg_prof){
			$vendor_store_desc     				= JRequest::getVar('vendor_store_desc', '', 'post', 'string', JREQUEST_ALLOWRAW);
			$vendor_terms_of_service     		= JRequest::getVar('vendor_terms_of_service', '', 'post', 'string', JREQUEST_ALLOWRAW);
			$vendor_legal_info     				= JRequest::getVar('vendor_legal_info', '', 'post', 'string', JREQUEST_ALLOWRAW);
		}
		$activity_stream					=	JRequest::getVar('activity_stream', '' , 'post');
		$slug								= str_replace( " " , "-" , $vendor_title );
		$slug								= $user->id.'-'.str_replace( "'" , "-" , strtolower( $slug ) );		
		$q = "SELECT  `virtuemart_vendor_id` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id` = '".$user->id."' ";
		$db->setQuery($q);
		$vendor_id = $db->loadResult();		
		if($vendor_id){
			$update = 1;
			if($multilang_mode >0){ // check if data allready exists in the current language to know if we update or insert.
				$q = "SELECT  COUNT(*)  FROM `#__virtuemart_vendors_".$dblang."`  WHERE `virtuemart_vendor_id` 	='".$vendor_id."' ";
				$db->setQuery($q);
				$allready_in = $db->loadResult();	
				if($allready_in <1){
					$update = 0;
					$q = "INSERT INTO `#__virtuemart_vendors_".$dblang."` 
					( `virtuemart_vendor_id` , `vendor_store_desc` , `vendor_terms_of_service` , `vendor_legal_info` , `vendor_store_name` , `vendor_phone` , `vendor_url` , `slug` ) 
					VALUES
					('".$vendor_id."' , 
					'".$db->getEscaped( $vendor_store_desc )."'  ,
					 '".$db->getEscaped( $vendor_terms_of_service )."' ,
					 '".$db->getEscaped( $vendor_legal_info )."' , 
					 '".$db->getEscaped( $vendor_title )."' , 
					 '".$db->getEscaped( $vendor_telephone )."' , 
					 '".$db->getEscaped( $vendor_url )."' , 
					 '".$db->getEscaped( $slug )."'  ) ";			
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
				}
			}
			
			if($update==1){
				$q = "UPDATE `#__virtuemart_vendors_".$dblang."` SET 
				`vendor_store_desc` 			= '".$db->getEscaped( $vendor_store_desc )."'  ,
				`vendor_terms_of_service` 		= '".$db->getEscaped( $vendor_terms_of_service )."' , 
				`vendor_legal_info` 			= '".$db->getEscaped( $vendor_legal_info )."' , 
				`vendor_store_name`				= '".$db->getEscaped( $vendor_title )."' , 
				`vendor_phone` 					= '".$db->getEscaped( $vendor_telephone )."' , 
				`vendor_url` 					= '".$db->getEscaped( $vendor_url )."' , 
				`slug` 							= '".$db->getEscaped( $slug )."' 
				WHERE `virtuemart_vendor_id`='".$vendor_id."' ";			
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				
				$q = "UPDATE `#__virtuemart_vendors` SET 
				`vendor_name`				= '".$db->getEscaped( $vendor_title )."' 
				WHERE `virtuemart_vendor_id`='".$vendor_id."' ";			
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}

			/*if($multilang_mode >0){ 					
				for($i = 0 ; $i < count( $active_languages ) ; $i++){
					$app->enqueueMessage($active_languages[$i]); //en-GB
					if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
						$q = "UPDATE `#__virtuemart_vendors_".Vstrtolower( str_replace('-' , '_' , $active_languages[$i]) ) ."` SET 
						`vendor_store_desc` 			= '".$db->getEscaped( $vendor_store_desc )."'  ,
						`vendor_terms_of_service` 		= '".$db->getEscaped( $vendor_terms_of_service )."' , 
						`vendor_legal_info` 			= '".$db->getEscaped( $vendor_legal_info )."' , 
						`vendor_store_name`				= '".$db->getEscaped( $vendor_title )."' , 
						`vendor_phone` 					= '".$db->getEscaped( $vendor_telephone )."' , 
						`vendor_url` 					= '".$db->getEscaped( $vendor_url )."' , 
						`slug` 							= '".$db->getEscaped( $slug )."' 
						WHERE `virtuemart_vendor_id` 	='".$vendor_id."' ";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));			
					}				
				}
			}*/
			jimport('joomla.filesystem.file');
			$image = JRequest::getVar('vendor_image', null, 'files', 'array');
			$image['name'] = JFile::makeSafe($image['name']);
			if($image['name']!=''){
				/////// check if there allready is a vendor image
				$imgisvalid = 1;
				$q = "SELECT vm.`virtuemart_media_id` , vm.`file_url` , vm.`file_url_thumb` 
				FROM `#__virtuemart_medias` vm 
				LEFT JOIN `#__virtuemart_vendor_medias` vvm ON vvm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
				WHERE vvm.`virtuemart_vendor_id`='".$vendor_id."' ";
				$db->setQuery($q);
				$vendorimages = $db->loadRow();
				$virtuemart_media_id = $vendorimages[0];
				$file_url = $vendorimages[1];
				$file_url_thumb = $vendorimages[2];
				$vendorimage_path ='images/stories/virtuemart/vendor/';
				$vendorthumb_path ='images/stories/virtuemart/vendor/resized/';
				$infosImg = getimagesize($image['tmp_name']);		
				//if ( (substr($image['type'],0,5) != 'image' || $infosImg[0] > $maximgside || $infosImg[1] > $maximgside ) ){
				if ( (substr($image['type'],0,5) != 'image' ) ){
					JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMGEXTNOT'));
					$imgisvalid = 0;
				}
				//JError::raiseWarning( 100,$imgisvalid);
				$vendor_image = strtolower($user->id ."_".$image['name']);														
				$target_imagepath = JPATH_BASE . DS . $vendorimage_path . $vendor_image;
				if($imgisvalid){
					if( JFile::upload( $image['tmp_name'] , $target_imagepath )  ){
						$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOADRENAME_SUCCESS').' '.$vendor_image);
					}
					else
						JError::raiseWarning( 100,JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_ERROR') );
				}
				$ext = JFile::getExt( $image['name'] ) ; 
				$ext = strtolower($ext);
				$ext = str_replace('jpeg','jpg',$ext);
								//SWITCHES THE IMAGE CREATE FUNCTION BASED ON FILE EXTENSION
				switch($ext) {
								case 'jpg':
									$source = imagecreatefromjpeg($target_imagepath);
									$large_source = imagecreatefromjpeg($target_imagepath);
								break;
								case 'png':
									$source = imagecreatefrompng($target_imagepath);
									$large_source = imagecreatefrompng($target_imagepath);
								break;
								case 'gif':
									$source = imagecreatefromgif($target_imagepath);
									$large_source = imagecreatefromgif($target_imagepath);
								break;
								default:
									//JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_INVALID') );
									$imgisvalid = 0;
								break;
				} 
				if($vendor_image!='' && $imgisvalid ){
					list($width,  $height) = getimagesize($target_imagepath); 
					if($width>$maximgside){
									$resizedH = ( $maximgside * $height) / $width;
						if($ext=='gif')
							$largeone = imagecreate( $maximgside ,  $resizedH);
						else
							$largeone = imagecreatetruecolor( $maximgside ,  $resizedH);
						imagealphablending( $largeone, false);
						imagesavealpha( $largeone,true);
						$transparent = imagecolorallocatealpha($largeone, 255, 255, 255, 127);
						imagefilledrectangle($largeone, 0, 0, $maximgside, $resizedH, $transparent);
						imagecopyresampled( $largeone,  $large_source,  0,  0,  0,  0,  $maximgside ,  $resizedH,  $width,  $height );
					}
                     else{
                                     $largeone = $target_imagepath;
                    }
					switch($ext) {
									case 'jpg':
										imagejpeg($largeone, JPATH_BASE . DS . $image_path .DS .$vendor_image,  $thumbqual);
									break;
									case 'jpeg':
										imagejpeg($largeone, JPATH_BASE . DS . $image_path .DS .$vendor_image,  $thumbqual);
									break;
									case 'png':
										imagepng($largeone, JPATH_BASE . DS . $image_path .DS .$vendor_image);
									break;
									case 'gif':
										imagegif($largeone, JPATH_BASE . DS . $image_path .DS .$vendor_image);
									break;
					} 
					imagedestroy($largeone);
					
					
					
					
					
					
					
					if($width>=$height){ 
									$resizedH = ( VmConfig::get('img_width') * $height) / $width;
									if($ext=='gif')
										$thumb = imagecreate( VmConfig::get('img_width') ,  $resizedH);
									else
										$thumb = imagecreatetruecolor( VmConfig::get('img_width') ,  $resizedH);
									imagealphablending( $thumb, false);
									imagesavealpha( $thumb,true);
									$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
									imagefilledrectangle($thumb, 0, 0, VmConfig::get('img_width') , $resizedH, $transparent);
									imagecopyresampled( $thumb,  $source,  0,  0,  0,  0,  VmConfig::get('img_width') ,  $resizedH,  $width,  $height );
								}
								else{
									$resizedW = ( VmConfig::get('img_height') * $width) / $height;
									if($ext=='gif')
										$thumb = imagecreate($resizedW,  VmConfig::get('img_height') );
									else
										$thumb = imagecreatetruecolor($resizedW,  VmConfig::get('img_height') );
									imagealphablending( $thumb, false);
									imagesavealpha( $thumb,true);
									$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
									imagefilledrectangle($thumb, 0, 0, $resizedW , VmConfig::get('img_height'), $transparent);
									imagecopyresampled($thumb ,  $source ,  0 ,  0 ,  0 ,  0 ,  $resizedW,  VmConfig::get('img_height') ,  $width,  $height );
								}
								 switch($ext) {
									case 'jpg':
										imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$vendor_image,  $thumbqual);
									break;
									case 'jpeg':
										imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$vendor_image,  $thumbqual);
									break;
									case 'png':
										imagepng($thumb, JPATH_BASE . DS . $thumb_path .DS .$vendor_image);
									break;
									case 'gif':
										imagegif($thumb, JPATH_BASE . DS . $thumb_path .DS .$vendor_image);
									break;
								} 
								imagedestroy($thumb);
					if($virtuemart_media_id!=''){ // we updated the picture
							// delete all media file
						if($file_url !=  $image_path.JFile::makeSafe($vendor_image) ){ // only delete old file if new filename and old are diferent. If same file has allready been overwritten
							if($file_url!='')
								JFile::delete($file_url);
							if($file_url_thumb!='')
								JFile::delete($file_url_thumb);
						}						
						$q ="UPDATE `#__virtuemart_medias` SET 
						 `file_title`='".$db->getEscaped($vendor_title)."' , 
						 `file_mimetype`='".$image['type']."' , 
						`file_url` = '".$vendorimage_path.JFile::makeSafe($vendor_image)."' , 
						 `file_url_thumb` ='".$vendorthumb_path.JFile::makeSafe($vendor_image)."' ,
						 `modified_on`='".date('Y-m-d H:i:s')."' , 
						 `modified_by` ='".$user->id."' 
						 WHERE `virtuemart_media_id` ='".$virtuemart_media_id."' ";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
					else{ // we insert the new file					
						$q = "INSERT INTO `#__virtuemart_medias` 
							( `virtuemart_vendor_id` , `file_title` , `file_mimetype` , `file_type` , `file_url` , `file_url_thumb` , `file_is_product_image` , `published` , `created_on` , `created_by`)
							VALUES
							(  '".$vendor_id."' , '".$db->getEscaped($vendor_title)."' , '".$image['type']."' , 'vendor' , '".$vendorimage_path.JFile::makeSafe($vendor_image)."' , '".$vendorthumb_path.JFile::makeSafe($vendor_image)."' , '1', '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
						$virtuemart_media_id = $db->insertid();
						$q = "INSERT INTO `#__virtuemart_vendor_medias` 
							( `virtuemart_vendor_id` , `virtuemart_media_id` )
							VALUES
							(  '".$vendor_id."' , '".$virtuemart_media_id."'  )";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
				}		
			}
			if(JRequest::getVar('activity_stream')=='on' && $profileman==2){
				$jspath = JPATH_ROOT . DS . 'components' . DS . 'com_community';
				include_once($jspath. DS . 'libraries' . DS . 'core.php');//activity stream  - added a blog
				CFactory::load('libraries', 'activities');          
				$act = new stdClass();
				$act->cmd    = 'wall.write';
				$act->actor    = $user->id;
				$act->target    = 0; // no target
				$act->title    = JText::_( 'COM_VMVENDOR_JOMSOCIAL_EDITEDPROFILE').' <a href="'.JRoute::_('index.php?option=com_vmvendor&view=vendorprofile&userid='.$user->id).'">'.ucfirst($vendor_title).'</a>' ;		
				$act->content    = '';
				$act->app    = 'vmvendor.vendorupdate';
				$act->cid    = 0;
				$act->comment_id	= CActivities::COMMENT_SELF;
				$act->comment_type	= 'vmvendor.vendorupdate';
				$act->like_id		= CActivities::LIKE_SELF;		
				$act->like_type		= 'vmvendor.vendorupdate';
				CActivityStream::add($act);	
			}
			$app->enqueueMessage( JText::_('COM_VMVENDOR_UPDATED_SUCCESS') );
		
		}
		else
			JError::raiseWarning( 100, JText::_('COM_VMVENDOR_EDITPRO_NOTAVENDORYET') );
		
		$app->redirect('index.php?option=com_vmvendor&view=vendorprofile&Itemid='.JRequest::getVar('Itemid'));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function updateproduct() 
	{
		$app 					= JFactory::getApplication();
		$user 					= & JFactory::getUser();		
		$juri 					= JURI::base();
		$db						=& JFactory::getDBO();	
		$doc 			= &JFactory::getDocument();
		//$model      			= &$this->getModel ( 'editproduct' );
		//$view       			= $this->getView  ( 'editproduct','html' );
		if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$dblang = VMLANG;	
		if($multilang_mode >0){
			//$active_languages	=	VmConfig::get( 'active_languages' );
			$lang =& JFactory::getLanguage(); 
			$dblang = strtolower( str_replace('-' , '_' , $lang->getTag() ) );
		}
		$virtuemart_product_id = JRequest::getVar( 'productid' , '' , 'get' );

		$cparams 				=& JComponentHelper::getParams( 'com_vmvendor' );
		$profileman 			= $cparams->getValue( 'profileman' );
		$naming 			= $cparams->getValue( 'naming' );
		//echo $userid					=	JRequest::getVar( 'userid' , '' , 'post' );

		$forbidcatids 	= $cparams->getValue('forbidcatids');	
		$vmitemid 		= $cparams->getValue('vmitemid', '103');
		$profileitemid 	= $cparams->getValue('profileitemid', '2');
		
		$autopublish 	= $cparams->getValue('autopublish', 1);
		$enablerss 		= $cparams->getValue('enablerss', 1);
		$rsslimit 		= $cparams->getValue('rsslimit', 10);
		$shoutbox 		= $cparams->getValue('shoutbox', 0);
		$shoutboxlink 	= $cparams->getValue('shoutboxlink', 0);
		$emailnotify_updated 	= $cparams->getValue('emailnotify_updated', 1);
		
		$to 			= $cparams->getValue('to');
		$maxfilesize 	= $cparams->getValue('maxfilesize', '4000000');//4 000 000 bytes   =  4M
		$max_imagefields= $cparams->getValue('max_imagefields', 4);
		$max_filefields	= $cparams->getValue('max_filefields', 4);
		$maximgside 	= $cparams->getValue('maximgside', '800');
		$thumbqual 		= $cparams->getValue('thumbqual', 90);
		$wysiwyg_prod 		= $cparams->getValue('wysiwyg_prod', 0);
		$enablefiles 	= $cparams->getValue('enablefiles', 1);
		$stream		 	= $cparams->getValue('stream', 0);
		$maxspeed 		= $cparams->getValue('maxspeed', '3000');
		$maxtime		= $cparams->getValue('maxtime', '365');
		$enableprice	= $cparams->getValue('enableprice', 1);
		$enablestock	= $cparams->getValue('enablestock', 1);
		$enableweight	= $cparams->getValue('enableweight', 1);
		$weightunits	= $cparams->getValue('weightunits');
		
		$enable_corecustomfields	= $cparams->getValue('enable_corecustomfields', 1);
		$enable_vm2tags	= $cparams->getValue('enable_vm2tags', 0);
		$enable_vm2geolocator	= $cparams->getValue('enable_vm2geolocator', 0);
		$enable_vm2dropdownfield = $cparams->getValue('enable_vm2dropdownfield', 0);
		$latitude			= JRequest::getVar('latitude');
		$longitude			= JRequest::getVar('longitude');
		$zoom				= JRequest::getVar('zoom');
		$maptype			= JRequest::getVar('maptype');
		
		$tagslimit		= $cparams->getValue('tagslimit', '5');
		
		$reset_onpricestatus = $cparams->getValue('reset_onpricestatus', 1);
		
		
		$filemandatory 	= $cparams->getValue('filemandatory', 1);
		$imagemandatory = $cparams->getValue('imagemandatory', 0);
		$allowedexts 	= $cparams->getValue('allowedexts', 'zip,mp3');
		$minimum_price	= $cparams->getValue('minimum_price');
		$sepext 		= explode( "," , $allowedexts );
		$countext 		= count($sepext);

		$image_path 	=  VmConfig::get('media_product_path');
		$safepath 		= VmConfig::get( 'forSale_path' );
		
		
		
		
		$thumb_path = $image_path.'resized/';
		
		$formfile='';
		
		$formname 				= JRequest::getVar('formname');
		$formdesc		 	= JRequest::getVar('formdesc');
		//if($enable_sdesc)
			$form_s_desc		 = JRequest::getVar('form_s_desc');
		$formmanufacturer	= JRequest::getVar('formmanufacturer');
		/*else{
			$length = 250; 
			if (strlen($formdesc) <= $length) {
			   $form_s_desc	=  $formdesc; //do nothing
			} else {
			   $form_s_desc = substr( $formdesc ,0,strpos($formdesc ,' ',$length));
			   $form_s_desc .= '...';
			}
		}*/
		if($wysiwyg_prod)
			$formdesc     		= JRequest::getVar('formdesc', '', 'post', 'string', JREQUEST_ALLOWRAW);
			
		
		if($enableprice)
			$formprice 			= JRequest::getVar('formprice');
		else
			$formprice			= 0;
		$oldprice 			= JRequest::getVar('oldprice');
		$formweight		= JRequest::getVar('formweight');
		$formweightunit		= JRequest::getVar('formweightunit');
		$formcat 			= JRequest::getVar('formcat');
		$announceupdate		= JRequest::getVar('announceupdate');
		$file1				= JRequest::getVar('file1', null, 'files', 'array');
		$file1name			= $file1['name'];
		
		if($enablefiles && $safepath=='')
			JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_SAFEPATHREQUIRED') );
		if(VmConfig::get('multix','none')!='admin')
			JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_MULTIVENDORREQUIRED') );
			
		$q = " SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id`='".$user->id."' " ;
		$db->setQuery($q);
		$virtuemart_vendor_id = $db->loadResult();


if( $formcat 
   && $formname 
   && $formdesc 
   && ( ($enableweight && $formweight!='' && $formweightunit!='') OR !$enableweight  ) 
   && ( ($enableprice && $formprice!='') OR !$enableprice )
   && $user->id > 0 
  
   /*&& ( ($filemandatory  
		 && $enablefiles 
		 && $file1name!='' )  
	  OR !$filemandatory   
	  OR !$enablefiles    
	  )*/
   ){ 
	
	jimport('joomla.filesystem.file');
	$formsku 			= JRequest::getVar('formsku');
	
		$formcurrency 		= JRequest::getVar('formcurrency');
		if($enablestock)
			$formstock 			= JRequest::getVar('formstock');
		else
			$formstock = '1';
			
			
			
			
		/////////// check if any image is removed
		for($l = 2 ; $l<= $max_imagefields ; $l++){
			if(JRequest::getVar('delimg'.$l)=='on'){
				$virtuemart_media_id = JRequest::getVar('media_id'.$l);
				$q = "SELECT `file_url` , `file_url_thumb` FROM `#__virtuemart_medias` WHERE `virtuemart_media_id`='".$virtuemart_media_id."' ";
				$db->setQuery($q);
				$media_files = $db->loadRow();
				$image_url 		= $media_files[0];
				$thumb_url		= $media_files[1];
				if($image_url!='')
					JFile::delete($image_url);
				if($thumb_url!='')
					JFile::delete($thumb_url);
				$q ="DELETE FROM `#__virtuemart_medias` WHERE `virtuemart_media_id`='".$virtuemart_media_id."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$q ="DELETE FROM `#__virtuemart_product_medias` WHERE `virtuemart_media_id`='".$virtuemart_media_id."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_IMGREMOVEDSUCCESSFULLY'));				
			}	
		}
		
		/////////// check if any file is removed
		for($n = 1 ; $n<= $max_filefields ; $n++){
			if(JRequest::getVar('delfile'.$n)=='on'){
				$virtuemart_media_id = JRequest::getVar('filemedia_id'.$n);
				$q = "SELECT `file_url`  FROM `#__virtuemart_medias` WHERE `virtuemart_media_id`='".$virtuemart_media_id."' ";
				$db->setQuery($q);
				$file_url = $db->loadResult();
				if($file_url!='')
					JFile::delete($file_url);
				$q ="DELETE FROM `#__virtuemart_medias` WHERE `virtuemart_media_id`='".$virtuemart_media_id."' ";
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				if($oldprice>0){ // we delete the st42_download entry
					$q ="DELETE FROM `#__virtuemart_product_customfields` WHERE `custom_value`='st42_download' AND  `custom_param` LIKE CONCAT('%' , CONCAT('\"media_id\":\"' , '".$virtuemart_media_id."' , '\"') , '%' ) ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));				
				}
				else{ // we delete the regular media entry
					$q ="DELETE FROM `#__virtuemart_product_medias` WHERE `virtuemart_media_id`='".$virtuemart_media_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
				}
				$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_FILEREMOVEDSUCCESSFULLY'));				
			}	
		}
		
		
		
			
	
	
		
		if($enablefiles){
			for ($i=1; $i <= $max_filefields ;$i++){ ////////////// images
				$fileisvalid = 0;
				$file = JRequest::getVar('file'.$i, null, 'files', 'array');
				if($file){
					$filename = JFile::makeSafe($file['name']);
					$ext =  JFile::getExt($filename);
					$formfilesize 	= $file['size'];
					$form_mime 		= $file['type'];					
					for ( $j=0 ; $j < $countext ; $j++ ){
						if ($sepext[$j] == $ext)
							$fileisvalid = 1; // file has an allowed extention					
					}
					
					if($filename!=''){							
						if	(!$fileisvalid)
							JError::raiseWarning( 100,JText::_('COM_VMVENDOR_FILEEXTNOT') );
						if($formfilesize > $maxfilesize OR $formfilesize==0){
							$fileisvalid = 0;
							JError::raiseWarning( 100, JText::_('COM_VMVENDOR_MAXFILESIZEX').' '.$formsku ."_".$filename );
						}
					}
					else
						$fileisvalid = 0;
						
					$target_filepath = $safepath .  $formsku ."_".$filename;
						//echo 'error: '.$_FILES['file'.$i]['error'];
						
					if($fileisvalid){
						$file_is_downloadable = 0;
						$file_is_forSale = 1;
						$target_filepath = $safepath .  $formsku ."_".$filename;
						if( $formprice == '0'){
							$file_is_downloadable = 1;
							$file_is_forSale = 0;
							$target_filepath = $image_path. $formsku ."_".$filename;
						}
						
						if( JFile::upload($file['tmp_name'] , $target_filepath ) )
							$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_FILEUPLOADRENAME_SUCCESS').' '. $formsku.'_'. $filename);
						else{
							JError::raiseWarning( 100, JText::_('COM_VMVENDOR_FILEUPLOAD_ERROR') );
							//$fileisvalid = 0;	
						}
						
						
						if(JRequest::getVar('filemedia_id'.$i,'','post')!=''){ // we updated the file
						//echo 'we update the file';
							$q = "SELECT `file_url`  FROM `#__virtuemart_medias` WHERE `virtuemart_media_id`='".JRequest::getVar('filemedia_id'.$i,'','post')."' ";
							$db->setQuery($q);
							$file_url = $db->loadResult();
							if($file_url !=  $target_filepath ){ // only delete old file if new filename and old are diferent. If same file has allready been overwritten
								if($file_url!='')
									JFile::delete($file_url);
							}
							$q ="UPDATE `#__virtuemart_medias` SET 
							 `file_title`='".$db->getEscaped($formsku.'_'.$filename)."' , 
							 `file_mimetype`='".$file['type']."' , 
							 `file_url` = '".addslashes($target_filepath)."' , 
							 `modified_on`='".date('Y-m-d H:i:s')."' , 
							 `modified_by` ='".$user->id."' 
							 WHERE `virtuemart_media_id` ='".JRequest::getVar('filemedia_id'.$i)."' ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}
						else{ // we add the new file
							$q = "INSERT INTO `#__virtuemart_medias` 
							( `virtuemart_vendor_id` , `file_title` , `file_mimetype` , `file_type` , `file_url` ,  `file_is_downloadable` , `file_is_forSale` , `published` , `created_on` , `created_by`)
							VALUES
							(  '".$virtuemart_vendor_id."' , '".$db->getEscaped($formsku.'_'.$filename)."' , '".$file['type']."' , 'product' , '".addslashes($target_filepath)."' , '".$file_is_downloadable."', '".$file_is_forSale."' , '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
							$virtuemart_media_id = $db->insertid();
							
							if($formprice>0){
								$q = "SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='st42_download' ";
								$db->setQuery($q);
								$virtuemart_custom_id = $db->loadresult();
								$q = "INSERT INTO `#__virtuemart_product_customfields` 
								( `virtuemart_product_id` , `virtuemart_custom_id` , `custom_value` ,  `custom_param` , `published` , `created_on` , `created_by` )
								VALUES 
								( '".$virtuemart_product_id."' , '".$virtuemart_custom_id."' , 'st42_download' ,  '{\"media_id\":\"".$virtuemart_media_id."\",\"stream\":\"".$stream."\",\"maxspeed\":\"".$maxspeed."\",\"maxtime\":\"".$maxtime."\"}' , '0' , '".date('Y-m-d H:i:s')."' , '".$user->id."'  )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
							}
							else{
								$q = "INSERT INTO `#__virtuemart_product_medias` 
								( `virtuemart_product_id` , `virtuemart_media_id` )
								VALUES
								(  '".$virtuemart_product_id."' , '".$virtuemart_media_id."'  )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
							}	
						}
					}				
				}
			}
		}

	

	
	
		if($admitted = 1){

			if (strlen($form_s_desc) > 255){ 
				$form_s_desc = substr($form_s_desc,0,251);
				$splitted = split(" ",$form_s_desc);
				$keys = array_keys($splitted);
				$lastKey = end($splitted);
				$countlastkey = strlen($lastKey);
				$form_s_desc = substr_replace($form_s_desc.' ','...',-($countlastkey+1),-1);
			}
			

			$q ="UPDATE `#__virtuemart_products` SET `product_in_stock`='".$formstock."' , `published`='".$autopublish."' , `modified_on`='".date('Y-m-d H:i:s')."' , `modified_by`='".$user->id."' ";
			if ($enableweight)
				$q .= " , `product_weight`='".$formweight."' , `product_weight_uom`='".$formweightunit."' ";
			$q .="WHERE `virtuemart_product_id`='".$virtuemart_product_id."'";
			
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			if($formdesc=='') $formdesc = $form_s_desc;
				if($form_s_desc=='') $form_s_desc = $formdesc;
			
			$q = "UPDATE `#__virtuemart_products_".$dblang."` 
			 SET  `product_s_desc`='".$db->getEscaped($form_s_desc)."' , 
			 `product_desc`='".$db->getEscaped($formdesc)."' , 
			 `product_name`='".$db->getEscaped($formname)."' , 
			 `slug`='".strtolower(str_replace(' ','-', $db->getEscaped($formname))).$virtuemart_product_id."'  
			WHERE `virtuemart_product_id`='".$virtuemart_product_id."'   ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			
			/*if($multilang_mode >0){ 					
				for($i = 0 ; $i < count( $active_languages ) ; $i++){
					$app->enqueueMessage($active_languages[$i]); //en-GB
					if( str_replace('_' , '-' , VMLANG) != strtolower( $active_languages[$i]) ){
						$q = "UPDATE `#__virtuemart_products_".strtolower( str_replace('-' , '_' , $active_languages[$i]) ) ."` 
						 SET  `product_s_desc`='".$db->getEscaped($form_s_desc)."' , 
						 `product_desc`='".$db->getEscaped($formdesc)."' , 
						 `product_name`='".$db->getEscaped($formname)."' , 
						 `slug`='".strtolower(str_replace(' ','-', $db->getEscaped($formname))).$virtuemart_product_id."'  
						WHERE `virtuemart_product_id`='".$virtuemart_product_id."'   ";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
				}
			}*/

			
			$q ="UPDATE `#__virtuemart_product_categories` SET `virtuemart_category_id`='".$formcat."' WHERE `virtuemart_product_id`='".$virtuemart_product_id."' ";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));
			if( $formprice < $minimum_price ){
				$formprice = $minimum_price;
				JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.JText::_('COM_VMVENDOR_VMVENADD_PRICECHANGED').' '.$minimum_price.'</b>');
			}
			
			if($formmanufacturer){
				$q="SELECT COUNT(*) FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id='".$virtuemart_product_id."' ";
				$db->setQuery($q);
				$manuf = $db->loadResult();
				if($manuf>0)
					$q = "UPDATE #__virtuemart_product_manufacturers SET virtuemart_manufacturer_id='".$formmanufacturer."' WHERE virtuemart_product_id='".$virtuemart_product_id."'";
				else
					$q= "INSERT INTO #__virtuemart_product_manufacturers 
					(virtuemart_product_id , virtuemart_manufacturer_id) 
					VALUES ('".$virtuemart_product_id."' , '".$formmanufacturer."') ";	
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}
			
			
			/////////////////////////////   3rd party custom plugins
			$formtags = JRequest::getVar('formtags');
			if($enable_vm2tags ){
				$q ="SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2tags' ";
				$db->setQuery($q);
				$virtuemart_custom_id = $db->loadResult();
				
				$septags = explode(',' ,$formtags); 
				$i=0;
				$limited_tags='';
				foreach ( $septags as $septag ){
					$i++;
					if ( $i <= $tagslimit && strlen($septag)>=2 && strlen($septag)<=20 ){
						
						if( $i > 1)
							$limited_tags .=',';
						$limited_tags .= $septag ;			
					}				
				}
				$tags_array =array('product_tags' => $limited_tags);
				$limited_tags = json_encode($tags_array);
				$q = "SELECT COUNT(*) FROM `#__virtuemart_product_customfields` 
				WHERE `virtuemart_product_id` ='".$virtuemart_product_id."' 
				AND `virtuemart_custom_id` ='".$virtuemart_custom_id."' ";
				$db->setQuery($q);
				$allready_tagged = $db->loadResult();
				if($allready_tagged >0){
					$q = "UPDATE `#__virtuemart_product_customfields` 
					SET `custom_param` = '".$db->getEscaped($limited_tags)."' 
					WHERE `virtuemart_product_id` ='".$virtuemart_product_id."' 
					AND `virtuemart_custom_id` ='".$virtuemart_custom_id."' ";
				}
				else{
					$q = "INSERT INTO `#__virtuemart_product_customfields` 
					( `virtuemart_product_id` , `virtuemart_custom_id` , `custom_value` , `custom_param` , `created_on` , `created_by`  ) 
					VALUES 
					('".$virtuemart_product_id."' , '".$virtuemart_custom_id."' , 'vm2tags' , '".$db->getEscaped($limited_tags)."' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
				}
				
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
				
				if(count($septags) > $tagslimit){
					JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.$tagslimit.'</b> '.JText::_('COM_VMVENDOR_VMVENADD_FIRSTTAGSONLY').'');	
					
				}	
			}
			
			if($enable_vm2geolocator && $latitude!='' && $longitude!=''){
				$q="SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element` = 'vm2geolocator' ";
				$db->setQuery($q);
				$virtuemart_custom_id = $db->loadResult();
				if($virtuemart_custom_id!=''){
				
					//check if product has coordinates yet. If yes->update, if not->insert
					$q ="SELECT `custom_param` FROM `#__virtuemart_product_customfields`  WHERE `virtuemart_product_id` ='".$virtuemart_product_id."' AND `custom_value`='vm2geolocator' ";
					$db->setQuery($q);
					$custom_param = $db->loadResult();
					if($custom_param!=''){ // we update product coordinates
						$q ="UPDATE `#__virtuemart_product_customfields` SET `custom_param` ='{\"latitude\":\"".$latitude."\",\"longitude\":\"".$longitude."\",\"zoom\":\"".$zoom."\",\"maptype\":\"".$maptype."\"}' WHERE `custom_value`='vm2geolocator' AND `virtuemart_product_id`=".$virtuemart_product_id." AND `virtuemart_custom_id` = ".$virtuemart_custom_id." ";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					
					}
					else{ // we insert 
				
					
						$q="INSERT INTO `#__virtuemart_product_customfields` 
					(`virtuemart_product_id`,`virtuemart_custom_id`,`custom_value`,`custom_param`,`published`,`created_on`,`created_by`)
					VALUES 
					('".$virtuemart_product_id."','".$virtuemart_custom_id."','vm2geolocator','{\"latitude\":\"".$latitude."\",\"longitude\":\"".$longitude."\",\"zoom\":\"".$zoom."\",\"maptype\":\"".$maptype."\"}','1','".date('Y-m-d H:i:s')."','".$user->id."')";
						$db->setQuery($q);
						if (!$db->query()) die($db->stderr(true));
					}
				}
			}
		
			if($enable_vm2dropdownfield){
				$q = "SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='vm2dropdownfield' AND `published`='1' ";
				$db->setQuery($q);
				$vm2drops = $db->loadObjectList();
				$i = 1;
				foreach($vm2drops as $vm2drop){
					if(JRequest::getVar('vm2dropdownfield'.$i ,'','post') !='' ){
						$q = "SELECT custom_param FROM #__virtuemart_product_customfields 
						WHERE virtuemart_product_id='".$virtuemart_product_id."'  
						AND virtuemart_custom_id='".JRequest::getVar('hiddendropfieldid_'.$i ,'','post')."' ";
						$db->setQuery($q);
						$exists = $db->loadResult();
						if($exists !=''){
							$q = "UPDATE `#__virtuemart_product_customfields` 
							SET `custom_param` ='{\"options\":\"".$db->getEscaped( JRequest::getVar('vm2dropdownfield'.$i ,'','post') )."\"}' ,
							`modified_on`='".date('Y-m-d H:i:s')."' ,
							`modified_by`='".$user->id."' 
							WHERE `custom_value`='vm2dropdownfield' AND `virtuemart_product_id`=".$virtuemart_product_id." AND `virtuemart_custom_id` = ".$db->getEscaped( JRequest::getVar('hiddendropfieldid_'.$i ,'','post') ) ." ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}
						else{						
							$q = "INSERT INTO `#__virtuemart_product_customfields` 
							(`virtuemart_product_id`,`virtuemart_custom_id`,`custom_value`,`custom_param`,`published`,`created_on`,`created_by`)
							VALUES 
						('".$virtuemart_product_id."','".$vm2drop->virtuemart_custom_id."','vm2dropdownfield','{\"options\":\"".$db->getEscaped( JRequest::getVar('vm2dropdownfield'.$i ,'','post')."\"}','1','".date('Y-m-d H:i:s') )."','".$user->id."')";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}
					}
					$i++;
				}	
			}
		
			//////////////////////
			
			$q = "UPDATE `#__virtuemart_product_prices` 
			SET `product_price`='".$formprice."' , 
			`modified_on`='".date('Y-m-d H:i:s')."' , 
			`modified_by`='".$user->id."' WHERE `virtuemart_product_id`='".$virtuemart_product_id."'";
			$db->setQuery($q);
			if (!$db->query()) die($db->stderr(true));	
				
			for ($i=1; $i <= $max_imagefields ;$i++){ ////////////// images
				$imgisvalid = 1;
				$image = JRequest::getVar('image'.$i, null, 'files', 'array');
				$image['name'] = JFile::makeSafe($image['name']);
				if($image['name']!=''){
					$infosImg = getimagesize($image['tmp_name']);							
					//if ( (substr($image['type'],0,5) != 'image' || $infosImg[0] > $maximgside || $infosImg[1] > $maximgside )){
					if ( (substr($image['type'],0,5) != 'image'  )){
						JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMGEXTNOT') );
						$imgisvalid = 0;
					}

					$product_image = strtolower($formsku ."_".$image['name']);														
					$target_imagepath = JPATH_BASE . DS . $image_path . $product_image;
					if($imgisvalid){
						if( JFile::upload( $image['tmp_name'] , $target_imagepath )  ){
							$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOADRENAME_SUCCESS').' '.$product_image);
						}
						else
							JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_ERROR') );

					}
							
										// we store thumb
					$ext = JFile::getExt( $image['name'] ) ; 
					$ext = strtolower($ext);
					$ext = str_replace('jpeg','jpg',$ext);
								//SWITCHES THE IMAGE CREATE FUNCTION BASED ON FILE EXTENSION
					switch(strtolower($ext)) {
						case 'jpg':
									$source = imagecreatefromjpeg($target_imagepath);
									$large_source = imagecreatefromjpeg($target_imagepath);
								break;
								case 'png':
									$source = imagecreatefrompng($target_imagepath);
									$large_source = imagecreatefrompng($target_imagepath);
								break;
								case 'gif':
									$source = imagecreatefromgif($target_imagepath);
									$large_source = imagecreatefromgif($target_imagepath);
								break;
						default:
							//JError::raiseWarning( 100, JText::_('COM_VMVENDOR_VMVENADD_IMAGEUPLOAD_INVALID') );
							$imgisvalid = 0;
						break;
					} 
					if($product_image!='' && $imgisvalid ){		
						list($width,  $height) = getimagesize($target_imagepath); 
						/*if($width>=$height){ 
							$resizedH = ( VmConfig::get('img_width') * $height) / $width;
							$thumb = imagecreatetruecolor( VmConfig::get('img_width') ,  $resizedH);
							imagecopyresampled( $thumb,  $source,  0,  0,  0,  0,  VmConfig::get('img_width') ,  $resizedH,  $width,  $height );
						}
						else{
							$resizedW = ( VmConfig::get('img_height') * $width) / $height;
							$thumb = imagecreatetruecolor($resizedW,  VmConfig::get('img_height') );
							imagecopyresampled($thumb ,  $source ,  0 ,  0 ,  0 ,  0 ,  $resizedW,  VmConfig::get('img_height') ,  $width,  $height );
						}*/
						
						if($width>$maximgside){
									$resizedH = ( $maximgside * $height) / $width;
									if($ext=='gif')
										$largeone = imagecreate( $maximgside ,  $resizedH);
									else
										$largeone = imagecreatetruecolor( $maximgside ,  $resizedH);
									imagealphablending( $largeone, false);
									imagesavealpha( $largeone,true);
									$transparent = imagecolorallocatealpha($largeone, 255, 255, 255, 127);
									imagefilledrectangle($largeone, 0, 0, $maximgside, $resizedH, $transparent);
									imagecopyresampled( $largeone,  $large_source,  0,  0,  0,  0,  $maximgside ,  $resizedH,  $width,  $height );
                                  }
                                else{
                                     $largeone = $target_imagepath;
                               }
						
						 switch($ext) {
									case 'jpg':
										imagejpeg($largeone, JPATH_BASE . DS . $image_path .DS .$product_image,  $thumbqual);
									break;
									case 'jpeg':
										imagejpeg($largeone, JPATH_BASE . DS . $image_path .DS .$product_image,  $thumbqual);
									break;
									case 'png':
										imagepng($largeone, JPATH_BASE . DS . $image_path .DS .$product_image);
									break;
									case 'gif':
										imagegif($largeone, JPATH_BASE . DS . $image_path .DS .$product_image);
									break;
								} 
								imagedestroy($largeone);

						//imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image,  $thumbqual);
						
						if($width>=$height){ 
									$resizedH = ( VmConfig::get('img_width') * $height) / $width;
									if($ext=='gif')
										$thumb = imagecreate( VmConfig::get('img_width') ,  $resizedH);
									else
										$thumb = imagecreatetruecolor( VmConfig::get('img_width') ,  $resizedH);
									imagealphablending( $thumb, false);
									imagesavealpha( $thumb,true);
									$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
									imagefilledrectangle($thumb, 0, 0, VmConfig::get('img_width') , $resizedH, $transparent);
									imagecopyresampled( $thumb,  $source,  0,  0,  0,  0,  VmConfig::get('img_width') ,  $resizedH,  $width,  $height );
								}
								else{
									$resizedW = ( VmConfig::get('img_height') * $width) / $height;
									if($ext=='gif')
										$thumb = imagecreate($resizedW,  VmConfig::get('img_height') );
									else
										$thumb = imagecreatetruecolor($resizedW,  VmConfig::get('img_height') );
									imagealphablending( $thumb, false);
									imagesavealpha( $thumb,true);
									$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
									imagefilledrectangle($thumb, 0, 0, $resizedW , VmConfig::get('img_height'), $transparent);
									imagecopyresampled($thumb ,  $source ,  0 ,  0 ,  0 ,  0 ,  $resizedW,  VmConfig::get('img_height') ,  $width,  $height );
								}
						
						switch($ext) {
									case 'jpg':
										imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image,  $thumbqual);
									break;
									case 'jpeg':
										imagejpeg($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image,  $thumbqual);
									break;
									case 'png':
										imagepng($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image);
									break;
									case 'gif':
										imagegif($thumb, JPATH_BASE . DS . $thumb_path .DS .$product_image);
									break;
								} 
						imagedestroy($thumb);
						
						
						

						if(JRequest::getVar('media_id'.$i,'','post')!=''){ // we updated the picture
							// delete all media file
							$q = "SELECT `file_url` , `file_url_thumb` FROM `#__virtuemart_medias` WHERE `virtuemart_media_id`='".JRequest::getVar('media_id'.$i,'','post')."' ";
							$db->setQuery($q);
							$media_files = $db->loadRow();
							$image_url 		= $media_files[0];
							$thumb_url		= $media_files[1];
							if($image_url !=  $image_path.JFile::makeSafe($product_image) ){ // only delete old file if new filename and old are diferent. If same file has allready been overwritten
								if($image_url!='')
									JFile::delete($image_url);
								if($thumb_url!='')
									JFile::delete($thumb_url);
							}
							$q ="UPDATE `#__virtuemart_medias` SET 
							`file_title`='".JFile::makeSafe($product_image)."' , 
							 `file_mimetype`='".$image['type']."' , 
							 `file_url` = '".$image_path.JFile::makeSafe($product_image)."' , 
							 `file_url_thumb` ='".$thumb_path.JFile::makeSafe($product_image)."' ,
							 `modified_on`='".date('Y-m-d H:i:s')."' , 
							 `modified_by` ='".$user->id."' 
							 WHERE `virtuemart_media_id` ='".JRequest::getVar('media_id'.$i)."' ";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}
						else { // we add a new file					
							$q = "INSERT INTO `#__virtuemart_medias` 
							( `virtuemart_vendor_id` , `file_title` , `file_mimetype` , `file_type` , `file_url` , `file_url_thumb` , `file_is_product_image` , `published` , `created_on` , `created_by`)
							VALUES

							(  '".$virtuemart_vendor_id."' , '".$db->getEscaped($product_image)."' , '".$image['type']."' , 'product' , '".$image_path.JFile::makeSafe($product_image)."' , '".$thumb_path.JFile::makeSafe($product_image)."' , '1', '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' )";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
							$virtuemart_media_id = $db->insertid();
							$q = "INSERT INTO `#__virtuemart_product_medias` 
							( `virtuemart_product_id` , `virtuemart_media_id` , `ordering` )
							VALUES
							(  '".$virtuemart_product_id."' , '".$virtuemart_media_id."' , '".$i."'  )";
							$db->setQuery($q);
							if (!$db->query()) die($db->stderr(true));
						}
					}
				}
			}
		
			////////////////////////////// Core Custom fields support
					
					if($enable_corecustomfields){
						$q ="SELECT `virtuemart_custom_id` , `custom_parent_id` , `virtuemart_vendor_id` , `custom_jplugin_id` , `custom_title` , `custom_tip` , `custom_value`, `custom_field_desc` , `field_type` , `is_list` , `shared`  
						FROM `#__virtuemart_customs`
						WHERE `custom_jplugin_id`='0' 
						AND `admin_only`='0' 
						AND `published`='1' 
						AND `custom_element`!='' 
						ORDER BY `ordering` ASC , `virtuemart_custom_id` ASC ";
						$db->setQuery($q);
						$core_custom_fields	= $db->loadObjectList();
						$i = 0;
						foreach ($core_custom_fields as $core_custom_field){
							$i++;
							$q ="SELECT count(*) FROM #__virtuemart_product_customfields 
							WHERE `virtuemart_product_id` ='".$virtuemart_product_id."' 
							AND `virtuemart_custom_id` ='".$core_custom_field->virtuemart_custom_id."' ";
							$db->setQuery($q);
							$exists = $db->loadResult();
							if($exists>0){
								$q ="UPDATE `#__virtuemart_product_customfields`
								SET 
								`custom_value`='".$db->getEscaped( JRequest::getVar( 'corecustomfield_'.$i ) )."' ,
								`modified_on` ='".date('Y-m-d H:i:s')."' ,
								`modified_by` = '".$user->id."' 
								WHERE `virtuemart_product_id` ='".$virtuemart_product_id."' 
								AND `virtuemart_custom_id` ='".$core_custom_field->virtuemart_custom_id."' ";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));
							}
							else{
								$q ="INSERT INTO #__virtuemart_product_customfields 
								( virtuemart_product_id , virtuemart_custom_id , custom_value , published , created_on , created_by , ordering )
								VALUES
								(  '".$virtuemart_product_id."' , '".$core_custom_field->virtuemart_custom_id."' , '".$db->getEscaped( JRequest::getVar( 'corecustomfield_'.$i ) )."' , '1' , '".date('Y-m-d H:i:s')."' , '".$user->id."' , '".$i."'  )";
								$db->setQuery($q);
								if (!$db->query()) die($db->stderr(true));	
							}
								
						}
					}
					
			
			if ($enablerss){							
				if(!file_exists(JPATH_BASE."/media/vmvendorss"))
					mkdir(JPATH_BASE."/media/vmvendorss", 0777);	
				$xml_path = JPATH_BASE.'/media/vmvendorss/'.$user->id.'.rss';
				
				$q = "SELECT vm.`file_url_thumb` FROM `#__virtuemart_medias` vm 
				LEFT JOIN `#__virtuemart_vendor_medias` vvm ON vvm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
				WHERE vvm.`virtuemart_vendor_id`='".$virtuemart_vendor_id."'";
				$db->setQuery($q);
				$avatar = $db->loadResult();
				$feed_img = $juri.$avatar;
				if($avatar == NULL)
					$feed_img = $juri.'components/com_vmvendor/assets/img/noimage.gif';
				$profile_url =JRoute::_('index.php?option=com_vmvendor&view=vendorprofile&userid='.$user->id);			
				$rss_xml = '<?xml version="1.0" encoding="utf-8" ?>';
				$rss_xml .='<rss version="2.0" xmlns:blogChannel="http://'.$_SERVER["SERVER_NAME"].$profile_url.'">';
				$rss_xml .='<channel>';
				$rss_xml .='<title>'.ucfirst($user->username).' - '.JText::_('COM_VMVENDOR_RSS_CAT').'</title>';
				$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].$profile_url.'</link>';
				$rss_xml .='<description>My online catalogue at '.$juri.'</description>';
				$rss_xml .='<generator>Nordmograph.com - VMVendor user catalogue RSS feed for Virtuemart and Joomla</generator>';
				$rss_xml .='<lastBuildDate>'.date("D, d M Y H:i:s O").'</lastBuildDate>';
				$rss_xml .='<language>'.substr($doc->getLanguage(),0,2).'</language>';
				$rss_xml .='<image>';
				$rss_xml .='<url>'.$feed_img.'</url>';
				$rss_xml .='<title>'.ucfirst($user->username).' - '.JText::_('COM_VMVENDOR_RSS_CAT').'</title>';
				$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].$profile_url.'</link>';
				$rss_xml .='</image>';
				$q = "SELECT p.`virtuemart_product_id` , p.`virtuemart_vendor_id` ,p.`created_on` , 
				pl.`product_s_desc` , pl.`product_name` , 
	
				vu.`virtuemart_user_id` , 
				vc.`virtuemart_category_id`
				FROM `#__virtuemart_products` p 
				LEFT JOIN `#__virtuemart_products_".VMLANG."` pl ON pl.`virtuemart_product_id` = p.`virtuemart_product_id` 
				LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_product_id` = p.`virtuemart_product_id`

				LEFT JOIN `#__virtuemart_vmusers` AS vu ON vu.`virtuemart_vendor_id` = p.`virtuemart_vendor_id`
				LEFT JOIN `#__virtuemart_product_categories` vc ON vc.`virtuemart_product_id` = p.`virtuemart_product_id` 
				WHERE vu.`virtuemart_user_id` ='".$user->id."' 
				
				AND p.`published`='1' 
				ORDER BY p.`virtuemart_product_id` DESC LIMIT ".$rsslimit;
				$db->setQuery($q);				
				$products = $db->loadObjectList();				
				foreach($products as $product){
					$q = "SELECT vm.`file_url_thumb` 
					FROM `#__virtuemart_medias` vm 
					LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
					WHERE vpm.`virtuemart_product_id` = '".$product->virtuemart_product_id."' 
					AND vm.`file_is_product_image`='1' ";
					$db->setQuery($q);				
					$product_file_url_thumb = $db->loadResult();
					$rss_xml .='<item>';
					$rss_xml .='<title>'.$product->product_name.'</title>';
					$rss_xml .='<link>http://'.$_SERVER["SERVER_NAME"].JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id.'&Itemid='.$vmitemid).'</link>';
					$feeditem_img = $juri.$product_file_url_thumb;
					if(!$product_file_url_thumb)
						$feeditem_img = $juri.'components/com_virtuemart/assets/images/vmgeneral/'. VmConfig::get('no_image_set');
					$rss_xml .='<description><img src="'.$feeditem_img.'" width="'.VmConfig::get('img_width').'" height="'.VmConfig::get('img_height').'"/>'.$product->product_s_desc.'</description>';
					$rss_xml .='<pubDate>'.$product->created_on.'</pubDate>';
					$rss_xml .='</item>';
				}
				$rss_xml .='</channel>';
				$rss_xml .='</rss>';		
				$feed = fopen($xml_path, "w");	
				fwrite($feed,$rss_xml);
				fclose($feed);									
			}
		
		
			
			$product_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$virtuemart_product_id.'&virtuemart_category_id='.$formcat.'&Itemid='.$vmitemid);
						
						// Shoutbox
						
			if ($shoutbox >0 && $autopublish){
				$entryby = JText::_('COM_VMVENDOR_SHTBX_NEWPRODUCT');
				$thetime = time();
				//$msg = ucfirst($my_username)." ";
				$msg = $formname." ".JText::_('COM_VMVENDOR_SHTBX_HASJUST')." ".$user->$naming.JText::_('COM_VMVENDOR_SHTBX_CATALOGUE');
				$url = $product_url;
				if ($shoutboxlink ==1) //CB
					$url = JRoute::_('index.php?option=com_comprofiler&task=userProfile&Itemid='.$profileitemid.'&user='.$user->id.'&tab=getcbvmvendortab');
				if ($shoutboxlink ==2) // Jomsocial
					$url = JRoute::_('index.php?option=com_community&view=profile&Itemid='.$profileitemid.'&userid='.$user->id.'&task=app&app=vmvendor');
				$sbtable ='shoutbox';
				if ($shoutbox ==2)
					$sbtable ='liveshoutbox';							
				$q = 'INSERT INTO #__'.$sbtable.' (time,name,text,url)'
				.' VALUES ("'.$thetime.'","'.$entryby .'","'.$msg.'","'.$url.'")';
				$db->setQuery($q);
				if (!$db->query()) die($db->stderr(true));
			}
			
			// Jomsocial activity feed
			if($profileman==2 && $autopublish){
				$jspath = JPATH_ROOT . DS . 'components' . DS . 'com_community';
				include_once($jspath. DS . 'libraries' . DS . 'core.php');//activity stream  - added a blog
				CFactory::load('libraries', 'activities');          
				$act = new stdClass();
				$act->cmd    = 'wall.write';
				$act->actor    = $user->id;
				$act->target    = 0; // no target
				$act->title    = JText::_( 'COM_VMVENDOR_JOMSOCIAL_HASJUSTEDITED').' <a href="'.$product_url.'">'.stripslashes( ucfirst( $formname ) ).'</a>' ;
				$output = '';
				if($enablerss)
					$output .='<div><a href="'.$juri.'media/vmvendorss/'.$user->id.'.rss" ><img src="'.$juri.'components/com_vmvendor/assets/img/rss.png" alt="rss" width="14" height="14" /> '.JText::_('COM_VMVENDOR_VENDORRSS').'</a></div>';
				$act->content    = $output;
				$act->app    = 'vmvendor.productedition';
				$act->cid    = 0;
				$act->comment_id	= CActivities::COMMENT_SELF;
				$act->comment_type	= 'vmvendor.productedition';
				$act->like_id		= CActivities::LIKE_SELF;		
				$act->like_type		= 'vmvendor.productedition';
				CActivityStream::add($act);
			}
			
			
			// Email Notification when new product edited
			if( ($emailnotify_updated && $to!= NULL ) || !$autopublish){
				$subject = JText::_('COM_VMVENDOR_EMAIL_HELLO_EDITED')." ".$juri." ".JText::_('COM_VMVENDOR_EMAIL_BYUSER')." ".$user->$naming;				
				$mailurl= $juri.'index.php?option=com_vmvendor&view=vendorprofile&userid='.$user->id.'&Itemid='.$profileitemid;									
				$body = JText::_('COM_VMVENDOR_EMAIL_VISIT_EDITED')." <a href='".$juri."index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=".$virtuemart_product_id."&virtuemart_category_id=".$formcat."&Itemid=".$vmitemid."' >"
					.JText::_('COM_VMVENDOR_EMAIL_HERE')."</a>."
					.JText::_('COM_VMVENDOR_EMAIL_SUBMITTEDBY')." <a href='"
					.$mailurl."'>".$user->$naming."</a>. ";
				if(!$autopublish)
					$body .=JText::_('COM_VMVENDOR_EMAIL_BUTFIRSTREVIEW').' <a href="'.$juri.'administrator/index.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart">'.JText::_('COM_VMVENDOR_EMAIL_SHOPADMIN').'</a>.';												
				$mailerror = "<img src='".$juri."components/com_vmvendor/assets/img/bad.png' width='16' height='16' alt='' align='absmiddle' /> <font color='red'><b>".JText::_('COM_VMVENDOR_EMAIL_FAIL')."</b></font>";							
				$message =& JFactory::getMailer();
				$message->addRecipient($to);
				$message->setSubject($subject);
				$message->setBody($body);
				$config =& JFactory::getConfig();
				$sender = array( 
					$config->getValue( 'config.mailfrom' ),
					$config->getValue( 'config.fromname' )
				);				
				$message->isHTML(true);
				$message->Encoding = 'base64';
				$message->setSender($sender);
				$message->addRecipient( $config->getValue( 'config.mailfrom' ) );
				$sent = $message->send();
				if ($sent != 1) 
					echo  $mailerror;		}
			}
			
			if($oldprice == 0 && $formprice >0){ // becomes commercial, we move downloads to safe path
				// get downloadable files
				$q = "SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `custom_element`='st42_download' ";
				$db->setQuery($q);
				$virtuemart_custom_id = $db->loadresult();
				
				$q ="SELECT vm.`virtuemart_media_id` , vm.`file_title` , vm.`file_url` 
				FROM `#__virtuemart_medias` vm 
				LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
				WHERE vpm.`virtuemart_product_id` ='".$virtuemart_product_id."' 
				AND vm.`file_is_downloadable`='1' ";
				
				//////
				//JError::raiseNotice( 100, $q);
				//////
				
				
				$db->setQuery($q);
				$filestomove = $db->loadObjectList();
				
				//////
				//JError::raiseNotice( 100, count($filestomove) );
				//////
				
				
				foreach($filestomove as $filetomove){
					JFile::copy( $filetomove->file_url , $safepath.$filetomove->file_title);
					$q= "UPDATE `#__virtuemart_medias` 
					SET `file_url`='".$safepath.$filetomove->file_title."' ,
					`file_is_downloadable`='0' ,
					`file_is_forSale`='1' 
					WHERE `virtuemart_media_id`='".$filetomove->virtuemart_media_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q ="DELETE FROM `#__virtuemart_product_medias` 
					WHERE `virtuemart_media_id`='".$filetomove->virtuemart_media_id."' 
					AND `virtuemart_product_id`='".$virtuemart_product_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q ="INSERT INTO `#__virtuemart_product_customfields` 
					( `virtuemart_product_id` , `virtuemart_custom_id` , `custom_value` , `custom_param` , `created_on` , `created_by` , `modified_on` , `modified_by` )
					VALUES
					('".$virtuemart_product_id."' , '".$virtuemart_custom_id."' , 'st42_download' , '{\"media_id\":\"".$filetomove->virtuemart_media_id."\",\"stream\":\"".$stream."\",\"maxspeed\":\"".$maxspeed."\",\"maxtime\":\"".$maxtime."\"}' , '".date('Y-m-d H:i:s')."' , '".$user->id."' , '".date('Y-m-d H:i:s')."' , '".$user->id."'  )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
				}
				if(count($filestomove)>0)
					JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.JText::_('COM_VMVENDOR_FILESMADESAFE').'</b>');
				if($reset_onpricestatus){
					$q ="DELETE FROM `#__virtuemart_ratings` WHERE `virtuemart_product_id`='".$virtuemar_product_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q ="DELETE FROM `#__virtuemart_rating_reviews` WHERE `virtuemart_product_id`='".$virtuemar_product_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q ="DELETE FROM `#__virtuemart_rating_votes` WHERE `virtuemart_product_id`='".$virtuemar_product_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));	
					JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.JText::_('COM_VMVENDOR_REVIEWSRESET').'</b>');
				}	
			}
			elseif($oldprice > 0 && $formprice == '0'){ // becomes free, we move files from safe path to media files
				// get safepath files and make these doanlodable
				//custom_param LIKE CONCAT('%' , CONCAT('\"media_id\":\"' , '".$virtuemart_media_id."' , '\"') , '%' )
				
				$q ="SELECT vm.`virtuemart_media_id` , vm.`file_title` , vm.`file_url` 
				FROM `#__virtuemart_medias` vm 
				LEFT JOIN `#__virtuemart_product_customfields` vpc ON vpc.`custom_param` LIKE CONCAT('%' , CONCAT('\"media_id\":\"' , vm.`virtuemart_media_id` , '\"') , '%' )
				WHERE vpc.`virtuemart_product_id` ='".$virtuemart_product_id."' 
				AND vpc.`custom_value`='st42_download' 
				AND vm.`file_is_forSale`='1' ";
				
				//LEFT JOIN `#__virtuemart_product_customfields` vpc ON vpc.`custom_param` LIKE CONCAT('%' , CONCAT('\"media_id\":\"' , '".$virtuemart_media_id."' , '\"') , '%' )
				
				//////
				//JError::raiseNotice( 100, $q);
				//////
				
				$db->setQuery($q);
				$filestomove = $db->loadObjectList();
				
				//////
				//JError::raiseNotice( 100, count($filestomove) );
				//////
				
				
				foreach($filestomove as $filetomove){
					JFile::copy( $filetomove->file_url , $image_path.$filetomove->file_title);
					$q= "UPDATE `#__virtuemart_medias` 
					SET `file_url`='".$image_path.$filetomove->file_title."' ,
					`file_is_downloadable`='1' ,
					`file_is_forSale`='0' 
					WHERE `virtuemart_media_id`='".$filetomove->virtuemart_media_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					$q ="DELETE FROM `#__virtuemart_product_customfields`
					WHERE `custom_value`='st42_download' 
					AND `virtuemart_product_id`='".$virtuemart_product_id."' ";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					
					$q = "INSERT INTO `#__virtuemart_product_medias` 
					( `virtuemart_product_id` , `virtuemart_media_id` ) 
					VALUES
					( '".$virtuemart_product_id."' , '".$filetomove->virtuemart_media_id."' )";
					$db->setQuery($q);
					if (!$db->query()) die($db->stderr(true));
					
				}
				if(count($filestomove)>0)
					JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/warning.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.JText::_('COM_VMVENDOR_FILESMADEDOWNLOADABLE').'</b>');
			}
			if($autopublish){
				$app->enqueueMessage( '<img src="'.$juri.'components/com_vmvendor/assets/img/good.png" width="16" height="16" alt="" align="absmiddle" /> '.JText::_('COM_VMVENDOR_PRODUCTUPDATED'));
			}
			else
				JError::raiseNotice( 100, '<img src="'.$juri.'components/com_vmvendor/assets/img/clock.png" width="16" height="16" alt="" align="absmiddle" /> <b>'.JText::_('COM_VMVENDOR_TOBEREVIEWD').'</b>');
		}
		if (isset($_POST['formsku']) && $user->id == 0) // user has been inactive for too long and session time out
			JError::raiseWarning( 100,  JText::_('COM_VMVENDOR_UPLOAD_TIMEOUT') );
		$app->redirect( 'index.php?option=com_vmvendor&view=vendorprofile&Itemid='.JRequest::getVar('Itemid') );
	}
	
	public function askvendor()  {
		$user 			= &JFactory::getUser();
		$db 			= &JFactory::getDBO();
		$doc 			= &JFactory::getDocument();
		$juri 			= JURI::base();
		$app 			= JFactory::getApplication();
		$cparams 					=& JComponentHelper::getParams('com_vmvendor');
		$profileman 			= $cparams->getValue('profileman');
		$naming 		= $cparams->getValue('naming', 'username');	
		if(JRequest::getVar('formname')!='' && JRequest::getVar('formemail')!='' &&  JRequest::getVar('formsubject')!='' && JRequest::getVar('formmessage')!='' ){
			$product_url = JRequest::getVar('formhref');
			$message =& JFactory::getMailer(); 
			$config =& JFactory::getConfig();
			$mailfrom = JRequest::getVar('formemail');
			$fromname = JRequest::getVar('formname');
			$subject = JRequest::getVar('formsubject');
			$emailto		= JRequest::getVar('emailto');
			$body = JRequest::getVar('formmessage').",\r\n\r\n";
			$body .= urldecode($product_url);
			$mailerror = JText::_('COM_VMVENDOR_ASKVENDOR_EMAILFAILED');
			$message->addRecipient($emailto); 
			$message->addBCC( $mailfrom );
			$message->setSubject($subject);
			$message->setBody($body);
			$sender = array( $mailfrom, $fromname );
			$message->setSender($sender);
			$sent = $message->send();
			if ($sent != 1) 
				$sent = 2;
			$app->redirect('index.php?option=com_vmvendor&view=askvendor&format=raw&sent='.$sent);
		}
	}
}