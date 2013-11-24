<?php
/**
* @package Frontend-User-Access (com_frontenduseraccess)
* @version 4.1.7
* @copyright Copyright (C) 2008-2012 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class frontenduseraccessViewNoaccess extends JView
{
	function display($tpl = null)
	{
		$controller = new frontenduseraccessController();					
		$this->assignRef('controller', $controller);
		
		$tmpl = JRequest::getVar('tmpl', '', 'get');
		$this->assignRef('tmpl', $tmpl);
		
		$type = JRequest::getVar('type', '', 'get', 'int');		
		if($type==1 || $type==2){
			//article
			$no_access_message = $controller->fua_config['message_no_item_access_full'];
		//}elseif($type==2){
			//category
			//$no_access_message = $controller->fua_config['message_no_category_access'];		
		}elseif($type==4){
			//component
			$no_access_message = $controller->fua_config['message_no_component_access'];
		//}elseif($type==5){
		}else{
			//menuitem/page
			//and when no type is defined
			$no_access_message = $controller->fua_config['message_no_menu_access'];
		//}else{
			//die('you have no access to this page<br />(no-access type defined)');
		}
		$this->assignRef('no_access_message', $no_access_message);
				
		$back = JText::_( 'back' );		
		$this->assignRef('back', $back);
		
		parent::display($tpl);
	}
}
?>