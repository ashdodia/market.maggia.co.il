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
	$k = 0;
	$get = JRequest::get('get');
	$n = count ($this->orders);
	$currencydef = $this->currencydef;
	$plugs = $this->plugs;
	$params = $this->params;

	$itemid = $this->itemid;
    $itemid_ads = $this->itemid_ads;
    $itemid_adv = $this->itemid_adv;
    $itemid_cmp = $this->itemid_cmp;
    $itemid_pkg = $this->itemid_pkg;
    $item_id_cpn = $this->itemid_cpn;
	if($itemid != 0) { $Itemid = "&Itemid=" . $itemid; } else { $Itemid = NULL; }
    if($itemid_ads != 0) { $Itemid_ads = "&Itemid=" . $itemid_ads; } else { $Itemid_ads = NULL; }
    if($itemid_adv != 0) { $Itemid_adv = "&Itemid=" . $itemid_adv; } else { $Itemid_adv = NULL; }
    if($itemid_cmp != 0) { $Itemid_cmp = "&Itemid=" . $itemid_cmp; } else { $Itemid_cmp = NULL; }
    if($itemid_pkg != 0) { $Itemid_pkg = "&Itemid=" . $itemid_pkg; } else { $Itemid_pkg = NULL; }
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }


	require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
	$document =& JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
	JHTML::_('behavior.mootools');
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid);
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "' . $cpn_link . '";
			});
		});');
	if(isset($this->aid)&&($this->aid > 0)) {
        $cpanel_home = "<div class='cpanelimg'>";
        $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
        $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
        $cpanel_home .= "</div>";
    } else { $cpanel_home = ""; }
?>
<div class="ijadagencyorders" id="adagency_container">
<?php if(isset($get['p'])&&(intval($get['p'])==1)) { ?><div class="adag_pending"><?php
	echo JText::_('ADAG_ORPEN');
?></div><?php } ?>

<div class="componentheading" id="ijadagencyorders"><?php echo JText::_("AD_CP_ORDERS"); ?><?php echo $cpanel_home; ?></div>
		<div style="width: 100%; margin-bottom:13px;">
			<table align="center" id="camp_buts">
				<tr>
					<td>
		<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0' . $Itemid_cmp); ?>" method="POST">
			<input class="agency_continue" name="" type="submit" value="<?php echo JText::_('ADAG_ACMP');?> >>"/>
		</form>
					</td>
					<td>
		<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds' . $Itemid_ads); ?>" method="POST">
			<input class="agency_continue" name="" type="submit" value="<?php echo JText::_('ADAG_ADDBANNER2');?> >>"/>
		</form>
					</td>
				</tr>
			</table>
		</div>

<table class="content_table" width="100%">
	<tr>
		<td class="sectiontableheader" width="20">
			<?php echo JText::_('VIEWORDERSID');?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWORDERSORDERDATE');?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWORDERSORDERDESC');?>
		</td>

		<td class="sectiontableheader">
			<?php echo JText::_('VIEWORDERSTYPE');?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWORDERSPRICE');?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWORDERSMETHOD');?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('VIEWORDERSSTATUS');?>
		</td>

	</tr>
<?php
$j=0;
	for ($i = 0; $i < $n; $i++):
		$order =& $this->orders[$i];
		$id = $order->oid;
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_adagency&controller=adagencyOrders&task=edit&cid[]=" . $id . $Itemid);
		$customerlink = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=" . $order->aid . $Itemid_adv);
		$packagelink = JRoute::_("index.php?option=com_adagency&controller=adagencyPackages&task=edit&cid[]=" . $order->tid . $Itemid_pkg);

		$payment_method = "";
		$name = $order->notes;
		$ok=0;
		foreach($plugs as $a_plug) {
			if($a_plug[0] == $order->payment_type.".php"){
				if(strtoupper($a_plug["1"]) == "AUTHORIZENET"){
					$name = JText::_("VIEWORDERSPACKAGE");
				}
				$payment_method = JText::_("ADAG_".strtoupper($a_plug["1"])."_PAYMENT");
				$ok=1;
			}
		}
		
		if($ok==0){
			if(trim(strtolower($order->payment_type)) == 'free'){
				$payment_method = JText::_("VIEWPACKAGEFREE");
			}
			else{
				$payment_method = JText::_("ADAG_".strtoupper($order->payment_type)."_PAYMENT");
			}
		}
		$ok=0;

?>
	<tr class="row<?php echo $k;?>">
        <td><?php $j++; echo $j;?></td>
        <td><?php  echo adagencyAdminHelper::formatime($order->order_date, $params['timeformat']); ?></td>
        <td>
			<?php
            	echo $name;
			?>
		</td>

        <td><?php echo JText::_("ADAG_".strtoupper($order->type)."_TEXT"); ?></td>
        <td><?php
				if(isset($order->currency)&&($order->currency != NULL)) {
                    echo JText::_("ADAG_C_".$order->currency);
                	echo $order->cost.' ';
					echo $order->currency;
                }
                else{
                    if(isset($order->currencydef)){
						echo JText::_("ADAG_C_".$order->currencydef);
                		echo $order->cost.' ';
						echo $order->currency;
					}
                }
            ?>
        </td>
        <td>
			<?php
                echo $payment_method;
            ?>
        </td>
        <td>
            <?php 
                if($order->status=='paid'){
                    echo JText::_("VIEWORDERSPAID");
                }
                else{
                    echo JText::_("VIEWORDERSPENDING");
                }
            ?>
        </td>
	</tr>

<?php
		$k = 1 - $k;
	endfor;
?>
	<tr>
    	<td colspan="8"><?php //echo $this->pagination->getListFooter(); ?></td>
	</tr>
</table>
</div>
