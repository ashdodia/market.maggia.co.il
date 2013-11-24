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

class adagencyViewadagencyAds extends JView {

	function display ($tpl =  null ) {
		$db =& JFactory::getDBO();
		$configs =& $this->_models['adagencyconfig']->getConfigs();
		$ads =& $this->get('listAds');
		$advertiser = &$this->get('CurrentAdvertiser');
		$pagination = & $this->get( 'Pagination' );
        $itemid = & $this->getModel("adagencyConfig")->getItemid('adagencyads');
		$itemid_camp = & $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

		$imgfolder = $configs->imgfolder;
		if(isset($advertiser->aid)) {
			$advertiser_id = $advertiser->aid;
		} else {
			$advertiser_id = NULL;
		}
		$wiz = JRequest::getVar('w');

        $this->assign("itemid_cpn", $itemid_cpn);
        $this->assignRef('itemid_camp', $itemid_camp);
        $this->assignRef('itemid', $itemid);
		$this->assignRef('wiz', $wiz);
		$this->assignRef('ads', $ads);
		$this->assignRef('pagination', $pagination);
		$this->assign("configs", $configs);
		$this->assign("imgfolder", $imgfolder);
		$this->assign("advertiser_id", $advertiser_id);
		parent::display($tpl);

	}

	function addbanners($tpl = null) {
		$type=JRequest::getVar('type','');
		$configs =& $this->_models['adagencyconfig']->getConfigs();
        $itemid = & $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
		$get = JRequest::get('get');
		if(isset($get['w'])) {$wiz = $get['w'];} else {$wiz = NULL;}

        $this->assignRef('itemid', $itemid);
        $this->assignRef('itemid_cpn', $itemid_cpn);
		$this->assignRef('wiz', $wiz);
		$this->assign("configs", $configs);
		$this->assign("type",$type);
		parent::display($tpl);
	}

	function approve( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= ($row->approved=='Y') ? $imgY : $imgX;
		$task 	= ($row->approved=='Y') ? 'unapprove' : 'approve';
		$alt 	= ($row->approved=='Y') ? JText::_( 'Approve' ) : JText::_( 'Unapprove' );
		$action = ($row->approved=='Y') ? JText::_( 'Unapprove Item' ) : JText::_( 'Approve item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}

	function editForm($tpl = null) {

		$db =& JFactory::getDBO();
		$license =& $this->_models['adagencylicense']->getLicense();
		$isNew = isset($license->id)&&($license->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('License').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel();

		} else {
			JToolBarHelper::cancel ('cancel', 'Close');

		}

		$this->assign("license", $license);

		$configs =& $this->_models['adagencyconfig']->getConfigs();
		$lists = array();

		$prods =& $this->_models['adagencyproduct']->getListProducts();
		$opts = array();
		$opts[] = JHTML::_('select.option',  "", JText::_("Select product") );
		foreach ( $prods as $prod ) {
			$opts[] = JHTML::_('select.option',  $prod->id, $prod->name );
		}
		$lists['productid'] = JHTML::_('select.genericlist',  $opts, 'productid', 'class="inputbox" size="1" ', 'value', 'text', isset($license->productid)?$license->productid:"");

		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("currency_options", array());
		$plugin_handler = new stdClass;
		$plugin_handler->encoding_plugins = array();
		$this->assign("plugin_handler", $plugin_handler);
		parent::display($tpl);


	}

	function click() {
		die('working on that');
	}

}

?>
