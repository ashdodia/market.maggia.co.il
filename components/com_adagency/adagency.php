 <?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/

//echo "<hr />" . JRequest::getInt('Itemid') . "<hr />";

defined ('_JEXEC') or die ("Go away.");
global $mainframe;
$mainframe = &JFactory::getApplication();
//check for access
$my = & JFactory::getUser();

$database = & JFactory :: getDBO();
$meniu=0;
$task = JRequest::getVar('task', '');
$control = JRequest::getVar('controller', '', 'get');
$available_controllers = array('adagencyAdcode','adagencyAds','adagencyAdvertisers','adagencyCampaigns','adagencyCPanel','adagencyFlash','adagencyFloating','adagencyOrders','adagencyPackages','adagencyPopup','adagencyReports','adagencySajax','adagencyStandard','adagencyTextlink','adagencyTransition');

require_once (JPATH_COMPONENT.DS.'controller.php');
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
$controller = JRequest::getWord('controller');
if(!in_array($controller,$available_controllers)) { $controller = '';}
$view = JRequest::getWord('view');
$layout = JRequest::getWord('layout');
if(($task =='')&&($layout!='')) {
	if(strtolower($layout)=='editform') {$layout = 'edit';}
	$task = $layout;
}

if ($controller) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once($path);
	} else {
	}
} else {
	if($view) { 
		$controller='adagency'.ucfirst(str_replace('adagency','',$view)); 
		if($view == 'adagencycpanel') {$controller = 'adagencyCPanel';}
		if($view == 'adagencypackage') {$controller = 'adagencyPackages';}
	} else {	$controller = 'adagencyPackages'; }
	$specialCases = array('adagencyflash','adagencyadcode','adagencyfloating','adagencypopup','adagencystandard','adagencytextlink','adagencytransition');
	if(in_array(strtolower($controller),$specialCases)&&(($task=='')||($task=='default'))) { $task='edit'; }
	if((strtolower($controller) == 'adagencyadvertisers')&&(($task == 'default')||($task == ''))) {
		$task = 'edit';
	}
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once($path);
	} else {
	}
}

JHTML::_("behavior.mootools");
$classname = "adagencyController".$controller;
$ajax_req = JRequest::getVar("no_html", 0, "request");
if (class_exists($classname)) {
	$controller = new $classname();
	$controller->execute($task);
	$controller->redirect();
}
?>
