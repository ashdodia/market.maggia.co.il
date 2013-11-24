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


jimport('joomla.application.component.controller');


class frontenduseraccessController extends JController
{

	var $fua_config;

	function display()
	{		
		// Set a default view if none exists
		if (!JRequest::getCmd('view')){
			JRequest::setVar('view', 'noaccess');
		}		
		
		//$this->fua_config = $this->get_config();		
		
		parent::display();
				
	}
	
	function __construct()
	{	
		$this->fua_config = $this->get_config();	
		parent::__construct();			
	}
	
	
	function get_config(){	
			
		$database = JFactory::getDBO();			
		
		$database->setQuery("SELECT config "
		."FROM #__fua_config "
		."WHERE id='fua' "
		."LIMIT 1"
		);		
		$raw = $database->loadResult();		
		
		$params = explode( "\n", $raw);
		
		for($n = 0; $n < count($params); $n++){		
			$temp = explode('=',$params[$n]);
			$var = $temp[0];
			$value = '';
			if(count($temp)==2){
				$value = trim($temp[1]);
				if($value=='false'){
					$value = false;
				}
				if($value=='true'){
					$value = true;
				}
			}							
			$config[$var] = $value;	
		}				
		return $config;			
	}

	
	
	

}
?>