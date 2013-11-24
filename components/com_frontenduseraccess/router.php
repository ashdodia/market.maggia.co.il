<?php
/**
* @package Frontend-User-Access (com_frontenduseraccess)
* @version 4.1.7
* @copyright Copyright (C) 2008-2012 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

defined('_JEXEC') or die;

function frontenduseraccessBuildRoute( &$query ){

	$segments = array();
	if(isset($query['view'])){
		$segments[] = $query['view'];
		unset( $query['view'] );
	}
	if(isset($query['tmpl'])){
		$segments[] = $query['tmpl'];
		unset( $query['tmpl'] );
	}
	if(isset($query['type'])){
		$segments[] = $query['type'];
		unset( $query['type'] );
	}	
	return $segments;
}

function frontenduseraccessParseRoute($segments){

       $vars = array();
       switch($segments[0]){
	   
               case 'noaccess':
                       $vars['view'] = 'noaccess';
					   $vars['tmpl'] = $segments[1];
					   $vars['type'] = (int) $segments[2];
                       break;
		}
		//print_r($vars);
		//exit;
       return $vars;
}

?>