<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2013 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user 			= &JFactory::getUser();
$app 			= JFactory::getApplication();
$db 			= &JFactory::getDBO();
$juri 			= JURI::base();
$doc 			= &JFactory::getDocument();

if (!class_exists( 'VmConfig' ))
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	
$use_as_catalog 	=  VmConfig::get('use_as_catalog');

$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/dashboard.css');

$cparams 					=& JComponentHelper::getParams('com_vmvendor');
$naming 					= $cparams->getValue('naming', 'username');
$profileman 				= $cparams->getValue('profileman');
$vmitemid	 				= $cparams->getValue('vmitemid');
$profileitemid				= $cparams->getValue('profileitemid');
$date_display				= $cparams->getValue('date_display','Y.m.d');
$customercontactform		= $cparams->getValue('customercontactform');
$show_postalinfo 			= $cparams->getValue('show_postalinfo',1);
$show_worldmapstats			= $cparams->getValue('show_worldmapstats',1);
$allow_orderstatuschange	= $cparams->getValue('allow_orderstatuschange',1);
$manage_reviews 			= $cparams->getValue('manage_reviews',1);
$tax_mode					= $cparams->getValue('tax_mode',0);
$load_jquery			= $cparams->getValue('load_jquery', 1);
$jquery_url			= $cparams->getValue('jquery_url','https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
if($load_jquery)
	$doc->addScript($jquery_url);
$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/bootstrap.min.css');
$doc->addScript($juri.'components/com_vmvendor/assets/js/bootstrap.min.js');

$allowed = 1;
$profiletypes_mode		= $cparams->getValue('profiletypes_mode', 0);
$profiletypes_ids		= $cparams->getValue('profiletypes_ids');
if($profiletypes_mode>0 && $profiletypes_ids!='' && $profileman ==2)
	$allowed = VmvendorModelDashboard::getJSProfileallowed($profiletypes_ids);
	



if($allowed){

$currency_symbol 			= $this->main_currency[0];
$currency_positive_style	= $this->main_currency[1];
$currency_decimal_place 	= $this->main_currency[2];
$currency_decimal_symbol 	= $this->main_currency[3];
$currency_thousands 		= $this->main_currency[4];

$q = "SELECT `id` FROM `#__menu` WHERE `link`='index.php?option=com_vmvendor&view=vendorprofile' AND `type`='component' AND `published`='1' ";
$db->setQuery($q);
$vmvendorprofile_itemid = $db->loadResult();
		
		
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

jimport( 'joomla.utilities.date' );



echo '<h1>'.JText::_('COM_VMVENDOR_DASHBOARD_TITLE').'</h1>';
if($user->id>0)
{
	
	$vendor_profile_url = JRoute::_('index.php?option=com_vmvendor&amp;view=vendorprofile&amp;userid='.$user->id.'&amp;Itemid='.$vmvendorprofile_itemid);
	echo '<div class="vmvendor-toolbar btn-group" >
	<a href="'.$vendor_profile_url.'"  class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_VENDORPROFILE' ).'"><span class="glyphicon glyphicon-user"></span></a>
	<a href="'.$vendor_profile_url.'" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_MYPRODUCTS' ).'"  class="btn btn-xs btn-default '.$tooltip_class.'">
	<span class="glyphicon glyphicon-th-large"></span></a></div>';
	
$top_modules =& JModuleHelper::getModules('vmv-dashboard-top');
foreach ($top_modules as $top_module){	
	echo '<h3 class="module-title">'.$top_module->title.'</h3>';
	echo JModuleHelper::renderModule($top_module);
}
//////////////// tabs navigation header
echo '<div>';
echo '<ul id="dashboardTab" class="nav nav-tabs">';
if(!$use_as_catalog){
	$start_date = JRequest::getVar('start_date');
	echo '<li ';
	if($start_date=='')
		echo ' class="active" ';
	echo '><a href="#mysells" data-toggle="tab"><span class="glyphicon glyphicon-shopping-cart"></span> '.JText::_( 'COM_VMVENDOR_DASHBOARD_MYSALES' ).'</a></li>';
}
echo '<li><a href="#mypoints" data-toggle="tab"><span class="glyphicon glyphicon-star"></span> '.JText::_( 'COM_VMVENDOR_DASHBOARD_MYPOINTSTITLE' ).'</a></li>';
if(!$use_as_catalog)
	echo '<li';
	if($start_date!='')
		echo ' class="active" ';
	echo '><a href="#mystats" data-toggle="tab"><span class="glyphicon glyphicon-stats"></span> '.JText::_( 'COM_VMVENDOR_DASHBOARD_STATISTICS' ).'</a></li>';
if ($manage_reviews){
	$tr = '';
	$unpublished_count = 0;
	if(count($this->myreviews) >0){
		foreach($this->myreviews as $review){
			$tr .='<tr>';
			$tr .='<td>';
			if($review->published == 1){ // allow deletion
				$review_status= JText::_( 'COM_VMVENDOR_DASHBOARD_PUBLISHED');
				$review_status_img= 'published.png';
			}
			else{
				$review_status= JText::_( 'COM_VMVENDOR_DASHBOARD_UNPUBLISHED');
				$review_status_img= 'unpublished.png';
				$unpublished_count ++;
			}
			$reviewed_item_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$review->virtuemart_product_id.'&virtuemart_category_id='.$review->virtuemart_category_id.'&Itemid='.$vmitemid);
			$tr .='<img src="'.$juri.'components/com_vmvendor/assets/img/'.$review_status_img.'" title="'.$review_status.'" alt="'.$review_status.'" class="'.$tooltip_class.'" width="16" height="16" />';
			$tr .='</td>';
			$tr .='<td>';
			$tr .=$review->created_on;
			$tr .='</td>';
			$tr .='<td>';
			$tr .='<a href="'.$reviewed_item_url.'">'.$review->product_name.'</a>';
			$tr .='</td>';
			$tr .='<td>';
			$tr .=$review->comment;
			$tr .='</td>';
			$tr .='<td>';
			$tr .=$review->review_rating.'/5';
			$tr .='</td>';
			$tr .='<td>';
			$tr .= ucfirst($review->$naming);
			$tr .='</td>';
			$tr .='<td>';
			

			if(!$review->published){
				$tr .= '<script type="text/javascript">
					function confirm_reviewpublish(){
						var conf = confirm(\''.JText::_('COM_VMVENDOR_DASHBOARD_PUBLISHAREYOUSURE').'\');
						if (conf == true){
							it.submit();	
						}
						else
							return false;
					}
					</script>';
					$tr .='<form method="POST" name="publish_review'.$review->virtuemart_rating_review_id.'" onSubmit="return confirm_reviewpublish();">
				<input type="hidden" name="task" value="publishreview" />';
				$tr .='<input type="image" src="'.$juri.'components/com_vmvendor/assets/img/good.png" name="image" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_PUBLISH').'" alt="Publish" class="'.$tooltip_class.'" width="16" height="16" >';
				$tr .='<input name="review_id" type="hidden" value="'.$review->virtuemart_rating_review_id.'" />
				<input name="created_on" type="hidden" value="'.$review->created_on.'" />
				<input type="hidden" name="option" value="com_vmvendor" />
				<input type="hidden" name="controller" value="dashboard" />';
				$tr .='</form>';
				
			}
			$tr .= '<script type="text/javascript">
					function confirm_reviewdelete(){
						var conf = confirm(\''.JText::_('COM_VMVENDOR_DASHBOARD_DELETEAREYOUSURE').'\');
						if (conf == true){
							it.submit();	
						}
						else
							return false;
					}
					</script>';
			$tr .='<form method="POST" name="delete_review'.$review->virtuemart_rating_review_id.'" onSubmit="return confirm_reviewdelete();">
			<input type="hidden" name="task" value="deletereview" />';
			$tr .='<input type="image" src="'.$juri.'components/com_vmvendor/assets/img/del.png" name="image" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_DELETE').'" alt="Delete" class="'.$tooltip_class.'" width="16" height="16" >';
			$tr .='<input name="review_id" type="hidden" value="'.$review->virtuemart_rating_review_id.'" />
			<input name="created_on" type="hidden" value="'.$review->created_on.'" />
			<input type="hidden" name="option" value="com_vmvendor" />
			<input type="hidden" name="controller" value="dashboard" />';
			$tr .='</form>';
			$tr .='</td>';
			$tr .='</tr>';
			
		}
	
	}
	$panel_title = '<span class="glyphicon glyphicon-comment"></span> '.JText::_( 'COM_VMVENDOR_DASHBOARD_REVIEWS' );
	if($unpublished_count>0)
		$panel_title .=' <font style=" color:#FF6600;" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_REVIEWS_UNPUBLISHEDCOUNT' ).'" class="'.$tooltip_class.'">('.$unpublished_count.')</font>';
	echo '<li><a href="#productreviews" data-toggle="tab">'.$panel_title.'</a></li>';
}
if(!$use_as_catalog)
	echo '<li><a href="#mytaxes" data-toggle="tab"><span class="glyphicon glyphicon-barcode"></span> '.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXES' ).'</a></li>';
$modules =& JModuleHelper::getModules('vmv-dashboard-tab');
	$i = 1;
	foreach ($modules as $module){
		echo '<li><a href="#module'.$i.'" data-toggle="tab">'.$module->title.'</a></li>';
		$i++;	
	}
echo '</ul>';
//////////////// end navigationtab






echo '<div id="dashboardTabContent" class="tab-content">';
        
if(!$use_as_catalog){
	echo '<div class="tab-pane ';
	if($start_date=='')
		echo 'active';
	echo '" id="mysells"><br />';
	echo '<table class="table table-striped table-condensed table-hover">';
	echo '<thead><tr style="text-align:center;">';
	echo '<th  colspan="4">'.JText::_( 'COM_VMVENDOR_DASHBOARD_LATEST_ORDERS' ).'</th>';
	echo '<th  colspan="8">'.JText::_( 'COM_VMVENDOR_DASHBOARD_CUSTOMERS' ).'</th>';
	echo '</tr>';
	echo '<tr>';
	echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_ORDER' ).' / ('.JText::_( 'COM_VMVENDOR_DASHBOARD_DATE' ).')</th>';
	echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_ITEM' ).'</th>';
	echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_QTYXPRICE' ).'</th>';
	echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_NAME' ).'</th>';
	if($show_postalinfo){
		echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_COMPANY' ).'</th>';
		echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_ADDRESS' ).'</th>';
		echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_ZIPCITY' ).'<br />'.JText::_( 'COM_VMVENDOR_DASHBOARD_STATE' ).'</th>';
		echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_COUNTRY' ).'</th>';
	}
	if($allow_orderstatuschange){
		echo '<th>'.JText::_( 'COM_VMVENDOR_DASHBOARD_ORDERITEMSTATUS' ).'</th>';
	}
	echo '<th></th>';
	echo '</tr></thead>';
	
	foreach($this->mysales as $sale)
	{
		
		echo '<tbody ><tr>';
		////////////////////// Order 
		$date = new JDate($sale->created_on);
		$date = $date->toUnix();
		echo '<td >#'.$sale->order_number.' ('.JHTML::_('date', $date, JText::_($date_display)).')';
		
		if($sale->customer_note)
		{
			echo ' <div title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_CUSTNOTE' ).'::'.$sale->customer_note.'" class="'.$tooltip_class.'">
			<span class="glyphicon glyphicon-warning-sign"></span>
			</div>';
		}
		echo '</td>';
		
		$item_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$sale->virtuemart_product_id.'&virtuemart_category_id='.$sale->virtuemart_category_id.'&Itemid='.$vmitemid);
		echo '<td title="Product Sku::'.$sale->order_item_sku.'" class="'.$tooltip_class.'"><a href="'.$item_url.'">'.$sale->order_item_name.'</a></td>';
		echo '<td>'.$sale->product_quantity.'x';
		$res = number_format((float)$sale->product_item_price,$currency_decimal_place,$currency_decimal_symbol,$currency_thousands);
		$search = array('{sign}', '{number}', '{symbol}');
			$replace = array('+', $res, $currency_symbol);
			$formattedRounded_price = str_replace ($search,$replace,$currency_positive_style);
		
		echo $formattedRounded_price.'</td>';

		/////////////////////////////// Customer
		echo '<td>'.ucfirst($sale->title).' '.ucfirst($sale->first_name).' '.ucfirst($sale->middle_name).' '.ucfirst($sale->last_name).'</td>';
		if($show_postalinfo){
			echo '<td>'.$sale->company.'</td>';
			echo '<td>'.$sale->address_1.' '.$sale->address_2.'</td>';
			echo '<td>'.$sale->zip.'<br />'.$sale->city.'<br />'.$sale->state_name.'</td>';
			echo '<td>'.$sale->country_name.'</td>';
		}
		
		if($allow_orderstatuschange){
			echo '<td>';
			if($sale->order_status =='C'   ){
				$q = "SELECT `custom_param` FROM `#__viruemart_product_customfields` WHERE `custom_value`='st42_download' AND `virtuemart_product_id`='".$sale->virtuemart_product_id."' "; // check if item has a forsale file
				$db->setQuery($q);
				$hasfile = $db->loadresult();
				if(!$hasfile){
					echo '<script type="text/javascript">
					function confirm_orderststuschange(it){
						var conf = confirm(\''.JText::_('COM_VMVENDOR_DASHBOARD_CONFIRMSTATUSUPDATE1').'\');
						if (conf){
							it.form.submit();	
						}
						
					}
					</script>';
					echo '<form method="POST" name="statusform'.$sale->virtuemart_order_item_id.'" >';
					echo '<select name="neworderstatus" onchange="confirm_orderststuschange(this);">';
					
					echo '<option value="C" selected="selected">'.JText::_('COM_VMVENDOR_DASHBOARD_CONFIRMED').'</option>';
					echo '<option value="S" >'.JText::_('COM_VMVENDOR_DASHBOARD_SHIPPED').'</option>';
					echo '<option value="X" >'.JText::_('COM_VMVENDOR_DASHBOARD_CANCELED').'</option>';
					echo '</select>';
					//echo '<div title="'.JText::_('COM_VMVENDOR_DASHBOARD_NOTIFYCUSTOMER_TOOLTIP').'" ><input type="checkbox" name="notify_customer" value="notify_customer"> '.JText::_('COM_VMVENDOR_DASHBOARD_NOTIFYCUSTOMER').'</div>';
					echo ' <input type="hidden" name="orderitemid" value="'.$sale->virtuemart_order_item_id.'" />
							<input type="hidden" name="saleordernumber" value="'.$sale->order_number.'" />
							<input type="hidden" name="option" value="com_vmvendor" />
							<input type="hidden" name="controller" value="dashboard" />
							<input type="hidden" name="task" value="updateorderstatus" />';
					echo '</form>';
				}
				else
					echo JText::_('COM_VMVENDOR_DASHBOARD_CONFIRMED');	
			}
			elseif($sale->order_status =='S')
				echo JText::_('COM_VMVENDOR_DASHBOARD_SHIPPED');
			elseif($sale->order_status =='X')
				echo JText::_('COM_VMVENDOR_DASHBOARD_CANCELED');
			elseif($sale->order_status =='P')
				echo JText::_('COM_VMVENDOR_DASHBOARD_PENDING');
			elseif($sale->order_status =='R')
				echo JText::_('COM_VMVENDOR_DASHBOARD_REFUNDED');
			elseif($sale->order_status =='U')
				echo JText::_('COM_VMVENDOR_DASHBOARD_CONFBYSHOPPER');
			else
				echo $sale->order_status;		
			echo '</td>';	
		}
		
		echo '<td>';
		if($profileman==1)
			$profile_url =JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$sale->virtuemart_user_id.'&Itemid='.$profileitemid);
		elseif($profileman==2)
			$profile_url = JRoute::_('index.php?option=com_community&view=profile&userid='.$sale->virtuemart_user_id.'&Itemid='.$profileitemid);
		elseif($profileman==3){
			$db 			= &JFactory::getDBO();
			$q = "SELECT referreid FROM #__alpha_userpoints WHERE userid ='".$sale->virtuemart_user_id."' "	;
			$db->setQuery($q);
			$referreid = $db->loadResult();

			$profile_url = JRoute::_('index.php?option=com_alphauserpoints&view=account&userid='.$referreid.'&Itemid='.$profileitemid);
		}
			
		if($profileman>0)	
			echo '<div><a href="'.$profile_url.'"  title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_VISITPROFILE' ).'" class="btn btn-xs btn-default '.$tooltip_class.'">
			<span class="glyphicon glyphicon-user" ></span></a></div>';

		if($profileman==2 && $customercontactform == 2)
		{
			echo  '<a href="javascript:void(0)" onclick="javascript: joms.messaging.loadComposeWindow('.$sale->virtuemart_user_id.');" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_PMCUSTOMER' ).'" class="btn btn-xs btn-default '.$tooltip_class.'">';
			echo  '<span class="glyphicon glyphicon-envelope"></a>';
		}
		else
		{
			JHTML::_('behavior.modal'); 
			echo   '<a data-toggle="modal"  href="'.JRoute::_('index.php?option=com_vmvendor&view=mailcustomer&orderitem_id='.$sale->virtuemart_order_item_id.'&customer_userid='.$sale->virtuemart_user_id.'&format=raw').'"  	data-target="#emailModal"  	class="btn btn-xs btn-default '.$tooltip_class.'"  title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_EMAILCUSTOMER' ).'" >';
			echo  '<span class="glyphicon glyphicon-envelope">';
		//	echo   '<div title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_EMAILCUSTOMER' ).'" class=" btn btn-xs btn-default '.$tooltip_class.'"><span class="glyphicon glyphicon-envelope"></span></div>
		//	echo '<img src="'.$juri.'components/com_vmvendor/assets/img/pms.png" alt="ask" width="16" height="16" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_EMAILCUSTOMER' ).'" class="'.$tooltip_class.'" />';
			echo '</a>';
			echo '<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>';
		}
			
		if($sale->phone_1)
			echo '<div title="'.$sale->phone_1.'" class=" btn btn-xs btn-default '.$tooltip_class.'"><span class="glyphicon glyphicon-earphone"></span></div>';
		if($sale->phone_2)
			echo '<div title="'.$sale->phone_2.'" class=" btn btn-xs btn-default '.$tooltip_class.'"><span class="glyphicon glyphicon-earphone"></span></div>';	
			
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	
	
	echo '<div class="pagination" >';
	echo $this->pagination->getResultsCounter();
	echo $this->pagination->getPagesLinks();
	echo $this->pagination->getPagesCounter();
	echo '</div>';
	echo '</div>';
}








echo '<div class="tab-pane" id="mypoints">';
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';

		if ( file_exists($api_AUP)) {
			require_once ($api_AUP);
			$listActivity = AlphaUserPointsHelper::getListActivity('VMVendor', $user->id, $app->getCfg('list_limit') );
		}
		$ref = AlphaUserPointsHelper::getAnyUserReferreID($user->id);
		echo '<div id="points_div"><h3>'.JText::_( 'COM_VMVENDOR_DASHBOARD_POINTS' ).' '.AlphaUserPointsHelper::getCurrentTotalPoints($ref).'</h3></div>';
		if(count($listActivity) >0) {
			echo '<table class="table table-striped table-condensed table-hover"><thead>';
			echo  '<tr ><th width="15%">';
			echo  JText::_( 'COM_VMVENDOR_DASHBOARD_AUP_DATE' );
			echo '</th><th width="30%">';
			echo  JText::_( 'COM_VMVENDOR_DASHBOARD_AUP_ACTION' );
			echo '</th><th >';
			echo  JText::_( 'COM_VMVENDOR_DASHBOARD_AUP_AMOUNT' );
			echo '</th><th width="70%">';
			echo  JText::_( 'COM_VMVENDOR_DASHBOARD_AUP_DETAIL' );
			echo '</th></tr></thead>';
			$i=0;
			foreach ( $listActivity as $activity ) {
				echo '<tbody><tr><td>';
				echo  '<span style="color:#333;">'.JHTML::_('date', $activity->insert_date, JText::_($date_display)).'</span><br /><span style="color:#777;font-style:oblique;">'.JHTML::_('date', $activity->insert_date, JText::_('H:i:s')).'</span>';
					$color = $activity->points>0 ? "#009900" : ($activity->points<0 ? "#ff0000" : ($activity->points==0.00 ? "#777" : "#777"));
				echo '</td><td >';
				echo  JText::_( $activity->rule_name );
				echo '</td><td style="text-align: right; color:'. $color .';">';
				echo  $activity->points;
				echo '&nbsp;&nbsp;</td><td  style="color:#777;">';
				echo  $activity->datareference;
				echo '</td></tr></tbody>';
			}
			echo '</table>';
		} 
echo '</div>';




if(!$use_as_catalog){
	echo '<div class="tab-pane ';
	if($start_date!='')
		echo ' active ';
		echo '" id="mystats"><br />';
		$start_date = JRequest::getVar('start_date');
				if(!$start_date)
					$start_date= date('Y-m').'-01';
				$end_date = JRequest::getVar('end_date');
				if(!$end_date)
					$end_date= date('Y-m-d');
				$time_unit = JRequest::getVar('time_unit');
				if(!$time_unit)
					$time_unit = 'days';
				$subject = JRequest::getVar('subject');
				if(!$subject)
					$subject = 'revenue';
		
		echo '<div >';
		echo '<form method="POST" class="form-inline"><div class="form-group">';
		
		
		
		
		
		echo '<div class="form-group" style="padding:0 5px;">'.JText::_( 'COM_VMVENDOR_DASHBOARD_STATSSTARTDATE' ).' '.JHTML::_('calendar', $start_date, 'start_date', 'start_date',  '%Y-%m-%d', array('class'=>'inputbox form-control', 'size'=>'5',  'maxlength'=>'10')).'</div>'; 


		echo '<div class="form-group" style="padding:0 5px;">'.JText::_( 'COM_VMVENDOR_DASHBOARD_STATSENDDATE' ).' '. JHTML::_('calendar', $end_date, 'end_date', 'end_date',  '%Y-%m-%d', array('class'=>'inputbox form-control', 'size'=>'5',  'maxlength'=>'10')).'</div>'; 
		
		
		
		echo '<div class="form-group" style="padding:0 5px;" ><select id="time_unit" name="time_unit" class="form-control">';
		echo '<option value="days" ';
		if($time_unit == 'days')
			echo 'selected ';
		echo '>'.JText::_('COM_VMVENDOR_DASHBOARD_DAYS').'</option>';
		echo '<option value="months" ';
		if($time_unit == 'months')
			echo 'selected ';
		echo '>'.JText::_('COM_VMVENDOR_DASHBOARD_MONTHS').'</option>';
		echo '<option value="years" ';
		if($time_unit == 'years')
			echo 'selected ';
		echo '>'.JText::_('COM_VMVENDOR_DASHBOARD_YEARS').'</option>';
		echo '</select></div>';


		echo '<div class="form-group" style="padding:0 5px;"><select id="subject" name="subject" class="form-control">';
		echo '<option value="revenue" ';
		if($subject == 'revenue')
			echo 'selected ';
		echo '>'.JText::_('COM_VMVENDOR_DASHBOARD_REVENUE').'</option>';
		echo '<option value="orders" ';
		if($subject == 'orders')
			echo 'selected ';
		echo '>'.JText::_('COM_VMVENDOR_DASHBOARD_ORDERS').'</option>';
		echo '</select></div>';

		echo '<div class="form-group" style="padding:0 5px;"><input type="submit" value="' . JText::_( 'COM_VMVENDOR_DASHBOARD_DISPLAY' ) . '" class="btn btn-primary" /></div>';
		echo '</form></div>';
				
		if($time_unit == 'days'){
			$data = '';
			$countrydata = '';
			$total_revenue = 0;
			$total_orders = 0;
			$total_medium = 0; // revenue / orders
			$date1 = $start_date; 
			$date2 = $end_date;
			//Extraction des donn�es
			list($annee1, $mois1, $jour1) = explode('-', $date1); 
			list($annee2, $mois2, $jour2) = explode('-', $date2);	 
			//Calcul des timestamp
			$timestamp1 = mktime(0,0,0,$mois1,$jour1,$annee1); 
			$timestamp2 = mktime(0,0,0,$mois2,$jour2,$annee2); 
			$daycount = abs($timestamp2 - $timestamp1)/86400; //Affichage du nombre de jour : 10.0416666667 au lieu de 10
			$countries = array();
			for( $i=0 ; $i <= $daycount ; $i++){
				$day_revenue = 0;
				$day_orders =  0;
				$day_timestp = $timestamp1 + ($i * 86400);
				$day_date = date('Y-m-d',$day_timestp);		
				$q = "SELECT voi.`product_quantity`, voi.`product_item_price` ,
				vo.`order_number` , vc.`country_name` 
					FROM `#__virtuemart_order_items` voi  
					LEFT JOIN `#__virtuemart_products` vp ON vp.`virtuemart_product_id` = voi.`virtuemart_product_id` 
					LEFT JOIN `#__virtuemart_vmusers` vv ON vv.`virtuemart_vendor_id` = vp.`virtuemart_vendor_id` 
					LEFT JOIN `#__virtuemart_orders` vo ON vo.`virtuemart_order_id` = voi.`virtuemart_order_id` 
					LEFT JOIN `#__virtuemart_userinfos` vu ON vu.`virtuemart_user_id` = vo.`virtuemart_user_id` 
					LEFT JOIN `#__users` u ON u.`id` = vo.`virtuemart_user_id` 
					LEFT JOIN `#__virtuemart_countries` vc ON vc.`virtuemart_country_id` = vu.`virtuemart_country_id` 
					LEFT JOIN `#__virtuemart_states` vs ON vs.`virtuemart_state_id` = vu.`virtuemart_state_id` 
					WHERE vv.`virtuemart_user_id` = '".$user->id."' AND (vo.`order_status`='C' OR vo.`order_status`='S') 
					AND SUBSTR(voi.`created_on` , 1, 10) = '".$day_date."'   ";
				$db->setQuery($q);
				$day_orderitems = $db->loadObjectList();
				$day_orders = count($day_orderitems);
				foreach($day_orderitems as $day_orderitem){
					array_push($countries , $day_orderitem->country_name);
					$day_revenue = $day_revenue +  ($day_orderitem->product_quantity * $day_orderitem->product_item_price);			
					
				}
				
				$total_revenue = $total_revenue + $day_revenue;
				$total_orders = $total_orders + $day_orders;
				if($subject=='revenue' && $day_orders>0)
						$data .= '[\''.$day_date.'\','.$day_revenue.','. $day_revenue / $day_orders.'],';
				
						
					if($subject=='orders')
						$data .= '[\''.$day_date.'\','.$day_orders.'],';
						
						
				if(!count($day_orderitems) && $subject=='revenue')
					$data .= '[\''.$day_date.'\',0,0],';
				elseif(!count($day_orderitems) && $subject=='orders')
					$data .= '[\''.$day_date.'\',0],';
			}
			$countries =  array_count_values($countries) ;	
			$country_list ='';
			while (list($key, $value) = each($countries)) {
				$country_list .= '[\''.$key.'\', '.$value.'],';
			}
		}
		
		elseif($time_unit == 'months'){
			$data = '';
			$countrydata = '';
			$total_revenue = 0;
			$total_orders = 0;
			$total_medium = 0; // revenue / orders
			$date1 = $start_date; 
			$date2 = $end_date;
			//Extraction des donn�es
			list($annee1, $mois1, $jour1) = explode('-', $date1);  // 2004 - 12 
			list($annee2, $mois2, $jour2) = explode('-', $date2);	//2008 - 7
			$d1 = strtotime($date1);
			$d2 = strtotime($date2);
			$min_date = min($d1, $d2);
			$max_date = max($d1, $d2);
			$monthcount = 0;
			
			while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
				$monthcount++;
			}
		
			//Calcul des timestamp
			//echo $monthcount = ( ($annee2 - $annee1) * 12 ) +12 - ( 13 - $mois1) + $mois2;
			//$timestamp1 = mktime(0,0,0,$mois1,'1',$annee1); 
			//$timestamp2 = mktime(0,0,0,$mois2,$jour2,$annee2); 
			//$monthcount = abs($timestamp2 - $timestamp1)/2678400; //Affichage du nombre de jour : 10.0416666667 au lieu de 10  30 days months
			$countries = array();
			$y = $annee1;
			$m = $mois1;
			for( $i=0 ; $i <= $monthcount ; $i++){
				
				$month_revenue = 0;
				$month_orders =  0;
			
				$month_date = $y.'-'.sprintf("%02d", $m);		
				$q = "SELECT voi.`product_quantity`, voi.`product_item_price` ,
				vo.`order_number` , vc.`country_name` 
					FROM `#__virtuemart_order_items` voi  
					LEFT JOIN `#__virtuemart_products` vp ON vp.`virtuemart_product_id` = voi.`virtuemart_product_id` 
					LEFT JOIN `#__virtuemart_vmusers` vv ON vv.`virtuemart_vendor_id` = vp.`virtuemart_vendor_id` 
					LEFT JOIN `#__virtuemart_orders` vo ON vo.`virtuemart_order_id` = voi.`virtuemart_order_id` 
					LEFT JOIN `#__virtuemart_userinfos` vu ON vu.`virtuemart_user_id` = vo.`virtuemart_user_id` 
					LEFT JOIN `#__users` u ON u.`id` = vo.`virtuemart_user_id` 
					LEFT JOIN `#__virtuemart_countries` vc ON vc.`virtuemart_country_id` = vu.`virtuemart_country_id` 
					LEFT JOIN `#__virtuemart_states` vs ON vs.`virtuemart_state_id` = vu.`virtuemart_state_id` 
					WHERE vv.`virtuemart_user_id` = '".$user->id."' AND (vo.`order_status`='C' OR vo.`order_status`='S') 
					AND SUBSTR(voi.`created_on` , 1, 7) = '".$month_date."'   ";
				$db->setQuery($q);
				$month_orderitems = $db->loadObjectList();
				$month_orders = count($month_orderitems);
				foreach($month_orderitems as $month_orderitem){
					array_push($countries , $month_orderitem->country_name);
					$month_revenue = $month_revenue +  ($month_orderitem->product_quantity * $month_orderitem->product_item_price);			
					
				}
				
				$total_revenue = $total_revenue + $month_revenue;
				$total_orders = $total_orders + $month_orders;
				if($subject=='revenue' && $month_orders>0)
						$data .= '[\''.$month_date.'\','.$month_revenue.','. $month_revenue / $month_orders.'],';
				
						
					if($subject=='orders')
						$data .= '[\''.$month_date.'\','.$month_orders.'],';
						
						
				if(!count($month_orderitems) && $subject=='revenue')
					$data .= '[\''.$month_date.'\',0,0],';
				elseif(!count($month_orderitems) && $subject=='orders')
					$data .= '[\''.$month_date.'\',0],';
				$m++;
				if($m>12){
					$m=1;
					$y++;
				}
			}
			$countries =  array_count_values($countries) ;	
			$country_list ='';
			while (list($key, $value) = each($countries)) {
				$country_list .= '[\''.$key.'\', '.$value.'],';
			}
		}
		
		
		
		elseif($time_unit == 'years'){
			$data = '';
			$countrydata = '';
			$total_revenue = 0;
			$total_orders = 0;
			$total_medium = 0; // revenue / orders
			$date1 = $start_date; 
			$date2 = $end_date;
			//Extraction des donn�es
			list($annee1, $mois1, $jour1) = explode('-', $date1);  // 2004 - 12 
			list($annee2, $mois2, $jour2) = explode('-', $date2);	//2008 - 7
		
			$yearcount = $annee2 - $annee1 + 1;
		
		
			//Calcul des timestamp
			//echo $monthcount = ( ($annee2 - $annee1) * 12 ) +12 - ( 13 - $mois1) + $mois2;
			//$timestamp1 = mktime(0,0,0,$mois1,'1',$annee1); 
			//$timestamp2 = mktime(0,0,0,$mois2,$jour2,$annee2); 
			//$monthcount = abs($timestamp2 - $timestamp1)/2678400; //Affichage du nombre de jour : 10.0416666667 au lieu de 10  30 days months
			$countries = array();
			$y = $annee1;
		
			for( $i=1 ; $i <= $yearcount ; $i++){
				
				$year_revenue = 0;
				$year_orders =  0;
			
				$year_date = $y;		
				$q = "SELECT voi.`product_quantity`, voi.`product_item_price` ,
				vo.`order_number` , vc.`country_name` 
					FROM `#__virtuemart_order_items` voi  
					LEFT JOIN `#__virtuemart_products` vp ON vp.`virtuemart_product_id` = voi.`virtuemart_product_id` 
					LEFT JOIN `#__virtuemart_vmusers` vv ON vv.`virtuemart_vendor_id` = vp.`virtuemart_vendor_id` 
					LEFT JOIN `#__virtuemart_orders` vo ON vo.`virtuemart_order_id` = voi.`virtuemart_order_id` 
					LEFT JOIN `#__virtuemart_userinfos` vu ON vu.`virtuemart_user_id` = vo.`virtuemart_user_id` 
					LEFT JOIN `#__users` u ON u.`id` = vo.`virtuemart_user_id` 
					LEFT JOIN `#__virtuemart_countries` vc ON vc.`virtuemart_country_id` = vu.`virtuemart_country_id` 
					LEFT JOIN `#__virtuemart_states` vs ON vs.`virtuemart_state_id` = vu.`virtuemart_state_id` 
					WHERE vv.`virtuemart_user_id` = '".$user->id."' AND (vo.`order_status`='C' OR vo.`order_status`='S') 
					AND SUBSTR(voi.`created_on` , 1, 4) = '".$year_date."'   ";
				$db->setQuery($q);
				$year_orderitems = $db->loadObjectList();
				$year_orders = count($year_orderitems);
				foreach($year_orderitems as $year_orderitem){
					array_push($countries , $year_orderitem->country_name);
					$year_revenue = $year_revenue +  ($year_orderitem->product_quantity * $year_orderitem->product_item_price);			
					
				}
				
				$total_revenue = $total_revenue + $year_revenue;
				$total_orders = $total_orders + $year_orders;
				
				if($subject=='revenue' && $year_orders>0)
						$data .= '[\''.$year_date.'\','.$year_revenue.','. $year_revenue / $year_orders.'],';
				
						
					if($subject=='orders')
						$data .= '[\''.$year_date.'\','.$year_orders.'],';
						
						
				if(!count($year_orderitems) && $subject=='revenue')
					$data .= '[\''.$year_date.'\',0,0],';
				elseif(!count($year_orderitems) && $subject=='orders')
					$data .= '[\''.$year_date.'\',0],';
				$m++;
		
					$y++;
		
			}
			$countries =  array_count_values($countries) ;	
			$country_list ='';
			while (list($key, $value) = each($countries)) {
				$country_list .= '[\''.$key.'\', '.$value.'],';
			}
		}
		
	
		if(JRequest::getVar('start_date')){	
			$doc->addScript('https://www.google.com/jsapi');
			$chart_script = " google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
				   google.setOnLoadCallback(drawVisualization);
				   function drawVisualization() {
					var data = google.visualization.arrayToDataTable([ ";
					if($subject=='revenue')											  
					 $chart_script .= "['".ucfirst($time_unit)."', '".JText::_('COM_VMVENDOR_DASHBOARD_REVENUE')."','".JText::_('COM_VMVENDOR_DASHBOARD_AVERAGEPERORDER')."'],";
					 elseif($subject=='orders')											  
					 $chart_script .= "['".ucfirst($time_unit)."', '".JText::_('COM_VMVENDOR_DASHBOARD_ORDERS')."'],";
					  $chart_script .=$data."
					]);
					
					options = {
					  title: '".ucfirst($subject)."',
					
					  vAxis: {title: '".JText::_('COM_VMVENDOR_DASHBOARD_STATS_AMOUNT')."'}, ";
					  if($subject=='revenue')
					 $chart_script .= " hAxis: {title: '".JText::_( 'COM_VMVENDOR_DASHBOARD_STATS_TOTAL' )." ".$total_revenue." '},";
					 elseif($subject=='orders')	
					 $chart_script .= " hAxis: {title: '".JText::_( 'COM_VMVENDOR_DASHBOARD_STATS_TOTALORDERS' )." ".$total_orders." '},";
					 
					 $chart_script .= " seriesType: 'bars',
					  series: {1: {type: 'line'} }
					};
					var chart = new google.visualization.ComboChart(document.getElementById('columnchart_div'));
					chart.draw(data, options);
				  }";
			if($show_worldmapstats){
				  $chart_script .="google.load(\"visualization\", \"1\", {packages:[\"geochart\"]});
				   google.setOnLoadCallback(drawRegionsMap);
				  function drawRegionsMap() {
					var data2 = google.visualization.arrayToDataTable([
					  ['Country', '".JText::_('COM_VMVENDOR_DASHBOARD_STATS_SALES')."'],
					  ".$country_list." 
					]);
					var options2 = {
						colorAxis: {minValue: 0,  colors: ['red', 'yellow', 'green']}
						};
					var chart2 = new google.visualization.GeoChart(document.getElementById('mapchart_div'));
					chart2.draw(data2, options2);
				 }";
			}
			$doc->addScriptDeclaration($chart_script);
		echo '<h3>'.JText::_('COM_VMVENDOR_DASHBOARD_COMBOCHARTTITLE').'</h3>
		<div id="columnchart_div" ></div>';
		echo '<div style="clear:both" > </div>';
		if($show_worldmapstats){
			echo '<h3>'.JText::_('COM_VMVENDOR_DASHBOARD_GEOCHARTTITLE').'</h3>
			<div id="mapchart_div" ></div>';
			
			echo '<div style="clear:both" > </div>';
		}
	}
	echo '</div>';
	echo '</div>';
}




if ($manage_reviews){
	$tr = '';
	$unpublished_count = 0;
	if(count($this->myreviews) >0){
		foreach($this->myreviews as $review){
			$tr .='<tr>';
			$tr .='<td>';
			if($review->published == 1){ // allow deletion
				$review_status= JText::_( 'COM_VMVENDOR_DASHBOARD_PUBLISHED');
				$review_status_img= 'published.png';
			}
			else{
				$review_status= JText::_( 'COM_VMVENDOR_DASHBOARD_UNPUBLISHED');
				$review_status_img= 'unpublished.png';
				$unpublished_count ++;
			}
			$reviewed_item_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$review->virtuemart_product_id.'&virtuemart_category_id='.$review->virtuemart_category_id.'&Itemid='.$vmitemid);
			$tr .='<img src="'.$juri.'components/com_vmvendor/assets/img/'.$review_status_img.'" title="'.$review_status.'" alt="'.$review_status.'" class="'.$tooltip_class.'" width="16" height="16" />';
			$tr .='</td>';
			$tr .='<td>';
			$tr .=$review->created_on;
			$tr .='</td>';
			$tr .='<td>';
			$tr .='<a href="'.$reviewed_item_url.'">'.$review->product_name.'</a>';
			$tr .='</td>';
			$tr .='<td>';
			$tr .=$review->comment;
			$tr .='</td>';
			$tr .='<td>';
			$tr .=$review->review_rating.'/5';
			$tr .='</td>';
			$tr .='<td>';
			$tr .= ucfirst($review->$naming);
			$tr .='</td>';
			$tr .='<td>';
			

			if(!$review->published){
				$tr .= '<script type="text/javascript">
					function confirm_reviewpublish(){
						var conf = confirm(\''.JText::_('COM_VMVENDOR_DASHBOARD_PUBLISHAREYOUSURE').'\');
						if (conf == true){
							it.submit();	
						}
						else
							return false;
					}
					</script>';
					$tr .='<form method="POST" name="publish_review'.$review->virtuemart_rating_review_id.'" onSubmit="return confirm_reviewpublish();">
				<input type="hidden" name="task" value="publishreview" />';
				$tr .='<input type="image" src="'.$juri.'components/com_vmvendor/assets/img/good.png" name="image" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_PUBLISH').'" alt="Publish" class="'.$tooltip_class.'" width="16" height="16" >';
				$tr .='<input name="review_id" type="hidden" value="'.$review->virtuemart_rating_review_id.'" />
				<input name="created_on" type="hidden" value="'.$review->created_on.'" />
				<input type="hidden" name="option" value="com_vmvendor" />
				<input type="hidden" name="controller" value="dashboard" />';
				$tr .='</form>';
				
			}
			
			
			
			$tr .= '<script type="text/javascript">
					function confirm_reviewdelete(){
						var conf = confirm(\''.JText::_('COM_VMVENDOR_DASHBOARD_DELETEAREYOUSURE').'\');
						if (conf == true){
							it.submit();	
						}
						else
							return false;
						
					}
					</script>';
			$tr .='<form method="POST" name="delete_review'.$review->virtuemart_rating_review_id.'" onSubmit="return confirm_reviewdelete();">
			<input type="hidden" name="task" value="deletereview" />';
			$tr .='<input type="image" src="'.$juri.'components/com_vmvendor/assets/img/del.png" name="image" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_DELETE').'" alt="Delete" class="'.$tooltip_class.'" width="16" height="16" >';

			
			
			$tr .='<input name="review_id" type="hidden" value="'.$review->virtuemart_rating_review_id.'" />
			<input name="created_on" type="hidden" value="'.$review->created_on.'" />
			<input type="hidden" name="option" value="com_vmvendor" />
			<input type="hidden" name="controller" value="dashboard" />';
			$tr .='</form>';
			$tr .='</td>';
			$tr .='</tr>';
			
		}
	
	}
	
	
	echo '<div class="tab-pane" id="productreviews"><br />';
	echo '<div>';
	if(count($this->myreviews)>0){
		echo '<table class="table table-striped table-condensed table-hover">';
		echo '<thead><tr>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_STATUS' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_DATE' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_PRODUCT' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_REVIEW' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_RATING' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_USER' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_ACTION' );
		echo '</th>';
		echo '</tr></thead>';
		
		echo '<tbody>'.$tr.'</tbody>';
		
		echo  '</table>';
	}
	else
		echo JText::_( 'COM_VMVENDOR_DASHBOARD_NOREVIEWSYET');
	echo '</div>';
	//echo $this->pane->endPanel();
	echo '</div>';
}






if(!$use_as_catalog){

	//echo 'mytaxes: '.count($this->mytaxes);
	if(count($this->mytaxes)>0)
	{
		//$panel_title = '<span class="glyphicon glyphicon-barcode"></span> '.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXES' );
		//echo $this->pane->startPanel($panel_title  , "dashboard-panel-taxes" );
		echo '<div class="tab-pane" id="mytaxes"><br />';
		
		if($tax_mode==1){
			echo '<div style="width=100%;text-align:right;padding-bottom:3px;" >';
			echo '<a href="'.JRoute::_('index.php?option=com_vmvendor&view=edittax&Itemid='.JRequest::getInt('Itemid')).'" class="btn btn-xs btn-default">';
			echo '<span class="glyphicon glyphicon-plus"></span> '.JText::_( 'COM_VMVENDOR_EDITTAX_FORM_NEWTAX' );
			echo '</a>';
			echo '</div>';
			
		}
		
		echo '<table class="table table-striped table-condensed table-hover">';
		echo '<thead><tr>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_TAX_NAME' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_TAX_VALUE' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXCAT' );
		echo '</th>';
		echo '<th >'.JText::_( 'COM_VMVENDOR_DASHBOARD_TAXTYPE' );
		echo '</th>';
		echo '</tr></thead><tbody>';
		
		foreach($this->mytaxes as $mytax){
			echo '<tr>';
			echo '<td>';
			echo $mytax->calc_name;
			if($mytax->calc_descr)
				echo ' <span class="glyphicon glyphicon-info-sign '.$tooltip_class.'" title="'.$mytax->calc_descr.'"></span>';
			echo '</td>';
			echo '<td>';
			echo $mytax->calc_value_mathop.' '.$mytax->calc_value;
			echo '</td>';
			echo '<td>';
			if (!class_exists( 'VmConfig' ))
				require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			VmConfig::loadConfig();
			
			$q ="SELECT COUNT(*) FROM `#__virtuemart_categories` WHERE `published`='1' ";
			$db->setQuery($q);
			$categories_total_count = $db->loadResult();
			
			$q ="SELECT vcl.`category_name` 
			FROM `#__virtuemart_categories_".VMLANG."` vcl 
			LEFT JOIN `#__virtuemart_calc_categories`  vcc ON vcc.`virtuemart_category_id` = vcl.`virtuemart_category_id` 
			LEFT JOIN `#__virtuemart_categories` vc ON vc.`virtuemart_category_id` = vcl.`virtuemart_category_id`
			WHERE vcc.`virtuemart_calc_id` = '".$mytax->virtuemart_calc_id."' 
			AND vc.`published`='1' ";
			$db->setQuery($q);
			$tax_categories = $db->loadObjectList();
			if(count($tax_categories)== $categories_total_count){
				echo JText::_( 'COM_VMVENDOR_DASHBOARD_TAX_CAT_ALL' );
			}
			else{
				$i = 0;
				foreach($tax_categories as $tax_category){
					$i++;
					echo $tax_category->category_name;
					if($i < count($tax_categories))
						echo' - ' ;
				}			
			}
			echo '</td>';
			echo '<td>';
			if(!$mytax->shared ){
				if($tax_mode==1){
					echo '<script type="text/javascript">
						function confirm_taxdelete(){
							var conf = confirm(\''.JText::_('COM_VMVENDOR_DASHBOARD_TAX_DELETE_AREYOUSURE').'\');
							if (conf == true){
								it.submit();	
							}
							else
								return false;
						}
						</script>';
	
					echo ' <form name="deletetax" id="deletetax" method="post" onSubmit="return confirm_taxdelete();"  >';
					echo '<div class="btn-group">';
					echo '<a href="'.JRoute::_('index.php?option=com_vmvendor&view=edittax&taxid='.$mytax->virtuemart_calc_id.'&Itemid='.Jrequest::getVar('Itemid')).'" class="btn btn-xs btn-default '.$tooltip_class.'" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_TAX_EDIT' ).'">
				<span class="glyphicon glyphicon-edit"></span></a> ';
				
				
					echo '<input type="hidden" name="option" value="com_vmvendor">
					<input type="hidden" name="controller" value="dashboard">
					<input type="hidden" name="task" value="deletetax">
					<input type="hidden" name="delete_taxid" value="'.$mytax->virtuemart_calc_id.'">
					<input type="hidden" name="userid" value="'.$user->id.'">
					<button type="submit" title="'.JText::_( 'COM_VMVENDOR_DASHBOARD_TAX_DELETE' ).'" class="btn btn-xs btn-default '.$tooltip_class.'">
					<span class="glyphicon glyphicon-trash"></span></button>
					</div>
					</form>';
				}
			}
			else
				echo JText::_( 'COM_VMVENDOR_DASHBOARD_TAX_SHARED' );
			echo '</td>';
			echo '</tr>';
			
			
		}
		
		echo  '</tbody></table>';
		echo '</div>';
		//echo $this->pane->endPanel();
	}
	
	
	// renders modules from the vmv-dashboard module position, each module own tab.
	//$modules =& JModuleHelper::getModules('vmv-dashboard-tab');
	$i = 1;
	foreach ($modules as $module){
		//echo $this->pane->startPanel( $module->title  , "vmv-dashboard-tab".$i );
		echo '<div class="tab-pane" id="module'.$i.'">';
		echo '<div>'.JModuleHelper::renderModule($module).'</div>';
		//echo $this->pane->endPanel();
		$i++;
		echo '</div>';
	}
}

//echo $this->pane->endPanel();
echo '</div>';
echo '</div>';
$bot_modules =& JModuleHelper::getModules('vmv-dashboard-bot');
foreach ($bot_modules as $bot_module){	
	echo '<h3 class="module-title">'.$bot_module->title.'</h3>';
	echo '<div>'.JModuleHelper::renderModule($bot_module).'</div>';
}

}
else
	echo '<div class="alert alert-warning">'.JText::_('COM_VMVENDOR_DASHBOARD_MUSTLOGIN').'</div>';
	
	
	
}
else
	echo '<div class="alert alert-warning">'.JText::_('COM_VMVENDOR_JSPROFILE_NOTALLOWED').'</div>';
?>