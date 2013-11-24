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

class adagencyViewadagencyAdvertisers extends JView {

	function display ($tpl =  null ) {
		$advertisers =& $this->get('listAdvertisers');
		$this->assignRef('advertisers', $advertisers);
		$pagination = & $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);

		parent::display($tpl);

	}
	
	function register($tpl = null ){
        $itemid = & $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $this->assign("itemid", $itemid);
		parent::display($tpl);
	}

	function editForm($tpl = null) { 
		$my = & JFactory::getUser();	
        //echo "<pre>";var_dump($my);die();
        require_once( JPATH_SITE.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'sajax.php' );
		$db =& JFactory::getDBO();
        $advertiser =& $this->get('Advertiser');
        //echo "<pre>";var_dump($advertiser);die();
		$isNew = ($advertiser->aid < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		//BUG registered users		
		jimport("joomla.database.table.user");
		$user = new JUser();
		if (!$isNew) $user->load($advertiser->user_id);
        $itemid = & $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        $this->assign("itemid_cpn", $itemid_cpn);

		$configs =& $this->get('Conf');	
		
		if(isset($configs->show)){
			$show=explode(";",$configs->show);
		} else {$show = NULL;}
		if(isset($configs->mandatory)){
			$mandatory=explode(";",$configs->mandatory);
		} else {$mandatory = NULL;}
		if(count($show)>=2){ unset($show[count($show)-1]);}
		if(count($mandatory)>=2){ unset($mandatory[count($mandatory)-1]);}
		
		$configs->show = $show;
		$configs->mandatory = $mandatory;
		
		$this->assign("conf", $configs);
		$this->assign("user", $user);
		$this->assign("advertiser", $advertiser);
		if(isset($_SESSION['ad_country'])) $advertiser->country = $_SESSION['ad_country'];
		$configs =& $this->_models['adagencyconfig']->getConfigs();
		$country_option = adagencyAdminHelper::get_country_options($advertiser, false, $configs);
		//echo "<pre>";var_dump($country_option);die();
		$lists['country_option'] = $country_option;
		
		$query = "SELECT country FROM #__ad_agency_states GROUP BY country ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();
		//echo "<pre>";var_dump($countries);die();

		$profile = new StdClass();
		$profile->country = $advertiser->country;
		$profile->state = $advertiser->state;
		if(isset($_SESSION['ad_state']) && $_SESSION['ad_state']!='') $advertiser->state = $_SESSION['ad_state'];
		$shipcountry_option = adagencyAdminHelper::get_country_options($advertiser, true, $configs);
		$lists['shipcountry_options'] = $shipcountry_option;

		$lists['customerlocation'] = adagencyAdminHelper::get_store_province($advertiser);

		$profile = new StdClass();
		$profile->country = $advertiser->shipcountry;
		$profile->state = $advertiser->state;
		$lists['customershippinglocation'] = adagencyAdminHelper::get_store_province($profile, true, $configs);
		
		$content = $this->_models['adagencyplugin']->getPluginOptions($advertiser->paywith);
		$lists['paywith'] = $content;
		$captch=$configs->captcha;
		$this->assign("is_captcha",$captch);
		$this->assign("lists", $lists);
        $this->assign("itemid", $itemid);
        
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
	
	function block( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= ($row->block==0) ? $imgY : $imgX;
		$task 	= ($row->block==0) ? 'block' : 'unblock';
		$alt 	= ($row->block==0) ? JText::_( 'Block' ) : JText::_( 'Unblock' );
		$action = ($row->block==0) ? JText::_( 'Unblock Item' ) : JText::_( 'Block item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	function overview ($tpl =  null ) {
		$packages = & $this->get('PublishedPacks');
		$packages = & $this->_models['adagencyadvertiser']->getZonesForPacks($packages);

		$getItemid=JRequest::getInt('Itemid');
		$configs =& $this->get('Conf');
		$advertiserid = & $this->get('AID');

        $itemid = new StdClass();
        $itemid->ads = & $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid->adv = & $this->getModel("adagencyConfig")->getItemid('adagencyadvertisers');
        $itemid->cmp = & $this->getModel("adagencyConfig")->getItemid('adagencycampaigns');
        $itemid->pkg = & $this->getModel("adagencyConfig")->getItemid('adagencypackage');
        $itemid->cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        
		$link1 = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0"';
        $linkAMP1 = 'index.php?option=com_adagency&amp;controller=adagencyAdvertisers&amp;task=edit&cid[]=0"';
		$link11 = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0&Itemid=' . $itemid->adv . '"';
		$link2 = 'index.php?option=com_adagency&controller=adagencyPackages"';
        $linkAMP2 = 'index.php?option=com_adagency&amp;controller=adagencyPackages"';
		$link22 = 'index.php?option=com_adagency&controller=adagencyPackages&Itemid=' . $itemid->pkg . '"';
		$link3 = 'index.php?option=com_adagency&controller=adagencyAds"';
        $linkAMP3 = 'index.php?option=com_adagency&amp;controller=adagencyAds"';
		$link33 = 'index.php?option=com_adagency&controller=adagencyAds&Itemid=' . $itemid->ads . '"';
		$link4 = 'index.php?option=com_adagency&controller=adagencyCampaigns"';
        $linkAMP4 = 'index.php?option=com_adagency&controller=adagencyCampaigns"';
		$link44 = 'index.php?option=com_adagency&controller=adagencyCampaigns&Itemid=' . $itemid->cmp . '"';
		$link5 = 'index.php?option=com_adagency&amp;controller=adagencyAdvertisers&amp;task=register"';
        $link55 = 'index.php?option=com_adagency&amp;controller=adagencyAdvertisers&amp;task=register&Itemid=' . $itemid->adv . '"';
        $link6 = 'index.php?option=com_adagency&amp;controller=adagencyAds&amp;task=addbanners"';
        $link66 = 'index.php?option=com_adagency&amp;controller=adagencyAds&amp;task=addbanners&Itemid=' . $itemid->ads . '"';
        $link7 = 'index.php?option=com_adagency&amp;controller=adagencyCampaigns&amp;task=edit&amp;cid=0"';
        $link77 = 'index.php?option=com_adagency&amp;controller=adagencyCampaigns&amp;task=edit&amp;cid=0&Itemid=' . $itemid->cmp . '"';
        
        $content = $configs->overviewcontent;
		$content = str_replace($link1, $link11, $content);
        $content = str_replace($linkAMP1, $link11, $content);
		$content = str_replace($link2, $link22, $content);
        $content = str_replace($linkAMP2, $link22, $content);
		$content = str_replace($link3, $link33, $content);
        $content = str_replace($linkAMP3, $link33, $content);
		$content = str_replace($link4, $link44, $content);
        $content = str_replace($linkAMP4, $link44, $content);
        $content = str_replace($link5, $link55, $content);
        $content = str_replace($link6, $link66, $content);
        $content = str_replace($link7, $link77, $content);
        
		$configs->overviewcontent = $content;			
		$showZoneInfo = & $this->get('ShowZInfo');
		
		$this->assignRef('showZoneInfo', $showZoneInfo);		
		$this->assign("advertiserid", $advertiserid);
		$this->assign("packages", $packages);
		$this->assign("configs", $configs);
		$this->assign("itemid", $itemid);
		parent::display($tpl);
	}	

}

?>