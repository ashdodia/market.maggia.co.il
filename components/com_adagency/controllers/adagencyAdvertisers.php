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

class adagencyControlleradagencyAdvertisers extends adagencyController {
	var $model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "register");
		$this->_model =& $this->getModel('adagencyAdvertiser');

	}

	function listAdvertisers() {
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model2 =& $this->getModel("adagencyConfig");
		$view->setModel($model2);
		$model =& $this->getModel("adagencyPlugin");
		$view->setModel($model);
		$view->editForm();
	}

	function register(){
		$user =& JFactory::getUser();
		$model =& $this->getModel("adagencyConfig");
		$layout = JRequest::getVar('layout','','get');
        $itemid_adv = $model->getItemid('adagencyadvertisers');
        $itemid_ads = $model->getItemid('adagencyads');
        $itemid_pkg = $model->getItemid('adagencypackage');

        if($itemid_pkg != 0) { $Itemid = "&Itemid=" . $itemid_pkg; } else { $Itemid = NULL; }
        if($itemid_adv != 0) { $Itemid_adv = "&Itemid=" . $itemid_adv; } else { $Itemid_adv = NULL; }
        if($itemid_ads != 0) { $Itemid2 = "&Itemid=" . $itemid_ads; } else { $Itemid2 = NULL; }

		if(isset($user->id)&&($user->id>0)){
			if($model->isWizzard()){
				$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAds&task=addbanners" . $Itemid2);
			} else {
                $link = JRoute::_("index.php?option=com_adagency" . $Itemid);
            }
			$adv_id = $this->_model->getAdvertiserByUserId($user->id);
			if(!isset($adv_id) || ($adv_id==0) || ($layout=='register')) {
                $link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=" . $user->id . $Itemid_adv);
            }
			$this->setRedirect($link);
		}

		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("register");
		$view->setModel($this->_model, true);
		$model =& $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->register();
	}

	function login($user = NULL, $pass = NULL, $link = NULL, $redirect = 0){
		$data=JRequest::get('post');
		global $mainframe;
		$options = array();
		if(isset($data['remember_me'])) { $option['remember'] = true;} else { $options['remember'] = false;}
        $model =& $this->getModel("adagencyConfig");
		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=" . $item_id; } else { $Itemid = NULL; }
		$options['return'] = 'index.php?option=com_adagency' . $Itemid;
		$credentials = array();
		if($user != NULL) {
			$credentials['username'] = $user;
		} else {
			$credentials['username'] = JRequest::getVar('adag_username', '', 'method', 'username');
		}
		if($pass != NULL) {
			$credentials['password'] = $pass;
		} else {
			$credentials['password'] = JRequest::getString('adag_password', '', 'post', JREQUEST_ALLOWRAW);
		}
		//perform the login action
		$error = $mainframe->login($credentials, $options);
		if($link == NULL){
			$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register'.$Itemid);
		}
		
		$returnpage = JRequest::getVar("returnpage", "");
		if($returnpage == "buy"){
			$pid = JRequest::getVar("pid", "0");
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0&pid=".$pid.$Itemid);
		}
		
		if($redirect == 0) {
			$this->setRedirect($link);
		}
	}

	function save () {
		$db = & JFactory::getDBO();
		$data = JRequest::get('post');
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
		$error = "";
		$the_aid=JRequest::getVar("aid");
		if ($this->_model->store($error) ) {
			$msg = JText::_('ADVSAVED');
		} else {
			$msg = JText::_('ADVSAVEFAILED');
			$msg .= $error;
		}
		// if user updated his profile -> ... , else if he just registered
		if($the_aid!=0) {$msg = JText::_('ADAG_PROFILE_SUCC_UPDATE');}
		//$link = "index.php?option=com_adagency&controller=adagencyCPanel".$Itemid;
        $link = "index.php?option=com_adagency".$Itemid;
		$msg2=JRequest::getVar("msgafterreg");
		if (isset($msg2)&&($msg2!='')) $msg = $msg2;
		if($the_aid==0) {
			$sql = "SELECT `show` FROM `#__ad_agency_settings` WHERE `show` LIKE '%wizzard%' LIMIT 1";
			$db->setQuery($sql);
			$isWizzard = $db->loadResult();

			$usr = $this->_model->getLastAdvertiser();
			if(isset($usr->approved)&&($usr->approved=='Y')) {
				$msg = JText::_('ADVSAVED2');
			} else if($isWizzard) {
				$sql = 'SELECT u.block,a.approved FROM `#__users` AS u, `#__ad_agency_advertis` AS a WHERE u.username = "'.addslashes(trim($data['username'])).'" AND u.id = a.user_id';
				$db->setQuery($sql);
				$result = $db->loadObject();
				if(($result->block == '0')&&($result->approved == 'Y')) {
					$this->login($data['username'],$data['password'],NULL,1);
					$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid);
					$msg = NULL;
				}
			}
		}
		$this->setRedirect($link, $msg);
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

	function overview () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAdvertisers", "html");
        $model =& $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->setModel($this->_model, true);
		$view->setLayout("overview");
		$view->overview();
	}

};

?>
