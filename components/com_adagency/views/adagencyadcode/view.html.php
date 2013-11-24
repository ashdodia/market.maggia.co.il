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

class adagencyViewadagencyAdcode extends JView {

	function display ($tpl =  null ) {

		$orders =& $this->get('listPackages');
		$this->assignRef('packages', $orders);
		$pagination = & $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);

		parent::display($tpl);

	}

	function editForm($tpl = null) {
		global $mainframe;
		$data = JRequest::get('post');
		$db =& JFactory::getDBO();
		$ad =& $this->get('ad');
		$my = & JFactory::getUser();

		$configs =& $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);

        $itemid = & $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

		$advertiser = $this->getModel("adagencyAdcode")->getCurrentAdvertiser();
		$advertiser_id = (int)$advertiser->aid;

		$camps2 = NULL;

		//check for valid id of the banner
		if ($ad->id!=0) {
		if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
		if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Advanced")) die('This banner id is not an Ad Code banner');
		}
		//check for valid id of the banner
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		///===================select available campaigns============================
		$adv_id = $advertiser_id;

		if ($adv_id) {
			$camps = $this->getModel("adagencyAdcode")->getCampsByAid($adv_id);
		} else { $camps=''; }

		if(isset($camps)&&(is_array($camps)))
		foreach ($camps as &$camp){
			if( (!isset($camp->adparams['width'])) || (!isset($camp->adparams['height'])) || ($camp->adparams['width'] == '') || ($camp->adparams['height'] == '') ) {
				$camps2[] = $camp;
			} elseif((!isset($ad->width))||($ad->width != $camp->adparams['width'])||(!isset($camp->adparams['height']))||(!isset($ad->height))||($ad->height != $camp->adparams['height'])) {
				//@unset($camp);
				$camp = NULL;
			} else { $camps2[] = $camp; }
		}
		$camps = $camps2;

		$these_campaigns = $this->getModel("adagencyAdcode")->getSelectedCamps($advertiser_id, $ad->id);

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyAdcode")->getChannel($ad->id); } else { $channel = NULL; }

		$czones = $this->getModel("adagencyAdcode")->processCampZones($camps);
		$czones = $this->getModel("adagencyAdcode")->createSelectBox($czones,$ad->id);

        $camps = $this->getModel("adagencyAdcode")->getCampsByAid($adv_id, 1);
        if (!isset($czones) || empty($czones)) {
            $camps = array();
        }

        $this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
        $this->assign("czones", $czones);
        $this->assign("ad", $ad);
        $this->assign("channel",$channel);
        $this->assign("configs", $configs);
        $this->assign("data", $data);
        $this->assign("camps", $camps);
        $this->assign("advertiser_id", $advertiser_id);
        $this->assign("these_campaigns", $these_campaigns);

        parent::display($tpl);
    }

}

?>
