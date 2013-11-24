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
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=" . $item_id; } else { $Itemid = NULL; }
if($this->itemid_camp != 0) { $Itemid2 = "&Itemid=" . $this->itemid_camp; } else { $Itemid2 = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$k = 0;
$n = count ($this->ads);
$configs = $this->configs;
$imgfolder = $this->imgfolder;
$advertiser_id = $this->advertiser_id;
JHTML::_('behavior.combobox');
// JHTML::_('behavior.mootools');
$root = JURI::root();
$document =& JFactory::getDocument();
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
require_once(JPATH_BASE . "/components/com_adagency/includes/js/ads.php");

?>
<div class="ijadagencyads" id="adagency_container">
<?php if(isset($_GET['p'])&&(intval($_GET['p'])==1)) { ?>
<div class="adag_pending"><?php echo JText::_('ADAG_PENDING_ADS');?></div>
<?php } elseif(isset($_GET['w'])&&(intval($_GET['w'])==1)) {
	echo "<div class='adag_pending'>".JText::_('ADAG_PENDING_ADS2')."</div>";
} ?>
<?php
	if(isset($advertiser_id)&&($advertiser_id > 0)) {
        $cpanel_home = "<div class='cpanelimg'>";
        $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
        $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
        $cpanel_home .= "</div>";
    } else { $cpanel_home = ""; }
?>
<div class="componentheading" id="ijadagencyads"><?php echo JText::_('VIEWAD_ADS'); ?><?php
	echo $cpanel_home;
?></div>
<div><?php echo JText::_('VIEWAD_INTRO_TEXT2').'<br />'; ?></div>
<script type="text/javascript">
function redirect(){
	document.location.href="<?php echo JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid;?>";
}
</script>
<p><div style="width: 100%; margin-bottom:12px;"><table align="center"><tr><td>
<input type="button" class="agency_continue" onClick="javascript:redirect()" value="<?php echo JText::_('ADAG_ADDBANNER');?>" />&nbsp;&nbsp;
<input type="button" class="agency_continue" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0' . $Itemid2); ?>'" value="<?php echo JText::_('AD_CR_CAMP');?>" /></td></tr></table>
</div>
<?php
//adagencyModeladagencyAds::displayBannerToolbar('com_adagency', 'view',$configs);
if ($n > 0) {
?>
<?php include(JPATH_BASE.DS."components".DS."com_adagency".DS."includes".DS."js".DS."ads_del_ad.php"); ?>
<form action="<?php JRoute::_('index.php?option=com_adagency&controller=adagencyAds'.$Itemid);?>" name="adminForm" id="adminForm" method="post">
<div id="editcell" >
<table class="content_table" width="100%">
<thead>

	<tr>
    	<td class="sectiontableheader" width="5%" align="center">
			<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" />
		</td>
		<td class="sectiontableheader" width="5%" align="center">
			<?php echo JText::_('ID');?>
		</td>
        <td class="sectiontableheader" width="30%">
			<?php echo JText::_('VIEWADTITLE');?>
		</td>
		<!--
        <td class="sectiontableheader">
			<?php //echo JText::_('VIEWADACTION'); ?>
		</td>
        -->
		<td class="sectiontableheader"><?php echo JText::_("VIEWADTYPE");?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWADSIZE');?>
		</td>
		<td class="sectiontableheader" width="5%">
			<?php echo JText::_('VIEWADCAMPAIGNS');?>
		</td>
		<td class="sectiontableheader" align="center">
			<?php echo JText::_('VIEWADSTATUS');?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWADPREVIEW');?>
		</td>
	</tr>
</thead>

<tbody>

<?php
	for ($i = 0; $i < $n; $i++):
		$ads =& $this->ads[$i];
		$ads->parameters = unserialize($ads->parameters);
		$approved = $ads->approved;
		if($approved == 'Y') { $color = "green"; $alt = JText::_("NEWADAPPROVED"); } elseif ($approved == 'P') { $color = "orange"; $alt = JText::_("ADAG_PENDING"); } else { $color  = 'red'; $alt = JText::_("ADAG_REJECTED");}
		if (!isset($ads->parameters['align'])) $ads->parameters['align']='';
		if (!isset($ads->parameters['valign'])) $ads->parameters['valign']='';
		if (!isset($ads->parameters['padding'])) $ads->parameters['padding']='';
		if (!isset($ads->parameters['border'])) $ads->parameters['border']='';
		if (!isset($ads->parameters['bg_color'])) $ads->parameters['bg_color']='';
		if (!isset($ads->parameters['border_color'])) $ads->parameters['border_color']='';
		if (!isset($ads->parameters['font_family'])) $ads->parameters['font_family']='';
		if (!isset($ads->parameters['font_size'])) $ads->parameters['font_size']='';
		if (!isset($ads->parameters['font_weight'])) $ads->parameters['font_weight']='';
		$id = $ads->id;
		$checked = JHTML::_('grid.id', $i, $id);
		$mediatype = $ads->media_type;
		switch ($mediatype) {
			case 'Advanced':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid=".$id.$Itemid);
			break;
			case 'Standard':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid=".$id.$Itemid);
			break;
			case 'Flash':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid=".$id.$Itemid);
			break;
			case 'Transition':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid=".$id.$Itemid);
			break;
			case 'Floating':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid=".$id.$Itemid);
			break;
			case 'Popup':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid=".$id.$Itemid);
			break;
			case 'TextLink':
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid=".$id.$Itemid);
			break;
		}

		$zonelink = JRoute::_("index.php?option=com_adagency&controller=adagencyZones&task=edit&cid=".$ads->zone.$Itemid);
		$customerlink = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=".$ads->advertiser_id2.$Itemid);

		if (!isset($ads->impressions)) $ads->impressions=0;
		if (!isset($ads->click)) $ads->click=0;
		if (!isset($ads->click_rate)) $ads->click_rate=0;
?>
	<tr class="row<?php echo $k;?>">
		<td align="center"><?php echo JHtml::_('grid.id', $i, $ads->id); ?></td>
	     <td align="center"><?php echo $id; ?></td>
         <td><a href="<?php echo $link;?>" ><?php echo $ads->title;?></a></td>
     	 <!-- <td><a href="javascript: Change(<?php //echo $ads->id; ?>)"><?php
		 //echo JText::_("ADAG_DELETE");?></a></td>
         -->
	     <td><?php 
			$adtype = strtoupper($ads->media_type);
			if ($adtype == 'STANDARD') { echo JText::_("ADAG_STANDARD"); }
			elseif ($adtype == 'TEXTLINK') { echo JText::_("ADAG_TEXTAD"); }
			else {
				echo JText::_('VIEWTREEADD'.strtoupper($ads->media_type)); 
			}
		?></td>
		 <td><?php if(!$ads->width || !$ads->height) { echo "-"; } elseif($ads->media_type != "TextLink") { echo "{$ads->width}x{$ads->height}"; } else { echo "-"; } ?></td>
	     <td align="center"><?php
		 	$cmp_count = adagencyModeladagencyAds::getCampaignCount($ads->id);
			if($cmp_count == 0) {
				echo "<span class='redMessage'>".$cmp_count."</span>";
			}
			else {
				echo $cmp_count;
			}

		 ?></td>
		 <td align="center"><font color="<?php echo $color; ?>"><?php echo $alt; ?></font></td>
		<td align="center"><?php

			if($ads->media_type == "Popup" && $ads->parameters['popup_type']=="webpage"){ ?>
			<a class="modal2" href='<?php echo $ads->parameters['page_url']; ?>'><?php echo JText::_('VIEWADPREVIEW');?></a>
			<?php }
			else
			if (($ads->media_type == "Transition") || $ads->media_type == "Floating" || $ads->media_type == "TextLink" || $ads->media_type == "Standard" || $ads->media_type == "Advanced" || $ads->media_type == "Flash" || $ads->media_type == "Popup") {?>
				<a class="modal2"  href='<?php echo JRoute::_("index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".$ads->id.$Itemid)?>'><?php echo JText::_('VIEWADPREVIEW');?></a>
		<?php  } ?>

				</td>

	</tr>


<?php
		$k = 1 - $k;
	endfor;
?>
<tr>
	<td style="padding-top:10px;" colspan="8"><input type="button" onclick="javascript:Change(0);" class="agency_cancel" value="<?php echo JText::_("ADAG_DELETE"); ?>" /></td>
</tr>

</tbody>


</table>

</div>

<input type="hidden" name="option" value="com_adagency" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="sid" value="" />
<input type="hidden" name="controller" value="adagencyAds" />
</form>

<?php } ?>
<br /><br />
<?php /* include(JPATH_BASE.DS."components".DS."com_adagency".DS."includes".DS."js".DS."ads_sqz_box.php"); */ ?>
</div>
