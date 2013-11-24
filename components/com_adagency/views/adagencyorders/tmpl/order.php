<?php
$order = $this->order;
$configs = $this->configs;
$paywith = $this->paywith;
$lists = $this->lists;
$allplug = $this->allplug;

if($order->type=="fr"){
	if(strpos($order->validity,"|")>0){
		$temp = explode("|",$order->validity);
		$temp[1] = JText::_("ADAG_".strtoupper($temp[1]));
		$order->details = implode("|",$temp);
	}
	else{
		$order->details = $order->validity;
	}
}
else{
	$order->details = $order->quantity.' '.JText::_("ADAG_".strtoupper($order->type));
}

$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_adagency/includes/css/adagency_template.css');
$document->addStyleSheet(JURI::root() . 'components/com_adagency/includes/css/ad_agency.css');

$cost = $order->cost;
$total = $order->cost;
$discount = "";
if(isset($_SESSION["new_cost"]) && trim($_SESSION["new_cost"]) != ""){
	$total = trim($_SESSION["new_cost"]);
	if($configs->showpromocode == 1){
		$cost = trim($_SESSION["new_cost"]);
	}
}

if(isset($_SESSION["discount"]) && trim($_SESSION["discount"]) != ""){
	$discount = trim($_SESSION["discount"]);
	$discount = round($discount, 2);
}

?>
<div class="ijadagencyorders">
<div class="contentheading"><h2><?php echo JText::_("AD_BUY_PACK");?></h2></div>
	<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyOrders&task=checkout');?>" method="post" name="adminForm">
	<table id="adagency_order_table" cellpadding="5" cellspacing="5">

	<tr>
			<td nowrap><b><?php echo JText::_('BUY_PACKNAME');?>: </b></td>
			<td nowrap><?php echo $order->description;?></td>
	</tr>
	<tr>
			<td nowrap><b><?php echo JText::_('BUY_PACKTYPE');?>: </b></td>
			<td nowrap><?php if ($order->type=="cpm") { echo JText::_('BUY_PACKCPM'); } elseif ($order->type=="pc") { echo JText::_('BUY_PACKPC'); } elseif ($order->type=="fr") { echo JText::_('BUY_PACKFR'); } ?></td>
	</tr>
	<tr>
			<td nowrap><b><?php echo JText::_('BUY_PACKDETAILS');?>: </b></td>
			<td nowrap>
					<?php
                    	$details = $order->details;
						$details = str_replace("|", " ", $details);
						$details = ucwords($details);
						$number = intval($details);
						if($number > 1){
							$details .= "s";
						}
						echo $details;
					?>
            </td>
	</tr>
	<tr>
			<td nowrap><b><?php echo JText::_('BUY_PACKPRICE');?>: </b></td>
			<td nowrap><?php echo $cost.' '.$configs->currencydef;?></td>
	</tr>
    <?php
    	if($configs->showpromocode == 0 && trim(@$_SESSION["new_cost"]) != ""){
	?>
    <tr>
			<td nowrap><b><?php echo JText::_('ADAG_DISCOUNT');?>: </b></td>
			<td nowrap><?php echo $discount.' '.$configs->currencydef;?></td>
	</tr>
    <tr>
			<td nowrap><b><?php echo JText::_('ADAG_TOTAL_PRICE');?>: </b></td>
			<td nowrap><?php echo $total.' '.$configs->currencydef;?></td>
	</tr>
	<?php
    	}
	?>
	<tr>
			<td nowrap width="15%"><b><?php echo JText::_('BUY_PACKPAYMENT') ?>:</b></td>
			<td nowrap>
			<?php  if (!$paywith) { ?>

			<?php
			echo $lists['payment_type'];
			if (isset($lists['payment_type'])) $valid=1;
			?>

			<?php } else {
					echo '<strong>'.strtoupper($this->paywith_display_name).'</strong>';
					$valid=1;
					echo '<input type="hidden" name="payment_type" value="'.$paywith.'">';
			}
			?>
			</td>

	</tr>

	</table>
	<?php if ($allplug==0) $valid=0;?>
	<INPUT id="buy" class="agency_continue" TYPE="submit" value="<?php echo JText::_("AD_BUY_PACK2");?> >>" <?php if(!$valid) echo 'disabled="disabled"'; ?>>
	<input type="hidden" name="task" value="checkout" />
	<input type="hidden" name="tid" value="<?php echo $order->tid; ?>" />
	<?php
		if(JRequest::getInt('Itemid','0','get') != '0') {
			echo "<input type='hidden' name='Itemid' value='".JRequest::getInt('Itemid','0','get')."' />";
		}
	?>
	<input type="hidden" name="aurorenew" value="<?php echo intval($_SESSION["aurorenew"]); ?>" />
    <input type="hidden" name="orderid" value="<?php echo intval(JRequest::getVar("orderid", "0")); ?>" />
	</form>
</div>