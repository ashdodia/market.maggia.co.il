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

JHtml::_('behavior.multiselect');

	$k = 0;
	$n = count ($this->camps);
	$task = $this->task2;
	$nrads = $this->nrads;
	$camps = $this->camps;

	$rezultat = $this->rezultat;
	$params = $this->params;
	$advertiser = $this->advertiser;

	$item_id = $this->itemid;
    $item_id2 = $this->itemid_ads;
    $item_id3 = $this->itemid_pkg;
	if($item_id != 0) { $Itemid = "&Itemid=" . $item_id; } else { $Itemid = NULL; }
    if($item_id2 != 0) { $Itemid_ads = "&Itemid=" . $item_id2; } else { $Itemid_ads = NULL; }
    if($item_id3 != 0) { $Itemid_pkg = "&Itemid=" . $item_id3; } else { $Itemid_pkg = NULL; }
    $item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }


	require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
	$document =& JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
	JHTML::_('behavior.mootools');
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "' . $cpn_link . '";
			});
		});');
	if(isset($advertiser->aid)&&($advertiser->aid > 0)) {
        $cpanel_home = "<div class='cpanelimg'>";
        $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
        //$cpanel_home .= "<a href='#' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
		$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
        $cpanel_home .= "</div>";
    } else { $cpanel_home = ""; }
?>
<div class="ijadagencycampaigns" id="adagency_container">
	<?php if(isset($_SESSION['cmp_pending_to_approved'])&&($_SESSION['cmp_pending_to_approved']=='N')) {
		echo "<div class='adag_pending'>".JText::_('ADAG_ORPEN')."</div>";
		unset($_SESSION['cmp_pending_to_approved']);
	}  ?>
<div class="componentheading" id="ijadagencycampaigns"><?php echo JText::_('VIEW_CAMPAIGN_CMP'); ?><?php echo $cpanel_home; ?></div>

<div>
		<?php if ($task=="complete") { ?>
		<?php echo "<strong>".JText::_('AD_PAYSUCCESSFUL')."</strong>";?><br /><br />
		<?php } /*else if ($task=="complete" && !$nrads){ ?>
		<?php echo "<strong>".JText::_('AD_PAYSUCCESSFUL_ADD')."</strong>"; ?><br /><br /><?php }*/ ?>
		<?php  if ($task=="failed") { ?>
		<?php echo JText::_('AD_PAY_ERROR');?><br /><br />
		 <?php } ?>

	<?php echo JText::_('ADAG_CAMP_INTROTEXT'). ' ';?>
</div>
		
<div>
    <table id="camp_buts">
        <tr>
            <td>
<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0' . $Itemid)?>" method="POST">
    <input class="agency_continue" name="" type="submit" value="<?php echo JText::_('VIEW_CAMPAIGN_CREATE_CMP');?>"/>
</form>
            </td>
            <td>
<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds' . $Itemid_ads)?>" method="POST">
    <input class="agency_continue" name="" type="submit" value="<?php echo JText::_('VIEW_CAMPAIGN_ADD_ADS');?>"/>
</form>
            </td>
        </tr>
    </table>
</div>

<form action="index.php" method="post" name="adminForm">

<?php
	$status_filter = JRequest::getVar("status_filter", "-1");
	$payment_filter = JRequest::getVar("payment_filter", "-1");
	$approval_filter = JRequest::getVar("approval_filter", "-1");
?>

<script language="javascript" type="application/javascript">
	function refreshPageForFilters(value){
		status_filter = document.getElementById("status_filter").value;
		payment_filter = document.getElementById("payment_filter").value;
		approval_filter = document.getElementById("approval_filter").value;
		window.location = '<?php echo JURI::root(); ?>index.php?option=com_adagency&view=adagencycampaigns&Itemid=<?php echo $item_id; ?>&status_filter='+status_filter+"&payment_filter="+payment_filter+"&approval_filter="+approval_filter;
	}
	
	function deleteCampaigns(action){
		if(confirm("<?php echo JText::_("ADAG_SURE_DELETE_CAMPAIGNS"); ?>")){
			if(action == 'remove'){
				document.adminForm.remove_action.value='remove';
			}
			document.adminForm.task.value='remove';
			document.adminForm.submit();
		}
	}
	
	function renewCampaign(id, name, orderid, campaign_id){
		document.adminForm.task.value = 'edit';//'checkout';
		document.adminForm.otid.value = id;
		document.adminForm.tid.value = id;
		document.adminForm.name.value = name;
		//document.adminForm.controller.value = 'adagencyOrders';
		document.adminForm.remove_action.value='renew';
		document.adminForm.orderid.value=orderid;
		document.adminForm.campaign_id.value=campaign_id;
		
		//document.adminForm.action ="index.php?option=com_adagency&controller=adagencyOrders&task=order&tid="+id;
		document.adminForm.action = "index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0&Itemid=<?php echo $item_id; ?>"
		document.adminForm.submit();
	}
</script>

<table align="right">
	<tr>
    	<td>
        	<select name="status_filter" id="status_filter" onchange="refreshPageForFilters();">
            	<option value="-1" <?php if($status_filter == -1){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEWADSTATUS"); ?></option>
                <option value="1" <?php if($status_filter == 1){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_ACTIVE"); ?></option>
                <option value="0" <?php if($status_filter == 0){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_INACTIVE"); ?></option>
                <option value="2" <?php if($status_filter == 2){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_EXPIRED"); ?></option>
            </select>
        </td>
        <td>
        	<select name="payment_filter" id="payment_filter" onchange="refreshPageForFilters();">
            	<option value="-1" <?php if($payment_filter == -1){ echo 'selected="selected"'; } ?>><?php echo JText::_("BUY_PACKPAYMENT"); ?></option>
                <option value="0" <?php if($payment_filter == 0){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEWORDERSPAID"); ?></option>
                <option value="1" <?php if($payment_filter == 1){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEWORDERSUNPAID"); ?></option>
            </select>
        </td>
        <td>
        	<select name="approval_filter" id="approval_filter" onchange="refreshPageForFilters();">
            	<option value="-1" <?php if($approval_filter == -1){ echo 'selected="selected"'; } ?>><?php echo JText::_("ADAG_APPROVAL"); ?></option>
                <option value="Y" <?php if($approval_filter == "Y"){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_APPROVED"); ?></option>
                <option value="P" <?php if($approval_filter == "P"){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_PENDING"); ?></option>
                <option value="N" <?php if($approval_filter == "N"){ echo 'selected="selected"'; } ?>><?php echo JText::_("VIEW_CAMPAIGN_REJECTED"); ?></option>
            </select>
        </td>
    </tr>
</table>

<br/>
<br/>

<table class="content_table" width="100%">
<thead>

	<tr>
    	<td class="sectiontableheader" align="center" width="5%">
        	<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" />
        </td>
	    <td class="sectiontableheader" width="5%" align="center">
			<?php echo JText::_('VIEW_CAMPAIGN_ID');?>
		</td>
        <td class="sectiontableheader" width="65%">
			<?php echo JText::_('EDITZONEDETAILS');?>
		</td>
        <td class="sectiontableheader">
			<?php echo JText::_('VIEW_CAMPAIGN_MEDIA_BANNERS');?>
		</td>
        <td class="sectiontableheader">
			<?php echo JText::_('VIEW_CAMPAIGN_MEDIA_ACTIONS');?>
		</td>
	</tr>
</thead>

<tbody>

<?php
	$j=0;
	//for($i = 0; $i < $n; $i++):
	foreach($this->camps as $i=>$camp){
	//$camp =& $this->camps[$i];

	$approved = $camp->approved;
	if ($approved=='Y') {
		$alt = JText::_('VIEW_CAMPAIGN_APPROVED');
		$color = 'green';
	} elseif ($approved == 'P') {
		$alt = JText::_('VIEW_CAMPAIGN_PENDING');
		$color = 'orange';
	} elseif ($approved == 'N') {
		$alt = JText::_('VIEW_CAMPAIGN_REJECTED');
		$color = 'red';
	}

	$id = $camp->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=" . $id . $Itemid);
	$expired=0;
		if(($camp->type=="cpm"  || $camp->type=="pc") && $camp->quantity < 1){
			$expired=1;
		}
		
		if ($camp->type=="fr") {
			$datan = date("Y-m-d H-i-s");
			if ($datan > $camp->camp_validity) { $expired=1; } 
		}
?>
	<tr class="camp<?php echo $k;?>">
	   	<td align="center" valign="top" style="padding-top:10px;">
        	<?php echo JHtml::_('grid.id', $i, $camp->id); ?>
        </td>
        <td align="center" valign="top" style="padding-top:10px;">
			<?php echo $id; ?>
		</td>
        <td>
        	<table style="margin-bottom:20px; margin-top:5px; font-weight: normal;">
            	<tr>
                	<td colspan="2">
	       				<a class="campaign_title" href="<?php echo $link;?>" ><?php echo $camp->name;?></a>
					</td>
                </tr>
                <tr>
                	<td nowrap="nowrap"><span class="details_label"><?php echo JText::_('VIEW_CAMPAIGN_STARTDATE'); ?></span></td>
                    <td nowrap="nowrap"><?php echo adagencyAdminHelper::formatime($camp->start_date, $params); ?></td>
                </tr>
                <tr>
                	<td nowrap="nowrap"><span class="details_label"><?php echo JText::_('VIEWPACKAGETERMS'); ?></span></td>
                    <td nowrap="nowrap">
						<?php
							if(in_array($camp->type, array("pc", "cpm"))){
								echo $camp->quantity . " ";
								echo JText::_('ADAG_TERM_'.strtoupper($camp->type));
							}
							elseif ($camp->type == 'fr') {
								$temp = explode('|', $camp->validity);
								$jtext_duration = 'VIEWPACKAGE_' . strtoupper($temp[1]);
								if ($temp[1] == 'month' || $temp[1] == 'year') {
									$jtext_duration .= 'S';
								}
								$duration = JText::_($jtext_duration);
								echo $temp[0] . ' ' . $duration;
							}
							if($expired == 1){ // Expired
								$package_id = $camp->otid;
								$order_details = $this->getOrderDetails($id, $package_id);
								echo '&nbsp;&nbsp;<span class="expired_camp_text">('.JText::_("VIEW_CAMPAIGN_EXPIRED").')</span> <a class="campaign_title"  onclick="javascript:renewCampaign('.$camp->otid.', \''.trim(addslashes($camp->name)).'\', '.intval(@$order_details["0"]["oid"]).', '.intval($id).');">'.JText::_("ADAG_RENEW").'</a>';
							}
						 ?>
                    </td>
                </tr>
                <tr>
                	<td nowrap="nowrap"><span class="details_label"><?php echo JText::_('VIEWADPUBLISHED'); ?></span></td>
                    <td nowrap="nowrap">
						<?php
                        	if($camp->approved == "Y"){
								echo '<span class="ad_approved_yes">'.JText::_("VIEWORDERSYES").'</span>';
							}
							elseif($camp->approved == "P"){
								echo '<span class="ad_approved_no">'.JText::_("VIEWORDERSPENDING");
							}
							elseif($camp->approved == "N"){
								echo '<span class="ad_approved_no">'.JText::_("VIEW_CAMPAIGN_REJECTED").'</span>';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<span class="details_label"><?php echo JText::_("VIEWORDERSPAID");?></span>
                    </td>
                    <td>
                    	<?php
                        	$package_id = $camp->otid;
							$order_details = $this->getOrderDetails($id, $package_id);
							if(isset($order_details["0"]) && $order_details["0"]["payment_type"] == "Free"){
								echo '<span class="ad_paid_yes">'.JText::_("VIEWPACKAGEFREE").'</span>';
							}
							elseif(isset($order_details) && isset($order_details["0"]) && $order_details["0"]["status"] == "paid"){
								echo '<span class="ad_paid_yes">'.JText::_("VIEWORDERSYES").'</span>';
							}
							else{
								echo '<span class="ad_paid_no">'.JText::_("VIEWORDERSNO").'</span>&nbsp;&nbsp;&nbsp;<a class="campaign_title" onclick="javascript:renewCampaign('.$camp->otid.', \''.trim(addslashes($camp->name)).'\', '.intval(@$order_details["0"]["oid"]).', '.intval($id).');">'.JText::_("ADAG_PAY_NOW").'</a>';
							}
						?>
                    </td>
            	</tr>
                <tr>
                	<td><span class="details_label"><?php echo JText::_("VIEWADSTATUS"); ?></span></td>
                    <td>
                    	<?php
							if($expired == 1){
								echo '<span class="ad_inactive_camp">'.JText::_("VIEW_CAMPAIGN_EXPIRED").'</span>';
							}
							elseif($camp->status == 0){
								echo '<span class="ad_inactive_camp">'.JText::_("VIEW_CAMPAIGN_INACTIVE").'</span>';
							}
                        	elseif($camp->status == 1){
								echo '<span class="ad_active_camp">'.JText::_("VIEW_CAMPAIGN_ACTIVE").'</span>';
							}
						?>
                    </td>
            	</tr>
            </table>
        </td>
		<td valign="top" <?php if($camp->cnt){echo 'align="center"';} ?>>
   	    	<?php
                if($camp->cnt){
                   echo $camp->cnt;
                }
				else{
                    echo "<a class='red_adag' href='";
                    echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=addbanners' . $Itemid_ads);
                    echo "'>";
                    echo JText::_("ADAG_ADD_BANNERS");
                    echo "</a>";
                }
            ?>
		</td>
		<td valign="top">
	     	<a class="camp_edit_pause" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=<?php echo $camp->id . $Itemid; ?>">
				<?php echo JText::_('VIEW_CAMPAIGN_EDIT'); ?>
            </a>
			<?php if (!$expired) { ?> / <a class="camp_edit_pause" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=<?php echo(($camp->status) ? 'pause' : 'unpause')?>&cid=<?php echo $camp->id.$Itemid; ?>"><?php echo(($camp->status) ? JText::_('VIEW_CAMPAIGN_PAUSE') : JText::_('VIEW_CAMPAIGN_ACTIVATE') )?></a><?php } ?>
		</td>

	</tr>


<?php
		$k = 1 - $k;
	}//foreach
?>
</tbody>

</table>
<br/>
<table>
	<tr>
    	<td>
        	<input class="delete_camp_button" type="button" name="delete_selected" id="delete_selected" onclick="javascript:deleteCampaigns('');" value="<?php echo JText::_("ADAG_DELETE_SELECTED"); ?>" />
        </td>
        <td>
        	&nbsp;&nbsp;<input class="delete_camp_button" type="button" name="delete_expired" id="delete_expired" onclick="javascript:deleteCampaigns('remove');" value="<?php echo JText::_("ADAG_DELETE_EXPIRED"); ?>" />
        </td>
    </tr>
</table>

</div>

	<input type="hidden" name="boxchecked" value="0" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="controller" value="adagencyCampaigns" />
    
    <input type="hidden" name="remove_action" value="" />
    <input type="hidden" name="otid" value="" />
    <input type="hidden" name="tid" value="" />
    <input type="hidden" name="name" value="" />
    <input type="hidden" value="0" name="aurorenew" />
    <input type="hidden" value="0" name="orderid" />
    <input type="hidden" value="0" name="campaign_id" />
	
	<?php echo JHtml::_('form.token'); ?>
</form>
