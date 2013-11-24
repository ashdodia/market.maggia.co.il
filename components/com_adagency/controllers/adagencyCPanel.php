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

class adagencyControlleradagencyCPanel extends adagencyController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listCategories");
		$this->registerTask ("unpublish", "publish");	
		$this->_model =& $this->getModel("adagencycpanel");
        $this->model2 =& $this->getModel("adagencyConfig");
	}

	function listCategories() {
		$my = & JFactory :: getUser();
		$database = & JFactory :: getDBO();
		$item_id = $this->model2->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		$link="index.php?option=com_adagency".$Itemid;
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();

		// Check if user is logged in 
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!isset($adv_id[0])){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		} elseif($adv_id[1]!='Y'){
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'), 'notice');
		}
		//////////////////////////////////////
		
       	JRequest::setVar ("view", "adagencycpanel");
		$view = $this->getView("adagencycpanel", "html");
		$view->setModel($this->_model, true);
        $view->setModel($this->model2);
		parent::display();

	}

};

?>