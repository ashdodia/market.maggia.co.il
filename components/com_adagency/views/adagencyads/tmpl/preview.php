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
JHTML::_('behavior.combobox');

	$document =& JFactory::getDocument();
	$document->addScript(JURI::base()."components/com_adagency/js/modal.js");
	$document->addStyleSheet(JURI::base()."components/com_adagency/css/modal.css");
	$ad_id = JRequest::getVar( 'adid', 0,'get','int' );
	$database = & JFactory :: getDBO();
	$database->setQuery("SELECT * FROM `#__ad_agency_banners` WHERE `id` = '".$ad_id."'");
	$result=$database->loadObjectList();
	if ( $database->getErrorMsg() ) {
        die( 'SQL error' );
	}
	$imgfolder = $this->imgfolder;
	if(!isset($lists)){$lists=NULL;}
	$lists['image_directory']=substr($lists['image_directory'],0,-1);
	$lists['image_directory']= JURI::base().$lists['image_directory'];
	$result[0]->parameters = unserialize($result[0]->parameters);
		$approved = $result[0]->approved == 'Y';
		$alt 	= $approved ? "Approved" : "Pending";
		$color  = $approved ? 'green' : 'red';
		if (!isset($result[0]->parameters['align'])) $result[0]->parameters['align']='';
		if (!isset($result[0]->parameters['valign'])) $result[0]->parameters['valign']='';
		if (!isset($result[0]->parameters['padding'])) $result[0]->parameters['padding']='';
		if (!isset($result[0]->parameters['border'])) $result[0]->parameters['border']='';
		if (!isset($result[0]->parameters['bg_color'])) $result[0]->parameters['bg_color']='';
		if (!isset($result[0]->parameters['border_color'])) $result[0]->parameters['border_color']='';
		if (!isset($result[0]->parameters['font_family'])) $result[0]->parameters['font_family']='';
		if (!isset($result[0]->parameters['font_size'])) $result[0]->parameters['font_size']='';
		if (!isset($result[0]->parameters['font_weight'])) $result[0]->parameters['font_weight']='';?>

	<?php
	switch ($result[0]->media_type){
		case "Popup":
			if($result[0]->parameters['popup_type']=='image'){
				echo '<a href="'.$result[0]->target_url.'"><img src="'.$lists['image_directory']."/images/stories/".$imgfolder."/".$result[0]->advertiser_id."/".$result[0]->image_url.'"/></a>';
			} else if($result[0]->parameters['popup_type']=='html') {
                if ( !isset($result[0]->parameters['linktrack'][1]) ) {
                    $result[0]->parameters['linktrack'][1] = 'ad_url';
                }
				$the_ad = str_replace("&lt;","<",$result[0]->parameters['html']);
                $the_ad = str_replace("ad_url", $result[0]->parameters['linktrack'][1] , $the_ad);
				echo str_replace("&gt;",">",$the_ad);
			}
			break;

		case "Advanced":
			echo $result[0]->ad_code;
			break;


		case "Flash":
			$imageurl = JURI::base().'images/stories/'.$imgfolder.'/'.$result[0]->advertiser_id.'/'.$result[0]->swf_url;
			echo '<div onmousedown=\'document.location.href="'.$result[0]->target_url.'";\'><EMBED SRC="'.$imageurl.'" width='.$result[0]->width.' height='.$result[0]->height.' QUALITY="high" wmode="transparent" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED></div>';
			break;


		case "Standard":
			$imageurl = JURI::base().'images/stories/'.$imgfolder.'/'.$result[0]->advertiser_id.'/'.$result[0]->image_url; ?>
			<table><tr><td style=" <?php if(isset($result[0]->parameters['padding']) && ($result[0]->parameters['padding']!=''))echo 'padding: '.$result[0]->parameters['padding'].'px;'; ?>" width="100%" height="100%"><img src="<?php echo $imageurl; ?>" border="0" width="<?php echo $result[0]->width; ?>" height="<?php echo $result[0]->height; ?>" alt="<?php echo $result[0]->parameters['alt_text']; ?>" <?php if(isset($result[0]->target_url) && $result[0]->target_url!="") echo ('onclick=\'document.location.href="'.$result[0]->target_url.'";\''); ?> ></td></tr></table>
		<?php break;


		case "Transition": ?>
			<table><tr><td style=" <?php if (isset($result[0]->parameters['padding'])&& ($result[0]->parameters['padding']!='')) echo 'padding: '.$result[0]->parameters['padding'].'px;'?>" width="100%" height="100%"><span style=" <?php if (isset($result[0]->parameters['font_family']) && ($result[0]->parameters['font_family']!='') ) echo 'font-family: '. $result[0]->parameters['font_family'] .';'; if(isset( $result[0]->parameters['font_size'])) echo  'font-size: '. $result[0]->parameters['font_size'] .'px;'; if (isset ($result[0]->parameters['font_weight'])&& ($result[0]->parameters['font_weight']!='') ) echo 'font-weight: '. $result[0]->parameters['font_weight'] .';'; ?>" ><?php echo stripslashes($result[0]->ad_code); ?></span></td></tr></table>
	<?php
		break;


		case "Floating": ?>
            <?php echo "<script type='text/javascript' src='" . JURI::root() . "/components/com_adagency/includes/js/jquery.js'></script>"; ?>
            <?php echo "<script type='text/javascript' src='" . JURI::root() . "/components/com_adagency/includes/js/jquery.adagency.js'></script>"; ?>
            <?php echo "<script type='text/javascript'>
ADAG(function() {
    var width = ADAG('#AutoNumber2').attr('width'),
        height = ADAG('#AutoNumber2 td:first').attr('height');

    ADAG('#floatit').css({
        left:   '',
        right:  '',
        bottom: '',
        top:    '',
        position: 'relative',
        margin: '0 auto',
        width : width,
        height: height
    });
});
        </script>"; ?>
			<table><tr><td style=" <?php if (isset($result[0]->parameters['padding'])&& ($result[0]->parameters['padding']!='')) echo ('padding: '.$result[0]->parameters['padding'].'px;'); ?>"  width="100%" height="100%"><span style=" <?php if (isset($result[0]->parameters['font_family']) && ($result[0]->parameters['font_family']!='') ) echo 'font-family: '. $result[0]->parameters['font_family'] .';'; if(isset( $result[0]->parameters['font_size'])) echo  'font-size: '. $result[0]->parameters['font_size'] .'px;'; if (isset ($result[0]->font_weight)&& ($result[0]->parameters['font_weight']!='') ) echo 'font-weight: '. $result[0]->parameters['font_weight'] .';'; ?>" ><a href="<?php echo $result[0]->target_url; ?>"><?php echo $result[0]->ad_code; ?></a></span></td></tr></table>
	<?php
		break;


		case "TextLink":
			$banner_row = $result[0];
			$mosConfig_live_site = JURI::root();
			$sqla = "SELECT `imgfolder` FROM #__ad_agency_settings LIMIT 1";
			$database->setQuery($sqla);
			$database->query();
			$ad_agency_folder = $database->loadResult();

			if (isset($banner_row->image_url)&&($banner_row->image_url!='')) {$txtimageurl=$mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banner_row->advertiser_id.'/'. $banner_row->image_url;}
			if(isset($txtimageurl)) {$imagetxtcode='<img class="standard_adv_img" src="'. $txtimageurl .'" border="0" alt="'.$banner_row->parameters['img_alt'].'" />'; } else {$imagetxtcode='';}

			//border, color, background
			if ($banner_row->parameters['border']>0) {
				$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
			}
			else {
				$table_style="border: none;";
			}
			$bg_color="";
			if ($banner_row->parameters['bg_color']!="") {
				$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
			}
			$title_color="";
			if(isset($banner_row->parameters['title_color'])&&($banner_row->parameters['title_color']!=""))
				$title_color='style="color: #'.$banner_row->parameters['title_color'].';"';
			$body_color="";
			if(isset($banner_row->parameters['body_color'])&&($banner_row->parameters['body_color']!=""))
				$body_color="color: #".$banner_row->parameters['body_color'];
			$action_color="";
			if(isset($banner_row->parameters['action_color'])&&($banner_row->parameters['action_color']!=""))
				$action_color='style="color: #'.$banner_row->parameters['action_color'].';"';
			$underlined="";
			$font_weight="";
			$isUnderlined=strstr($banner_row->parameters['font_weight'],'underlined');
			if($isUnderlined=='underlined'){
				$str_length=strpos($banner_row->parameters['font_weight'],'underlined');
				$font_weight=substr($banner_row->parameters['font_weight'],0,$str_length);
				$underlined="text-decoration:underline;";
			}
			else $font_weight=$banner_row->parameters['font_weight'];

			$underlined_a="";
			$font_weight_a="";
			$isUnderlined_a=strstr($banner_row->parameters['font_weight'],'underlined');
			if($isUnderlined_a=='underlined'){
				$str_length_a=strpos($banner_row->parameters['font_weight_a'],'underlined');
				$font_weight_a=substr($banner_row->parameters['font_weight_a'],0,$str_length_a);
				$underlined_a="text-decoration:underline;";
			}
			elseif (isset($banner_row->parameters['font_weight_a'])) { $font_weight_a=$banner_row->parameters['font_weight_a']; } else { $font_weight_a = NULL; }

			$underlined_b="";
			$font_weight_b="";
			if(!isset($banner_row->parameters['font_weight_b'])) { $banner_row->parameters['font_weight_b'] = NULL; }
			$isUnderlined_b=strstr($banner_row->parameters['font_weight_b'],'underlined');
			if($isUnderlined_b=='underlined'){
				$str_length_b=strpos($banner_row->parameters['font_weight_b'],'underlined');
				$font_weight_b=substr($banner_row->parameters['font_weight_b'],0,$str_length_b);
				$underlined_b="text-decoration:underline;";
			}
			else $font_weight_b=$banner_row->parameters['font_weight_b'];
			if(isset($banner_row->target_url)) { $link = $banner_row->target_url; } else {$link = "#";}

			//td padding
			$padding="";
			if ($banner_row->parameters['padding']>0) {
				$padding="padding: ".$banner_row->parameters['padding']."px;";
			}
			if(isset($banner_row->parameters['sizeparam'])&&($banner_row->parameters['sizeparam']==1)) {$sizeparam='%';} else { $sizeparam='px';}
			$width = ($banner_row->width > 0) ? 'width:'.$banner_row->width.$sizeparam.';':'';
			$height = ($banner_row->height > 0) ? 'height:'.$banner_row->height.$sizeparam.';':'';
			if(!isset($banner_row->parameters['target_window'])) { $banner_row->parameters['target_window'] = NULL; }
			if(!isset($banner_row->parameters['font_family_a'])) { $banner_row->parameters['font_family_a'] = NULL; }
			if(!isset($banner_row->parameters['font_family_b'])) { $banner_row->parameters['font_family_b'] = NULL; }
			if(!isset($banner_row->parameters['font_size_a'])) { $banner_row->parameters['font_size_a'] = NULL; }
			if(!isset($banner_row->parameters['font_size_b'])) { $banner_row->parameters['font_size_b'] = NULL; }

			$output_this= '<div class="textlink_adv" align="'.$banner_row->parameters['align'].'" style="text-align:'.$banner_row->parameters['align'].';display:table; '.$width.' '.$height.' '.$table_style.' '.$bg_color.' '.$padding.'">
					<p><a target="'. $banner_row->parameters['target_window'] .'" href="'. $link .'" '.$title_color.'>
						<span style="font-family: '. $banner_row->parameters['font_family'] .'; font-size: '. $banner_row->parameters['font_size'] .'px; font-weight: '. $font_weight.'; '.$underlined.'">
							<font face="'.$banner_row->parameters['font_family'].'">'.$banner_row->parameters['alt_text_t'].'</font></span></a>
							</p><a target="'. $banner_row->parameters['target_window'] .'" href="'. $link .'" '.$title_color.'>'.$imagetxtcode.'</a><br />
					<span style="font-family: '. $banner_row->parameters['font_family_b'] .'; font-size: '. $banner_row->parameters['font_size_b'] .'px; font-weight: '. $font_weight_b.';'.$underlined_b." ".$body_color.';">'.$banner_row->parameters['alt_text'].'</span>
					<br />
						<a class="textlink_adv_link" href="'. $link .'" target="'. $banner_row->parameters['target_window'] .'" '.$action_color.'>
							<span style="font-family: '. $banner_row->parameters['font_family_a'] .'; font-size: '. $banner_row->parameters['font_size_a'] .'px; font-weight: '. $font_weight_a .';'.$underlined_a.'">'.$banner_row->parameters['alt_text_a'].'</span></a>
					</div>';
		echo "<table align='center' width='100%'><tr><td align='center'>".$output_this."</td></tr></table>";
		break;
	}?>

<?php /* include(JPATH_BASE.DS."components".DS."com_adagency".DS."includes".DS."js".DS."ads_sqz_box2.php"); */?>
<?php
	function strip_only2($str, $tags) {
    if(!is_array($tags)) {
        $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
        if(end($tags) == '') array_pop($tags);
    }
    foreach($tags as $tag) $str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
    return $str;
}

?>

