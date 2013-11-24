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

class adagencyViewadagencyFlash extends JView {


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

	function listswfs( $name, $active = NULL, $javascript = NULL, $directory = NULL )
	{
		if ( !$directory ) {
			$directory = '/images/stories/';
		}

		if ( !$javascript ) {
			$javascript = "onchange=\"javascript:if (document.forms.adminForm." . $name . ".options[selectedIndex].value!='') {document.imagelib.src='..$directory' + document.forms.adminForm." . $name . ".options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		}

		jimport( 'joomla.filesystem.folder' );
		$imageFiles = JFolder::files( JPATH_SITE.DS.$directory );
		$images 	= array(  JHTML::_('select.option',  '', '- '. JText::_( 'Select Image' ) .' -' ) );
		foreach ( $imageFiles as $file ) {
			if ( eregi( "swf|SWF", $file ) ) {
				$images[] = JHTML::_('select.option',  $file );
			}
		}
		$images = JHTML::_('select.genericlist',  $images, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $images;
	}


	function uploadflash() {
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$advertiser = $this->get('CurrentAdvertiser');
		$advertiser_id = $advertiser->aid;
		if (!$advertiser_id) return;
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
				if (!in_array($extension, array('swf'))) {
					$text = strip_tags( addslashes( nl2br( "The image must be swf." )));
					echo "<script>alert('$text');</script>";
					$failed=1;
				}
			if ($failed != 1) {
				$filename.='.'.$extension;
				if (!move_uploaded_file ($_FILES['image_file']['tmp_name'],$targetPath.$filename) || !chmod($targetPath.$filename, 0644)) {
					$text = strip_tags( addslashes( nl2br( "Upload of ".$filename." failed." )));
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

		$advertiser = $this->get('CurrentAdvertiser');
		$advertiser_id = (int)$advertiser->aid;
		//check for valid id of the banner
		if ($ad->id!=0) {
		if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
		if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Flash")) die('This banner id is not a Flash banner');
		}
		//check for valid id of the banner

		$camps2 = NULL;

		$configs =& $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
		$imgfolder = $configs->imgfolder;

        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid_cpn = & $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

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

		if (!isset($ad->parameters['align'])) $ad->parameters['align']='';
		if (!isset($ad->parameters['target_window'])) $ad->parameters['target_window']='';

		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );
	    $advertisersloaded = adagencyModeladagencyFlash::getflashlistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);

	    // Padding  property
		$lists['padding'] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[padding]', 'class="inputbox"', @$ad->parameters['padding'] );
		// Border property
		$lists["border"] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[border]', 'class="inputbox"', @$ad->parameters['border']);

		// Alignment options :
		$alignments[] = JHTML::_('select.option',  "0", JText::_('pozitia'), 'value', 'option' );
		$alignments[] = JHTML::_('select.option',  "left", JText::_('left'), 'value', 'option' );
		$alignments[] = JHTML::_('select.option',  "center", JText::_('center'), 'value', 'option' );
		$alignments[] = JHTML::_('select.option',  "right", JText::_('right'), 'value', 'option' );
		$lists['alignment']  =  JHTML::_( 'select.genericlist', $alignments, 'parameters[align]', 'class="inputbox" size="1"','value', 'option', $ad->parameters['align']);

		// Window option
		$window[] 	= JHTML::_('select.option', '_blank', JText::_('open in new window'), 'value', 'option' );
		$window[] 	= JHTML::_('select.option', '_self', JText::_('open in the same window'), 'value', 'option' );
		$lists['window'] = JHTML::_( 'select.genericlist', $window, 'parameters[target_window]', 'class="inputbox" size="1"  id="show_hide_box"','value', 'option', $ad->parameters['target_window']);

	    // Imagelist
		$javascript 	= 'onchange="changeDisplayImage();"';
		$directory 	= "images/stories/".$imgfolder."/{$advertiser_id}";
		$livesite = JURI::base();
		$absolutepath = JPATH_SITE;
		$image_folder = "{$absolutepath}{$directory}";
		if(isset($this->uploaded_file)) {$ad->swf_url = $this->uploaded_file; $ad->image_url = $this->uploaded_file;}
		$lists['image_path'] = "/images/stories/".$imgfolder."/{$advertiser_id}/";
		$director=$image_folder;
		$imgs=adagencyViewadagencyFlash::scandir_php4($director);
		$realimgs = array();
		foreach($imgs as $img)
			if(is_file($director."/".$img))
			{
				$props = @getimagesize($director."/".$img);
				if($props === false) continue;
				array_push($realimgs, array("width"=>$props[0],"height"=>$props[1],"name"=>"'".addslashes($img)."'"));
			}

		////startflash
		$javascript = 'onchange="changeDisplayFlash();"';
		$lists['flash_directory'] = $livesite . "/images/stories/".$imgfolder."/{$advertiser_id}/";
		///endflash

		///===================select available campaigns============================
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyFlash")->getCampsByAid($adv_id);
		} else { $camps=''; }

		if(isset($camps)&&(is_array($camps)))
		foreach ($camps as &$camp){
			//$camp->adparams = @unserialize($camp->adparams);
			//echo "<pre>";var_dump($camp);die();

			if( (!isset($camp->adparams['width'])) || (!isset($camp->adparams['height'])) || ($camp->adparams['width'] == '') || ($camp->adparams['height'] == '') ) {
				$camps2[] = $camp;
			} elseif((!isset($ad->width))||($ad->width != $camp->adparams['width'])||(!isset($ad->height))||($ad->height != $camp->adparams['height'])) {
				//@unset($camp);
				$camp = NULL;
			} else {
				$camps2[] = $camp;
			}
		}
		$camps = $camps2;

		$these_campaigns = $this->getModel("adagencyFlash")->getSelectedCamps($advertiser_id, $ad->id);

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyFlash")->getChannel($ad->id); } else { $channel = NULL; }
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
		$czones = $this->getModel("adagencyFlash")->processCampZones($camps);
		$czones = $this->getModel("adagencyFlash")->createSelectBox($czones,$ad->id);

        $camps = $this->getModel("adagencyFlash")->getCampsByAid($adv_id, 1);
        if (!isset($czones) || empty($czones)) {
            $camps = array();
        }

        $this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assign("czones", $czones);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);
		$this->assign("ad", $ad);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("realimgs", $realimgs);
		$this->assign("advertiser_id", $advertiser_id);
		$this->assign("these_campaigns", $these_campaigns);

		parent::display($tpl);
	}
}

?>
