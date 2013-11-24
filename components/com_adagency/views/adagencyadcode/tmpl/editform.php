<?php
defined ('_JEXEC') or die ("Go away.");
$configs = $this->configs;
$current = $this->channel;
$data = $this->data;
$camps = $this->camps;
$_row = $this->ad;
$czones = $this->czones;
$advertiser_id = $this->advertiser_id;
$nullDate = 0;
$banners_camps = $this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }

if (!isset($type)) $type='cpm';
if (!isset($package->type)) $package->type=$type;
$item_id = $this->itemid;
$item_id_cpn = $this->itemid_cpn;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$cpanel_home = "<div class='cpanelimg'>";
$cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
$cpanel_home .= "</div>";

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");

include_once(JPATH_BASE."/components/com_adagency/includes/js/adcode.php");
require_once('components/com_adagency/helpers/geo_helper.php');

if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}

require_once('components/com_adagency/includes/js/adcode_geo.php');
?>
<div class="ijadagencyadcode" id="adagency_container">
    <p id="hidden_adagency">
        <a id="change_cb">#</a><br />
        <a id="close_cb">#</a>
    </p>
<div class="componentheading" id="ijadagencyadcode">
	<h1><?php echo JText::_('VIEWTREEADDADCODE'); ?></h1><?php echo $cpanel_home; ?>
</div>

<?php
	if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
		//echo "<div class='adag_pending'>".JText::_('ADAG_STATUS_CHANGE_WARNING')."</div>";
	}
?>

 <form action="<?php echo JRoute::_( 'index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid='.$_row->id);?>" method="post" name="adminForm" id="adminForm">

	<table class="content_table" width="100%" border="0">
		<tr>
			<td colspan="2" class="sectiontableheader">
				<span class="agency_subtitle"><?php echo  JText::_('NEWADDETAILS');?></span>
			</td>
		</tr>
		<tr>
			<td width="25%" valign="top"><span class="agency_label"><?php echo JText::_('NEWADTITLE');?></span>:<font color="#ff0000">*</font></td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" name="title" id="affiliate_title" style="width: 99%;" value="<?php echo $_row->title;?>">
			</td>
		</tr>
		<tr>
			<td width="25%" valign="top">
				<span class="agency_label">
					<?php echo JText::_('NEWADDESCTIPTION');?>:
				</span>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" name="description" id="affiliate_description" style="width: 99%;" value="<?php echo $_row->description;?>" />
			</td>
		</tr>
		</tr>

<?php 
		if($configs->allow_add_keywords == 1){
?>
        	<tr>
				<td>
					<span class="agency_label"><?php echo JText::_('ADAG_KEYWORDS');?>
				</td>
				<td>
                	<input class="formField agency_textbox" type="text" id="keywords" name="keywords" style="width: 99%;" maxlength="200" value="<?php if ($_row->keywords != ""){echo $_row->keywords;} else {echo "";} ?>" >
                    <br/>
                    <span class="ad_small_text">
                    	<?php echo JText::_("ADAG_ENTER_KEYWORDS"); ?>
                    </span>
				</td>
			</tr>
<?php
		}
?>

		</table>

		<table class="content_table" width="100%" border="0">
			<tr>
				<td colspan="2" class="sectiontableheader">
					<span class="agency_subtitle"><?php echo JText::_('NEWADADCODEMSG');?></span>
				</td>
			</tr>

			<tr>
				<td width="25%" valign="top">
					<span class="agency_label"><?php echo JText::_('NEWADBANCODE');?></span>:<font color="#ff0000">*</font>
				</td>
				<td width="75%">
				<TEXTAREA class="formField agency_textbox" name="ad_code" id="affiliate_code" style="margin-left:0px;width: 99%; height: 170px;"><?php echo htmlspecialchars(stripslashes($_row->ad_code));?></TEXTAREA>
				</td>
			</tr>

			<tr>
				<td width="25%" valign="top">
					<span class="agency_label"><?php echo JText::_('ADAG_SIZE'); ?></span>:<font color="#ff0000">*</font>
				</td>
				<td width="75%">
					<input class="inputbox agency_textbox" type="text" name="width" id="affiliate_width" size="3"
						value="<?php echo $_row->width;?>" /> x
					<input class="inputbox agency_textbox" type="text" name="height" id="affiliate_height" size="3"
						value="<?php echo $_row->height;?>" />
					<span class="agency_comment">
					<?php echo JText::_('ADAG_WH22'); ?>
					</span>
				</td>
			</tr>

		</table>

        <?php
			output_geoform($advertiser_id);
			require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
			JomSocialTargeting::render_front($_row->id);			
		?>

        <input type="hidden" id="affiliateMarker" />

		<?php if($camps && is_array($camps) && count($camps) > 0){
				$i=0;?>
		<table id="affiliateCampaigns" class="adminform" width="100%" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td width="100%" colspan="2" class="sectiontableheader">
				<span class="agency_subtitle"><?php echo JText::_('NEWADCMPS'); ?></span>
			</td>
		</tr>
		<?php
			$displayed = array();
			foreach ($camps as $camp) {
				if(!isset($czones[$camp->id])){
					continue;
				}
				
				if(in_array($camp->id,$displayed)) continue;
				$displayed[] = $camp->id;
				$i++;
		?>
        
        <?php if(isset($czones) && isset($czones[$camp->id])){ ?>
        
        <tr>
            <td class="check_camp">
                <!--<input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />-->
                <input class="formField adv_cmp camp<?php echo $camp->id; ?>" type="checkbox" <?php
                    if(in_array($camp->id,$banners_camps)) { echo 'checked="cheched"';}
                    ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
                <label><?php echo $camp->name; ?></label>
            </td>
            <td align="left" class="check_ad">
                <?php echo JText::_('NEWADZONE'); ?>: <?php echo $czones[$camp->id]; ?>
            </td>
        </tr>
        
        <?php
        	}
		?>
        
		<?php } ?>
		</table>
	<?php } else {
    ?>
        <!-- <table class="adminform" id="campaign_table" width="100%">
            <tr>
                <td width="100%" colspan="2" class="sectiontableheader">
					<span class="agency_subtitle"><?php //echo JText::_('NEWADCMPS'); ?></span>
                </td>
            </tr>
        </table> -->
    <?php
        $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
        /*echo "<div class='adag_pending'>" .
            JText::sprintf('ADAG_NO_CAMP_STDRD', $url_to_camps )
            . "</div>";*/
        }
    ?>

		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
		<input type="hidden" name="media_type" value="Advanced" />
		<input type="hidden" name="controller" value="adagencyAdcode" />
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
        <input style="float: left;" class="agency_cancel" type="button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		<input style="float: left; margin-left: 5px;" class="agency_continue" type="button" value="<?php echo JText::_("AD_SAVE"); ?>" onclick="Joomla.submitbutton('save');">
		</form>
<br /><br />
</div>
