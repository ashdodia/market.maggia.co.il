<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if (!class_exists( 'VmConfig' ))
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	
$user 				= &JFactory::getUser();
$db 				= &JFactory::getDBO();
$juri 				= JURI::base();
$lang 				= &JFactory::getLanguage();
$langtag 			=  $lang->get('tag');
$langtag			=str_replace("-","_",$langtag);
			
$doc 				= &JFactory::getDocument();
$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/vendorprofile.css');
JHTML::_('behavior.modal');

$cparams 				=& JComponentHelper::getParams('com_vmvendor');
$naming 				= $cparams->getValue('naming', 'username');
$profileman 			= $cparams->getValue('profileman');
$vmitemid	 			= $cparams->getValue('vmitemid');
$profileitemid			= $cparams->getValue('profileitemid');
$vendorcontactform		= $cparams->getValue('vendorcontactform');
$allow_deletion			= $cparams->getValue('allow_deletion'); //0 no 1 unpublish only 2 yes but 3 yes
$enablerss 				= $cparams->getValue('enablerss', 1);
$enablestock			= $cparams->getValue('enablestock', 1);
$enableprice			= $cparams->getValue('enableprice', 1);

$facebooklike			= $cparams->getValue('facebooklike',1);
$appid					= $cparams->getValue('appid');
$fb_width				= $cparams->getValue('fb_width','80');
$fb_action				= $cparams->getValue('fb_action','like');
$twitter				= $cparams->getValue('twitter',1);
$googleplus				= $cparams->getValue('googleplus',1);
$linkedin				= $cparams->getValue('linkedin',1);
$enable_vendormap		= $cparams->getValue('enable_vendormap',1);
$map_width				= $cparams->getValue('map_width','700');
$map_height				= $cparams->getValue('map_height','300');

$enable_jcomments		= $cparams->getValue('enable_jcomments',0);

$load_jquery			= $cparams->getValue('load_jquery', 1);
$jquery_url				= $cparams->getValue('jquery_url','https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
if($load_jquery)
	$doc->addScript($jquery_url);
$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/bootstrap.min.css');
$doc->addScript($juri.'components/com_vmvendor/assets/js/bootstrap.min.js');


$userid 					= JRequest::getVar('userid');
$currency_symbol 			= $this->main_currency[0];
$currency_positive_style	= $this->main_currency[1];
$currency_decimal_place 	= $this->main_currency[2];
$currency_decimal_symbol 	= $this->main_currency[3];
$currency_thousands 		= $this->main_currency[4];
$vendor_store_desc 			= $this->vendor_data[0];
$vendor_terms_of_service	= $this->vendor_data[1];
$vendor_legal_info			= $this->vendor_data[2];	
$vendor_store_name			= ucfirst($this->vendor_data[3]);
$vendor_phone				= $this->vendor_data[4];
$vendor_url					= $this->vendor_data[5];
$vendor_id 					= $this->vendor_data[6];

$page_title = $doc->getTitle('Browser Title');
$doc -> setTitle( $page_title . ' - '. ucfirst( $vendor_store_name ) );
		
if($profileman == 2){
	require_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
	require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'window.php' );
	CWindow::load();
	$config		=& CFactory::getConfig();
	$js	= '/assets/script-1.2';
	$js	.= ( $config->get('usepackedjavascript') == 1 ) ? '.pack.js' : '.js';
	CAssets::attach($js, 'js');
	$tooltip_class = 'jomNameTips';	
	
}
else{
	JHTML::_('behavior.tooltip');
	$tooltip_class = 'hasTip';
}

$ismyprofile = 0;
$allowed = 1;
if ($userid == $user->id OR !$userid){
	$ismyprofile = 1;
	$userid = $user->id;

	$profiletypes_mode		= $cparams->getValue('profiletypes_mode', 0);
	$profiletypes_ids		= $cparams->getValue('profiletypes_ids');
	if($profiletypes_mode>0 && $profiletypes_ids!='' && $profileman ==2)
	$allowed = VmvendorModelVendorprofile::getJSProfileallowed($profiletypes_ids);
}

if($allowed){
	
	$q = "SELECT vm.`file_url` 
	FROM `#__virtuemart_medias` vm 
	LEFT JOIN `#__virtuemart_vendor_medias` vvm ON vvm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
	WHERE vvm.`virtuemart_vendor_id` = '".$vendor_id."' 
	AND vm.`file_type`='vendor' ORDER BY `file_is_product_image` DESC ";
	$db->setQuery($q);
	$vendor_thumb_url = $db->loadResult();
	
	$q = "SELECT `id` FROM `#__menu` WHERE `link`='index.php?option=com_vmvendor&view=dashboard' AND `type`='component' AND `published`='1' ";
	$db->setQuery($q);
	$dashboard_itemid = $db->loadResult();
	
	$q = "SELECT `id` FROM `#__menu` WHERE `link`='index.php?option=com_vmvendor&view=addproduct' AND `type`='component' AND `published`='1' ";
	$db->setQuery($q);
	$addproduct_itemid = $db->loadResult();




	echo '<h1>'.JText::_('COM_VMVENDOR_PROFILE_TITLE').'</h1>';
	if(count($this->myproducts)>0){

	if($userid !='0'){
		echo '<div class="vmvendor-toolbar btn-group" >';
		if($user->id>0 && $ismyprofile){
			$dashboard_url = JRoute::_('index.php?option=com_vmvendor&amp;view=dashboard&amp;Itemid='.$dashboard_itemid);
			echo '<a href="'.$dashboard_url.'" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_DASHBOARD' ).'"><span class="glyphicon glyphicon-cog " ></span></a>';
		}
		if( $enablerss && file_exists(JPATH_BASE.DS.'media'.DS.'vmvendorss'.DS.$userid.'.rss')){
			$feed_url = $juri.'media/vmvendorss/'.$userid.'.rss';
			echo '<a href="'.$feed_url.'" target="_blank" class="btn btn-xs btn-default '.$tooltip_class.'"  title="'.JText::_( 'COM_VMVENDOR_PROFILE_RSSFEED' ).'" ><img src="'.$juri.'components/com_vmvendor/assets/img/feed.png" alt="" width="12" height="12"  > </a>';
		}
		if($user->id>0 && $user->id == $userid){
			echo '<a  href="'.JRoute::_('index.php?option=com_vmvendor&view=editprofile&Itemid='.JRequest::getVar('Itemid') ).'" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_EDITPROFILE' ).'"><span class="glyphicon glyphicon-edit"></span> </a>';
		}
		
		
	
		
		echo '</div>';
	
	
	
	
	
	
	
	
	
	
	
		echo '<div id="vendor-img">';
	
		if($vendor_thumb_url)
			$vendor_thumb_url = $juri.$vendor_thumb_url;
		else
			$vendor_thumb_url = $juri.'components/com_vmvendor/assets/img/noimage.gif';
		$thumb_title = $vendor_store_name;
		if($ismyprofile){
			$thumb_title = JText::_( 'COM_VMVENDOR_PROFILE_CLICKTOCHANGEAVATAR' );
			echo '<a href="'.JRoute::_('index.php?option=com_vmvendor&amp;view=editprofile&Itemid='.JRequest::getInt('Itemid')).'" >';
		}
		echo '<img src="'.$vendor_thumb_url.'" alt="" width="'.VmConfig::get('img_width').'"  title="'.$thumb_title.'" class="'.$tooltip_class.'"/>';
		if($ismyprofile)
			echo '</a>';
		echo '</div>';
		echo '<div id="storename">';
		echo '<h2>'.$vendor_store_name.'</h2>';
	
	
	
	
	
	
		if($profileman >0){
			$user_naming = $this->user_thumb[0];
			$user_avatar = $this->user_thumb[1];
			if($profileman==1)
				$user_avatar = 'images/comprofiler/'.$this->user_thumb[1];
			if($profileman==3)
				$referreid = $this->user_thumb[2];
			if(!$user_avatar){
				if($profileman==1){
					$user_avatar = 'components/com_comprofiler/plugin/templates/default/images/avatar/tnnophoto_m.png';
					
				}
				elseif($profileman==2){
					$user_avatar = 'components/com_community/assets/user_thumb.png';
					
				}
				elseif($profileman==3){
					$user_avatar = 'components/com_alphauserpoints/assets/images/avatars/generic_gravatar_grey.png';
					
				}
			}
		}
	
		echo '<div style="padding:0 0 50px 5px;">';
		echo '<div style="float:left;width:50px">';
		if($profileman >0)
			echo '<img src="'.$juri.$user_avatar.'" width="40" alt="'.ucfirst($user_naming).'" style="vertical-align:middle" />';
		echo '</div>';
		echo '<div style="float:left">';
		if($profileman >0)
			echo '<h4>'. ucfirst($user_naming).'</h4>';
	
		
	
		if($profileman==1)
			$profile_url = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$userid.'&Itemid='.$profileitemid);
		elseif($profileman==2)
			$profile_url = JRoute::_('index.php?option=com_community&view=profile&userid='.$userid.'&Itemid='.$profileitemid);
		elseif($profileman==3)
			$profile_url = JRoute::_('index.php?option=com_alphauserpoints&view=account&userid='.$referreid.'&Itemid='.$profileitemid);
		echo '<div id="icon-buttons" class="btn-group">';
		if($profileman>0)
			echo '<a href="'.$profile_url.'" class="btn btn-default"><span class="glyphicon glyphicon-user '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_VISITUSERPROFILE' ).'"></span></a>';
		if($vendor_phone)
			echo '<a class="btn btn-default"><span class="glyphicon glyphicon-phone-alt '.$tooltip_class.'" title="'.$vendor_phone.'"></span></a>';
		if($vendor_url)
			echo '<a href="'.$vendor_url.'" target="_blank" class="btn btn-default"><span class="glyphicon glyphicon-globe '.$tooltip_class.'" title="'.$vendor_url.'"></span></a>';
		if($userid != $user->id){
			if($profileman==2 && $vendorcontactform == 2)
			{
				echo  '<a href="javascript:void(0)" onclick="javascript: joms.messaging.loadComposeWindow('.$userid.');" class="btn btn-default">';
				echo  '<span class="glyphicon glyphicon-envelope '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_PMVENDOR' ).'"></span></a>';
			}
			else
			{	 
				echo   '<a 
				rel="{handler: \'iframe\', size: {x: 500, y: 350}}" 
				href="index.php?option=com_vmvendor&view=askvendor&orderitem_id=&customer_userid='.$userid.'&format=raw" 
				target="_blank" 
				class="modal btn btn-default" >';
				echo   '<span class="glyphicon glyphicon-envelope '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_EMAILVENDOR' ).'"></span></a>';
			}
		}
	
	
	
			
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		
		$doc->addCustomTag('<meta property="og:image" content="'.$juri.$vendor_thumb_url.'"/>');
		$doc->addCustomTag('<meta property="og:site_name" content="'.$vendor_store_name.'"/>');
		$doc->addCustomTag('<meta property="og:description" content="'.strip_tags($vendor_store_desc).'"/>');
		if($facebooklike OR $twitter OR $googleplus OR $linkedin){
			$uri 	 = & JFactory::getURI();
			$href	 = $uri->toString();
			$encodedurl = urlencode($href);
			
			//echo '<div style="clear:both;" ></div>';
			echo '<div id="social_share" >';	
			if($facebooklike>0){
			//	if(!$fb_lang){
					$fb_lang = $langtag;
				//}
				echo '<div id="vm_fblike" style="width:'. $fb_width .'px;float:right;padding:0 5px;text-align:right;">';
									
						if($facebooklike==1){
							echo '<iframe src="http://www.facebook.com/plugins/like.php?locale='.$fb_lang.'&amp;href='.$encodedurl.'&amp;layout=box_count&amp;show_faces=false&amp;width='.$fb_width.'&amp;action='.$fb_action.'&amp;font=arial&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none;overflow:hidden; width:'.$fb_width.'px;"></iframe>';
						}
						elseif($facebooklike==2){
							if($appid!=""){
								$doc->addScript('http://connect.facebook.net/'.$fb_lang.'/all.js');
								echo '<div id="fb-root"></div>
												 <script>
													FB.init({ 
														appId:'.$appid.', cookie:true, 
														status:true, xfbml:true 
													 });
												 </script>';
							}
							echo '<div class="fb-'.$fb_action.'" data-send="true" data-layout="box_count" data-width="100" data-show-faces="false"></div>';
						}
						echo '</div>';
					}			
					if($linkedin){
									echo '<div style="width:60px;float:right;padding:0 5px;">';
									$doc->addScript('http://platform.linkedin.com/in.js');
									//$doc->addScriptDeclaration("{lang: '".$plusone_lang."'}");
									echo '<div id="vm_linkedin"> <script type="IN/Share" data-url="'.$href.'"  data-counter="top" 	></script></div>';
									echo '</div>';
					}		
					if($googleplus){
									$doc->addScript('http://apis.google.com/js/plusone.js');
									//$doc->addScriptDeclaration("{lang: '".$plusone_lang."'}");
									echo '<div id="vm_plusone" style="width:50px;float:right;padding:0 5px;" >
										<g:plusone size="tall" count="1" callback="" >
										</g:plusone>
										</div>';
					}	
					if($twitter){
									$doc->addScript('http://platform.twitter.com/widgets.js');
									echo '<div id="vm_tweet" style="width:60px;float:right;padding:0 5px;" >
									<a href="http://twitter.com/share" class="twitter-share-button" 
									data-count="vertical" data-counturl="'.$href.'" 
									data-url="'.$href.'" 
									data-lang="'.substr($langtag,0,2).'"
									>
									Tweet
									</a>
									</div>';			
					}
			echo '</div>';
		}
	
	
	
	
	
	
		echo '<div style="clear:both" ></div>';
	
	///////////////// start tab nav header
		echo '<ul id="vendorprofileTab" class="nav nav-tabs">';
       	echo '<li class="active"><a href="#vendorproducts" data-toggle="tab">'.JText::_( 'COM_VMVENDOR_PROFILE_PRODUCTS' ).'</a></li>';
	   	echo '<li><a href="#vendorprofile" data-toggle="tab">'.JText::_( 'COM_VMVENDOR_PROFILE_VENDORPROFILE' ).'</a></li>';
	  	if($enable_vendormap && ( $profileman ==1 or $profileman ==2) )
			 echo '<li><a href="#vendormap" data-toggle="tab">'.JText::_( 'COM_VMVENDOR_PROFILE_VENDORMAP' ).'</a></li>';
		if($enable_jcomments)
			echo '<li><a href="#vendorcomments" data-toggle="tab">'.JText::_( 'COM_VMVENDOR_PROFILE_COMMENTS' ).'</a></li>';	
		echo '</ul>';
	

		echo '<div id="myTabContent" class="tab-content">';
    
        
    	echo '<div class="tab-pane active" id="vendorproducts">';
		if($ismyprofile)
			echo '<div id="add-product"><a href="'.JRoute::_('index.php?option=com_vmvendor&view=addproduct&Itemid='.$addproduct_itemid).'" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus"></span> '.JText::_( 'COM_VMVENDOR_PROFILE_ADDAPRODUCT' ).'</a></div><div style="clear:both"></div>';			
		function applytaxes( $pricebefore, $catid , $is_vendor , $vendor_id , $userid){
			$is_shopper = 0;
			if(!$is_vendor);
				$is_shopper = 1;			
			$db = &JFactory::getDBO();
			$q ="SELECT vc.`virtuemart_calc_id` , vc.`calc_name` , vc.`calc_kind` , vc.`calc_value_mathop` , vc.`calc_value` , vc.`calc_currency` ,  vc.`ordering` ,
				vv.`virtuemart_vendor_id` 
				FROM `#__virtuemart_calcs` vc 
				LEFT JOIN `#__virtuemart_vendors` vv ON vv.`created_by`='".$userid."' 
				LEFT JOIN `#__virtuemart_calc_categories` vcc ON vcc.`virtuemart_calc_id` = vc.`virtuemart_calc_id`
				WHERE vc.`published`='1' 
				AND (vc.`shared` ='1' OR vc.`virtuemart_vendor_id` = '".$vendor_id."' )" ;
			if($is_shopper)
				$q .= " AND vc.`calc_shopper_published` = '1' ";
			elseif($is_vendor)
				$q .= " AND vc.`calc_vendor_published` ='1' ";
			$q .= "AND (vc.`publish_up`='0000-00-00 00:00:00' OR vc.`publish_up` <= NOW() ) ";
			$q .= "AND (vc.`publish_down`='0000-00-00 00:00:00' OR vc.`publish_down` >= NOW() ) 
			AND vcc.`virtuemart_category_id` ='".$catid."' 
				ORDER BY vc.`ordering` ASC";
			$db->setQuery($q);
			$taxes = $db->loadObjectList();
			$price_withtax = $pricebefore;
			if(count($taxes)>0){
				foreach($taxes as $tax){
					$calc_value_mathop = $tax->calc_value_mathop;
					$calc_value = $tax->calc_value;
					switch ($calc_value_mathop){
						case '+':
							$price_withtax = $price_withtax + $calc_value;
						break;
						case '-':
							$price_withtax = $price_withtax - $calc_value;
						break;
						case '+%':
							$price_withtax = $price_withtax + ( ( $price_withtax * $calc_value ) / 100 );
						break;
						case '-%':
							$price_withtax = $price_withtax - ( ( $price_withtax * $calc_value ) / 100 );
						break;
					}	
				}
			}
			return $price_withtax;	
		}
		echo '<div id="container" class="clearfix">';
		foreach($this->myproducts as $product){
				$product_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id.'&Itemid='.$vmitemid);
				$category_url = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$product->virtuemart_category_id.'&Itemid='.$vmitemid);
				$q ="SELECT vm.`file_url_thumb` , vm.file_url 
				FROM `#__virtuemart_medias` vm 
				LEFT JOIN `#__virtuemart_product_medias` vpm ON vpm.`virtuemart_media_id` = vm.`virtuemart_media_id` 
				WHERE vpm.`virtuemart_product_id`='".$product->virtuemart_product_id."' 
				AND vm.`file_mimetype` LIKE 'image/%' 		
				ORDER BY vm.`file_is_product_image` DESC , vpm.ordering ASC";
				$db->setQuery($q);
				$prod_images =  $db->loadRow();
				$thumburl = $prod_images[0];
				$large_url= $prod_images[1];  
				
				
				if (!$thumburl  && $large_url!='' ){  // required in case pictures are added via the backend
					$thumburl = str_replace('virtuemart/product/','virtuemart/product/resized/',$large_url);
					$thum_side_width			=	VmConfig::get( 'img_width' );
					$thum_side_height			=	VmConfig::get( 'img_height' );
					$extension_pos = strrpos($thumburl, "."); // find position of the last dot, so where the extension starts
					$thumburl = substr($thumburl, 0, $extension_pos) . '_'.$thum_side_width.'x'.$thum_side_height . substr($thumburl, $extension_pos);
				}
		
		
				if(!$thumburl)
					$thumburl = 'components/com_virtuemart/assets/images/vmgeneral/'.VmConfig::get('no_image_set');
			echo '<div class="vmvthumb " >';
			echo '<span title="'.ucfirst($product->product_s_desc).'" class="'.$tooltip_class.'"  >';
			echo '<div class="prodtitle">';
			echo '<a href="'.$product_url.'" >'.ucfirst($product->product_name).'</a>';
			echo '</div>';
			echo '<div class="prodpic">';
			echo '<a href="'.$product_url.'" ><img src="'.$juri.$thumburl.'"  alt="'.$product->product_name.'" /></a>';
			echo '<div class="prodcat"  title="'.JText::_( 'COM_VMVENDOR_PROFILE_VISITCAT' ).'" class="'.$tooltip_class.'" >';
			echo '<a href="'.$category_url.'">
			<span class="glyphicon glyphicon-folder-open"></span></a> '.$product->category_name.'';
			echo '</div>';
			if($enableprice){
				echo '<div class="prodprice">';
				$product_with_tax = applytaxes($product->product_price , $product->virtuemart_category_id , $ismyprofile , $vendor_id , $userid);
				$res = number_format((float)$product_with_tax,$currency_decimal_place,$currency_decimal_symbol,$currency_thousands);
				$search = array('{sign}', '{number}', '{symbol}');
				$replace = array('+', $res, $currency_symbol);
				$formattedRounded_price = str_replace ($search,$replace,$currency_positive_style);
				echo  '<a href="'.$product_url.'" >'.$formattedRounded_price.'</a>';
				echo '</div>';
			}
			echo '</div>';
			echo '</span>';
			if($ismyprofile ){
				echo '<div class="product-edit " style="text-align:center;">';
				echo '<form name="delete_product" id="delete_product'.$product->virtuemart_product_id.'" method="post" onsubmit="return confirm(\''.JText::_( 'COM_VMVENDOR_PROFILE_SUREDELETE' ).'\');">';
				echo '<div style="float:left" class="btn-group" >';
				echo '<a href="'.JRoute::_('index.php?option=com_vmvendor&view=editproduct&productid='.$product->virtuemart_product_id.'&Itemid='.JRequest::getVar('Itemid')).'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_EDITPRODUCT' ).'" class="btn btn-xs btn-default '.$tooltip_class.'">
				<span class="glyphicon glyphicon-edit"></span></a>';
				?>
				<input type="hidden" name="option" value="com_vmvendor" />
				<input type="hidden" name="controller" value="vendorprofile" />
				<input type="hidden" name="task" value="deleteproduct" />
				<?php
				echo '<input type="hidden" name="delete_productid" value="'.$product->virtuemart_product_id.'" />';
				echo '<input type="hidden" name="userid" value="'.$userid.'" />';
				echo '<input type="hidden" name="price" value="'.$product->product_price.'" />';
				echo '<button title="'.JText::_( 'COM_VMVENDOR_PROFILE_DELPRODUCT' ).'"  class="btn btn-xs btn-default '.$tooltip_class.'" ><span class="glyphicon glyphicon-trash"></span></button>';
				echo '</form>';
				echo '</div>';
				echo '</div>';
				if($enablestock){
					echo '<div id="product_in_stock"  title="'.JText::_( 'COM_VMVENDOR_PROFILE_INSTOCK' ).'" class="'.$tooltip_class.'" >';
						echo '<span class="glyphicon glyphicon-th"></span> ';
						echo $product->product_in_stock;
					echo '</div>';
				}
			}
			echo '</div>';
		}
		echo '</div>';
		if(	count($this->myproducts) >2 ){
			echo '<script src="'.$jquery_url.'"></script>
			<script type="text/javascript" src="'.$juri.'components/com_vmvendor/assets/js/jquery.masonry.min.js"></script>';
			echo "<script type=\"text/javascript\">
			$(function(){
    $('#container').imagesLoaded( function(){
     $('#container').masonry({
        itemSelector : '.vmvthumb'
      });
    });  
  });
</script>";
		}		
		echo '<div style="clear:both" ></div>';
		echo '<div class="pagination" >';
		echo $this->pagination->getResultsCounter();
		echo $this->pagination->getPagesLinks();
		echo $this->pagination->getPagesCounter();
		echo '</div>';
	echo '</div>';
	
		
	
	


	echo '<div class="tab-pane" id="vendorprofile">';
	
	echo $this->slider->startPane("slider-pane");
	
	echo $this->slider->startPanel(JText::_( 'COM_VMVENDOR_PROFILE_DESCRIPTION' ), "vendorprofile-details-description" );
	if($vendor_store_desc)
		echo $vendor_store_desc;
	else
		echo JText::_('COM_VMVENDOR_PROFILE_NOTFILLEDINYET');
	if($user->id>0 && $ismyprofile){
			echo '<br /><a  href="'.JRoute::_('index.php?option=com_vmvendor&amp;view=editprofile').'#desc" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_EDITPROFILE' ).'"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	echo $this->slider->endPanel();	
	
	echo $this->slider->startPanel(JText::_( 'COM_VMVENDOR_PROFILE_TERMSOFSERVICE' ), "vendorprofile-details-termsofservice" );
	if($vendor_terms_of_service)
		echo $vendor_terms_of_service;
	else
		echo JText::_('COM_VMVENDOR_PROFILE_NOTFILLEDINYET');
	if($user->id>0 && $ismyprofile){
			echo '<br /><a  href="'.JRoute::_('index.php?option=com_vmvendor&amp;view=editprofile').'#tos" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_EDITPROFILE' ).'" ><span class="glyphicon glyphicon-edit"></span></a>';
	}
	echo $this->slider->endPanel();	
	
	echo $this->slider->startPanel(JText::_( 'COM_VMVENDOR_PROFILE_LEGALINFO' ), "vendorprofile-details-legalinfo" );
	if($vendor_legal_info)
		echo $vendor_legal_info;
	else
		echo JText::_('COM_VMVENDOR_PROFILE_NOTFILLEDINYET');	
	if($user->id>0 && $ismyprofile){
			echo '<br /><a  href="'.JRoute::_('index.php?option=com_vmvendor&amp;view=editprofile').'#legal" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_PROFILE_EDITPROFILE' ).'"><span class="glyphicon glyphicon-edit"></span></a>';
	}
	echo $this->slider->endPanel();	
	
	echo $this->slider->endPane("slider-pane");
	echo '</div>';


if($enable_vendormap && ( $profileman ==1 or $profileman ==2) ){
		if($profileman ==1)
			$q ="SELECT `geolat` , `geolng` FROM `#__comprofiler` WHERE `geoLat`!='' AND `geoLng` !='' AND `geoLat`!='0' AND `geoLng` !='0' AND `user_id` ='".$userid."' ";
		elseif($profileman ==2)
			$q ="SELECT `latitude` , `longitude` FROM `#__community_users` WHERE `latitude`!='255' AND `longitude` !='255' AND `userid` ='".$userid."' ";
		$db->setQuery($q);
		$coords = $db->loadRow();
		$user_lat = $coords[0];
		$user_lng = $coords[1];
		echo '<div class="tab-pane" id="vendormap">';
		echo '<div style="padding: 5px 0 25px 0;">';
		if($user_lat !='' && $user_lng!='' && $user_lat !='0' && $user_lng!='0' && $user_lat !='255' && $user_lng!='255' ){
			
			echo '<div style="width:50%;float:left" >'.JText::_('COM_VMVENDOR_PROFILE_CLICLTOZOOM');
			echo '</div>';
			echo '<div style="width:25px;float:right"><a href="http://maps.google.com/?q='.$user_lat.','.$user_lng.'&t=v&z=6" target="_blank" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_('COM_VMVENDOR_PROFILE_VIEWINGMAP').'">
			<span class="glyphicon glyphicon-link" ></span></a></div>';
			echo '</div>';
			echo'<div id="kmap10838" name="kmap10838" style="cursor: pointer; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; margin-top: 0px; ">';
			echo '<a 
			onmouseover="document.map10838.src = \'http://maps.google.com/maps/api/staticmap?center='.$user_lat.','.$user_lng.'&amp;zoom=10&amp;size='.$map_width.'x'.$map_height.'&amp;markers=color:orange;|'.$user_lat.','.$user_lng.'&amp;sensor=false\';" 
			onmouseout="document.map10838.src = \'http://maps.google.com/maps/api/staticmap?center='.$user_lat.','.$user_lng.'&amp;zoom=5&amp;size='.$map_width.'x'.$map_height.'&amp;markers=color:orange;|'.$user_lat.','.$user_lng.'&amp;sensor=false\';" 
			onclick="document.map10838.src = \'http://maps.google.com/maps/api/staticmap?center='.$user_lat.','.$user_lng.'&amp;zoom=15&amp;size='.$map_width.'x'.$map_height.'&amp;markers=color:orange;|'.$user_lat.','.$user_lng.'&amp;sensor=false\';">';
			echo '<img src="http://maps.google.com/maps/api/staticmap?center='.$user_lat.','.$user_lng.'&amp;zoom=5&amp;size='.$map_width.'x'.$map_height.'&amp;markers=color:orange;|'.$user_lat.','.$user_lng.'&amp;sensor=false" name="map10838" alt="Map"  width="'.$map_width.'" height="'.$map_height.'" class="img-rounded">';
			echo '</a>';
			
		}
		else{
			echo JText::_('COM_VMVENDOR_PROFILE_NOLOCATIONYET');
			if($ismyprofile)
				echo '<div class="alert alert-warning">'.ucfirst($user_naming).' , '. JText::_('COM_VMVENDOR_PROFILE_SETYOURLOCATION').'</div>';	
			
		}
		echo '</div>';
		echo '</div>';
	}
			
	if($enable_jcomments){
			echo '<div class="tab-pane" id="vendorcomments">';
			$comments = JPATH_BASE . '/components/com_jcomments/jcomments.php';
			if (file_exists($comments)) {
				require_once($comments);
				echo JComments::showComments($userid, 'com_vmvendor', $vendor_store_name);
			}
			else
				echo '<div class="alert alert-danger">jComments component required! Download it free from <a href="http://www.joomlatune.com/jcomments.html" target="_blank">here</a></div>';
		echo '</div>';
		}




		echo '</div>';
	}
	else
		JError::raiseWarning( 100, JText::_('COM_VMVENDOR_PROFILE_MUSTLOGIN') );
		//echo JText::_('COM_VMVENDOR_PROFILE_MUSTLOGIN');
}
else
	JError::raiseWarning( 100, JText::_('COM_VMVENDOR_PROFILE_NOVENDORYET') );
	//echo JText::_('COM_VMVENDOR_PROFILE_NOVENDORYET');
}
else
		JError::raiseWarning( 100, JText::_('COM_VMVENDOR_JSPROFILE_NOTALLOWED') );	
?>