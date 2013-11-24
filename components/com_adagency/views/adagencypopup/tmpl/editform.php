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
$configs = $this->configs;
$current = $this->channel;
$data = $this->data;
$realimgs = $this->realimgs;
$advertiser_id = $this->advertiser_id;
$camps = $this->camps;
$lists = $this->lists;
$_row = $this->ad;
$_row2 = $this->ad2;
$czones = $this->czones;
$nullDate = 0;
$editor1  = & JFactory::getEditor();
$banners_camps = $this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }

$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
include_once(JPATH_BASE."/components/com_adagency/includes/js/popup.php");
require_once('components/com_adagency/helpers/geo_helper.php');

if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}
include_once(JPATH_BASE."/components/com_adagency/includes/js/popup_geo.php");

$lists['image_directory']=substr($lists['image_directory'],0,-1);
$lists['image_directory']= JURI::base().$lists['image_directory'];

if (!isset($_row->parameters['ad_code'])) $_row->parameters['ad_code']='';
if (!isset($_row->parameters['window_width'])) $_row->parameters['window_width']='';
if (!isset($_row->parameters['window_height'])) $_row->parameters['window_height']='';
if (!isset($_row->parameters['show_ad'])) $_row->parameters['show_ad']='0';
if (!isset($_row->parameters['show_on'])) $_row->parameters['show_on']='';

$cpanel_home = "<div class='cpanelimg'>";
$cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
$cpanel_home .= "</div>";

	foreach($realimgs as $k=>$v)
			$realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
			$realimgs = implode(",\n", $realimgs);
?>
		<div class="ijadagencypopup" id="adagency_container">
        <p id="hidden_adagency">
            <a id="change_cb">#</a><br />
            <a id="close_cb">#</a>
        </p>
		<div class="componentheading" id="ijadagencypopup">
			<h1><?php echo JText::_('VIEWTREEADDPOPUP'); ?></h1>
			<?php echo $cpanel_home; ?>
		</div>
        <?php
			if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
				//echo "<div class='adag_pending'>".JText::_('ADAG_STATUS_CHANGE_WARNING')."</div>";
			}
		?>
		<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid='.$_row->id);?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<table border="0" width="100%" class="content_table">
		<tr>
			<td colspan="2" class="sectiontableheader">
				<span class="agency_subtitle"><?php echo  JText::_('NEWADDETAILS'); ?></span>
			</td>
		</tr>
		<tr>
			<td width="25%" nowrap>
				<span class="agency_label"><?php echo JText::_('ADAG_CHOOSE_POP');?></span>:<font color="#ff0000">*</font>
			</td>
			<td width="75%">
				<?php echo $lists['type'];?>
			</td>
		</tr>
		<tr>
			<td width="25%" nowrap>
				<span class="agency_label"><?php echo JText::_('NEWADTITLE');?></span>:<font color="#ff0000">*</font></td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="popup_title" name="title" style="width: 99%;" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
			</td>
		</tr>

		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADDESCTIPTION');?></span>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="popup_description" name="description" style="width: 99%;" value="<?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?>" />
			</td>
		</tr>

		<?php if ('html'==$_row->parameters['popup_type']) { ?>

		<tr>
			<td colspan="2"><input class="formField" type="hidden" name="target_url" style="width: 99%;" maxlength="200" value="URL" ></td>
		</tr>
		<?php }  else
		if ('webpage'==$_row->parameters['popup_type']) { ?>
		<tr>
			<td><span class="agency_label"><?php echo JText::_('JAS_TARGETURL'); ?></span>:<font color="#ff0000">*</font></td>

			<td><input class="formField agency_textbox" type="text" id="popup_pageurl" name="parameters[page_url]" style="width: 99%;" maxlength="200" value="<?php echo (@$_row->parameters['page_url']) ? $_row->parameters['page_url'] : 'http://'; ?>">
			</td>
		</tr>
		<?php }

		if ('image'==$_row->parameters['popup_type']) { ?>

		<tr>
			<td nowrap><span class="agency_label"><?php echo JText::_('JAS_TARGETURL');?></label>:<font color="#ff0000">*</font></td>
			<td><input class="formField agency_textbox" type="text" name="target_url" id="popup_targeturl_img" style="width: 99%;" maxlength="200" value="<?php if (isset($_row->target_url) && $_row->target_url!='URL') echo $_row->target_url; else echo 'http://'; ?>" ></td>
		</tr>
		<?php }

		if ('image'==$_row->parameters['popup_type']) { ?>
		</table>
		<table class="content_table" width="100%">
		<tr>
			<td colspan="2" class="sectiontableheader">
				<span class="agency_subtitle">
				<?php echo "Image"?>
				</span>
			</td>
		</tr>

		<tr>
			<td width="25%" nowrap><span class="agency_label"><?php echo JText::_('JAS_UPLOADIMAGE'); ?></span>:</td>
			<td width="75%"><input type="file" name="image_file" size="37"><!--  style="background-image:url(<?php echo JURI::root()."components/com_adagency/images/";?>upload_button.png);" class="uplbtn" -->
				<input type="submit" value="<?php echo JText::_('ADAG_UPLOAD');?>" onclick="return UploadImage();">
				<input type="hidden" name="image_url" id="popup_imageurl" value="<?php if(isset($_row->image_url)){echo $_row->image_url;}?>" />

				<?php include(JPATH_BASE."/components/com_adagency/includes/js/popup_upl_img.php"); ?>
			</td>
		</tr>

		<tr>
			<td valign="top">
				<span class="agency_label">
				<?php echo JText::_('JAS_IMGPREVIEW');?>:
				</span>
			</td>
			<td valign="top">
				<?php if (!isset($_row->image_url)) { //$_row->id?>
					<div id="imgdiv" style="display: block;">
					<img src="images/blank.png" name="imagelib" />
					</div>
					<?php } else { ?>
					<div id="imgdiv" style="display: block;">
					<img src="<?php echo $lists['image_directory']."/".$_row->image_url; ?>" name="imagelib" />
					</div>
					<?php } ?>
			</td>
		</tr>

		<?php 
			if ($_row->image_url) {
				$fileimg = JPATH_SITE.$lists['image_path'].$_row->image_url;
				$my_image = @getimagesize($fileimg);
				list($width, $height) = $my_image;
				$_row->width = $width;
				$_row->height = $height; }
			?>
			<input readonly="" class="formField"  type="hidden" name="width" size="3"	value="<?php echo @$_row->width; ?>" />
			<input readonly="" class="formField" type="hidden" name="height" size="3" value="<?php echo @$_row->height; ?>" />
		<?php }
			if ('html'==$_row->parameters['popup_type']) {
		?>
		<tr>
			<td width="25%" nowrap>
				<span class="agency_label"><?php echo JText::_('JAS_HTMLCONTENT'); ?></span>:<font color="#ff0000">*</font>
			</td>
			<td width="75%">
				<br />
		<?php
			if (isset($_row->parameters['html']))
				echo $editor1->display( 'parameters[html]', html_entity_decode(stripslashes($_row->parameters['html'])),'100%', '300px', '20', '40',false );
			else
				echo $editor1->display( 'parameters[html]', '','100%', '300px', '20', '40',false );
		?>
			</td>
		</tr>
		<?php } ?>
        
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

        <?php
			output_geoform($advertiser_id);
			require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
			JomSocialTargeting::render_front($_row->id);	
		?>

		<?php if ($camps) {
				$i=0;?>
		<table class="content_table" id="campaign_table" width="100%">
		<tr>
			<td class="sectiontableheader" colspan="2">
				<span class="agency_subtitle"><?php echo JText::_('NEWADCMPS'); ?></span>
			</td>
		</tr>
		<?php
			$displayed = array();
			foreach ($camps as $camp) {
				if(!isset($czones[$camp->id])){
					continue;
				}
				
				if(in_array($camp->id,$displayed)) { continue; }
				$displayed[] = $camp->id;
				$i++;
		?>
		<tr style="width: 35%;">
        	<td class="check_camp">
                <input class="formField camp<?php echo $camp->id; ?>" <?php
                    if(@in_array($camp->id,$banners_camps)) { echo 'checked="checked"';}
                    ?> type="checkbox" id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>" />
                <label><?php echo $camp->name; ?></label>
			</td>
            <td align="left" class="check_ad">
                <?php echo JText::_('NEWADZONE');?>: <?php echo $czones[$camp->id]; ?>
            </td>
        </tr>
		<?php }
	?>    
    </table>
	<?php } ?>
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="parameters['window_width']" value="<?php if(isset($_row->parameters['window_width'])) { echo $_row->parameters['window_width']; } ?>" />
		<input type="hidden" name="parameters['window_height']" value="<?php if(isset($_row->parameters['window_height'])) { echo $_row->parameters['window_height']; } ?>" />
		<input type="hidden" name="show_on" value="<?php if(isset($_row->parameters['show_on'])) { echo $_row->parameters['show_on']; }?>" />
		<input type="hidden" name="show_ad" value="<?php if(isset($_row->parameters['show_ad'])) { echo $_row->parameters['show_ad']; }?>" />
		<input type="hidden" name="media_type" value="Popup" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
		<input type="hidden" name="controller" value="adagencyPopup" />
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />

        <input type="hidden" name="bg_color" value="<?php if(isset($_row->parameters['bg_color'])) echo $_row->parameters['bg_color'];?>" />
		<?php //} ?>
        <input style="float: left;" class="agency_cancel" type="button" onclick="<?php
			if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyPopup")<1)) {
				$_SESSION['popupAdReferer'] = $_SERVER['HTTP_REFERER'];
			} elseif ( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['popupAdReferer']) ) {
				$_SESSION['popupAdReferer'] = '#';
			}

			echo "document.location = '".$_SESSION['popupAdReferer']."';";
		?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		<input style="float: left;margin-left: 5px;" class="agency_continue" type="button" value="<?php echo JText::_('AD_SAVE');?>" onclick="Joomla.submitbutton('save');">
		</form>
		<br /><br />
</div>
