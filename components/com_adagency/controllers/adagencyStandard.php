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

class adagencyControlleradagencyStandard extends adagencyController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "edit");
		$this->_model =& $this->getModel("adagencyStandard");
		$this->registerTask ("unpublish", "publish");	
	}

	function edit () {
		global $mainframe;
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyStandard", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model =& $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model2 =& $this->getModel("adagencyAds");
		$view->setModel($model2);
		/////////////////////////////////
		$my	=& JFactory::getUser();
		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
		$link="index.php?option=com_adagency".$Itemid;
		$adv_id = $this->_model->getCurrentAdvertiser();
		
		// Check if user is logged in 
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!$adv_id->aid){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		} else {
			$isWizzard = $model->isWizzard();
			// check if the user is not approved as an advertiser
			if(($adv_id->approved == 'N')||(($adv_id->approved == 'P')&&(!$isWizzard))){
				$mainframe->redirect($link, JText::_('AD_FAILEDAPPROVE'));
			} 
			//////////////////////////////////////
			$view->editForm();
		}
	}
	
	function upload() { 
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { 
			$Itemid = "&Itemid=".$item_id; 
			JRequest::setVar('Itemid',$item_id,'get');
		} else { $Itemid = NULL; }
		
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyStandard", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model =& $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model2 =& $this->getModel("adagencyAds");
		$view->setModel($model2);	
		//$view->uploaded_file=$view->uploadbannerimage();
		//$view->editForm();
		
		$title = JRequest::getVar("title", "");
		$_SESSION["title"] = trim($title);
		$description = JRequest::getVar("description", "");
		$_SESSION["description"] = trim($description);
		$target_url = JRequest::getVar("target_url", "");
		$_SESSION["target_url"] = trim($target_url);
		$keywords = JRequest::getVar("keywords", "");
		$_SESSION["keywords"] = trim($keywords);
		
		$cid = JRequest::getVar("cid", "0");
		$uploaded_file = $view->uploadbannerimage();
		$this->setRedirect("index.php?option=com_adagency&controller=adagencyStandard&task=edit&&uploaded_file=".trim($uploaded_file)."&cid[]=".intval($cid));
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