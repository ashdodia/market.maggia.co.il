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
$configs->params = @unserialize($configs->params);
$current = $this->channel;
$banners_camps = $this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }
$czones = $this->czones;
$data = $this->data;
$camps = $this->camps;
$lists = $this->lists;
$_row = $this->ad;
$max_chars = $this->max_chars;
$imgInfo = $this->imgInfo;
if(isset($imgInfo[0])) { $img_w = $imgInfo[0]; } else { $img_w = NULL; }
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$document =& JFactory::getDocument();
$url = JURI::base()."components/com_adagency/includes/css/ad_agency.css";
$document->addStyleSheet($url);
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/textlink.css.php?bid=".$_row->id);
$document->addScript(JURI::root()."components/com_adagency/includes/js/serialize_unserialize.js");
include_once(JPATH_BASE."/components/com_adagency/includes/js/textlink.php");
require_once('components/com_adagency/helpers/geo_helper.php');
if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}
include_once(JPATH_BASE."/components/com_adagency/includes/js/textlink_geo.php");
if(isset($_row->parameters["alt_text"])) {$_row->parameters["alt_text"]=str_replace("\"","'",$_row->parameters["alt_text"]);}
$nullDate = 0;
$advertiser_id = $this->advertiser_id;
$post_vars = JRequest::get( 'post' );
if (!isset($type)) $type='cpm';
if (!isset($package->type)) $package->type=$type;

$realimgs = $this->realimgs;
$lists['image_directory'] = substr($lists['image_directory'],0,-1);
$lists['image_directory'] = JURI::base().$lists['image_directory'];

if (!isset($type)) $type = 'cpm';
if (!isset($package->type)) $package->type=$type;

foreach($realimgs as $k=>$v) $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
$realimgs = implode(",\n", $realimgs);

$cpanel_home = "<div class='cpanelimg'>";
$cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
$cpanel_home .= "</div>";
?>

	<div class="ijadagencytextlink" id="adagency_container">
    <p id="hidden_adagency">
        <a id="change_cb">#</a><br />
        <a id="close_cb">#</a>
    </p>
	<div class="componentheading" id="ijadagencytextlink">
		<h1><?php echo JText::_('VIEWTREEADDTEXTLINK');  ?></h1>
		<?php echo $cpanel_home; ?>
	</div>
    <?php
		if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
			//echo "<div class='adag_pending'>".JText::_('ADAG_STATUS_CHANGE_WARNING')."</div>";
		}
	?>
 	<form action="<?php JRoute::_( 'index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid='.$_row->id);?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<table class="content_table" border="0" width="100%">
		<tr>
			<td class="sectiontableheader" colspan="2" >
				<span class="agency_subtitle"><?php echo  JText::_('NEWADDETAILS');?></span>
			</td>
		</tr>
		<tr>
			<td width="25%" nowrap>
				<span class="agency_label"><?php echo JText::_('AD_TEXTLINK_NAME');?></span>:<font color="#ff0000">*</font>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="text_title" name="title" style="width: 99%;" value="<?php echo $_row->title;?>">
			</td>
		</tr>
		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADDESCTIPTION');?></span>:
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="text_description" name="description" style="width: 99%;" value="<?php echo $_row->description;?>" />
			</td>
		</tr>
		</tr>

		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADTARGETURL');?></span>:<font color="#ff0000">*</font>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="text_url" name="target_url" style="width: 99%;" maxlength="200" value="<?php if (!$_row->target_url) echo 'http://'; else echo $_row->target_url;?>" >
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

		<table class="content_table" width="100%">
			<tr>
				<td class="sectiontableheader" colspan="2">
					<span class="agency_subtitle">
					<?php echo JText::_('NEWADHTMLPROP');?>
					</span>
				</td>
			</tr>

			<tr>
				<td>
					<span class="agency_label"><?php echo JText::_('NEWAD_LINKTEXT_TITLE');?></span>:<font color="#ff0000">*</font>
				</td>
				<td>
					<input type="text" class="agency_textbox" name="linktitle" id="clinktitle" style="width: 99%;" onkeyup="changeLinkTitle();" value="<?php if(isset($post_vars['linktitle'])) { echo stripslashes($post_vars['linktitle']); } else {echo stripslashes(@$_row->parameters['alt_text_t']);} ?>" />
				</td>
			</tr>

			<tr>
				<td valign="top" width="25%">
					<span class="agency_label"><?php echo JText::_('NEWADLINKTEXT');?></span>:<font color="#ff0000">*</font>
				</td>
				<td>
					<input class="formField" name="parameters[alt_text]" type="hidden"/>
					<textarea class="agency_textbox" name="linktext" id="linktext" wrap="physical" style="width: 99%; height: 200px;" onkeyup="var lng=<?php echo $max_chars;?>; if (document.adminForm.linktext.value.length > lng) document.adminForm.linktext.value=document.adminForm.linktext.value.substring(0,lng); else document.adminForm.nume.value = lng-document.adminForm.linktext.value.length; changeBody();"><?php if(isset($post_vars['linktext'])) { echo stripslashes($post_vars['linktext']); } else { echo stripslashes(@$_row->parameters['alt_text']);}?></textarea>
<br /><?php echo JText::_('ADAG_CLEFT');?>:
<input type="text" class="agency_textbox" size="4" readonly="" style="color:#FF0000; font-weight:bold; background-color:transparent;border:0px solid white" value="<?php echo ($max_chars - strlen(stripslashes(@$_row->parameters['alt_text'])));?>" name="nume" id="nume" />
				</td>
			</tr>

			<tr>
				<td>
					<span class="agency_label"><?php echo JText::_('NEWAD_LINKTEXT_ACTIONTEXT');?></span>:
				</td>
				<td>
					<input class="agency_textbox" type="text" onkeyup="changeAction();" id="clinkaction" name="linkaction" style="width: 99%;" value="<?php if(isset($post_vars['linkaction'])) { echo stripslashes($post_vars['linkaction']); } else { echo stripslashes(@$_row->parameters['alt_text_a']);} ?>" />
				</td>
			</tr>
		</table>

		<?php if(isset($configs->params['showtxtimg'])&&($configs->params['showtxtimg'] == '0')) {} else {?>
		<table class="adminform" width="100%">
		<tr>
			<td class="sectiontableheader" colspan="2">
				<span class="agency_subtitle">
				<a name="adimage"><?php echo JText::_('NEWADIMAGE'); ?></a>
				</span>
			</td>
		</tr>

		<tr>
			<td nowrap valign="top">
				<span class="agency_label"><?php echo JText::_('NEWADUPLOADIMG'); ?></span>
			</td>
			<td><input type="file" name="image_file" size="17">
				<input type="submit" value="<?php echo JText::_('ADAG_UPLOAD');?>" onclick="return UploadImage();">
				<input type="hidden" name="image_url" id="text_image" value="<?php if(isset($_row->image_url)) {echo $_row->image_url;}?>" />
                <?php
					if(isset($_row->image_url)) {
						echo '<p><img src="'.$lists['image_directory']."/".$_row->image_url.'" name="imagelib23" /></p>';
					}
				?>
				<?php
					include(JPATH_BASE."/components/com_adagency/includes/js/textlink_upl.php");
				?>

			</td>
		</tr>

        <?php if (isset($imgInfo[0])&&(isset($imgInfo[1]))&&($imgInfo[0]>0)&&($imgInfo[1]>0)) {?>
        <tr>
        	<td width="25%">&nbsp;</td>
            <td><a href="#" id="remimg"><?php echo JText::_('ADAG_REMIMG');?></a></td>
        </tr>
        <?php } ?>

		</table>
        <?php } ?>

		<table class="content_table" width="100%">
			<tr>
				<td class="sectiontableheader" colspan="2">
					<span class="agency_subtitle"><?php echo JText::_('VIEWADPREVIEW'); ?></span>
				</td>
			</tr>
			<tr>
				<td width="25%">&nbsp;</td>
				<td width="75%">
                	<div style="">
                	<?php echo JText::_('ADSELZONE'); ?>:&nbsp;<select id="zoneId" onchange="callZoneSettings();return false;"><option value="0">--------</option>						<?php echo $lists['prevzones'];?></select>
                    </div><br /><br />
				<?php if (!$_row->id) { ?>
					<div id="textlink">
						<a id="tlink">
							<span id="ttitle">&nbsp;</span>
						</a>
						<br />
						<div id="imgdiv2">
							<a id="tlink2">
								<img src="<?php
								if(isset($_row->image_url)&&($_row->image_url!='')){
									echo $lists["image_directory"].'/'.$_row->image_url;
								} else {
									echo "images/blank.png";
								}?>" name="imagelib" id="rt_image" />
							</a>
						</div>
						<div id="tbody">
							<span id="ttbody">&nbsp;</span>
						</div>
						<div id="taction">
							<a id="tlink2">
								<span id="ttaction">&nbsp;</span>
							</a>
						</div>
					</div>
						<?php } else { ?>
						<?php //echo "<pre>";var_dump($_row);die(); ?>
						<div id="textlink">
						<?php
							if(isset($_row->parameters['alt_text_t'])&&($_row->parameters['alt_text_t']!="")){
								if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
									echo "<a id='tlink' href='".$_row->target_url."' ";
									if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
										echo " target='".$_row->parameters['target_window']."' ";
									}
									echo ">";
								}
								echo "<span id='ttitle'>";
								echo $_row->parameters['alt_text_t']."</span><br />";
								if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
									echo "</a>";
								}
							}
						?>
						<div id="imgdiv2">
						<?php
							if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
								$outputz="<a id='tlink2' href='".$_row->target_url."' ";
								if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
										$outputz.="target='".$_row->parameters['target_window']."' ";
								}
								$outputz.=">";
								echo $outputz;
							}?>
							<?php if(isset($_row->image_url)&&($_row->image_url!='')) { ?><img id="rt_image"
							src="<?php echo $lists['image_directory']."/".$_row->image_url; ?>" name="imagelib" id="rt_image" /><?php } ?>					 											 						<?php
							if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
								echo "</a>";
							}
						?>
						</div>
						<div id="tbody">
							<?php
								if(isset($_row->parameters['alt_text'])&&($_row->parameters['alt_text']!="")){
									echo "<div id='ttbody'>";
									echo $_row->parameters['alt_text'];
									echo "</div>";
								}
							?>
						</div>
						<div id="taction">
							<?php
								if(isset($_row->parameters['alt_text_a'])&&($_row->parameters['alt_text_a']!="")){
									if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
										$outputs="<a id='tlink2' href='".$_row->target_url."' ";
										if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
											$outputs.="target='".$_row->parameters['target_window']."' ";
										}
										$outputs.=">";
										echo $outputs;
									}
									echo "<span id='ttaction'>";
									echo $_row->parameters['alt_text_a'];
									echo "</span></a>";
								}
							?>
						</div>
						</div>
						<?php } ?>
				</td>
			</tr>             
            
		</table>

        <?php
			output_geoform($advertiser_id);
			require_once(JPATH_BASE."/administrator/components/com_adagency/helpers/jomsocial.php");
			JomSocialTargeting::render_front($_row->id);			
		?>

		<?php if ($camps) {
				$i=0;?>
		<table class="adminform" id="campaign_table" width="100%">
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
				
				if(in_array($camp->id,$displayed)) { continue; }
				$displayed[] = $camp->id;
				$i++;
		?>
		<tr style="width:35%;">
            <td class="check_camp">
                <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
                <input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
                if(in_array($camp->id,$banners_camps)) { echo 'checked="cheched"';}
                ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
                <label><?php echo $camp->name; ?></label>
            </td>
            <td align="left" class="check_ad">
                <?php echo JText::_('NEWADZONE');?>: <?php echo $czones[$camp->id]; ?>
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
            JText::sprintf('ADAG_NO_CAMP_BNR_TYPE', $url_to_camps )
            . "</div>";*/
        }
    ?>

		<input type="hidden" name="controller" value="adagencyTextlink" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
		<input type="hidden" name="media_type" value="TextLink" />
		<input type="hidden" name="parameters[alt_text]" value="<?php if(isset($_row->parameters['alt_text'])) echo $_row->parameters['alt_text'];?>" />
		<input type="hidden" name="parameters[alt_text_t]" value="<?php if(isset($_row->parameters['alt_text_t'])) echo $_row->parameters['alt_text_t'];?>" />
		<input type="hidden" name="parameters[alt_text_a]" value="<?php if(isset($_row->parameters['alt_text_a'])) echo $_row->parameters['alt_text_a'];?>" />
       	<input type="hidden" name="parameters[img_alt]" maxlength="200" value="<?php if(isset($post_vars['parameters']['img_alt'])) { echo stripslashes($post_vars['parameters']['img_alt']); } else {echo stripslashes(@$_row->parameters['img_alt']);} ?>">

        <?php if(!isset($_row->id)||($_row->id == 0)){ ?>
		<input type="hidden" name="parameters['font_family']" value="<?php if(isset($_row->parameters['font_family'])) echo $_row->parameters['font_family'];?>" />
		<input type="hidden" name="parameters['font_size']" value="<?php if(isset($_row->parameters['font_size'])) echo $_row->parameters['font_size'];?>" />
		<input type="hidden" name="parameters['font_weight']" value="<?php if(isset($_row->parameters['font_weight'])) echo $_row->parameters['font_weight'];?>" />
		<input type="hidden" name="parameters['align']" value="<?php if(isset($_row->parameters['align'])) echo $_row->parameters['align'];?>" />
		<input type="hidden" name="parameters['border']" value="<?php if(isset($_row->parameters['border'])) echo $_row->parameters['border'];?>" />
		<input type="hidden" name="parameters['border_color']" value="<?php if(isset($_row->parameters['border_color'])) echo $_row->parameters['border_color'];?>" />
		<input type="hidden" name="parameters['bg_color']" value="<?php if(isset($_row->parameters['bg_color'])) echo $_row->parameters['bg_color'];?>" />
		<input type="hidden" name="parameters['target_window']" value="<?php if(isset($_row->parameters['target_window'])) echo $_row->parameters['target_window'];?>" />
		<?php } ?>
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
        <input style="float: left;" class="agency_cancel" type="button" onclick="<?php
			if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyTextlink")<1)) {
				$_SESSION['textadAdReferer'] = $_SERVER['HTTP_REFERER'];
			} elseif ( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['textadAdReferer']) ) {
				$_SESSION['textadAdReferer'] = '#';
			}

			echo "document.location = '".$_SESSION['textadAdReferer']."';";
		?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		<input style="float: left;margin-left: 5px;" class="agency_continue" type="button" value="<?php echo JText::_("AD_SAVE"); ?>" onclick="Joomla.submitbutton('save');">
        <?php echo $lists['hidden_zones']; ?>
		</form>
<br /><br />
</div>
