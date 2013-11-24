<?php
// JHTML::_('behavior.mootools' );
// JHTML::_('behavior.tooltip');
// JHTML::_('behavior.modal');
defined ('_JEXEC') or die ("Go away.");
$k = 0;
$n = count ($this->packages);
//echo "<pre>";var_dump($this->packages);die();
$my =& JFactory::getUser();
// echo "<pre>";var_dump($my);die();
$currencydef = $this->currencydef;
$advertiserid = $this->advertiserid;
// echo "<pre>";var_dump($advertiserid);die();
$showpreview = $this->showpreview;
$showZoneInfo = $this->showZoneInfo;
$document =& JFactory::getDocument();
// $document->addScript(JURI::base()."/components/com_adagency/includes/js/modal.js");
// $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/modal.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adag_tip.css");
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js");
$document->addScript(JURI::root()."components/com_adagency/includes/js/graybox.js");

$itemid = $this->itemid;
$itemid_adv = $this->itemid_adv;
$itemid_cmp = $this->itemid_cmp;
$item_id_cpn = $this->itemid_cpn;
if($itemid != 0) { $Itemid = "&Itemid=".$itemid; } else { $Itemid = NULL; }
if($itemid_adv != 0) { $Itemid_adv = "&Itemid=".$itemid_adv; } else { $Itemid_adv = NULL; }
if($itemid_cmp != 0) { $Itemid_cmp = "&Itemid=".$itemid_cmp; } else { $Itemid_cmp = NULL; }
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
/*$document->addScriptDeclaration('
    ADAG(function(){
        ADAG(\'.cpanelimg\').click(function(){
            document.location = "' . $cpn_link . '";
        });
    });'
);*/

if(isset($advertiserid)&&($advertiserid > 0)) {
    $cpanel_home = "<div class='cpanelimg'>";
    $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
    $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
    $cpanel_home .= "</div>";
} else { $cpanel_home = ""; }

?>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/package.php"); ?>
		<div class="ijadagencypackage">
		<div class="componentheading" id="ijadagencypackage"><?php echo JText::_('VIEWPACKAGE_LIST_PACKAGES'); ?><?php echo $cpanel_home; ?></div>
		<?php if (isset($_GET['act'])) $acts = $_GET['act']; else $acts='';
		if ($acts=="incomplete") { ?>
		<div align="left"><br /><?php echo JText::_('AD_PAY_ERROR');?></div><br />
		 <?php } ?>
<div class="below_packs">
<?php echo JText::_('VIEWPACKAGE_INTRO_TEXT_1'). ' ';
// Next we verify if we are dealing with a registered username or a guest
// that needs to register for both user and advertiser accounts
?>
<a href="<?php
	$a_href = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=register';
	//if ($my->id) { $a_href.= '&task=edit&cid='.$my->id.'&amp;user=registered'; } else { $a_href.= '0'; }
	echo JRoute::_($a_href . $Itemid_adv);
?>">
<?php echo JText::_('VIEWPACKAGE_INTRO_TEXT_CLICKHERE'); ?></a>
<?php echo JText::_('VIEWPACKAGE_INTRO_TEXT_2').'<br /><br />'; ?>
</div>

<table width="100%" class="content_table">
<thead>

	<tr>
	    <td class="sectiontableheader">
			<?php echo JText::_('VIEWPACKAGEDESC');?>
		</td>
        <?php if($showZoneInfo) { ?>
        <td class="sectiontableheader">
			<?php echo JText::_('ADAG_ZONE_INFO');?>
		</td>
        <?php } ?>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWPACKAGEPRICE');?>
		</td>

	</tr>
</thead>

<tbody>

<?php
	for ($i = 0; $i < $n; $i++):
		$order =& $this->packages[$i];
		$order->adparams = @unserialize($order->adparams);
		$id = $order->tid;
		$order->zones = str_replace("All Zones",JText::_('ADAG_ALL_ZONES'), $order->zones);
		if(adagencyModeladagencyPackage::getFreePermission($advertiserid,$id)==false) {continue;}
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0" . $Itemid_cmp);
?>
	<tr class="row<?php echo $k;?>">
	     	<td valign="top" style="padding:5px;"><strong><div style="font-size:120%;"><?php echo $order->description;?></div></strong>
            <?php if($order->pack_description != '') { ?><p style="margin-top:10px;"><em><?php echo stripslashes($order->pack_description);?></em></p><?php } ?>
            <p style="margin-top:10px;"><strong><?php echo JText::_("VIEWPACKAGETERMS"); ?>:</strong>
            <?php if ($order->type=="fr") { if ($order->validity!="") {
					$validity = explode("|", $order->validity, 2);
					$validity[1] = ($validity[1]=="day") ? JText::_('VIEWPACKAGE_DAY') : (($validity[1]=="week") ? JText::_('VIEWPACKAGE_WEEK') : (($validity[1]=="month") ? JText::_('VIEWPACKAGE_MONTHS') : (($validity[1]=="year") ? JText::_('VIEWPACKAGE_YEARS') : ""))) ;
					echo $validity[0]." ".$validity[1]; }
				  } else { echo $order->quantity; }
					if ($order->type == 'cpm') { echo ' '.JText::_('AGENCYIMPRESSIONS'); }
					elseif($order->type == 'pc') echo ' '.JText::_('AGENCYCLICKS');
			?>
			</p>
            <p><strong><?php echo JText::_('VIEWORDERSTYPE'); ?>:</strong>
            <span>
				<?php echo JText::_('ADAG_PK_'.strtoupper($order->type)); ?>
			</span>
			<span><?php
			if ($order->type == 'cpm') {
                echo '
                    <span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPM') . '</span>
                    </span>';
			} elseif($order->type == 'pc') {
                echo '
                    <span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_CPC') . '</span>
                    </span>';
			} else {
                echo '
                    <span class="adag_tip">
                        <img  src="components/com_adagency/images/tooltip.png" border="0" align="top" />
                        <span>' . JText::_('VIEWPACKAGE_TOOLTIP_FR') . '</span>
                    </span>';
			}
			?>
			</span>
			</p>
            </td>

	        <?php if($showZoneInfo) { ?>
            <td valign="top" style="padding:5px;"><?php
				if(isset($order->location)&&(is_array($order->location))) {
					foreach($order->location as $element){
						$element->adparams = @unserialize($element->adparams);
						if($element->rotatebanners == 1) { $element->rotatebanners = JText::_("ADAG_YES"); } else { $element->rotatebanners = JText::_("ADAG_NO"); }
						if($showpreview==1) { $sz_before = "<a class=\"modal2\" href=\"".JRoute::_('index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid='.$element->id.$Itemid)."\">"; $sz_after = "</a>"; } else {
							$sz_before = NULL; $sz_after = NULL;
						}
						
						$style= "";
						if(count($order->location)  == 1){
							$style = ' style="border:none !important;" ';
						}
						
						echo "<div class='azone' ".$style." >".JText::_('NEWADZONE').": ".$sz_before.$element->title.$sz_after."<br /><br />";
						echo JText::_("ADAG_ROTATION").": ".$element->rotatebanners."<br />";
						if(isset($element->adparams['width'])&&isset($element->adparams['height'])&&($element->adparams['width'] != "")&&($element->adparams['height'] != "")) {echo JText::_("VIEWADSIZE").": ".$element->adparams['width']." x ".$element->adparams['height']." px<br />"; }
						else { echo JText::_("VIEWADSIZE").": ".JText::_('ADAG_ANYSIZE')."<br />"; }
						echo JText::_('ADAG_SLOTS').": ".$element->rows*$element->cols." (".$element->rows. " " . JText::_("ADAG_ROWS").", ".$element->cols . " " . JText::_("ADAG_COLS").")<br />";
						echo JText::_('VIEWADTYPE').": ";
						$before = false;
                    	if(isset($element->adparams['standard']) || isset($element->adparams['affiliate']) || isset($element->adparams['flash'])){
							echo JText::_("VIEW_CAMPAIGN_MEDIA_BANNERS").": ";
							if(isset($element->adparams['standard'])) { echo JText::_('VIEWTREEADDSTANDARD'); $before = true; }
							if(isset($element->adparams['affiliate'])) {
								if($before) { echo ", "; }
								echo JText::_('VIEWTREEADDADCODE');
								$before = true;
							}
							if(isset($element->adparams['flash'])) {
								if($before) { echo ", "; }
								echo JText::_('VIEWTREEADDFLASH');
							}
						} elseif(isset($element->adparams['textad'])){
							echo JText::_('VIEWTREEADDTEXTLINK');
						} elseif(isset($element->adparams['popup']) || isset($element->adparams['transition']) || isset($element->adparams['floating'])){
							if(isset($element->adparams['popup'])) { echo JText::_('VIEWTREEADDPOPUP'); $before = true; }
							if(isset($element->adparams['transition'])) {
								if($before) { echo ", "; }
								echo JText::_('VIEWTREEADDTRANSITION');
								$before = true;
							}
							if(isset($element->adparams['floating'])) {
								if($before) { echo ", "; }
								echo JText::_('VIEWTREEADDFLOATING');
							}
						}
						echo "</div>";
					}
				}
			?></td>
            <?php } ?>
	     	<td valign="top" style="padding:5px;" class="ijd-product-price">
			<?php
				$free_not_free = JText::_('VIEWPACKAGE_BUY');
				if($order->cost > 0) {
					echo JText::_("ADAG_C_".$currencydef).$order->cost;
				} else {
					echo JText::_('VIEWPACKAGEFREE');
					$free_not_free = JText::_("ADAG_START");
				}
				?>
            <p class="start_buy">
            <form action="<?php
                if($my->id && (isset($advertiserid) && $advertiserid > 0)){
                    echo $link;
                }
				elseif($my->id &&  (!isset($advertiserid) || (isset($advertiserid) && $advertiserid == 0) )){
                    echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&user=reg&cid=0'.$Itemid);
                }
				elseif(!$my->id){
					echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register&returnpage=buy'.$Itemid);
                }
            ?>" method="post" >
			<input type="hidden" name="pid" value="<?php echo $order->tid; ?>" />
	     	<input class="agency_continue agency_continue2" type="submit" value="<?php echo $free_not_free; ?>"/>
			</form>
            </p>
			</td>
	</tr>
<!-- border-bottom:1px; border-bottom-color:#000000; border-bottom-style:solid; -->
<?php
		$k = 1 - $k;
	endfor;
?>

</tbody>
</table>
<br /><br />
</div>
