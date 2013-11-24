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
$camp_row = $this->camp;
$stats = $this->stats;
$package_row = $this->package_row;
$ban_row = $this->ban_row;
$cbrw = NULL;$bnrs = NULL; $awh = NULL;
$pstatus = $this->pstatus;
if(isset($ban_row)&&(is_array($ban_row))){
    foreach($ban_row as $el){
        $cbrw[] = $el->id;
    }
    $cbrw = @implode(",",$cbrw);
}

$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

$configs = $this->configs;

if ( isset($camp_row->params['adslim']) ) {
    $adslim = (int)$camp_row->params['adslim'];
} elseif ( (!isset($camp_row->id) || ($camp_row->id <= 0)) && isset($configs->params['adslim']) ) {
    $adslim = $configs->params['adslim'];
} else {
    $adslim = 999;
}

// echo "<pre>";var_dump($adslim);die();

$lists = $this->lists;
$task = $this->task;
$text = $this->text;
$post_data = JRequest::get('post');
$get_data = JRequest::get('get');
JHTML::_('behavior.mootools');
require_once(JPATH_BASE.DS."components".DS."com_adagency".DS."helpers".DS."helper.php");
$document = &JFactory::getDocument();
$count_total_banners = intval($this->count_total_banners);
$count_available_banners = count($ban_row);
$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/ad_agency.css');
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");

$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }
$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
/*$document->addScriptDeclaration('
    ADAG(function(){
        ADAG(\'.cpanelimg\').click(function(){
            document.location = "'. $cpn_link .'";
        });
    });');*/
$cpanel_home = "<div class='cpanelimg'>";
$cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
$cpanel_home .= "</div>";
?>

<?php
	$remove_action = JRequest::getVar("remove_action", "");
	if($remove_action == ""){
		include(JPATH_BASE."/components/com_adagency/includes/js/campaigns.php");
	}
?>

<style>
	#close_domwin{
		background: url("<?php echo JURI::root(); ?>components/com_adagency/images/closebox.png") no-repeat scroll center center transparent !important;
	}
</style>


<div class="ijadagencycampaigns2" id="adagency_container">
<div class="componentheading" id="ijadagencycampaigns2">
	<?php
    	$remove_action = JRequest::getVar("remove_action", "");
		if($remove_action == ""){
	?>
    		<?php echo ucfirst($text). ' ' . JText::_("AD_NEW_CAMP"); ?>
	<?php
    	}
		else{
	?>
    		<?php echo JText::_("AD_RENEW_CAMP")." ".$camp_row->name; ?>
    <?php	
		}
	?>
	<?php echo $cpanel_home;?>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyCampaigns'.$Itemid); ?>" method="post" name="adminForm" id="adminForm">
<table>
	<tr>
		<td class="agency_subtitle" nowrap="nowrap">
			<?php
					$remove_action = JRequest::getVar("remove_action", "");
                	if($task == 'new' && $remove_action == ""){
						echo JText::_("AD_NEW_CAMP_MAIN_INFO");
					}
					else{
						echo JText::_("AD_EDIT_CAMP_MAIN_INFO");
					}
				?>
		</td>
	</tr>
</table>
<table class="adminform">
		<tr>
			<td nowrap class="agency_label">
			<b><?php echo JText::_("AD_NEW_CAMP_NAME");?>:<?php  if ($task=='new') { ?><font color="#ff0000">*</font><?php  } ?></b>
			</td>
			<td>
			<?php
				$remove_action = JRequest::getVar("remove_action", "");
            	if($task=='new' && $remove_action == ""){ 
			?>
					<input class="agency_label"  type="text" name="name" size="25" maxlength="255" value="<?php
						if(isset($post_data['name'])) { echo $post_data['name']; }
					?>" />
			<?php 
				}
				else{  ?>
					<input class="agency_label"  type="hidden" name="name" size="25" maxlength="255" value="<?php echo $camp_row->name; ?> " />
			<?php 	echo $camp_row->name;
				}
			?>
			</td>
		</tr>

		<?php
            if (!$camp_row->id) {
        ?>
			<tr>
				<td class="agency_label" width="10%"><?php echo JText::_("AD_NEW_CAMP_PACKAGE"); ?>:<!-- <a name="refreshpackage"> -->
                	<font color="#ff0000">*</font></a></td>
				<td>
					<?php echo $lists["package"]; ?>
                	<?php
                    	$remove_action = JRequest::getVar("remove_action", "");
						if($remove_action == ""){
					?>
                    		&nbsp;&nbsp;<a href="index.php?option=com_adagency&controller=adagencyPackages&task=packs&tmpl=component<?php echo $Itemid; ?>" class="modal"><?php echo JText::_('ADAG_VIEW_PACKS');?></a>
                    <?php
                    	}
					?>
				</td>
			</tr>

			<tr>
				<td class="agency_label"><?php echo JText::_("AD_NEW_CAMP_START_DATE"); ?>:<font color="#ff0000">*</font>
				</td>
				<td>
					<?php
						if(($configs->params['timeformat']==1)||($configs->params['timeformat']==7)) {$ymd="%d-%m-%Y";}
						elseif(($configs->params['timeformat']==2)||($configs->params['timeformat']==8)) {$ymd="%d/%m/%Y";}
						elseif(($configs->params['timeformat']==3)||($configs->params['timeformat']==9)) {$ymd="%m-%d-%Y";}
						elseif(($configs->params['timeformat']==4)||($configs->params['timeformat']==10)) {$ymd="%m/%d/%Y";}
						elseif(($configs->params['timeformat']==5)||($configs->params['timeformat']==11)) {$ymd="%Y-%m-%d";}
						elseif(($configs->params['timeformat']==6)||($configs->params['timeformat']==12)) {$ymd="%Y/%m/%d";}
						else {$ymd="%d-%m-%Y";}

						if(!isset($camp_row->start_date)||($camp_row->start_date=='')) {
							$JApp =& JFactory::getApplication();
							$jnow = JFactory::getDate();
							$jnow->setOffset($JApp->getCfg('offset'));
							
							/*$jnow = &JFactory::getDate();
							$camp_row->start_date =	$jnow->toMySQL();*/
							
							adagencyAdminHelper::formatime($jnow->toMySql(true),$configs->params['timeformat']);						
						}
						if($configs->params['timeformat']>=7) {$hms = NULL;} else {$hms = ' %H:%M:%S';}
						JHTML::_('behavior.calendar');
						echo JHTML::_('calendar', $camp_row->start_date, 'start_date', 'start_date', ''.$ymd.$hms, array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
						echo "<input type='hidden' name='tfa' value='".$configs->params['timeformat']."' />";
					?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<?php 
						$db =& JFactory::getDBO();
						$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='renewcmp'";
						$db->setQuery($query);
						$result = $db->loadRow();
						$renewcmp = $result[4];
						if($renewcmp == 2){
							$db =& JFactory::getDBO();
							$pack_id = intval($camp_row->otid) == 0 ? JRequest::getVar("pid", "0") : intval($camp_row->otid);
							$sql = "select `type` from #__ad_agency_order_type where tid=".$pack_id;
							$db->setQuery($sql);
							$db->query();
							$result = $db->loadResult();
							if(isset($result) && trim($result) == "fr"){
					?>
								<input type="checkbox" name="autorenewcmp" value="1" id="autorenewcmp"/>&nbsp;<?php echo JText::_("ADAG_AA_RENEW_CMP"); ?>
					<?php
							}
						}
					?>
				</td>
			</tr>
		<?php }	?>

		<?php
        	$promos = $this->promoValid();
			if($promos > 0){
		?>
                <tr>
                    <td class="agency_label">
                        <?php echo JText::_("AD_PAYMENT_PROMOCODE"); ?>
                    </td>
                    <td>
                        <input type="text" value="" size="25" name="promocode" class="agency_label" />
                    </td>
                </tr>
        <?php
        	}
		?>

		<tr>
			<td>
			<?php ///echo "default campaign"?></td>
			<td>
				<input type="hidden" name="default" <?php if ($camp_row->default=='Y') echo 'checked'; ?> value ="Y"> 			</td>
		</tr>

		<tr>
			<td>
   			<?php //echo "notes" ?>
			</td>
			<td>
			<input class="inputbox" type="hidden" name="notes" size="40" maxlength="255" value="<?php //echo $camp_row->notes; ?>" />
			</td>
		</tr>
	</table>

	<?php
		if(($count_total_banners == 0)&& ($camp_row->id)&&(!isset($package_row->tid)||($package_row->tid == 0))) {
			$the_text = JText::_('ADAG_NO_ADS_AVAILABLE').JText::_('ADAG_NO_ADS_AVAILABLE3');
			echo '<div style="padding: 3px; border: 1px solid rgb(255, 0, 0); background-color: rgb(255, 255, 204);">'.str_replace('[L]','</a>',str_replace('[LINK]','<a href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners'.$Itemid.'">',$the_text)).'</div>';
		} elseif(isset($package_row->tid)&& ($camp_row->id)) {
			if($count_available_banners == 0) {
                //echo "<pre>";var_dump($package_row);die();
                if (is_array($package_row->allzones))
                foreach($package_row->allzones as $element) {
                    $package_row->adparams = $element->adparams;
                    if(is_array($package_row->adparams)) {
                        $temp2 = array();
                        $bnrs = array();
                        $awh = NULL;
                        foreach($package_row->adparams as $key=>$value) {
                            $temp2[] = $key;
                        }

                        if((in_array('width',$temp2))&& (in_array('height',$temp2)) && ($package_row->adparams['width']) && ($package_row->adparams['height']) ) {
                            $awh = "- ".JText::_('ADAG_MUST_BE').strtolower(JText::_('VIEWADSIZE'))." ".$package_row->adparams['width']." x ".$package_row->adparams['height']."<br />";
                        }
                        if(in_array('popup',$temp2)) { $bnrs[] = JText::_('JAS_POPUP'); $awh = ""; }
                        if(in_array('transition',$temp2)) { $bnrs[] = JText::_('JAS_TRANSITION'); $awh = ""; }
                        if(in_array('floating',$temp2)) { $bnrs[] = JText::_('JAS_FLOATING'); $awh = ""; }
                        if(in_array('textad',$temp2)) { $bnrs[] = JText::_('JAS_TEXT_LINK'); }
                        if(in_array('standard',$temp2)) { $bnrs[] = JText::_('JAS_STANDART'); }
                        if(in_array('affiliate',$temp2)) { $bnrs[] = JText::_('JAS_BANNER_CODE'); }
                        if(in_array('flash',$temp2)) { $bnrs[] = JText::_('JAS_FLASH'); }
                        $bnrs = implode(", ", $bnrs);
                        $bnrs = "- " . JText::_('ADAG_MUST_BE') . " " . $bnrs . "<br />";
                        $the_text[] = '<p>' . JText::_('NEWADZONE') . ' \'' . $element->z_title . '\' '
                                            . JText::_('ADAG_REQUIREMENTS') . ': <br />' . $bnrs . $awh
                                            . '</p>';
                    }
                }
                // JText::_('ADAG_NO_ADS_AVAILABLE2') . ."<br />" . JText::_('ADAG_NO_ADS_AVAILABLE3')
                // echo "<pre>";var_dump($the_text);die();
                //echo implode('<hr />', $the_text);die();

                $the_text = '<p>' . JText::_('ADAG_NO_ADS_AVAILABLE2') . '</p>' . implode('', $the_text)
                                . '<p>' . JText::_('ADAG_NO_ADS_AVAILABLE3') . '</p>';
				echo '<div style="padding: 3px; border: 1px solid rgb(255, 0, 0); background-color: rgb(255, 255, 204);">'.str_replace('[L]','</a>',str_replace('[LINK]','<a href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners'.$Itemid.'">',$the_text)).'</div>';
            }  else {
                echo "<div class='selctads_title'><h2>".JText::_("ADAG_CMP_SEL_ADS")."</h2></div>";

                if ( isset($adslim) && ($adslim != 999) ) {
                    echo "<p><span class='adslim'>" . JText::_('ADAG_CMP_ADS_LIM') . ": " . $adslim . "</span></p>";
                }

                echo JText::_("ADAG_CMP_REQ_NOTE")."<p />";
        ?>
                <TABLE id="banner_table" class="adminform" width="100%">
                <TR >
                    <TD class="sectiontableheader"  style="text-align:center;"><?php echo JText::_("AD_NEW_CAMP_BAN_ID"); ?></TD>
                    <TD class="sectiontableheader" ><?php echo JText::_("AD_NEW_CAMP_BAN_NAME"); ?></TD>
                    <TD class="sectiontableheader" ><?php echo JText::_("NEWADZONE"); ?></TD>
                    <TD class="sectiontableheader"  style="text-align:center;"><?php echo JText::_("AD_NEW_CAMP_BAN_APR"); ?></TD>
                    <TD class="sectiontableheader" style="text-align: center;" ><?php echo JText::_("ADAG_INCLUDE");?></TD>
                    <?php /* if ($task=="edit" || $task=='editA') { ?>
                    <TD class="sectiontableheader" ><?php echo JText::_("AD_NEW_CAMP_BAN_DEL");?></TD>
                    <?php } */ ?>
                    <TD class="sectiontableheader" ><?php echo JText::_("VIEWADPREVIEW");?></TD>
                </TR>
                <?php
                $k=0;
                for ($i=0, $n=count( $ban_row ); $i < $n; $i++) {

                    $row = &$ban_row[$i];
                        ?>
                        <TR class="<?php echo "row$k"; ?>">
                            <TD style="text-align:center;"><?php echo $row->id; ?></TD>
                            <TD><?php echo $row->title; ?> <!--(<?php if ($row->width==0 or $row->height==0) { echo  JText::_('ADAG_SIZUNKN'); } else {echo "$row->width x $row->height"; }?>)--></TD>
                            <TD><?php echo $row->zones; ?></TD>
                            <TD style="text-align:center;"><?php
                                if($row->approved == 'Y') { echo "<span style='color:green;'>".JText::_('NEWADAPPROVED'); }
                                elseif($row->approved == 'N') { echo "<span style='color:red;'>".JText::_('ADAG_REJECTED'); }
                                elseif($row->approved == 'P') { echo "<span style='color:orange;'>".JText::_('ADAG_PENDING'); }
                                echo "</span>";
                            ?> </TD>

                            <TD class="add_column" align="center">
                                <INPUT TYPE="checkbox" id="bid[<?php echo ($i+1);?>]" NAME="banner[<?php echo $row->id;?>][add]" VALUE="1" <?php if ($row->relative_weighting != NULL)  {echo 'checked="checked"'; } ?>
                            </TD>

                            <?php /* if ($task=="edit" || $task=='editA') { ?>
                            <TD><?php if ($row->relative_weighting>0) {?><INPUT TYPE="checkbox" NAME="banner[<?php echo $row->id;?>][del]" VALUE="1"><?php } ?></TD>
                            <?php } */ ?>
                            <!-- SqueezeBox.fromElement(this,{parse:'rel'}); return false; -->
                            <TD><a class="modal" href="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid='.$row->id.$Itemid);?>"><?php echo JText::_("VIEWADPREVIEW");?></a></TD>

                            <INPUT TYPE="hidden" NAME="banner[<?php echo $row->id;?>][rw]"  size="5" maxlength="6" value="<?php echo $row->relative_weighting>0?$row->relative_weighting: '100';?>">
                        </TR>
                        <?php
                        $k = 1 - $k;
                    //}
                }?>
                <?php if(!isset($get_data['cid'][0])||($get_data['cid'][0]==0)) {
                    echo "<input type='hidden' id='countbids' value='".$i."' />";
                } ?>
                </TABLE>

    <?php } } ?>

		<?php if ($camp_row->id > 0) { ?>
	<table class="adminform" cellpadding="0" cellspacing="0">
		<tr>
			<td class="sectiontableheader" colspan="2">
				<h2><?php echo JText::_("AD_NEW_CAMP_STS");?></h2>
			</th>
		</tr>

		<tr>
			<td><?php echo JText::_("AD_NEW_CAMP_PK_NAME");?>: </td>
			<td><?php echo $package_row->description; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_("AD_NEW_CAMP_PK_TYPE");?>: </td>
			<td><?php if ($camp_row->type=="cpm") { echo JText::_('ADAG_CPM_TEXT'); } elseif ($camp_row->type=="pc") { echo JText::_('ADAG_PC_TEXT'); } elseif ($camp_row->type=="fr") { echo JText::_('ADAG_FR_TEXT'); } ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_("AD_NEW_CAMP_PK_DET");?>: </td>
			<td><?php echo $package_row->details; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_("AD_NEW_CAMP_START_DATE");?>:</td>
			<td><?php echo adagencyAdminHelper::formatime($camp_row->start_date, $configs->params['timeformat']); ?></td>
		</tr>


		<?php if ($camp_row->type == "cpm") {
			if ($camp_row->quantity > 0) {
			$package_row->quantity = intval($package_row->quantity) - intval($camp_row->quantity);
			?>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
					<td colspan="2"><span style="font-size: 24px; font-weight: bold; color: #000000;"><span style="color: #FF0000;"><?php echo $package_row->quantity; ?></span> <?php echo JText::_("AD_CAMP_IMP");?>,&nbsp;<span style="color: #FF0000;"><?php echo $camp_row->quantity; ?></span> <?php echo JText::_("AD_CAMP_IMP_LEFT");?></span></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>

		<?php }
			else { ?>
			<tr>
				<td colspan="2">
				<span style="font-size: 24px; font-weight: bold; color: #FF0000;"><?php echo JText::_("AD_CAMP_EXPIRED");?></span>
				</td>
			</tr>
			<?php }
		}
		if ($camp_row->type == "pc") {
			if ($camp_row->quantity > 0) {

				$package_row->quantity = intval($package_row->quantity) - intval($camp_row->quantity);
				?>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2"><span style="font-size: 24px; font-weight: bold; color: #000000;"><span style="color: #FF0000;"><?php echo $package_row->quantity; ?></span> <?php echo JText::_("AD_CAMP_CLK");?>,&nbsp;<span style="color: #FF0000;"><?php echo $camp_row->quantity; ?></span> <?php echo JText::_("AD_CAMP_CLK_LEFT");?></span></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
		<?php } else { ?>
				<tr>
					<td colspan="2">
					<span style="font-size: 24px; font-weight: bold; color: #FF0000;"><?php echo JText::_("AD_CAMP_EXPIRED");?></span>
					</td>
				</tr>

			<?php }
		}
		if ($camp_row->type == "fr") {
		if ($camp_row->expired) {
		?>
			<tr>
				<td colspan="2">
				<span style="font-size: 24px; font-weight: bold; color: #FF0000;"><?php echo JText::_("AD_CAMP_EXPIRED");?></span>
				</td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
				<span style="font-size: 24px; font-weight: bold; color: #000000;"><span style="color: #FF0000;"><?php echo $camp_row->time_left['days']; ?></span> <?php echo JText::_("AD_CAMP_DAYS");?> <span style="color: #FF0000;"><?php echo $camp_row->time_left['hours']; ?></span> <?php echo JText::_("AD_CAMP_HOURS");?> <span style="color: #FF0000;"><?php echo $camp_row->time_left['mins']; ?></span> <?php echo JText::_("AD_CAMP_MINS");?> <?php echo JText::_("AD_CAMP_IMP_LEFT");?></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		<?php }
			} ?>
			</table>
			<table class="adminform" cellpadding="0" cellspacing="0">
			<tr>
				<td class="sectiontableheader" colspan="2">
					<h2><?php echo JText::_("AD_CAMP_STATS"); ?></h2>
				</th>
			</tr>
			<tr>
				<td><?php echo JText::_("AD_CAMP_DURATION");?>: </td>
				<td><?php echo $stats['days'] . "&nbsp;days&nbsp;" . $stats['hours'] . "&nbsp;".JText::_("AD_CAMP_HOURS")."&nbsp;" . $stats['mins'] . "&nbsp;".JText::_("AD_CAMP_MINS")."&nbsp;"; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("AD_CAMP_CLICKS");?>: </td>
				<td><?php if ($stats['click']) { echo $stats['click']; } else echo '0';?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("AD_CAMP_IMPS");?>: </td>
				<td><?php if ($stats['impressions']) { echo $stats['impressions']; } else echo '0'; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("AD_CLICK_RATE");?>: </td>
				<td><?php if ($stats['click_rate']) { echo $stats['click_rate']; } else echo '0.00';  ?>%</td>
			</tr>
			</table>
		<?php } ?>

		<?php
        $cid = JRequest::getVar("cid", "0");
		if(intval($cid) != "0"){
		?>
        
            <table class="adminform" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="sectiontableheader" colspan="3">
                        <h2><?php echo JText::_("ADAG_HISTORY"); ?></h2>
                    </th>
                </tr>
                <tr>
                    <td style="background-color:#CCCCCC; font-size: 14px;" width="20%">
                        <?php echo JText::_("ADAG_DATE"); ?>
                    </td>
                    
                    <td style="background-color:#CCCCCC; font-size: 14px;" width="30%">
                        <?php echo JText::_("VIEWADACTION"); ?>
                    </td>
                    
                    <td style="background-color:#CCCCCC; font-size: 14px;">
                        <?php echo JText::_("ADAG_BY"); ?>
                    </td>
                </tr>
                <?php
                    $activities = $camp_row->activities;
                    $users = array();
                    $db =& JFactory::getDBO();
                    if(trim($activities) != ""){
                        $activities_array = explode(";", $activities);
                        if(is_array($activities_array) && count($activities_array) > 0){
                            foreach($activities_array as $key=>$activity){
                                $activity_array = explode(" - ", $activity);
                                if(is_array($activity_array) && count($activity_array) > 0 && trim($activity_array["0"]) != ""){
                                    $row  = '<tr>';
                                    $row .= 	'<td style="font-size: 14px;">';
                                    $row .= 		date("m/d/Y", strtotime(trim($activity_array["1"])));
                                    $row .= 	'</td>';
                                    $row .= 	'<td style="font-size: 14px;">';
                                    if(trim($activity_array["0"]) == 'Purchased(new)'){
										$row .= 	JText::_('HISTORY_PURCHASED_NEW');
									}
									elseif(trim($activity_array["0"]) == 'Purchased(renewal)'){
										$row .= 	JText::_('HISTORY_PURCHASED_RENEW');
									}
									elseif(trim($activity_array["0"]) == 'Expired'){
										$row .= 	JText::_('HISTORY_EXPIRED');
									}
									elseif(trim($activity_array["0"]) == 'Deleted'){
										$row .= 	JText::_('HISTORY_DELETED');
									}
									elseif(trim($activity_array["0"]) == 'Paused'){
										  $row .= 	JText::_('HISTORY_PAUSED');
									}
									elseif(trim($activity_array["0"]) == 'Re-Started'){
										$row .= 	JText::_('HISTORY_RE_STARTED');
									}
									//$row .= 		trim($activity_array["0"]);
                                    $row .= 	'</td>';
                                    if(isset($activity_array["2"])){
                                        $usertype = "";
                                        $name = "";
                                        
                                        if(isset($users[trim($activity_array["2"])])){
                                            $usertype = trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
                                            $name = $users[$activity_array["2"]]["name"];	
                                        }
                                        else{
                                            
                                            $sql = "select u.id, u.name, ug.title, a.aid 
        from #__user_usergroup_map ugm, #__usergroups ug, #__users u left outer join #__ad_agency_advertis a on a.user_id = u.id
        where u.id=ugm.user_id and ugm.group_id=ug.id and u.id=".intval($activity_array["2"])." group by u.id and u.id=a.user_id";
                                            $db->setQuery($sql);
                                            $db->query();
                                            $result = $db->loadAssocList();
                                            
                                            if(isset($result) && count($result) > 0){
                                                $users[$activity_array["2"]] = $result["0"];
                                                $usertype = trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
                                                $name = $users[$activity_array["2"]]["name"];
                                            }
                                        }
                                        
                                        $row .= 	'<td style="font-size: 14px;">';
                                        $row .= 		trim($usertype)." (".trim($name).")";
                                        $row .= 	'</td>';
                                    }
                                    $row .= '</tr>';
                                    echo $row;
                                }
                            }
                        }
                    }
					else{
						$row  = '<tr>';
						$row .= 	'<td style="font-size: 14px;">';
						$row .= 		date("m/d/Y", strtotime(trim($camp_row->start_date)));
						$row .= 	'</td>';
						$row .= 	'<td style="font-size: 14px;">';
						$row .= 		'Purchased(new)';
						$row .= 	'</td>';
						
						$usertype = "";
						$name = "";
						
						$sql = "SELECT u.name FROM #__users u, #__ad_agency_advertis a WHERE u.id = a.user_id AND a.aid=".intval($camp_row->aid);
						$db->setQuery($sql);
						$db->query();
						$result = $db->loadAssocList();

						if(isset($result) && count($result) > 0){
							@$users[$activity_array["2"]] = $result["0"];
							@$usertype = trim($users[$activity_array["2"]]["aid"]) != "" ? "Advertiser" : $users[$activity_array["2"]]["title"];
							@$name = $users[$activity_array["2"]]["name"];
						}
						
						$row .= 	'<td style="font-size: 14px;">';
						$row .= 		'Advertiser'." (".trim($name).")";
						$row .= 	'</td>';
						$row .= '</tr>';
						echo $row;
					}
                ?>
            </table>
		<?php
        }
		?>
		
        <?php
			$remove_action = JRequest::getVar("remove_action", "");
			$campaign_id = $camp_row->id;
			if($remove_action != ""){
				$campaign_id = JRequest::getVar("campaign_id", "0");
				$orderid = JRequest::getVar("orderid", "0");
				$otid = JRequest::getVar("otid", "0");
				echo '<input type="hidden" name="orderid" value="'.intval($orderid).'" />';
				echo '<input type="hidden" name="otid" value="'.intval($otid).'" />';
				echo '<input type="hidden" name="remove_action" value="renew" />';
			}
		?>
        
		<input type="hidden" name="id" value="<?php echo $campaign_id; ?>" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="controller" value="adagencyCampaigns" />
		<input type="hidden" name="aid" value="<?php echo $camp_row->aid; ?>" />

		<?php if ($camp_row->id>0) { ?>
        <input type="hidden" name="cbrw" value="<?php echo $cbrw; ?>" />
		<input type="hidden" name="otid" value="<?php echo $camp_row->otid; ?>" />
		<input type="hidden" name="approved" value="<?php echo $camp_row->approved; ?>" />
		<input type="hidden" name="type" value="<?php echo $camp_row->type; ?>" />
		<input type="hidden" name="quantity" value="<?php echo $camp_row->quantity; ?>" />
		<input type="hidden" name="validity" value="<?php echo $camp_row->validity; ?>" />
		<input type="hidden" name="start_date" value="<?php echo $camp_row->start_date; ?>" />
		<input type="hidden" name="cost" value="<?php echo $camp_row->cost; ?>" />
		<?php } else {
			if(!isset($configs->params['timformat'])) {
				$configs->params['timformat'] = NULL;
			}
		?>
		<input type="hidden" name="now_datetime" value="<?php echo adagencyAdminHelper::formatime(date("Y-m-d H:i:s"),$configs->params['timformat']); ?>" />
		<?php } ?>
        <br />
        <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
        <input style="float: left; height:35px;" type="button" class="agency_cancel" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		<INPUT style="float: left; margin-left: 5px;" class="agency_continue" TYPE="button" onclick="Joomla.submitbutton('save')" value="<?php
			if($camp_row->id>0) { echo JText::_("AD_SAVE"); }
			else { echo JText::_('AD_CONTINUE');}
		?>" />
		</form>
</div>
<div style="clear:both;"></div>
