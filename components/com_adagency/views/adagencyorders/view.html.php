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
jimport ("joomla.application.component.view");

class adagencyViewadagencyOrders extends JView {

	function display ($tpl =  null ) {
		$database =& JFactory::getDBO();
		$orders =& $this->get('listOrders');
		$pagination = & $this->get( 'Pagination' );		
        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencyorders');
        $itemid_ads = $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid_adv = $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $itemid_cmp = $this->getModel("adagencyConfig")->getItemid('adagencycampaign');
        $itemid_pkg = $this->getModel("adagencyConfig")->getItemid('adagencypackage');
        
		$sql="SELECT `currencydef` FROM #__ad_agency_settings LIMIT 1";
		$database->setQuery($sql);
		$currencydef=$database->loadResult();
		
		$database->setQuery("SELECT filename,display_name FROM #__ad_agency_plugins");
		$plugs=$database->loadRowList();
		

		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$database->setQuery($query);
		$params = $database->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "-1"; }
		
		$my = & JFactory::getUser();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."'";
		$database->setQuery($sql);
		$aid = $database->loadResult();
				
		if(isset($aid)&&($aid!=NULL)) {
			$sql = "SELECT fax FROM #__ad_agency_advertis WHERE aid = '".intval($aid)."'";
			$database->setQuery($sql);
			$dFormat = $database->loadResult();
			if(isset($dFormat)&&($dFormat!=NULL)){
				$params = $dFormat;
			} else {
				$params = "-1";
			}
		}
		
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
      
        $this->assignRef('itemid', $itemid);
        $this->assignRef('itemid_ads', $itemid_ads);
        $this->assignRef('itemid_adv', $itemid_adv);
        $this->assignRef('itemid_pkg', $itemid_pkg);
        $this->assignRef('itemid_cmp', $itemid_cmp);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assignRef('aid', $aid);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('orders', $orders);
		$this->assignRef('plugs', $plugs);
		$this->assignRef('currencydef', $currencydef);
		$this->assign("params", $params);
		parent::display($tpl);
	}

	function order($tpl = null) {
		global $mainframe;
		$db =& JFactory::getDBO();
 		$my =& JFactory::getUser();		
 		$tid = intval($_REQUEST['tid']);
		$order = adagencyModeladagencyOrder::getPackage($tid);
		if($order->cost == "0.00") {
			$mainframe->redirect("index.php?option=com_adagency&controller=adagencyOrders&task=orderfree&tid=".$tid);
		}
		$configs =& $this->_models['adagencyconfig']->getConfigs();
		$db->setQuery("SELECT paywith FROM #__ad_agency_advertis WHERE user_id=".$my->id);
		$paywith = $db->loadResult();
		$db->setQuery("SELECT display_name FROM #__ad_agency_plugins WHERE filename='".trim($paywith).".php' ");
		$paywith_display_name = $db->loadResult();
		$db->setQuery("SELECT count(*) FROM #__ad_agency_plugins WHERE published='1'");
		$allplug = $db->loadResult();
		$content = $this->_models['adagencyplugin']->getPluginOptions();
		$lists['payment_type'] = $content;
		$this->assign("order", $order);
		$this->assign("paywith", $paywith);
		$this->assign("paywith_display_name", $paywith_display_name);
		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("allplug", $allplug);
		parent::display($tpl);
	}
	
	function orderfree() {
		function curPageURL() {
			 $pageURL = 'http';
			 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			 $pageURL .= "://";
			 if ($_SERVER["SERVER_PORT"] != "80") {
			  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			 } else {
			  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			 }
			 return $pageURL;
		}
		
		$db =& JFactory::getDBO();
 		$my =& JFactory::getUser();		
		$tid = JRequest::getInt('tid');
		if($tid=='') { 
			$begin=strpos(curPageURL(),'adagencyOrders');
			$current_page = curPageURL();
			$tid=NULL;
			for($i=$begin;$i<=strlen($current_page);$i++){
				if(ctype_digit($current_page[$i])) { 
					$tid.=$current_page[$i];
				}
			} 
		}
		$package = adagencyModeladagencyOrder::getPackage($tid);
		$notes = $db->getEscaped($package->description);
	    $quantity = $package->quantity;
	    $type = $package->type;
	    $cost = $package->cost;
		$payment_type='Free';		
		$order_date=date('Y-m-d');	
		$db->setQuery("SELECT aid FROM #__ad_agency_advertis WHERE user_id=".intval($my->id));
		$aid=$db->loadResult();	
		$sql="SELECT oid FROM #__ad_agency_order WHERE aid='".intval($aid)."' AND payment_type='Free' AND tid='".intval($tid)."' LIMIT 1";
		$db->setQuery($sql);	
		$free_permission=$db->loadResult();
		$sql="SELECT hide_after FROM #__ad_agency_order_type WHERE tid='".intval($tid)."'";
		$db->setQuery($sql);
		$hide_after = $db->loadResult();
		if(isset($free_permission)&&($hide_after==1)){ 
			return false;
			global $mainframe;
			$msg = JText::_('ADAG_NPFREE');
			$mainframe->redirect("index.php?option=com_adagency",$msg);
		}
		
		if(isset($_SESSION['LCC']) && isset($_SESSION['LCC2'])) {
			$confirm_data = $_SESSION['LCC'].";".$_SESSION['LCC2'];
		} else {
			$confirm_data = NULL;
		}
		
		$insersql = "INSERT INTO #__ad_agency_order (`oid`,`tid`,`aid`,`type`,`quantity`,`cost`,`order_date`,`payment_type`,`card_number`,`expiration`,`card_name`,`notes`,`status`,`pack_id`) VALUES ('','".intval($tid)."','".intval($aid)."','".trim($type)."','".intval($quantity)."','".trim($cost)."','".trim($order_date)."','".addslashes(trim($payment_type))."','".trim($confirm_data)."','','','".addslashes(trim($notes))."','paid','0');";
		$db->setQuery($insersql);
		$db->query();		
		return true;
	}	
}
?>