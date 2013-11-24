<?php
	$document =& JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
	$item_id = $this->itemid;
	if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
?>
<div class="ijadagencyadvertisersreg" id="adagency_container">
<div class="componentheading" id="ijadagencyadvertisersreg"><?php echo JText::_('AD_REGISTER_OR_LOGIN');?></div>
<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=login'.$Itemid); ?>" method="post" name="adaglogin" id="adag-form-login" >
<script type="text/javascript">
	function redirect(){
		document.location.href="<?php echo "index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=0".$Itemid; ?>";
	}
</script>
<table class="adaglogintable" cellpadding="15" cellspacing="15" style="border-collapse: collapse;">
	<tr>
        <td style="padding-right: 7px;padding-bottom:10px;border-right: 1px solid #CCCCCC;">
            <span class="adag_reg"><?php echo JText::_('ADAG_LOGACC'); ?></span>
        </td>
        <td style="padding-left: 7px;padding-bottom:10px;">
            <span class="adag_reg"><?php echo JText::_('ADAG_NOTMB'); ?></span>
        </td>
	</tr>
	<tr>
        <td style="padding-right: 7px;padding-bottom:10px;border-right: 1px solid #CCCCCC;">
            <span class="adag_reg"><?php echo JText::_('ADAG_LOG_BLW'); ?></span>
        </td>
		<td style="padding-left: 7px;padding-bottom:10px;"><span class="adag_reg"><?php echo JText::_('ADAG_REG_NOW'); ?></span></td>
	</tr>
	<tr>
		<td valign="top" style="padding-right: 7px;border-right: 1px solid #CCCCCC;">
			<table cellpadding="2" cellspacing="2" class="adagloginform">
				<tr>
					<td class="agency_label"><label for="adag_username"><?php echo JText::_('ADAG_USER');?></label></td>
					<td><input id="adag_username" type="text" name="adag_username" class="inputbox" alt="username" size="18" /></td>
				</tr>
				<tr>
					<td class="agency_label"><label for="adag_passwd"><?php echo JText::_('ADAG_PSW');?></label></td>
					<td><input id="adag_passwd" type="password" name="adag_password" class="inputbox" size="18" alt="password" /></td>
				</tr>
				<tr>
					<td colspan="2" align="left">
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="remember_me" value="1" checked="checked" />&nbsp;<?php echo JText::_('ADAG_REMME');?></td>
				</tr>
			</table>
            <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
		</td>
		<td valign="top" align="left" style="padding-left: 7px;">
			<?php echo JText::_('ADAG_REGPREV');?><p />
		</td>
	</tr>
    <tr>
        <td style="padding-right: 7px;border-right: 1px solid #CCCCCC;">
            <input class="agency_continue" type="submit" class="button" value="<?php echo JText::_('ADAG_LOGCONT');?> >>" />
        </td>
        <td style="padding-left: 7px;">
            <input class="agency_continue" type="button" onClick="javascript:redirect()" value="<?php echo JText::_('ADAG_CONTREG');?> >>" />
        </td>
    </tr>
</table>
	<input type="hidden" name="returnpage" value="<?php echo JRequest::getVar("returnpage", ""); ?>" />
	<input type="hidden" name="pid" value="<?php echo JRequest::getVar("pid", ""); ?>" />
</form>
</div>
