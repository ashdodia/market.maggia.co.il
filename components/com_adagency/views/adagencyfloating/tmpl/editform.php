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
$banners_camps=$this->these_campaigns;
if (!$banners_camps) { $banners_camps = array(); }
$data = $this->data;
$realimgs = '';
$camps = $this->camps;
$lists = $this->lists;
$czones = $this->czones;
$_row=$this->ad;
$advertiser_id = $this->advertiser_id;
$nullDate = 0;
$item_id = $this->itemid;
if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
$item_id_cpn = $this->itemid_cpn;
if($item_id_cpn != 0) { $Itemid_cpn = "&Itemid=".$item_id_cpn; } else { $Itemid_cpn = NULL; }

$cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/ad_agency.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/includes/css/adagency_template.css");
JHTML::_('behavior.mootools');
$editor1  = & JFactory::getEditor();

include_once(JPATH_BASE."/components/com_adagency/includes/js/floating.php");
require_once('components/com_adagency/helpers/geo_helper.php');

if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
    include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");
}

require_once('components/com_adagency/includes/js/floating_geo.php');
if (!isset($_row->parameters['ad_code'])) $_row->parameters['ad_code']='';
$cpanel_home = "<div class='cpanelimg'>";
$cpanel_home .= "<img src='" . JURI::root() . "components/com_adagency/includes/css/home2.png' />";
$cpanel_home .= "<a href='".$cpn_link."' >" . JText::_('ADAG_ADV_DASHB') . "</a>";
$cpanel_home .= "</div>";
?>
<?php //include(JPATH_BASE."/components/com_adagency/includes/js/floating.php"); ?>
		<div class="ijadagencyfloating" id="adagency_container">
        <p id="hidden_adagency">
            <a id="change_cb">#</a><br />
            <a id="close_cb">#</a>
        </p>
		<div class="componentheading" id="ijadagencyfloating">
			<h1><?php echo JText::_('VIEWTREEADDFLOATING');  ?></h1>
			<?php echo $cpanel_home; ?>
		</div>
        <?php
			if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
				//echo "<div class='adag_pending'>".JText::_('ADAG_STATUS_CHANGE_WARNING')."</div>";
			}
		?>
 	<form action="<?php echo JRoute::_('index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid='.$_row->id);?>" method="post" name="adminForm" id="adminForm">
<table border="0" width="100%" class="adminform">
		<tr>
			<td colspan="2" class="sectiontableheader">
				<span class="agency_subtitle"><?php echo  JText::_('NEWADDETAILS'); ?></span>
			</td>
		</tr>

		<tr>
			<td width="25%" nowrap>
				<span class="agency_label"><?php echo JText::_('NEWADTITLE');?></span>:<font color="#ff0000">*</font>
			</td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="floating_title" name="title" style="width: 99%;" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
			</td>
		</tr>

		<tr>
			<td width="25%"><span class="agency_label"><?php echo JText::_('NEWADDESCTIPTION');?></span></td>
			<td width="75%">
				<input class="formField agency_textbox" type="text" id="floating_description" name="description" style="width: 99%;" value="<?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?>" />
			</td>
		</tr>
		<input class="formField"  type="hidden" name="width" size="3" value="<?php if ($_row->width!="") {echo $_row->width;} else {echo "300";} ?>" />
		<input class="formField" type="hidden" name="height" size="3"	value="<?php if ($_row->height!="") {echo $_row->height;} else {echo "300";} ?>" />
        
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
					<?php echo JText::_('NEWADCONTENTAD');;?>
				</span>
			</td>
		</tr>

		<tr>
			<td colspan="2">
			 <?php
			 echo $editor1->display( 'transitioncode', ''.stripslashes($_row->parameters['ad_code']),'100%', '300px', '20', '60',false );?>
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
		<table class="adminform" width="100%" id="campaign_table">
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
		<tr style="width: 35%;">
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

		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="media_type" value="Floating" />
		<input type="hidden" name="advertiser_id" value="<?php echo $advertiser_id;?>" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="parameters['border']" value="<?php echo @$_row->parameters['border'];?>" />
		<input type="hidden" name="parameters['border_color']" value="<?php echo @$_row->parameters['border_color'];?>" />
		<input type="hidden" name="parameters['bg_color']" value="<?php echo @$_row->parameters['bg_color'];?>" />
		<input type="hidden" name="parameters['ad_code']" value="<?php echo htmlspecialchars($_row->parameters['ad_code']);?>" />
		<input type="hidden" name="controller" value="adagencyFloating" />
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
        <input style="float: left;" class="agency_cancel" type="button" onclick="history.go(-1);" value="<?php echo JText::_('ADAG_BACK'); ?>" />
		<input style="float: left;margin-left: 5px;" class="agency_continue" type="button" value="<?php echo JText::_("AD_SAVE");?>" onclick="Joomla.submitbutton('save');">
		</form>
<br /><br />
</div>
