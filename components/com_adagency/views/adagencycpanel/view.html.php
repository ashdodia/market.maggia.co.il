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

class adagencyViewadagencycpanel extends JView {

	function display ($tpl =  null ) {
		$my = & JFactory::getUser();
		$database = & JFactory::getDBO();
		$sql111	= "SELECT aid FROM #__ad_agency_advertis WHERE user_id=".intval($my->id);
		$database->setQuery($sql111);
		if (!$database->query()) {
			echo $database->stderr();
			return;
		}
	    $rezult = $database->loadResult();
		$database->setQuery("SELECT count(*) FROM #__ad_agency_banners WHERE advertiser_id='".intval($rezult)."'");
		$total_b = $database->loadResult();
		
		//get the total number of campaigns
			
		$database->setQuery("SELECT count(*) FROM #__ad_agency_campaign WHERE aid='".intval($rezult)."'");
		$total_c = $database->loadResult();
		
		//get the total number of orders
			
		$database->setQuery("SELECT count(*) FROM #__ad_agency_order WHERE aid='".intval($rezult)."'");
		$total_o = $database->loadResult();
		
		//verificam daca are pachete nedisponibile
		if ($rezult!=NULL) {
			$sql23 = "SELECT * FROM #__ad_agency_order WHERE pack_id='0' AND aid='".intval($rezult)."' AND `status`='paid'";
			$database->setQuery($sql23);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
			$rezultat = $database->loadResult();
		}
		else $rezultat=NULL;
        
        $itemid = new StdClass();
        $itemid->ads = & $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid->adv = & $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $itemid->cmp = & $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');
        $itemid->pkg = & $this->getModel("adagencyConfig")->getItemid('adagencypackage');
        $itemid->cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        $itemid->rep = & $this->getModel("adagencyConfig")->getItemid('adagencyreports');
        $itemid->ord = & $this->getModel("adagencyConfig")->getItemid('adagencyorders');
		       
        $this->assignRef('itemid', $itemid);
		$this->assignRef('rezultat', $rezultat);
		$this->assignRef('total_o', $total_o);	
		$this->assignRef('total_b', $total_b);	
		$this->assignRef('total_c', $total_c);	
		
		parent::display($tpl);

	}

}

?>