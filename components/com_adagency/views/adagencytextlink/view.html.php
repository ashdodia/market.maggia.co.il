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

jimport ("joomla.application.component.view");

class adagencyViewadagencyTextlink extends JView {

	function scandir_php4($dir){
  		$files = array();
  		if ($handle = @opendir($dir)){
	    	while (false !== ($file = readdir($handle)))
    	  	array_push($files, $file);
	    	closedir($handle);
  		}
  		return $files; 
	}
	
	function uploadbannerimage() { 
		$advertiser = & $this->get('CurrentAdvertiser');
		$advertiser_id = $advertiser->aid;
		$configs =& $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);			
		if (!$advertiser_id) die('return');//return;
		$test =& $this->getModel("adagencyAds")->rememberChannel();	
		$imgfolder = $configs->imgfolder;
		$targetPath = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/';
		$failed = '0';
		if (isset($_FILES['image_file'])) {
			$filename = time();
			$filename2 = $_FILES['image_file']['name'];
			if ($filename2) {
				$filenameParts = explode('.', $filename2);
				$extension = '';
				if (count($filenameParts) > 1)
					$extension = array_pop($filenameParts);
				$extension = strtolower($extension);
				if (!in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
					$text = strip_tags( addslashes( nl2br( "The image must be gif, png, jpg, jpeg." )));
					echo "<script>alert('$text'); </script>";
					$failed=1;
				}
				if ($failed != 1) {
				$filename.='.'.$extension;
				if (!move_uploaded_file ($_FILES['image_file']['tmp_name'],$targetPath.$filename)|| !chmod($targetPath.$filename, 0644)) {
					$text = strip_tags( addslashes( nl2br( "Upload of ".$filename2." failed." )));
					echo "<script>alert('$text'); </script>";
				} else {
					return $filename;
					}
				}
			  }	
			}
	}

	function editForm($tpl = null) { 
		global $mainframe;
		$data = JRequest::get('post');
		$db =& JFactory::getDBO();
		$ad =& $this->get('ad'); 
		$configs =& $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
		$advertiser = $this->get('CurrentAdvertiser');
		$advertiser_id = (int)$advertiser->aid;
		
		//check for valid id of the banner
		if ($ad->id!=0) {
		if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
		if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="TextLink")) die('This banner id is not a TextLink banner');
		}
		//get the image folder
		$imgfolder = $configs->imgfolder;
		if (intval($advertiser_id)>0) {
				$imagepath = str_replace("/administrator","",JPATH_BASE);
				$imagepath = $imagepath."/images/stories/";
				$newimgfolder = $imgfolder."/".$advertiser_id;
			if ( !is_dir ( $imagepath.$newimgfolder ) ) {
		       @mkdir ( $imagepath."/".$newimgfolder );
		       @chmod ( $imagepath."/".$newimgfolder, 0755 ); }
		     else {
		       @chmod ( $imagepath."/".$newimgfolder, 0755 ); 
		    } 
		}   
		//end image folder
	
		//check for valid id of the banner
		$isNew = ($ad->id < 1);		
		if (!$isNew) {
			if(!is_array($ad->parameters)) {$ad->parameters = @unserialize($ad->parameters);}
			if(is_array($ad->parameters)) {
				foreach($ad->parameters as $key=>$value){
					//echo "-".$key."-";
					$key = str_replace("\'","",$key);
					$ad->parameters[$key]=$value;
				}
			}
			if(!isset($ad->parameters['align'])) { $ad->parameters['align']=''; }
			if(!isset($ad->parameters['target_window'])) { $ad->parameters['target_window']=''; }
			if(!isset($ad->parameters['font_family'])) { $ad->parameters['font_family']=''; }
			if(!isset($ad->parameters['font_weight'])) { $ad->parameters['font_weight']=''; }
		}	
		
		//echo "<pre>";var_dump($ad->parameters);die();

		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );	
	    $advertisersloaded = adagencyModeladagencyTextlink::gettextlistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    		
		// Window option
		$window[] 	= JHTML::_('select.option', '_blank', JText::_('open in new window'), 'value', 'option' );
		$window[] 	= JHTML::_('select.option', '_self', JText::_('open in the same window'), 'value', 'option' );
		if(!isset($ad->parameters['target_window'])) {$ad->parameters['target_window']=NULL;}
		$lists['window'] = JHTML::_( 'select.genericlist', $window, 'parameters[target_window]', 'class="inputbox" size="1"  id="show_hide_box"','value', 'option', $ad->parameters['target_window']);
				
		// Font family
		$font_family[] 	= JHTML::_('select.option', 'Arial', 'Arial', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Helvetica', 'Helvetica', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Garamond', 'Garamond', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'sans-serif', 'Sans Serif', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Verdana', 'Verdana', 'value', 'option' );
		if(!isset($ad->parameters['font_family'])) {$ad->parameters['font_family'] = NULL;}
		$lists['font_family'] = JHTML::_( 'select.genericlist', $font_family, 'parameters[font_family]', 'class="inputbox" size="1"','value', 'option', $ad->parameters['font_family']);
		
		// Font size
		if (isset($row->parameters)) {
		$font_size_value = ($ad->parameters['font_size'] > 0) ? $ad->parameters['font_size'] : 12;
		$lists["font_size"] = JHTML::_('select.integerlist',1, 48, 1, 'parameters[font_size]', 'class="inputbox"', $font_size_value);
		} else {
		$font_size_value = 12;
		$lists["font_size"] = JHTML::_('select.integerlist',1, 48, 1, 'parameters[font_size]', 'class="inputbox"', $font_size_value);
		}
		 // Padding  property
		$lists['padding'] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[padding]', 'class="inputbox"', @$ad->parameters['padding'] );
		// Border property
		$lists["border"] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[border]', 'class="inputbox"', @$ad->parameters['border']);
		
		// Alignment options :
		$alignments[] = JHTML::_('select.option',  "0", JText::_('pozitia'), 'value', 'option' );	
		$alignments[] = JHTML::_('select.option',  "left", JText::_('left'), 'value', 'option' );	
		$alignments[] = JHTML::_('select.option',  "center", JText::_('center'), 'value', 'option' );	
		$alignments[] = JHTML::_('select.option',  "right", JText::_('right'), 'value', 'option' );	

		if(!isset($ad->parameters['align'])) { $ad->parameters['align'] = NULL; }
		$lists['alignment']  =  JHTML::_( 'select.genericlist', $alignments, 'parameters[align]', 'class="inputbox" size="1"','value', 'option', $ad->parameters['align']);
		
		// Font weight
		if (isset($ad->parameters)) {
		$font_weight_value = (isset($ad->parameters['font_weight']) &&($ad->parameters['font_weight'] != "")) ? $ad->parameters['font_weight'] : "normal";
		} else {
		$font_weight_value = "normal";
		}
		$font_weight[] 	= JHTML::_('select.option', 'lighter', 'lighter', 'value', 'option' );
		$font_weight[] 	= JHTML::_('select.option', 'normal', 'normal', 'value', 'option' );
		$font_weight[] 	= JHTML::_('select.option', 'bold', 'bold', 'value', 'option' );
		$font_weight[] 	= JHTML::_('select.option', 'bolder', 'bolder', 'value', 'option' );
		if(!isset($font_weight)) {$font_weight = NULL;}		
		$lists['font_weight'] = JHTML::_( 'select.genericlist', $font_weight, 'parameters[font_weight]', 'class="inputbox" size="1"','value', 'option', $font_weight_value);
		
		if(($ad->id>0)&&isset($ad->id)){
			$wh = $this->getModel("adagencyTextlink")->getWH($ad->id);
			$ad->width = $wh->width;
			$ad->height = $wh->height;
		} else {
			$ad->width = "";
			$ad->height = "";
		}

		if(isset($this->uploaded_file)) { $ad->image_url = $this->uploaded_file; }
		if(isset($ad->image_url)) { 
			$imgInfo = $this->getModel("adagencyTextlink")->getImageInfo(JPATH_BASE.DS."images".DS."stories".DS.$imgfolder.DS.$advertiser_id.DS.$ad->image_url);
		} else {$imgInfo = NULL;}
		
		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyTextlink")->getCampsByAid($adv_id);
		} else $camps='';
		
		$camps2 = array();
		/*foreach ($camps as &$camp){
			if((!isset($camp->adparams['width']))||(!isset($imgInfo[0]))||($imgInfo[0] > $camp->adparams['width'])||(!isset($camp->adparams['height']))||(!isset($imgInfo[1]))||($imgInfo[1] > $camp->adparams['height'])) {
				//@unset($camp);
				$camp = NULL;
			} else { $camps2[] = $camp; }
		}
		$camps = $camps2;*/
		
		$these_campaigns = $this->getModel("adagencyTextlink")->getSelectedCamps($advertiser_id, $ad->id);
	
		// Imagelist
		$javascript 	= 'onchange="changeDisplayImage();"';
		$directory 	= "/images/stories/".$imgfolder."/{$advertiser_id}";
		$livesite = JURI::base();
		$absolutepath = JPATH_SITE;
		$image_folder = "{$absolutepath}{$directory}";
		$lists['image_path'] = "/images/stories/".$imgfolder."/{$advertiser_id}/";
		if (!is_dir($image_folder)) {
			@mkdir($image_folder, 0755);
			@chmod($mosConfig_absolute_path."/images/stories/".$imgfolder, 0755);}
		$javascript 	= 'onchange="changeDisplayImage();"';
		$lists['image_directory'] = "images/stories/".$imgfolder."/".$advertiser_id."/";
		$director=$image_folder;
		$imgs=adagencyViewadagencyTextlink::scandir_php4($director);
		$realimgs = array();
		foreach($imgs as $img)
			if(is_file($director."/".$img))
			{
				$props = @getimagesize($director."/".$img);
				if($props === false) continue;
				array_push($realimgs, array("width"=>$props[0],"height"=>$props[1],"name"=>"'".addslashes($img)."'"));
			}

		$max_chars = $configs->maxchars;

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyTextlink")->getChannel($ad->id); } else { $channel = NULL; }
		if(isset($_SESSION['channelz'])){
			$channel = new stdClass();
			$channel->sets[0] = $_SESSION['channelz'];
			unset($_SESSION['channelz']);
		} 
		if(isset($_SESSION['channelz2'])){
			$channel = NULL;
			$ad->channel_id = $_SESSION['channelz2'];
			unset($_SESSION['channelz2']);
		}
		
		$lists['prevzones'] = NULL;
		$lists['hidden_zones'] = NULL;
		$text_zones = $this->getModel("adagencyTextlink")->getPrevZones();
		if(($text_zones != NULL)&&(is_array($text_zones))) {
			foreach($text_zones as $element){
				$lists['prevzones'] .= '<option value="'.$element->zoneid.'">'.$element->z_title.'</option>';
				$lists['hidden_zones'] .= "<input type='hidden' id='z".$element->zoneid."' value='".$element->textadparams."' />";
			}
		}
		//echo "<pre>";echo htmlentities($lists['hidden_zones']);die();
		$czones = $this->getModel("adagencyTextlink")->processCampZones($camps);
		$czones = $this->getModel("adagencyTextlink")->createSelectBox($czones,$ad->id);

        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        
        $camps = $this->getModel("adagencyTextlink")->getCampsByAid($adv_id, 1);
        
        $this->assign("itemid", $itemid);
		$this->assign("itemid_cpn", $itemid_cpn);
        $this->assign("czones", $czones);
		$this->assign("ad", $ad);
		$this->assign("imgInfo", $imgInfo);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);	
		$this->assign("max_chars",$max_chars);
		$this->assign("realimgs",$realimgs);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("advertiser_id", $advertiser_id);
		$this->assign("these_campaigns", $these_campaigns);
				
		parent::display($tpl);
	}

}

?>