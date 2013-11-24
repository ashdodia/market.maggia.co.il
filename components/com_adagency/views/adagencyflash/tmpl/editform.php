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
$banners_camps=$this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }
$data = $this->data;
$realimgs = $this->realimgs;
$camps = $this->camps;
$lists = $this->lists;
$_row=$this->ad;
$nullDate = 0;
$czones = $this->czones;
$advertiser_id = $this->advertiser_id;
$configs = $this->configs;
$current = $this->channel;
$document =& JFactory::getDocument();
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

require_once('components/com_adagency/helpers/geo_helper.php');
include_once(JPATH_BASE."/components/com_adagency/includes/js/flash.php");
if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}

require_once('components/com_adagency/includes/js/flash_geo.php');

if (!isset($type)) $type='cpm';
if (!isset($package->type)) $package->type=$type;
$cpanel_home = "<div class='cpanelimg'>";
$cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
$cpanel_home .= "</div>";

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");

foreach($realimgs as $k=>$v) {	$realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]"; }
$realimgs = implode(",\n", $realimgs);
?>

		<div class="ijadagencyflash" id="adagency_container">
        <p id="hidden_adagency">
            <a id="change_cb">#</a><br />
            <a id="close_cb">#</a>
        </p>
		<div class="componentheading" id="ijadagencyflash">
			<h1><?php echo JText::_('VIEWTREEADDFLASH'); ?></h1>
			<?php echo $cpanel_home; ?>
		</div>
		<?php
            if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
                //echo "<div class='adag_pending'>".JText::_('ADAG_STATUS_CHANGE_WARNING')."</div>";
            }
        ?>
		<form action="<?php JRoute::_('index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]='.$_row->id);?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <table border="0" width="100%" class="adminform">
		<tr>
            <td colspan="2" class="sectiontableheader">
				<span class="agency_subtitle"><?php echo  JText::_('NEWADDETAILS'); ?></span>
			</td>
		</tr>

		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADTITLE');?></span>:<font color="#ff0000">*</font>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" name="title" id="flash_title" style="width: 99%;" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
			</td>
		</tr>

		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADDESCTIPTION');?></span>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" name="description" id="flash_description" style="width: 99%;" value="<?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?>" />
			</td>
		</tr>
		<tr>
			<td><span class="agency_label"><?php echo JText::_('NEWADTARGET');?></span>:<font color="#ff0000">*</font></td>
			<td><input class="formField agency_textbox" type="text" name="target_url" id="flash_url" style="width: 99%;" maxlength="200" value="<?php if ($_row->target_url!="") {echo $_row->target_url;} else {echo "http://";} ?>" >
			</td>
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

		<table class="adminform" width="100%">
		<tr>
			<td colspan="2" class="sectiontableheader">
				<span class="agency_subtitle">
					<?php echo JText::_('NEWADIMAGE'); ?>
				</span>
			</th>
		</tr>

		<tr>
			<td valign="top">
				<span class="agency_label"><?php echo JText::_('NEWADSWFUPLOADIMG'); ?></span>
			</td>
			<td><input type="file" name="image_file" size="20">
            	<!-- style="background-image:url(<?php echo JURI::root()."components/com_adagency/images/";?>upload_button.png);" class="uplbtn" -->
				<input type="submit" value="<?php echo JText::_('ADAG_UPLOAD');?>" onclick="return UploadImage();">
				<input type="hidden" name="swf_url" id="flash_swf" value="<?php if(isset($_row->swf_url)) echo $_row->swf_url;?>" />
				<script  language="javascript" type="text/javascript">
					function UploadImage() {
						var fileControl = document.adminForm.image_file;
						var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
						if (thisext != ".swf" && thisext != ".SWF")
						{ alert('<?php echo 'File must be a swf.';?>');
						  return false;
						}
						if (fileControl.value) {
							document.adminForm.task.value = 'upload';
							return true;
						}
							return false;
						}
				</script>
				<!-- <br /> -->
				<span class="agency_comment">
				<?php echo JText::_('NEWADSWFUPLOADIMG2'); ?>
				</span>
			</td>
		</tr>

		<tr>
			<td valign="top">
				<span class="agency_label"><?php echo JText::_('NEWADSWFPREVIEW');?></span>:
			</td>
			<td valign="top">
				<span id="swf_file">

						<?php if ($_row->swf_url!="") { ?>
						<OBJECT id="flash_ad_obj" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ID="banner" <?php echo "WIDTH='" . $_row->width . "' HEIGHT='" . $_row->height . "'" ?>>
						<PARAM NAME="movie" VALUE="<?php echo $lists['flash_directory'].$_row->swf_url; ?>?link=&window=_self">
						<param name="wmode" value="transparent">
						<PARAM NAME="quality" VALUE="high">
						<EMBED id="flash_ad_embed" SRC="<?php echo $lists['flash_directory'].$_row->swf_url; ?>?link=&window=_self" <?php echo "WIDTH='" . $_row->width . "' HEIGHT='" . $_row->height . "'" ?> QUALITY="high" wmode="transparent" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
						</EMBED>
						</OBJECT>
						<?php } ?>
						</span>

				<input class="formField agency_textbox" type="text" name="width" id="flash_width" size="3"	value="<?php if ($_row->width>0) {echo $_row->width;} else {echo "";} ?>" /> x
				<input class="formField agency_textbox" type="text" name="height" id="flash_height" size="3"	value="<?php if ($_row->height>0) {echo $_row->height;} else {echo "";} ?>" />
				<span class="agency_comment"><?php echo JText::_('NEWADSIZE'); ?></span>
			</td>
		</tr>

		<?php /*
		<tr>
			<td width="25%">
				<!--<span class="agency_label"><?php echo JText::_('NEWADSIZE'); ?></span>:<font color="#ff0000">*</font>-->
			</td>

			<td width="75%" valign="top">
			</td>
		</tr>
		*/ ?>

		</table>

        <?php
			output_geoform($advertiser_id);
			require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
			JomSocialTargeting::render_front($_row->id);				
		?>
		<input type="hidden" id="flashMarker" />

			<?php if ($camps) {
				$i=0;?>
		<table class="adminform" width="100%" id="flashCampaigns">
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
		<?php } ?>
		</table>
	<?php } else {
    ?>
        <table class="adminform" id="campaign_table" width="100%">
            <tr>
                <td width="100%" colspan="2" class="sectiontableheader">
					<span class="agency_subtitle"><?php echo JText::_('NEWADCMPS'); ?></span>
                </td>
            </tr>
        </table>
    <?php
        $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
        /*echo "<div class='adag_pending'>" .
            JText::sprintf('ADAG_NO_CAMP_STDRD', $url_to_camps )
            . "</div>";*/
        }
    ?>

	    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id; ?>" />
		<input type="hidden" name="media_type" value="Flash" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="controller" value="adagencyFlash" />
		<?php ///if (!$_row->id) { ?>
		<input type="hidden" name="parameters['border']" value="<?php echo @$_row->parameters['border']; ?>" />
		<input type="hidden" name="parameters['border_color']" value="<?php echo @$_row->parameters['border_color']; ?>" />
		<input type="hidden" name="parameters['bg_color']" value="<?php echo @$_row->parameters['bg_color']; ?>" />
		<?php //} ?>
        <input style="float: left;" class="agency_cancel" type="button" onclick="<?php
			if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyFlash")<1)) {
				$_SESSION['flashAdReferer'] = $_SERVER['HTTP_REFERER'];
			} elseif ( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['flashAdReferer']) ) {
				$_SESSION['flashAdReferer'] = '#';
			}

			echo "document.location = '".$_SESSION['flashAdReferer']."';";
		?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		<input style="float:left;margin-left: 5px;" class="agency_continue" type="button" value="<?php echo JText::_("AD_SAVE"); ?>" onclick="Joomla.submitbutton('save');">
		</form>
</fieldset>
</div>
