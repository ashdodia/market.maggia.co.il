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

jimport ('joomla.application.component.controller');

class adagencyControlleradagencyAdcode extends adagencyController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "edit");
		$this->_model =& $this->getModel("adagencyAdcode");
		$this->registerTask ("unpublish", "publish");	
	}
	
	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAdcode", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model =& $this->getModel("adagencyConfig");
        global $mainframe;

		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=" . $item_id; } else { $Itemid = NULL; }
		
		$view->setModel($model);
		//////////////////////////////////////////
		$my	=& JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database = & JFactory :: getDBO();
		$link = "index.php?option=com_adagency" . $Itemid;
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."'";
		$database->setQuery($sql);
		$adv_id = $database->loadRow();
		
		// Check if user is logged in 
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!isset($adv_id[0])) {
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$isWizzard = $model->isWizzard();
		// check if the user is not approved as an advertiser
		if(($adv_id[1]=='N')||(($adv_id[1]=='P')&&(!$isWizzard))) {
			$mainframe->redirect($link, JText::_('AD_FAILEDAPPROVE'));
		} 
		//////////////////////////////////////
		$view->editForm();

	}


	function save () {
		
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }	
		
		if ($this->_model->store() ) {
			$msg = JText::_('AD_BANNERSAVED');
		} else {
			$msg = JText::_('AD_BANNERFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds".$Itemid;
		$this->setRedirect($link, $msg);

	}

};

?>