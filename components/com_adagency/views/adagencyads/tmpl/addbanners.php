<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/
	$document =& JFactory::getDocument();
	$item_id = $this->itemid;
	if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
	$item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }
	
	$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	
    $configs = $this->configs;
	$root = JURI::root();
	$url=JURI::base()."components/com_adagency/includes/css/ad_agency.css";
	$iconsaddr = JURI::base()."components/com_adagency/images/";
	$document->addStyleSheet($url);
	JHTML::_('behavior.mootools');
    $document->addStyleSheet('components/com_adagency/includes/css/adagency_template.css');
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
	require_once(JPATH_BASE . "/components/com_adagency/includes/js/ads.php");
	$type = $this->type;
?>
<div class="ijadagencyaddbanners" id="adagency_container">
<?php
	if(isset($this->wiz)) {
		echo "<div class='adag_pending'>".JText::_('ADAG_PENDING_ADS2')."</div>";
	}
	echo "<div class='adag_huge_title'>".JText::_('ADAG_ADDNEWAD');
    $cpanel_home = "<div class='cpanelimg'>";
    $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
    $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
    $cpanel_home .= "</div>";
    echo $cpanel_home . "</div>";
	echo "<div class='adag_top_bottom_spacer'>".JText::_('ADAG_SELADTYPE');
	if($type=='banner'){ echo "<a class='ad-padding-left' href='index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid."'><< ".JText::_('ADAG_BKTOADS')."</a>";}
	echo "</div>";
?>
	<div class="ad_banners_container">
	<?php
		if($type=='banner'){
			if($configs->allowstand) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title6' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0'.$Itemid)."'>"."<img src='".$iconsaddr."standard.gif' alt='standard' align='left' class='ad_imgs_bnr' />";
				echo JText::_('JAS_STANDART')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_STANDARD')."</div>";
				echo "</div><p class='clearad' />";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."standard.gif' alt='standard' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title6' href='".$link."'>".JText::_('JAS_STANDART')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_STANDARD')."</div>";
				echo 	'</div>';
				echo '</div>';
			}

			if($configs->allowswf) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title7' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0'.$Itemid)."'>"."<img src='".$iconsaddr."flash.gif' alt='flash' align='left' class='ad_imgs_bnr' />";
				echo JText::_('JAS_FLASH')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_FLASH')."</div>";
				echo "</div><p class='clearad' />";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."flash.gif' alt='flash' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title7' href='".$link."'>".JText::_('JAS_FLASH')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_FLASH')."</div>";
				echo 	'</div>';
				echo '</div>';
			}

			if($configs->allowadcode) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title7' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=0'.$Itemid)."'>"."<img src='".$iconsaddr."affiliate.gif' alt='affiliate' align='left' class='ad_imgs_bnr' />";
				echo JText::_('JAS_AFFILIATE')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_ADV')."</div>";
				echo "</div>";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."affiliate.gif' alt='affiliate' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title7' href='".$link."'>".JText::_('JAS_AFFILIATE')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_ADV')."</div>";
				echo 	'</div>';
				echo '</div>';
			}
		}
		else{
			if(($configs->allowstand)||($configs->allowswf)||($configs->allowadcode)) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title1' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners&type=banner'.$Itemid)."'>"."<img src='".$iconsaddr."banner.gif' alt='banner' align='left' class='ad_imgs_bnr' />";
				echo JText::_('ADAG_BANNERSFA')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_BANNER')."</div>";
				echo "</div>";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners&type=banner'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."banner.gif' alt='banner' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title1' href='".$link."'>".JText::_('ADAG_BANNERSFA')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_BANNER')."</div>";
				echo 	'</div>';
				echo '</div>';
			}

			if($configs->allowpopup) {
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."pop_up.gif' alt='popup' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title2' href='".$link."'>".JText::_('JAS_POPUP')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_POPUP')."</div>";
				echo 	'</div>';
				echo '</div>';
			}

			if($configs->allowtxtlink) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title3' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0'.$Itemid)."'>"."<img src='".$iconsaddr."text_ad.gif' alt='text ad' align='left' class='ad_imgs_bnr' />";
				echo JText::_('JAS_TEXT_LINK')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_TEXTAD')."</div>";
				echo "</div>";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."text_ad.gif' alt='text_ad' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title3' href='".$link."'>".JText::_('JAS_TEXT_LINK')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_TEXTAD')."</div>";
				echo 	'</div>';
				echo '</div>';
			}

			if($configs->allowtrans) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title4' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0'.$Itemid)."'>"."<img src='".$iconsaddr."transition.gif' alt='transition' align='left' class='ad_imgs_bnr' />";
				echo JText::_('JAS_TRANSITION')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_TRANSITION')."</div>";
				echo "</div>";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."transition.gif' alt='transition' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title4' href='".$link."'>".JText::_('JAS_TRANSITION')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_TRANSITION')."</div>";
				echo 	'</div>';
				echo '</div>';
			}

			if($configs->allowfloat) {
				/*echo "<div class='adag_bannersfa'>";
				echo "<a id='adag_bannersfa_title5' href='".JRoute::_('index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0'.$Itemid)."'>"."<img src='".$iconsaddr."floating.gif' alt='floating' align='left' class='ad_imgs_bnr' />";
				echo JText::_('JAS_FLOATING')."</a>";
				echo "<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_FLOATING')."</div>";
				echo "</div>";*/
				$link = JRoute::_('index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0'.$Itemid);
				echo "<div class='adag_bannersfa'>";
				echo 	'<div style="float:left; display: table-cell;">';
				echo 		"<a href='".$link."'><img src='".$iconsaddr."floating.gif' alt='floating' align='left' class='ad_imgs_bnr' /></a>";
				echo 	'</div>';
				echo 	'<div style="display: table-cell;">';
				echo 		"<a id='adag_bannersfa_title5' href='".$link."'>".JText::_('JAS_FLOATING')."</a>";
				echo 		"<div class='ad_desc_bnr'>".JText::_('ADAG_DSC_FLOATING')."</div>";
				echo 	'</div>';
				echo '</div>';
			}
		}
	?>
	</div>
</div>
<div style="clear:both;"></div>
<input style="" type="button" class="agency_cancel" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
