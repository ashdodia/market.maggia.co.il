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
	$rezultat = $this->rezultat;
	$total_o = $this->total_o;
	$total_b = $this->total_b;
	$total_c = $this->total_c;
	$my	= & JFactory::getUser();
	$document = &JFactory::getDocument();
	$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/ad_agency.css');
    $document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/adagency_template.css');
	$itemid = $this->itemid;
	if($itemid->cpn != 0) { $Itemid = "&Itemid=" . $itemid->cpn; } else { $Itemid = NULL; }
    if($itemid->ads != 0) { $Itemid_ads = "&Itemid=" . $itemid->ads; } else { $Itemid_ads = NULL; }
    if($itemid->adv != 0) { $Itemid_adv = "&Itemid=" . $itemid->adv; } else { $Itemid_adv = NULL; }
    if($itemid->cmp != 0) { $Itemid_cmp = "&Itemid=" . $itemid->cmp; } else { $Itemid_cmp = NULL; }
    if($itemid->pkg != 0) { $Itemid_pkg = "&Itemid=" . $itemid->pkg; } else { $Itemid_pkg = NULL; }
    if($itemid->ord != 0) { $Itemid_ord = "&Itemid=" . $itemid->ord; } else { $Itemid_ord = NULL; }
    if($itemid->rep != 0) { $Itemid_rep = "&Itemid=" . $itemid->rep; } else { $Itemid_rep = NULL; }
?>
<div id="adagency_container" class="ijadagencycpanel">
	<div class="componentheading" id="ijadagencycpanel"><?php echo JText::_("AD_ADV_CPANEL"); ?></div>

	<div id="cpanel_icons">
		<div id="myprofile">
			<a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=<?php echo $my->id . $Itemid_adv;?>">
				<?php echo JText::_('AD_CP_PROFILE'); ?>
			</a>
		</div>
		<div id="myads">
			<a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>">
				<?php echo JText::_('AD_CP_ADS'); ?> (<?php echo $total_b; ?>)
			</a>
		</div>
		<div id="myorders">
			<a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>">
				<?php echo JText::_('AD_CP_ORDERS'); ?> (<?php echo $total_o; ?>)
			</a>
		</div>
		<div id="newbanner">
			<a href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners<?php echo $Itemid_ads; ?>">
				<?php echo JText::_('ADAG_ADD_NB'); ?>
			</a>
		</div>
		<div id="myreports">
			<a href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>">
				<?php echo JText::_('AD_CP_REPORTS'); ?>
			</a>
		</div>
		<div id="mycampaigns">
			<a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>">
				<?php echo JText::_('AD_CP_CMPS'); ?> (<?php echo $total_c; ?>)
			</a>
		</div>
		<div id="packages">
			<a href="index.php?option=com_adagency&controller=adagencyPackages<?php echo $Itemid_pkg; ?>">
				<?php echo JText::_('VIEWDSADMINPACKAGES'); ?>
			</a>
		</div>
		<div id="newcampaign">
			<a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0<?php echo $Itemid_cmp; ?>">
				<?php echo JText::_('ADAG_ADD_NC'); ?>
			</a>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
