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
	$lists = $this->lists;
	$task = $this->task;
	$type = $this->type;
	$filds = $this->filds_out;
	$data_row = $this->data_row;
	$params = $this->params;
	$start_date = $this->start_date;
	$end_date = $this->end_date;

	$item_id = $this->itemid;
	if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
    $item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

	$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

	$k = 0;
	require_once(JPATH_BASE . DS . "components" . DS . "com_adagency" . DS . "helpers" . DS . "helper.php");
	$document =& JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
    $document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
	JHTML::_('behavior.mootools');
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.js");
	$document->addScript(JURI::root()."components/com_adagency/includes/js/jquery.adagency.js");
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	/*$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "' . $cpn_link . '";
			});
		});');*/
	$cpanel_home = "<div class='cpanelimg'>";
    $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
    $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
    $cpanel_home .= "</div>";
?>
<div class="ijadagencyreports" id="adagency_container">
<div class="componentheading" id="ijadagencyreports"><?php echo JText::_('REP_REPORTS'); ?><?php echo $cpanel_home; ?></div>

<div><?php echo JText::_('REP_INTRO_TEXT').'<br /><br />';?></div>

<?php include(JPATH_BASE."/components/com_adagency/includes/js/reports.php"); ?>

	<form action="<?php
		echo JRoute::_($_SERVER['REQUEST_URI']);
	?>" method="post" name="adminForm">
	<table class="adag_reports" cellpadding="0" cellspacing="5">
	<tr>
		<td valign="top" align="left">
			<?php echo JText::_('REPCAMPAIGN');?>:
		</td>
		<td valign="top" align="left">
			<?php echo $lists['cid']; ?>
		</td>
	</tr>
	<tr>
		<td valign="top" align="left">
			<?php echo JText::_('REPMTYPE');?>:
		</td>
		<td valign="top" align="left">
			<?php echo $lists['type'] ?>
		</td>
	</tr>
	<?php if ($type!='Click Detail') {?>
	<tr>
		<td valign="top" align="left">
			<?php echo JText::_('REPBREAKDOWN');?>:
		</td>
		<td valign="top" align="left">
            <input class="formField" name="chkCampaign" type="checkbox" value="1" <?php if (@$_REQUEST['chkCampaign']) { ?>checked<?php } ?> >
            <label><?php echo JText::_('REPCAMPAIGN');?></label><br />
            <input class="formField" name="chkBanner" type="checkbox" value="1" <?php if (@$_REQUEST['chkBanner']) { ?>checked<?php } ?> >
            <label><?php echo JText::_('REPBANNER');?></label><br>
            <input class="formField" name="chkDay" type="checkbox" value="1" <?php if (@$_REQUEST['chkDay']) { ?>checked<?php } ?>>
            <label><?php echo JText::_('REPDAY');?></label><br>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td nowrap><?php echo JText::_('REPSTARTDATE'); ?>:</td>
		<td><?php
					//echo "<pre>";var_dump($params);die();
					if(($params==1)||($params==7)) {$ymd="%d-%m-%Y";}
					elseif(($params==2)||($params==8)) {$ymd="%d/%m/%Y";}
					elseif(($params==3)||($params==9)) {$ymd="%m-%d-%Y";}
					elseif(($params==4)||($params==10)) {$ymd="%m/%d/%Y";}
					elseif(($params==5)||($params==11)) {$ymd="%Y-%m-%d";}
					elseif(($params==6)||($params==12)) {$ymd="%Y/%m/%d";}
					else {$ymd="%d-%m-%Y";}
					if(!isset($start_date)||($start_date==NULL)) {$start_date = date( 'Y-m-d', time() );}
					echo JHTML::_('calendar', adagencyAdminHelper::formatime($start_date, $params), 'start_date', 'start_date', ''.$ymd, array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
					?>
                	<input type="hidden" id="start_date2" value="" />
                </td>
				<td>&nbsp;&nbsp;
					<select id="adag_datepicker" name="adag_datepicker" onchange="adagsetdate(this.value)" class="inputbox">
					<option value="1" <?php if($this->adag_datepicker == "1") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_SELDATE');?></option>
					<option value="2" <?php if($this->adag_datepicker == "2") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_TODAY');?></option>
					<option value="3" <?php if($this->adag_datepicker == "3") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_YEST');?></option>
					<option value="4" <?php if($this->adag_datepicker == "4") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_LASTWK');?></option>
					<option value="5" <?php if($this->adag_datepicker == "5") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_LASTMT');?></option>
					<option value="6" <?php if($this->adag_datepicker == "6") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_LASTYR');?></option>
					<option value="7" <?php if($this->adag_datepicker == "7") { echo 'selected="selected"';}?>><?php echo JText::_('ADAG_ALLTM');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap><?php echo JText::_('REPENDDATE'); ?>:</td>
				<td>					<?php
					if(($params==1)||($params==7)) {$ymd="%d-%m-%Y";}
					elseif(($params==2)||($params==8)) {$ymd="%d/%m/%Y";}
					elseif(($params==3)||($params==9)) {$ymd="%m-%d-%Y";}
					elseif(($params==4)||($params==10)) {$ymd="%m/%d/%Y";}
					elseif(($params==5)||($params==11)) {$ymd="%Y-%m-%d";}
					elseif(($params==6)||($params==12)) {$ymd="%Y/%m/%d";}
					else {$ymd="%d-%m-%Y";}
					if(!isset($end_date)||($end_date==NULL)) {$end_date = date( 'Y-m-d', time() );}
					echo JHTML::_('calendar', adagencyAdminHelper::formatime($end_date, $params), 'end_date', 'end_date', ''.$ymd, array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
					echo "<input type='hidden' name='tfa' id='tfa_adag' value='".$params."' />";
					?>
                    <input type="hidden" id="end_date2" value="" />
				</td>
				<td>&nbsp;</td>
			</tr>
	</table>

		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="creat" />
		<input type="hidden" name="controller" value="adagencyReports" />
		<input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
	<?php if ($task=='creat') { ?>
	<TABLE width="100%">
			<TR >
			<?php foreach ($filds as $val) {
				echo '<th  style="text-align:left;">'.$val.'</th>';
			} ?>
			</TR>
			<?php
					$k=0;
					foreach ($data_row as $row) {
						?>
						<TR class="<?php echo "row$k"; ?>">
						<?php foreach ($row as $key=>$val) { ?>
									<TD  style="text-align:left;">
										<?php
										if ('ip_address'==$key) {
											echo long2ip($val);
										} elseif($key == 'entry_date'){
											echo adagencyAdminHelper::formatime($val, $params);
										} else {
											echo $val;
										}
								}?>
									</TD>
					<?php $k = 1 - $k; } ?>
						</TR>
	</TABLE>
	<?php } ?>

	<input class="agency_continue" type="button" onclick="Joomla.submitbutton('creat')" value="<?php echo JText::_('REP_CREATEREP_BTN');?>">
	</form>

	<br /><br />
	</div>
