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

class adagencyControlleradagencyCampaigns extends adagencyController {
	var $_model = null;

	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listCampaigns");
        $this->registerTask ("default", "listCampaigns");
		$this->_model =& $this->getModel("adagencyCampaigns");
        $this->model2 =& $this->getModel("adagencyConfig");
        $this->_plugins =& $this->getModel("adagencyPlugin");
		$this->registerTask ("unpublish", "publish");
	}

	function listCampaigns() {
		$Itemid = JRequest::getVar("Itemid", "0");
		$view = $this->getView("adagencyCampaigns", "html");
		$view->setModel($this->_model, true);
        $view->setModel($this->model2);
		//////////////////////////////
		$my	=& JFactory::getUser();
		$database = & JFactory :: getDBO();
		$mosConfig_absolute_path = JPATH_BASE;
		$mosConfig_live_site = JURI::base();
		$itemid = $this->model2->getItemid('adagencyadvertiser');
		if($itemid != 0) {
			$Itemid = "&Itemid=".$itemid;
		}
		$link="index.php?option=com_adagency".$Itemid;
		$advertiser = $this->_model->getCurrentAdvertiser();

		// Check if user is logged in
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!$advertiser->aid){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$model =& $this->getModel("adagencyConfig");
		$isWizzard = $model->isWizzard();

		$isCamp=0;
		$shown = explode(";",$model->getConfigs()->show);
		foreach($shown as $element){
			if($element == "nwtwo") {$isCamp =1;}
		}

		// check if the user is not approved as an advertiser
		if(($advertiser->approved=='N')||(($advertiser->approved=='P')&&(!$isWizzard)&&(!$isCamp))){
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		}
		//////////////////////////////////////
		$view->display();
	}

	function manage(){
		$key = JRequest::getVar('key','');
		$action = JRequest::getVar('action','');
		$cid = JRequest::getInt('cid', 0);
		if(($key!='')&&($action!='')&&($cid!=0)){
			$this->_model->manage($key,$action,$cid);
		} else {
			$this->setRedirect("index.php");
		}
	}

	function refresh() {
		$this->edit();
	}

    function changecb() {
        $view = $this->getView("adagencyCampaigns", "html");
		$view->setLayout("changecb");
        $view->setModel($this->_model, true);
        $view->changecb();
    }

    function savechangecb() {
        $res = $this->_model->savechangecb();
        if ($res !== false) {
            JRequest::setVar('tmpl', 'component');
            $view = $this->getView("adagencyCampaigns", "html");
            $view->setLayout("closeboxcb");
            $view->setModel($this->_model, true);
            $view->closeboxcb( NULL, $res );
        }
    }

	function edit(){
		$item_id = $this->model2->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
		$my	=& JFactory::getUser();
		$view = $this->getView("adagencyCampaigns", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
        $view->setModel($this->model2);
		$model =& $this->getModel("adagencyConfig");
		$view->setModel($model);
		/////////////////////////////////////
		$mosConfig_absolute_path = JPATH_BASE;
		$mosConfig_live_site = JURI::base();
		$database = & JFactory :: getDBO();
		$link="index.php?option=com_adagency".$Itemid;
		$advertiser = $this->_model->getCurrentAdvertiser();

		// Check if user is logged in
		// and if user is advertiser
		if($my->id == 0){
			$link = "index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid;
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!$advertiser->aid){
			$link = "index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid;
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$isWizzard = $model->isWizzard();
		$isCamp=0;
		$shown = explode(";",$model->getConfigs()->show);
		foreach($shown as $element){
			if($element == "nwtwo") { $isCamp = 1; }
		}

		// check if the user is not approved as an advertiser
		if ( ($advertiser->approved=='N') || ( ($advertiser->approved=='P') && (!$isWizzard) && (!$isCamp) ) ) {
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		}
		//////////////////////////////////////
		$view->editForm();
	}

	function save(){
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		if($this->_model->store()){
			$msg = JText::_('SAVEATTRIB');
		}
		else{
			$msg = JText::_('SAVEATTRIBFAILED');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns".$Itemid;
		$this->setRedirect($link, $msg);
	}

	function remove(){
		$item_id = JRequest::getInt('Itemid','0');
		
		if($item_id != 0){
			$Itemid = "&Itemid=".$item_id;
		}
		else{
			$Itemid = NULL;
		}

		if(!$this->_model->delete()){
			$msg = JText::_('REMOFEATTRIBFAIL');
		}
		else{
		 	$msg = JText::_('REMOFEATTRIBSUCC');
		}

		$link = "index.php?option=com_adagency&controller=adagencyCampaigns".$Itemid;
		$this->setRedirect($link, $msg);
	}

	function cancel () {
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

	 	$msg = JText::_('ATTRIBOPERATIONCANCELED');
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns".$Itemid;
		$this->setRedirect($link, $msg);
	}

	function approve () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('ATTRIBBLOCKIGERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('ATTRIBUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('ATTRIBPUBSUCC');
		} else {
           	$msg = JText::_('ATTRIBUNSPECERROR');
		}

		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}

	function unapprove () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('ATTRIBBLOCKIGERROR');
		} elseif ($res == -1) {
			$msg = JText::_('ATTRIBUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('ATTRIBPUBSUCC');
		} else {
	       	$msg = JText::_('ATTRIBUNSPECERROR');
		}

		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}

	function pause () {
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		if (!$this->_model->pause()) {
			$msg = JText::_('AD_CMP_CANTPAUSE');
		} else {
		 	$msg = JText::_('AD_CMP_PAUSED');
		}

		$link = "index.php?option=com_adagency&controller=adagencyCampaigns".$Itemid;
		$this->setRedirect($link, $msg);
	}

	function unpause () {
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		if (!$this->_model->unpause()) {
			$msg = JText::_('AD_CMP_CANTUNPAUSE');
		} else {
		 	$msg = JText::_('AD_CMP_UNPAUSED');
		}

		$link = "index.php?option=com_adagency&controller=adagencyCampaigns".$Itemid;
		$this->setRedirect($link, $msg);
	}

    function notifyPayment () {
    	$res = $this->_plugins->io();
    }

    function returnPayment () {
        $res = $this->_plugins->io();
    }

    function failPayment () {
        $res = $this->_plugins->io();
    }
};
?>
