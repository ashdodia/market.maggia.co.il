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
// JHTML::_('behavior.tooltip');
// JHTML::_('behavior.modal');
    $order = $this->packages;
    $k = 0;
    $n = count ($order);
    $my =& JFactory::getUser();
    $configs = $this->configs;
    $overview = stripslashes($configs->overviewcontent);
    $currencydef = $configs->currencydef;
    $advertiserid = $this->advertiserid;
    //$item_id=$this->getItemid;
    $showZoneInfo = $this->showZoneInfo;
    $itemid = $this->itemid;
    if($itemid->adv != 0) { $Itemid_adv = "&Itemid=" . $itemid->adv; } else { $Itemid_adv = NULL; }
    $Itemid = $Itemid_adv;
    if($itemid->cpn != 0) { $Itemid_cpn = "&Itemid=" . $itemid->cpn; } else { $Itemid_cpn = NULL; }
    if($itemid->cmp != 0) { $Itemid_cmp = "&Itemid=" . $itemid->cmp; } else { $Itemid_cmp = NULL; }
    if($itemid->ads != 0) { $Itemid_ads = "&Itemid=" . $itemid->ads; } else { $Itemid_ads = NULL; }
    if($itemid->pkg != 0) { $Itemid_ads = "&Itemid=" . $itemid->pkg; } else { $Itemid_pkg = NULL; }
	$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	$document =& JFactory::getDocument();
	// $document->addScript(JURI::base()."/components/com_adagency/includes/js/modal.js");
	// $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/modal.css");
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adag_tip.css");
	// JHTML::_('behavior.mootools');
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "'.JURI::root()."index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn . '";
			});
        });
    ');
	if(isset($advertiserid)&&($advertiserid > 0)) {
        $cpanel_home = "<div class='cpanelimg'>";
        $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
        $cpanel_home .= "<a href='".$cpn_link."'>" . JText::_('ADAG_ADV_DASHB') . "</a>";
        $cpanel_home .= "</div>";
    } else { $cpanel_home = ""; }
?>
<div class="adagency_overview">
<div class="componentheading"><?php echo JText::_('VIEW_OVERVIEW_CONTENT'); ?><?php echo $cpanel_home; ?></div>

<?php
	$replace_package_with = '
<div class="componentheading">'.JText::_('VIEWPACKAGE_LIST_PACKAGES').'</div>
<table width="100%" class="content_table">
<thead>

	<tr>
	    <td class="sectiontableheader">
			'.JText::_('VIEWPACKAGEDESC').'
		</td>';
	if($showZoneInfo) {
		$replace_package_with .='<td class="sectiontableheader">
				'.JText::_('ADAG_ZONE_INFO').'
			</td>';
	}
	$replace_package_with .='<td class="sectiontableheader">
			'.JText::_('VIEWPACKAGEPRICE').'
		</td>
	</tr>
</thead>

<tbody>';

	for ($i = 0; $i < $n; $i++)
	{
		$order =& $this->packages[$i];
		$order->adparams = @unserialize($order->adparams);
		$id = $order->tid;
		$order->zones = str_replace("All Zones",JText::_('ADAG_ALL_ZONES'), $order->zones);
		if(isset($order->visibility)&&($order->visibility==0)) { continue; }
		$checked = JHTML::_('grid.id', $i, $id);

		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0" . $Itemid_cmp);

		$validity_in = '';
		if ($order->type=="fr") { if ($order->validity!="") {
					$validity = explode("|", $order->validity, 2);
					$validity[1] = ($validity[1]=="day") ? JText::_('VIEWPACKAGE_DAY') : (($validity[1]=="week") ? JText::_('VIEWPACKAGE_WEEK') : (($validity[1]=="month") ? JText::_('VIEWPACKAGE_MONTHS') : (($validity[1]=="year") ? JText::_('VIEWPACKAGE_YEARS') : ""))) ;
					$validity_in = $validity[0]."<br />".$validity[1]; } } else { $validity_in = $order->quantity; }
					if ($order->type == 'cpm')
						$validity_in = $validity_in.'<br />'.JText::_('AGENCYIMPRESSIONS');
					elseif($order->type == 'pc') $validity_in =  $validity_in.'<br />'.JText::_('AGENCYCLICKS');

		if ($order->type == 'cpm') {
            $tooltip_in ='&nbsp;
                <span class="adag_tip">
                    <img align="top"  src="components/com_adagency/images/tooltip.png" border="0" />
                    <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPM') . '</span>
                </span>';
		} elseif($order->type == 'pc') {
            $tooltip_in ='&nbsp;
                <span class="adag_tip">
                    <img align="top"  src="components/com_adagency/images/tooltip.png" border="0" />
                    <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPC') . '</span>
                </span>';
		} else {
            $tooltip_in ='&nbsp;
                <span class="adag_tip">
                    <img align="top"  src="components/com_adagency/images/tooltip.png" border="0" />
                    <span>' . JText::_('VIEWPACKAGE_TOOLTIP_FR') . '</span>
                </span>';
		}

	 	$button_value = JText::_('VIEWPACKAGE_BUY');
		if($order->cost > 0) {
			$price_in = $order->cost.'&nbsp;'.$currencydef;
		} else {
			$price_in = JText::_('VIEWPACKAGEFREE');
			$button_value = JText::_('ADAG_START');
		}

        if( $my->id && (isset($advertiserid) && $advertiserid > 0) )  {
            $link_in = $link ;
        } else if ($my->id &&  (!isset($advertiserid) || (isset($advertiserid) && $advertiserid == 0) ) )  {
            $link_in = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&user=reg&cid=' . $order->tid . $Itemid_adv;
        } else if (!$my->id)  {
            $link_in = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=register' . $Itemid_adv;
        }


	$desc_stuff='<p>
                	<strong>'.JText::_('ADAG_SLOTS').'</strong> '.$order->banners*$order->banners_cols.' ('.$order->banners . " " . JText::_('ADAG_ROWS').' , '.$order->banners_cols." ".JText::_('ADAG_COLS').') <br />
                    <strong>'.JText::_('ADAG_ROTATION').':</strong>';
	if($order->rotatebanners == '0') { $desc_stuff .= JText::_('ADAG_NO');} else { $desc_stuff .= JText::_('ADAG_YES'); }
	$desc_stuff .= ' <br />';
	if(isset($order->adparams['width']) && ($order->adparams['width'] != '') &&($order->adparams['height'] != '') && isset($order->adparams['height'])) {
		$desc_stuff .= '<strong>'.JText::_('VIEWADSIZE').':</strong> '.$order->adparams['width'].' x '.$order->adparams['height'].' '.JText::_('ADAG_WIDTH_X_HEIGHT').' <br />';
	} else {
		$desc_stuff .= "<strong>".JText::_("ADAG_ANYSIZE")."</strong><br />";
	}
	$desc_stuff .= '<strong>'.JText::_('VIEWADTYPE').':</strong>';
	$before = false;
	if(isset($order->adparams['standard']) || isset($order->adparams['affiliate']) || isset($order->adparams['flash'])){
		$desc_stuff .= JText::_("VIEW_CAMPAIGN_MEDIA_BANNERS").": ";
		if(isset($order->adparams['standard'])) { $desc_stuff .= JText::_('VIEWTREEADDSTANDARD'); $before = true; }
		if(isset($order->adparams['affiliate'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDADCODE');
			$before = true;
		}
		if(isset($order->adparams['flash'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDFLASH');
		}
	} elseif(isset($order->adparams['textad'])){
		$desc_stuff .= JText::_('VIEWTREEADDTEXTLINK');
	} elseif(isset($order->adparams['popup']) || isset($order->adparams['transition']) || isset($order->adparams['floating'])){
		if(isset($order->adparams['popup'])) { $desc_stuff .= JText::_('VIEWTREEADDPOPUP'); $before = true; }
		if(isset($order->adparams['transition'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDTRANSITION');
			$before = true;
		}
		if(isset($order->adparams['floating'])) {
			if($before) { $desc_stuff .= ", "; }
			$desc_stuff .= JText::_('VIEWTREEADDFLOATING');
		}
	}
	$desc_stuff .= '<br /></p>';

	$add_description = '';
	$add_description = $add_description.'<tr>
			<td>
				'.$desc_stuff.'<em>'.stripslashes($order->pack_description).'</em>
			</td>
			<td colspan="4">&nbsp;</td>
	</tr>';
	 $desc_stuff = NULL;

if($configs->showpreview==1){
	$zones_preview = '<a class="modal2" href="index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.$order->zoneid.'">'.$order->z_title;
} else {
	$zones_preview=$order->z_title;
}

	$output = NULL; $output2 = NULL;

	if(isset($order->location)&&(is_array($order->location))) {
		foreach($order->location as $element){
			$element->adparams = @unserialize($element->adparams);
			if($element->rotatebanners == 1) { $element->rotatebanners = JText::_("ADAG_YES"); } else { $element->rotatebanners = JText::_("ADAG_NO"); }
			if($configs->showpreview==1) { $sz_before = "<a class=\"modal2\" href=\"".JRoute::_('index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.$element->id)."\">"; $sz_after = "</a>"; } else {
				$sz_before = NULL; $sz_after = NULL;
			}

			$output2 .= "<div class='azone'>".JText::_('NEWADZONE').": ".$sz_before.$element->title.$sz_after."<br /><br />";
			$output2 .= JText::_("ADAG_ROTATION").": ".$element->rotatebanners."<br />";
			if(isset($element->adparams['width'])&&isset($element->adparams['height'])&&($element->adparams['width'] != "")&&($element->adparams['height'] != "")) {
				$output2.= JText::_("VIEWADSIZE").": ".$element->adparams['width']." x ".$element->adparams['height']." px<br />";
			} else { $output2 .= JText::_("VIEWADSIZE").": ".JText::_('ADAG_ANYSIZE')."<br />"; }
			$output2 .= JText::_('ADAG_SLOTS').": ".$element->rows*$element->cols." (".$element->rows . " " . JText::_("ADAG_ROWS").", ".$element->cols . " " . JText::_("ADAG_COLS").")<br />";
			$output2 .= JText::_('VIEWADTYPE').": ";
			$before = false;
               	if(isset($element->adparams['standard']) || isset($element->adparams['affiliate']) || isset($element->adparams['flash'])){
				$output2 .= JText::_("VIEW_CAMPAIGN_MEDIA_BANNERS").": ";
				if(isset($element->adparams['standard'])) { $output2 .= JText::_('VIEWTREEADDSTANDARD'); $before = true; }
				if(isset($element->adparams['affiliate'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDADCODE');
					$before = true;
				}
				if(isset($element->adparams['flash'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDFLASH');
				}
			} elseif(isset($element->adparams['textad'])){
				$output2 .= JText::_('VIEWTREEADDTEXTLINK');
			} elseif(isset($element->adparams['popup']) || isset($element->adparams['transition']) || isset($element->adparams['floating'])){
				if(isset($element->adparams['popup'])) { $output2 .= JText::_('VIEWTREEADDPOPUP'); $before = true; }
				if(isset($element->adparams['transition'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDTRANSITION');
					$before = true;
				}
				if(isset($element->adparams['floating'])) {
					if($before) { $output2 .= ", "; }
					$output2 .= JText::_('VIEWTREEADDFLOATING');
				}
			}
			$output2 .= "</div>";
		}
	}


	$output = '<p><strong>'.JText::_("VIEWPACKAGETERMS").'</strong>:</p>';
	if ($order->type=="fr") { if ($order->validity!="") {
			$validity = explode("|", $order->validity, 2);
			$validity[1] = ($validity[1]=="day") ? JText::_('VIEWPACKAGE_DAY') : (($validity[1]=="week") ? JText::_('VIEWPACKAGE_WEEK') : (($validity[1]=="month") ? JText::_('VIEWPACKAGE_MONTHS') : (($validity[1]=="year") ? JText::_('VIEWPACKAGE_YEARS') : ""))) ;
			$output .= $validity[0]." ".$validity[1]; }
		  } else { $output .= $order->quantity; }
			if ($order->type == 'cpm') { $output .= ' '.JText::_('AGENCYIMPRESSIONS'); }
			elseif($order->type == 'pc') { $output .= ' '.JText::_('AGENCYCLICKS'); }

	$output .= '<p style="margin-top:10px;"><strong>'.JText::_('VIEWORDERSTYPE').'</strong></p>
	<span style="vertical-align:super;">'.JText::_('ADAG_PK_'.strtoupper($order->type)).'</span><span>';

	if ($order->type == 'cpm') {
        $output .= '<span class="adag_tip">
                        <img src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPM') . '</span>
                    </span>';
	} elseif($order->type == 'pc') {
        $output .= '<span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPC') . '</span>
                    </span>';
	} else {
        $output .= '<span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_FR') . '</span>
                    </span>';
	}
	$output .= '</span>';

$replace_package_with = $replace_package_with.'
	<tr class="row'.$k.'">
	     	<td valign="top"><strong><div style="font-size:120%;">'.$order->description.'</div></strong>
			<p style="margin-top: 10px;">'.$order->pack_description.'</p>
			'.$output.'
			</td>';


if($showZoneInfo) {
	$replace_package_with .= '<td valign="top">'.$output2.'</td>';
}
$replace_package_with .= '<td valign="top">'.$price_in.'
				<p class="start_buy">
		     		<form action="'.JRoute::_($link_in).'" method="post" >
					<input type="hidden" name="pid" value="'.$order->tid.'" />
	    	 		<input class="agency_continue" type="submit" value="'.$button_value.'"/>
					</form>
				</p>
			</td>
	</tr>
	<tr style="height:6px; ">
		<td colspan="5" style=""></td>
	</tr>';

	$k = 1 - $k;

	}
// border-bottom:1px; border-bottom-color:#000000; border-bottom-style:solid;

$replace_package_with = $replace_package_with.'
</tbody>
</table>
	';

?>

<?php /* include(JPATH_BASE."/components/com_adagency/includes/js/ads_sqz_box.php"); */ ?>

<?php echo str_replace('{packages}', $replace_package_with, $overview) . "</div>"; ?>
