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

class adagencyViewadagencyPackage extends JView {

	function display ($tpl =  null ) {
		$configs =& $this->get('Conf');
	 	$advertiserid = $this->get('Aid');
		$orders =& $this->get('listPackages');
		$pagination = & $this->get( 'Pagination' );
		$showpreview = $configs->showpreview;
		$currencydef = $configs->currencydef;
		$orders = & $this->_models['adagencypackage']->getZonesForPacks($orders);
		$showZoneInfo = & $this->get('ShowZInfo');
        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencypackage');
        $itemid_adv = $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $itemid_cmp = $this->getModel("adagencyConfig")->getItemid('adagencycampaign');
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');


        $this->assignRef('itemid', $itemid);
        $this->assignRef('itemid_adv', $itemid_adv);
        $this->assignRef('itemid_cmp', $itemid_cmp);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assignRef('showZoneInfo', $showZoneInfo);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('packages', $orders);
		$this->assignRef('advertiserid', $advertiserid);
		$this->assignRef('showpreview',$showpreview);
		$this->assignRef('currencydef', $currencydef);
		parent::display($tpl);
	}

	function packs ($tpl =  null ) {
		$configs =& $this->get('Conf');
		$advertiserid = $this->get('Aid');
		$orders =& $this->get('listPackages');
		$pagination = & $this->get( 'Pagination' );
		$showpreview = $configs->showpreview;
		$currencydef = $configs->currencydef;

		$this->assignRef('showpreview',$showpreview);
		$this->assignRef('packages', $orders);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('advertiserid', $advertiserid);
		$this->assignRef('currencydef', $currencydef);
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		parent::display($tpl);
	}

	function preview ($tpl = null) {
		$database =& JFactory::getDBO();
		$default_template = & $this->get('Template');
		$get = JRequest::get('get');

		//$link = str_replace('administrator/', '', JURI::base()).'index.php?template='.$default_template;
        $link = 'index.php?template='.$default_template;
		if(isset($get['cid'])) {
			$ItemidLink = & $this->get('ItemidLink');
			if($ItemidLink != NULL) {
				//$link = str_replace('administrator/', '', JURI::base().$ItemidLink);
                $link = $ItemidLink;
			}
		}
        $link .= '&tp=1';
		$this->assign("get", $get);
		$this->assign("default_template", $default_template);
		$this->assign("link", $link);
		parent::display($tpl);
	}
}
?>
