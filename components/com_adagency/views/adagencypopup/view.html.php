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

class adagencyViewadagencyPopup extends JView {
	
	function scandir_php4($dir)
	{
  		$files = array();
  		if ($handle = @opendir($dir))
  	{
    	while (false !== ($file = readdir($handle)))
      	array_push($files, $file);
    	closedir($handle);
  	}
  		return $files;
	}
	
	function uploadbannerimage() { 
		$advertiser = & $this->get('CurrentAdvertiser');
		$advertiser_id = $advertiser->aid;
		if (!$advertiser_id) die('return');//return;
		$test =& $this->getModel("adagencyAds")->rememberChannel();	
		$configs =& $this->getModel("adagencyConfig")->getConf();
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
		if(!isset($ad->id)||($ad->id == 0)) { $ad2 = NULL; } else { $ad2 = & $this->getModel("adagencyPopup")->getad2($ad->id); }
		$advertiser = & $this->get('CurrentAdvertiser');
		
		$advertiser_id = (int)$advertiser->aid;
		$configs =& $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
		
		//check for valid id of the banner
		if ($ad->id!=0) {
			if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
			if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Popup")) die('This banner id is not a Popup banner');
		}
		//check for valid id of the banner
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
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		if (!isset($data['parameters']["popup_type"])) $data['parameters']["popup_type"]='webpage';
		if (!$isNew) { 
			if(!is_array($ad->parameters)) { $ad->parameters = unserialize($ad->parameters);}
			//if ($ad->approved=='N') $ad->approved='0'; else $ad->approved='1';
		} else {
			$ad->parameters['ad_code']='';
			$ad->parameters['popup_type'] = $data['parameters']["popup_type"];
		}

	    // Padding  property
		$lists['padding'] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[padding]', 'class="inputbox"', @$ad->parameters['padding'] );
		// Border property
		$lists["border"] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[border]', 'class="inputbox"', @$ad->parameters['border']);
		
		$javascript = 'onchange="Change();"';
		$type[] 	= JHTML::_('select.option', 'webpage', 'webpage', 'value', 'option' );
		$type[] 	= JHTML::_('select.option', 'image', 'image', 'value', 'option' );
		$type[] 	= JHTML::_('select.option', 'html', 'html', 'value', 'option' );
		$lists['type'] =  JHTML::_( 'select.genericlist', $type, 'parameters[popup_type]', 'class="inputbox" size="1" '.$javascript,'value', 'option', $ad->parameters['popup_type']);
		
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
		if(isset($this->uploaded_file)) { $ad->image_url = $this->uploaded_file;}
		$lists['image_directory'] = "images/stories/".$imgfolder."/".$advertiser_id."/";
		$director=$image_folder;
		$imgs=adagencyViewadagencyPopup::scandir_php4($director);
		$realimgs = array();
		foreach($imgs as $img)
			if(is_file($director."/".$img))
			{
				$props = @getimagesize($director."/".$img);
				if($props === false) continue;
				array_push($realimgs, array("width"=>$props[0],"height"=>$props[1],"name"=>"'".addslashes($img)."'"));
			}

		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyPopup")->getCampsByAid($adv_id);
		} else $camps='';
		$these_campaigns = $this->getModel("adagencyPopup")->getSelectedCamps($advertiser_id, $ad->id);
		
		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyPopup")->getChannel($ad->id); } else { $channel = NULL; }
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
		
		$czones = $this->getModel("adagencyPopup")->processCampZones($camps);
		$czones = $this->getModel("adagencyPopup")->createSelectBox($czones,$ad->id);
        
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');
        
        $camps = $this->getModel("adagencyPopup")->getCampsByAid($adv_id, 1);
        
        $this->assign("itemid",$itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assign("czones",$czones);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);			
		$this->assign("ad", $ad);
		$this->assign("ad2", $ad2);
		$this->assign("realimgs", $realimgs);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("advertiser_id", $advertiser_id);
		$this->assign("these_campaigns", $these_campaigns);
		parent::display($tpl);
	}
}
?>