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
	$banners_camps = $this->these_campaigns;
    if (!$banners_camps) { $banners_camps = array(); }
	$data = $this->data;
	$realimgs = $this->realimgs;
	$advertiser_id = $this->advertiser_id;
	$camps = $this->camps;
	if (isset($this->cb_count)) {
		$cb_count = $this->cb_count;
	} else {
		$cb_count = 0;
	}
    //echo "<pre>";print_r($camps);die();
	$lists = $this->lists;
	$_row = $this->ad;
	
	if(isset($_SESSION["title"]) && trim($_SESSION["title"]) != ""){
		$_row->title = trim($_SESSION["title"]);
	}
	if(isset($_SESSION["description"]) && trim($_SESSION["description"]) != ""){
		$_row->description = trim($_SESSION["description"]);
	}
	if(isset($_SESSION["target_url"]) && trim($_SESSION["target_url"]) != ""){
		$_row->target_url = trim($_SESSION["target_url"]);
	}
	if(isset($_SESSION["keywords"]) && trim($_SESSION["keywords"]) != ""){
		$_row->keywords = trim($_SESSION["keywords"]);
	}
	unset($_SESSION["title"]);
	unset($_SESSION["description"]);
	unset($_SESSION["target_url"]);
	unset($_SESSION["keywords"]);
	
	$czones = $this->czones;
	$configs = $this->configs;
	$siz_sel = $this->size_selected;
	//if(isset($_row->width)&&isset($_row->height)&&(($_row->width==0)||($_row->height==0))) {
		if(isset($siz_sel[0])&&isset($siz_sel[1])&&($siz_sel[0]>0)&&($siz_sel[1]>0)){
			$_row->width=$siz_sel[0];$_row->height=$siz_sel[1];
		}
	//}
	$nullDate = 0;
	$item_id = $this->itemid;
	if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
	$item_id_cpn = $this->itemid_cpn;
    if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

	$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

	$document =& JFactory::getDocument();
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
	$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
	include_once(JPATH_BASE."/components/com_adagency/includes/js/standard.php");
	require_once('components/com_adagency/helpers/geo_helper.php');
	$current = $this->channel;

    if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
        include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
    }

	include_once(JPATH_BASE."/components/com_adagency/includes/js/standard_geo.php");

	$lists['image_directory']=substr($lists['image_directory'],0,-1);
	$lists['image_directory']= JURI::base().$lists['image_directory'];

    //echo "<pre>";var_dump($camps);die();

	if (!isset($type)) $type='cpm';
	if (!isset($package->type)) $package->type=$type;
	$cpanel_home = "<div class='cpanelimg'>";
    $cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
    $cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
    $cpanel_home .= "</div>";

		foreach($realimgs as $k=>$v)
			$realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
			$realimgs = implode(",\n", $realimgs);
?>
		<div class="ijadagencystandard" id="adagency_container">
		<div class="componentheading" id="ijadagencystandard">
			<h1><?php echo JText::_('VIEWTREEADDSTANDARD'); ?></h1>
			<?php echo $cpanel_home; ?>
		</div>
        <p id="hidden_adagency">
            <a id="change_cb">#</a><br />
            <a id="close_cb">#</a>
        </p>
        <?php
			if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
				//echo "<div class='adag_pending'>".JText::_('ADAG_STATUS_CHANGE_WARNING')."</div>";
			}
		?>
		<form action="<?php echo JRoute::_("index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid=".$_row->id . $Itemid);?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<table border="0" width="100%" class="content_table">
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
				<input class="formField agency_textbox" type="text" id="standard_title" name="title" style="width: 99%;" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
			</td>
		</tr>

		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADDESCTIPTION');?></span>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="standard_description" name="description" style="width: 99%;" value="<?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?>" />
			</td>
		</tr>

		<tr>
			<td>
				<span class="agency_label"><?php echo JText::_('NEWADTARGET');?></span>:<font color="#ff0000">*</font>
			</td>
			<td><input class="formField agency_textbox" type="text" id="standard_url" name="target_url" style="width: 99%;" maxlength="200" value="<?php if ($_row->target_url!="") {echo $_row->target_url;} else {echo "http://";} ?>" >
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
			<td class="sectiontableheader" colspan="2">
				<span class="agency_subtitle"><a name="adimage"><?php echo JText::_('NEWADIMAGE'); ?></a></span>
			</td>
		</tr>

		<tr>
			<td width="25%">
				<span class="agency_label"><?php echo JText::_('NEWADUPLOADIMG'); ?></span>
			</td>
			<td width="75%"><input type="file" name="image_file" size="20" onchange="document.getElementById('button_upload').click();" />
				<input style="display:none;" type="submit" value="<?php echo JText::_('ADAG_UPLOAD');?>" id="button_upload" onclick="return UploadImage();">
				<input type="hidden" id="standard_image" name="image_url" value="<?php echo $_row->image_url;?>" />
				<?php
					$document =& JFactory::getDocument();
					$document->addScript(JURI::root()."/components/com_adagency/includes/js/standard_upl.php");
					//include(JPATH_BASE."/components/com_adagency/includes/js/standard_upl.php");
				?>
			</td>
		</tr>
        <?php 
            if (isset($_row->image_url) && ($_row->image_url != NULL)) { 
        ?>
		<tr>
			<td valign="top">
				<span class="agency_label">
				<?php echo JText::_('NEWADPREVIEW');?>:
				</span>
			</td>
			<td valign="top">
				<?php 
                    if(!$_row->id){
                ?>
                        <div id="imgdiv" class="imgOutline" style="display: block;">
                <?php
                            if(!$_row->image_url){
                                echo '<img id="imagelib" src="images/blank.png" name="imagelib" />';
                            }
                            else{
                                echo '<img id="imagelib" src="'.$lists["image_directory"]."/".$_row->image_url.'" name="imagelib" />';
                            }
                ?>
                        </div>
                <?php
                    }
                    else{
                ?>
                        <div id="imgdiv" class="imgOutline" style="display: block;">
                            <img id="imagelib" src="<?php echo $lists['image_directory']."/".$_row->image_url; ?>" name="imagelib" />
                        </div>
                <?php
                    }
                ?>
                <div id="imgwait" style="display:none;">
                    <img src="<?php echo JURI::root()."components/com_adagency/images/pleasewait.gif"; ?>" />
                </div>
			</td>
		</tr>

<!--	<tr>
			<td><?php echo JText::_('NEWADALT');;?>:<font color="#ff0000">*</font></td>
			<td>
				<input class="formField" type="text" id="standard_alt" name="parameters[alt_text]" style="width: 99%;" maxlength="200" value="<?php if ($_row->parameters['alt_text']!="") {echo @$_row->parameters['alt_text'];} else {echo "";}?>">
			</td>
		</tr>		-->

		<tr>
			<td width="25%"><span class="agency_label"><?php echo JText::_('ADAG_SIZE'); ?></span>:</td>

			<td width="75%">
				<?php if ($_row->width>0) {echo $_row->width; echo "<input type='hidden' id='standard_width' name='width' value='".$_row->width."' />";} else {echo "";} ?> x <?php if ($_row->height>0) {echo $_row->height; echo "<input type='hidden' id='standard_height' name='height' value='".$_row->height."' />";} else {echo "";} ?>
				<span class="agency_comment"><?php echo JText::_('NEWADSIZE'); ?></span>
			</td>
		</tr>
        <?php
        	}
			else{
		?>		<tr>
        			<td></td>
        			<td>
                        <div id="imgwait" style="display:none;">
                            <img src="<?php echo JURI::root()."components/com_adagency/images/pleasewait.gif"; ?>" />
                        </div>
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

		<?php if (isset($camps)&&(count($camps)>0)) {
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
				$style = "";
				$style2 = ""; 
				if(!isset($czones[$camp->id])){
					$style = 'style="display:none;"';
					$style2 = 'display:none;';
				}
				
				if(in_array($camp->id, $displayed)){
					continue;
				}
				$displayed[] = $camp->id;
				$i++;
		?>
		<tr>
            <td class="check_camp" style="width:35%;  <?php echo $style2; ?> ">
                <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
                <input class="formField adv_cmp camp<?php echo $camp->id; ?>" type="checkbox" <?php
                    if(in_array($camp->id,$banners_camps)) { echo 'checked="checked"';}
                ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
                <label <?php echo $style; ?> ><?php echo $camp->name; ?></label>
            </td>
            <td align="left" class="check_ad" <?php echo $style; ?> >
                <?php echo JText::_('NEWADZONE'); ?>: <?php echo $czones[$camp->id]; ?>
            </td>
        </tr>
		<?php } ?>
		</table>
	<?php } elseif(isset($_row->image_url)) {
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

    <?php
        if(isset($displayed) && (count($displayed) == 0) && (isset($_row->image_url))) {
    ?>
        <table class="adminform" id="campaign_table" width="100%">
            <tr>
				<td width="100%" colspan="2" class="sectiontableheader">
					<span class="agency_subtitle">
					<?php echo JText::_('NEWADCMPS'); ?>
					</span>
				</td>
            </tr>
        </table>
    <?php

        $url_to_camps = '<a target="_blank" href="' . JURI::root() . '/index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&Itemid=' . $item_id . '">' . JText::_('ADAG_HERE') . '</a>';
        /*echo "<div class='adag_pending'>" .
            JText::sprintf('ADAG_NO_CAMP_STDRD2', $url_to_camps )
            . "</div>";*/
        }
    ?>
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
    <input type="hidden" name="media_type" value="Standard" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="controller" value="adagencyStandard" />
    <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
    <input style="float: left;" class="agency_cancel" type="button" onclick="<?php
        if(isset($_SERVER['HTTP_REFERER']) && (strpos(" ".$_SERVER['HTTP_REFERER'],"adagencyStandard")<1)) {
            $_SESSION['standardAdReferer'] = $_SERVER['HTTP_REFERER'];
        } elseif ( !isset($_SERVER['HTTP_REFERER']) || !isset($_SESSION['standardAdReferer']) ) {
            $_SESSION['standardAdReferer'] = '#';
        }

        echo "document.location = '".$_SESSION['standardAdReferer']."';";
    ?>" value="<?php echo JText::_('ADAG_BACK'); ?>" />
    <input style="float: left;margin-left: 5px;" class="agency_continue" type="button" value="<?php echo JText::_("Save");?>" onclick="Joomla.submitbutton('save');">
    </form>
<br /><br />
</div>
