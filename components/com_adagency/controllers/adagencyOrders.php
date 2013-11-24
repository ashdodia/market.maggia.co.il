 <?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
defined ('_JEXEC') or die ("Go away.");

jimport ('joomla.application.component.controller');

class adagencyControlleradagencyOrders extends adagencyController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();
        $this->registerTask ("default", "listOrders");
		$this->registerTask ("", "listOrders");
		$this->_model =& $this->getModel("adagencyOrder");
		$this->_plugins =& $this->getModel("adagencyPlugin");
	}

	function listOrders() {
		$view = $this->getView("adagencyOrders", "html");
		$view->setModel($this->_model, true);
        $model3 =& $this->getModel("adagencyConfig");
		///////////////////////////////
		$my	=& JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database = & JFactory :: getDBO();
		$item_id = $model3->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }	
		
		$link="index.php?option=com_adagency" . $Itemid;
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();
		// Check if user is logged in 
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!$adv_id[0]){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}
		
		$model2 =& $this->getModel("adagencyConfig");		
        $view->setModel($model2);
		$isWizzard = $model2->isWizzard();
			
		// check if the user is not approved as an advertiser
		if(($adv_id[1]=='N')||(($adv_id[1]=='P')&&(!$isWizzard))){
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		} 
		//////////////////////////////////////
		$view->display();
	}
	
	function confirm() {
		if (!$this->_model->confirm()) {
			$msg = JText::_('ORDREMERR');
		} else {
		 	$msg = JText::_('ORDREMSUCC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}

	function order () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyOrders", "html");
		$view->setLayout("order");
		$view->setModel($this->_model, true);

		$model1 =& $this->getModel("adagencyConfig");
		$view->setModel($model1); 
		$model =& $this->getModel("adagencyPlugin");
		$view->setModel($model);
		////////////////////////////////
		$my	=& JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database = & JFactory :: getDBO();
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }			
		$link="index.php?option=com_adagency".$Itemid;
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();
		// Check if user is logged in 
		// and if user is advertiser
		
		if($my->id == 0){
			$link = "index.php?option=com_adagency&controller=adagencyAdvertisers&task=register".$Itemid;
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		}
		elseif(!$adv_id[0]){
			$link = "index.php?option=com_adagency&controller=adagencyAdvertisers&task=register".$Itemid;
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}
		
		$isWizzard = $model1->isWizzard();
		$isBuy=0;
		$shown = explode(";",$model1->getConfigs()->show);
		foreach($shown as $element){
			//if($element == "wizzard") { $isWizzard = 1; }
			if($element == "nwtwo") {$isBuy =1;}
		}
			
		// check if the user is not approved as an advertiser
		if(($adv_id[1]=='N')||(($adv_id[1]=='P')&&(!$isWizzard)&&(!isBuy))){
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		} 
		//////////////////////////////////////
		$view->order();

	}
	
	
	function orderfree () { 
		$db = JFactory::getDBO();
		$my	=& JFactory::getUser();
		$campaign_id = $_SESSION['LCC'];
		$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, 'Purchased(new) - ".date("Y-m-d H:i:s")."', ' - ".intval($my->id)."', ';') WHERE `id` = '".intval($campaign_id)."' ";
		$db->setQuery($sql);
		$db->query();
		
		//////////////////////////
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database = & JFactory :: getDBO();
		$link="index.php";
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();
		// check if the user is not an advertiser 
		if(!$adv_id[0]){
			$this->setRedirect(JRoute::_($link), JText::_('AD_FAILEDACCESS'));
			$redirect="yes";
		}
		// check if the user is not approved as an advertiser)
		if($adv_id[1]=='N'){
			$this->setRedirect(JRoute::_($link), JText::_('AD_FAILEDAPPROVE'));
			$redirect="yes";
		}
		//////////////////////////
		JRequest::setVar ("hidemainmenu", 1);	
		$view = $this->getView("adagencyOrders", "html");
		$view->setLayout("order");		
		$view->orderfree();
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$msg = JText::_('ORDSAVED');
		
		$model =& $this->getModel("adagencyConfig");
		$isWizzard = $model->isWizzard();
		$configs = $model->getConfigs();
		if(isset($configs->show)&&($configs->show!='')) {
			$shown=explode(";",$configs->show);
			//echo "<pre>";var_dump($shown);die();
			if(in_array("aftercamp2",$shown)||in_array("aftercamp3",$shown)) {
				$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
			}
		}
				
		//echo "<pre>";var_dump($link);die();
		
		if(isset($_SESSION['LCC'])){
			$sql = "SELECT `approved` FROM #__ad_agency_campaign WHERE id = ".intval($_SESSION['LCC']);
			$database->setQuery($sql);
			$aa_cmp = $database->loadResult();
			$_SESSION['LCC'] = NULL;$_SESSION['LCC2']=NULL;
			unset($_SESSION['LCC']);unset($_SESSION['LCC2']);
		}
		
		if($aa_cmp == 'P') {
			$msg = JText::_("ADAG_CMPMSG1");
		} elseif($aa_cmp == 'Y') {
			$msg = JText::_("ADAG_CMPMSG2");
		}
		
		// check if the user is not approved as an advertiser
		if(($adv_id[1]=='P')&&($isWizzard)&&isset($aa_cmp)&&($aa_cmp=='P')){
			$link.="&p=1";
		} 
		//////////////////////////////////////
		if(!isset($redirect)) {$this->setRedirect($link, $msg);}
	}	
	
	function checkout(){
		
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_plugins";
		$db->setQuery($sql);
		$payment_plugins = $db->loadResult();
		$my =& JFactory::getUser();	
		
		$sql = "select `aid` from #__ad_agency_advertis WHERE `user_id` = '".intval($my->id)."' ";
		$db->setQuery($sql);
		$adv_id = $db->loadResult();		
			
		// v.1.5.3
		// changes to ADVERTISER 	
		//if ($my->usertype != "Advertiser") {
		
		if(!isset($adv_id) && (isset($adv_id) && $adv_id<1)){
			$this->setRedirect(JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0" ));
		}
		elseif(count($payment_plugins) < 1){
			echo('<script language="javascript"> '.
                	'alert ("'.(JText::_("ADAG_NO_PAYMT_PLUGINS")).'");' .
	                'self.history.go(-1);' .
        	        '</script>');
	        	return;
		}
		else{
			$getTid=JRequest::getInt('tid');
			$getPaymentType=JRequest::getVar('payment_type');
			$res = $this->_plugins->performCheckout($my->id);
			if($res == "https"){
				$orderid_from_request = JRequest::getVar("orderid", "0");
				$order_on_request = "";
				if($orderid_from_request != "0"){
					$order_on_request = "&orderid=".intval($orderid_from_request);
				}
				$site = JURI::root();
				$site = str_replace("http:", "https:", $site);
				$page_url = $site."index.php?option=com_adagency&controller=adagencyOrders&task=checkout&tid=".$getTid."&payment_type=".$getPaymentType.$order_on_request;
				$app = JFactory::getApplication("site");
				$app->redirect(JRoute::_($page_url));
				//$link = JRoute::_("index.php?option=com_adagency&controller=adagencyOrders&task=checkout&tid=".$getTid."&payment_type=".$getPaymentType, false, 1);
				//$link = str_replace('http:', 'https:', $link);
				//$this->setRedirect($link);
			}
			if($res < 0){
				$this->setRedirect(JRoute::_("index.php?option=com_adagency&controller=adagencyPackages"));
			}
		}
	}

	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('ORDSAVED');
		} else {
			$msg = JText::_('ORDFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('ORDREMERR');
		} else {
		 	$msg = JText::_('ORDREMSUCC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
		
	}
	
	function delete () {
		if (!$this->_model->remove()) {
			$msg = JText::_('ORDREMERR');
		} else {
		 	$msg = JText::_('ORDREMSUCC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
		
	}

	function cancel () {
	 	$msg = JText::_('ORDCANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('ORDBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('ORDUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('ORDPUB');
		} else {
                 	$msg = JText::_('ORDUNSPEC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}

};

?>