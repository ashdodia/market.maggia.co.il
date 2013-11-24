<?php

defined ('_JEXEC') or die ("Go away.");
JHtml::_('behavior.modal', 'a.modal');

global $mainframe;
$my	= & JFactory::getUser();
$document = & JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/ad_agency.css');
$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/adagency_template.css');
$document->addScript(JURI::root().'components/com_adagency/includes/js/jquery.js');
$document->addScript(JURI::root().'components/com_adagency/includes/js/jquery.adagency.js');
$charset = 'abcdefghijklmnopqrstuvxyz';
$code = '';
$code_length=5;
for($i=0; $i < $code_length; $i++) {
    $code = $code . substr($charset, mt_rand(0, strlen($charset) - 1), 1);
}
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$advertiser = $this->advertiser;
$lists = $this->lists;
$user = $this->user;
$configs = $this->conf;
$cid = JRequest::getInt('cid');
if($cid == NULL) {$cid = 0;}
$xx = rand(1,5); $yy = rand(1,5);
$configs->xx = $xx; $configs->yy = $yy;
$_SESSION['ADAG_CALC'] = NULL;
if($cid!=$my->id && $my->id)

    //$mainframe->redirect(JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.$my->id.$Itemid), '');
    $getUser= JRequest::getVar('user', NULL, 'get');
    $getTask= JRequest::getVar('task', NULL, 'get');
    $getStatus= JRequest::getVar('status', NULL, 'get');

    if (isset($getUser)) $reg=$getUser;
        else $reg="";
    if (isset($getTask)) $editpf=$getTask;
        else $editpf="";
    if (isset($getStatus)) $statusadv=$getStatus;
        else $statusadv="";
    if ($statusadv=="pending") { echo _JAS_LOGIN_FAILED_MSG ;}
if ($advertiser->aid==0) {
    if(isset($_SESSION['ad_company'])) $advertiser->company = $_SESSION['ad_company'];
    if(isset($_SESSION['ad_description'])) $advertiser->description = $_SESSION['ad_description'];
    if(isset($_SESSION['ad_approved'])) $advertiser->approved = $_SESSION['ad_approved'];
    if(isset($_SESSION['ad_enabled'])) $user->block = $_SESSION['ad_enabled'];
    if(isset($_SESSION['ad_username'])) $user->username = $_SESSION['ad_username'];
    if(isset($_SESSION['ad_email'])) $user->email = $_SESSION['ad_email'];
    if(isset($_SESSION['ad_name'])) $user->name = $_SESSION['ad_name'];
    if(isset($_SESSION['ad_website'])) $advertiser->website = $_SESSION['ad_website'];
    if(isset($_SESSION['ad_address'])) $advertiser->address = $_SESSION['ad_address'];
    if(isset($_SESSION['ad_city'])) $advertiser->city = $_SESSION['ad_city'];
    if(isset($_SESSION['ad_zip'])) $advertiser->zip = $_SESSION['ad_zip'];
    if(isset($_SESSION['ad_telephone'])) $advertiser->telephone = $_SESSION['ad_telephone'];
    if(isset($_SESSION['toagreecond'])) $advertiser->agreecond = 1;

    if($my->id>0){
        $user->email=$my->email;
        $user->username=$my->username;
        $user->name = $my->name;
    }
}
?>

<?php
	if(isset($advertiser->aid)&&($advertiser->aid > 0)) {
        $cpanel_home = "<div class='cpanelimg'>";
        $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
        $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
        $cpanel_home .= "</div>";
    } else { $cpanel_home = ""; }
?>

<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers.php"); ?>
<div id="adagency_container" class="ijadagencyadvertisers">
<div class="componentheading" id="ijadagencyadvertisers">
<?php
	if ($my->id > 0) { echo JText::_('VIEWADVERTISER_MY_PROFILE'); } else { echo JText::_('VIEWADVERTISER_ADVERTISE'); }
	echo $cpanel_home;
?></div>
<div><?php if ($advertiser->aid == 0) echo JText::_('VIEWADVERTISER_INTRO_TEXT').'<br /><br />'; ?></div>
 <form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid='.$advertiser->user_id.$Itemid)?>" method="post" name="adminForm" id="adminForm">


    <table class="content_table" width="100%">
		<tr>
			<td class="agency_subtitle" colspan="2">
			<?php echo JText::_('ADAG_BASIC_INFO'); ?>
			</td>
		</tr>
		<tr>
			<td width="30%" class="agency_label">
			<?php echo JText::_('VIEWADVERTISERCONTACT'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" size="25" maxlength="50" value="<?php echo $user->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="agency_label">
			<?php echo JText::_('VIEWADVERTISEREMAIL');?>:<font color="#ff0000">*</font>
			</td>
			<td>
				<input class="inputbox" type="text" name="email" size="25" maxlength="100"
				<?php
						if (($advertiser->aid > 0)||($my->id>0)) echo ' readonly disabled ';
				?>
				value="<?php echo $user->email; ?>" />
			</td>
		</tr>

		<?php if(isset($configs->show)&&(in_array('phone',$configs->show))) { ?>
		<tr>
			<td class="agency_label">
                <?php echo JText::_('VIEWADVERTISERPHONE'); ?>:
                <?php if(isset($configs->mandatory)&&(in_array('phone',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
			</td>
			<td>
                <input class="inputbox" type="text" name="telephone" size="25"
                    maxlength="20" value="<?php echo $advertiser->telephone; ?>" />
			</td>
		</tr>
        <?php } ?>

		<?php if(isset($configs->show)&&(in_array('url',$configs->show))) { ?>
		<tr>
			<td class="agency_label">
                <?php echo JText::_('VIEWADVERTISERURL'); ?>:
                <?php if(isset($configs->mandatory)&&(in_array('url',$configs->mandatory))) { ?>
                    <font color="#ff0000">*</font>
                <?php } ?>
			</td>

			<td>
			<input class="inputbox" type="text" name="website" size="25" maxlength="255" value="<?php echo $advertiser->website?$advertiser->website:'http://'; ?>" />
			</td>
		</tr>
        <?php } ?>

		<?php if($advertiser->aid > 0) { ?>
		<tr>
			<td class="agency_label">
				<?php echo JText::_('ADAG_TIMEF'); ?>:
			</td>

			<td>
				<select name="fax">
					<option value="10" <?php if(isset($advertiser->fax)&&($advertiser->fax==10)) { echo 'selected="selected"';}?>>mm/dd/yyyy</option>
					<option value="9" <?php if(isset($advertiser->fax)&&($advertiser->fax==9)) { echo 'selected="selected"';}?>>mm-dd-yyyy</option>
					<option value="7" <?php if(isset($advertiser->fax)&&($advertiser->fax==7)) { echo 'selected="selected"';}?>>dd-mm-yyyy</option>
					<option value="8" <?php if(isset($advertiser->fax)&&($advertiser->fax==8)) { echo 'selected="selected"';}?>>dd/mm/yyyy</option>
					<option value="11" <?php if(isset($advertiser->fax)&&($advertiser->fax==11)) { echo 'selected="selected"';}?>>yyyy-mm-dd</option>
					<option value="12" <?php if(isset($advertiser->fax)&&($advertiser->fax==12)) { echo 'selected="selected"';}?>>yyyy/mm/dd</option>
					<option value="1" <?php if(isset($advertiser->fax)&&($advertiser->fax==1)) { echo 'selected="selected"';}?>>dd-mm-yyyy hh:mm:ss</option>
					<option value="2" <?php if(isset($advertiser->fax)&&($advertiser->fax==2)) { echo 'selected="selected"';}?>>dd/mm/yyyy hh:mm:ss</option>
					<option value="3" <?php if(isset($advertiser->fax)&&($advertiser->fax==3)) { echo 'selected="selected"';}?>>mm-dd-yyyy hh:mm:ss</option>
					<option value="4" <?php if(isset($advertiser->fax)&&($advertiser->fax==4)) { echo 'selected="selected"';}?>>mm/dd/yyyy hh:mm:ss</option>
					<option value="5" <?php if(isset($advertiser->fax)&&($advertiser->fax==5)) { echo 'selected="selected"';}?>>yyyy-mm-dd hh:mm:ss</option>
					<option value="6" <?php if(isset($advertiser->fax)&&($advertiser->fax==6)) { echo 'selected="selected"';}?>>yyyy/mm/dd hh:mm:ss</option>

				</select>
			</td>
		</tr>
		<?php } ?>

				<tr>
			<td class="agency_subtitle" colspan="2">
			 <?php echo JText::_('ADAD_LOGIN_INFO') ?>
			</td>
		</tr>
		<tr>
			<td class="agency_label">
			<?php echo JText::_('VIEWADVERTISERLOGIN');?>:<?php if($my->id<1) {?><font color="#ff0000">*</font><?php } ?>
			</td>
			<td><?php if((isset($advertiser->aid)&&($advertiser->aid>0))||(isset($my->id)&&($my->id>0))) { ?>
				<font color="#FF0000"><strong><?php echo $my->username; ?></strong></font>
			<?php } else { ?>
			<input class="inputbox" type="text" name="username" size="25" maxlength="25" value="<?php echo $user->username; ?>" />
			<?php } ?>
			</td>
		</tr>

		<?php //if((isset($advertiser->aid)&&($advertiser->aid>0))||(isset($my->id)&&($my->id>0))) {
			if(!isset($my->id)||($my->id<=0)) {
		?>
		<tr>
			<td class="agency_label">
			<?php echo JText::_('ADAG_NEWPASS'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input class="inputbox" id="newpswd" type="password" name="password" size="25" maxlength="100" value="" />
			</td>
		</tr>
		<tr>
			<td nowrap class="agency_label">
			<?php echo JText::_('VIEWADVERTISERPASS2'); ?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input class="inputbox" id="newpswd2" type="password" name="password2" size="25" maxlength="100" value="" />
			</td>
		</tr>
		<?php } ?>

		<?php if(isset($configs->show)&&(in_array('company',$configs->show))) { ?>
		<tr>
			<td class="agency_subtitle" colspan="2">
			<?php echo JText::_('VIEWADVERTISERINFO'); ?>
			</td>
		</tr>
		<tr>
			<td class="agency_label">
			<?php echo JText::_('VIEWADVERTISERCOMPNAME'); ?><?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="company" size="25" maxlength="100" valign="top" value="<?php echo $advertiser->company; ?>" />
			</td>
		</tr>
		<tr>
			<td class="agency_label">
			<?php echo JText::_('VIEWADVERTISERDESC');?><?php if(isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
		<TEXTAREA NAME="description" ROWS="3" COLS="50"><?php echo $advertiser->description;?></TEXTAREA>
			<br>
			</td>
		</tr>
		<?php } ?>

	<?php if(isset($configs->show)&&(in_array('address',$configs->show))) { ?>
		<tr>
			<td class="agency_subtitle" colspan="2">
   			<?php echo JText::_('VIEWADVERTISERINFO3');?>
			</td>
		</tr>

<?php include(JPATH_BASE."/components/com_adagency/includes/js/advertisers_country.php"); ?>

    <tr>
      <td class="agency_label"><?php echo JText::_("VIEWADVERTISERCOUNTRY");?><?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?></td>
      <td><?php echo $this->lists['country_option']; ?></td>
    </tr>
    <tr>
      <td class="agency_label"><?php echo JText::_("VIEWADVERTISERPROV");?><?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?></td>
      <td> <?php
		echo $this->lists['customerlocation'];
?>
      </td>
		<tr>
			<td class="agency_label">
		     <?php echo JText::_('ADAG_CITY'); ?><?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="city" size="40" maxlength="100" value="<?php echo $advertiser->city; ?>" />
			</td>
		</tr>
    </tr>
		<tr>
			<td class="agency_label">
		     <?php echo JText::_('VIEWADVERTISERADDRESS'); ?><?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>:<font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="address" size="40" maxlength="100" value="<?php echo $advertiser->address; ?>" />
			</td>
		</tr>
    </tr>
			<td class="agency_label">
     <?php echo JText::_('VIEWADVERTISERZIP'); ?><?php if(isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) { ?>::<font color="#ff0000">*</font><?php } ?>
			</td>
			<td>
			<input class="inputbox" type="text" name="zip" size="12" maxlength="12" value="<?php echo $advertiser->zip; ?>" />
			</td>
		</tr>
		<?php } ?>

		<?php if(isset($configs->show)&&(in_array('email',$configs->show))) { ?>
		<tr>
			<td class="agency_subtitle" width="10%" colspan="2">
			  <?php echo JText::_('VIEWADVERTISEREMAILOPT')?>
			</td>
		</tr>

		<tr>
			<td>&nbsp;

			</td>
			<td>
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_daily_report=='Y') echo 'checked' ?> NAME="email_daily_report" value="Y">
				<?php echo JText::_('VIEWADVERTISERDAY'); ?><br />
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_weekly_report=='Y') echo 'checked' ?> NAME="email_weekly_report" value="Y">
				<?php echo JText::_('VIEWADVERTISERWEEK'); ?><br />
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_month_report=='Y') echo 'checked' ?> NAME="email_month_report" value="Y">
				<?php echo JText::_('VIEWADVERTISERMONTH'); ?><br />
				<INPUT  TYPE="checkbox" <?php if ($advertiser->email_campaign_expiration=='Y') echo 'checked' ?> NAME="email_campaign_expiration" value="Y">
				<?php echo JText::_('VIEWADVERTISEREXP'); ?>
			</td>
		</tr>
		<?php } ?>

		<?php
			if ($advertiser->aid==0) {
				if($my->id<1) {
					if(isset($configs->show)&&(in_array('calculation',$configs->show))) {
						$_SESSION['ADAG_CALC'] = $xx+$yy;
		?>
		<tr>
			<td class="agency_subtitle" colspan="2">
			  <?php echo JText::_('ADAG_VERIFY_HUMAN'); ?>
			</td>
		</tr>

		<tr>
		<td class="agency_label"><?php echo JText::_("ADAG_MATHCALC");?> <?php echo $xx; ?>+<?php echo $yy; ?> ? :<font color="#ff0000">*</font></td>
		<td><input class="inputbox" type="text" name="calculation" id="verif_human" size="6" maxlength="6" value="" />
		</td>
		</tr>
        <?php } // end verify human
					if(isset($configs->show)&&(in_array('captcha',$configs->show))) {
		?>
		<tr>
			<td class="agency_subtitle" colspan="2">
			  <?php echo JText::_('ADAG_DSC_CAPTCHA')?>
			</td>
		</tr>

		<tr>
		<td class="agency_label"><?php echo JText::_("JS_CAPTCHA");?>:<font color="#ff0000">*</font></td>
		<td><input class="inputbox" type="text" name="captcha" size="12" maxlength="6" value="" />&nbsp;&nbsp;
		<span id="cptka2"><img align="absbottom" alt="code" id="cptka" src="<?php echo JURI::root()."components/com_adagency/views/adagencyadvertisers/tmpl/captcha.php?code=".$code;?>" /></span>&nbsp;&nbsp;<input type="hidden" id="cptchd" value="<?php echo $code; ?>" />
		<?php if(isset($configs->show)&&(in_array('refresh',$configs->show))) { ?><img align="absbottom" style="cursor:pointer;" alt="refresh_captcha" onClick="refreshcaptcha()" src="<?php echo JURI::root()."components/com_adagency/images/recaptcha_refresh.png";?>" /><?php } ?>
		</td>
		</tr>
        <?php } // end captcha
			} // end my->id<1
		} // end advertiser->aid ==0
		if (($configs->askterms == '1')&&(!isset($advertiser->aid)||($advertiser->aid<1))) { ?>
        <tr>
        <!--<td></td>-->
        	<td colspan="2" align="left">
        		<input type="checkbox" name="agreeterms" <?php if(isset($advertiser->agreecond)) { echo " checked='checked' ";}?>/>
				<input type="hidden" name="checkagreeterms" value="1" />
				
                <a rel="{handler: 'iframe', size: {x: 600, y: 400}}" class="modal" href="index.php?option=com_content&view=article&id=<?php echo $configs->termsid;/*.$Itemid*/ ?>&tmpl=component"><?php echo JText::_("VIEWADVERTISERAGREETERMS");?></a>
                
        	</td>
        </tr>
		<?php }else{?>
        <tr>
        <!--<td></td>-->
        	<td colspan="2" align="left">
        		<input type="hidden" name="checkagreeterms" value="0" />
        	</td>
        </tr>
		<?php } ?>

		<tr>
			<td colspan="3">
			</td>
		</tr>
   </table>

   <input style="float: left;" type="button" class="agency_cancel" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_CANCEL'); ?>" />

	<INPUT style="float: left;margin-left: 5px;" class="agency_continue" TYPE="button" onclick="Joomla.submitbutton('save')" value="<?php if ($advertiser->aid==0) {
				echo JText::_("ADAG_CREATE_ACCOUNT");
			} else {
				echo JText::_("AD_SAVE");
			}
			echo " >";
	?>">
<br /><br />
			<?php if($my->id == 0) { echo JHTML::_( 'form.token' ); } ?>
			<input name="is_already_registered" id="is_already_registered" value="<?php echo $my->id;?>" type="hidden" />
	        <input type="hidden" name="option" value="com_adagency" />
	        <input type="hidden" name="aid" value="<?php echo $advertiser->aid; ?>" />
	        <input type="hidden" name="block" value="<?php echo $my->block; ?>" />
	        <input type="hidden" name="user_id" value="<?php echo $advertiser->user_id; ?>" />
	        <input type="hidden" name="lastreport" value="<?php if (isset($advertiser->lastreport)) echo $advertiser->lastreport; else echo time(); ?>" />
	        <input type="hidden" name="weekreport" value="<?php if (isset($advertiser->weekreport)) echo $advertiser->weekreport; else echo time(); ?>" />
	        <input type="hidden" name="monthreport" value="<?php if (isset($advertiser->monthreport)) echo $advertiser->monthreport; else echo time(); ?>" />
	        <input type="hidden" name="task" value="save" />
		    <input type="hidden" name="controller" value="adagencyAdvertisers" />
        </form>
</div>
