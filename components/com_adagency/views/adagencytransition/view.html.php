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

class adagencyViewadagencyTransition extends JView {
	
	function scandir_php4($dir)
	{
  		$files = array();
  		if ($handle = @opendir($dir))
  	{
    	while (false !== ($file = readdir($handle)))
      	array_push($files, $file);
    	closedir($handle);
  	}
  		return $files;
	}
	
	function editForm($tpl = null) { 
		global $mainframe;
		$data = JRequest::get('post');
		$db =& JFactory::getDBO();
		$ad =& $this->get('ad'); 
		$currentAdvertiser = $this->get('CurrentAdvertiser');
		$advertiser_id = (int)$currentAdvertiser->aid;

		$configs =& $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
		$itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
		//check for valid id of the banner
		if ($ad->id!=0) {
		if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
		if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Transition")) die('This banner id is not a Transition banner');
		}
		//check for valid id of the banner
		
		$isNew = ($ad->id < 1);
		if (!$isNew) {
			$ad->parameters = unserialize($ad->parameters);
			
		}	
				
		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );	
	    $advertisersloaded = adagencyModeladagencyTransition::gettransitionlistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
	    
	    // Padding  property
		$lists['padding'] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[padding]', 'class="inputbox"', @$ad->parameters['padding'] );
		// Border property
		$lists["border"] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[border]', 'class="inputbox"', @$ad->parameters['border']);
		
		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyTransition")->getCampsByAid($adv_id);
		} else { $camps=''; }	
		
		$these_campaigns = $this->getModel("adagencyTransition")->getSelectedCamps($advertiser_id, $ad->id);

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyTransition")->getChannel($ad->id); } else { $channel = NULL; }
		$czones = $this->getModel("adagencyTransition")->processCampZones($camps);
		$czones = $this->getModel("adagencyTransition")->createSelectBox($czones,$ad->id);
		$itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

        $camps = $this->getModel("adagencyTransition")->getCampsByAid($adv_id, 1);
        
        $this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assign("czones",$czones);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);		
		$this->assign("ad", $ad);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("advertiser_id", $advertiser_id);
		$this->assign("these_campaigns", $these_campaigns);

		parent::display($tpl);
	}
}
?>