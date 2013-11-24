<?php
/*
 * @component AlphaUserPoints, Copyright (C) 2008-2012 Bernard Gilly, http://www.alphaplug.com
 * Extension menu created by Mike Gusev (migus)
 * @copyright Copyright (C) 2011 Mike Gusev (migus)
 * @license : GNU/GPL
 * @Website : http://migusbox.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class alphauserpointsModelrules extends Jmodel {

	function __construct(){
		parent::__construct();
		
	}
	
	function _getRulesList() {
	
		$app = JFactory::getApplication();
		$db			    = JFactory::getDBO();
		$total 			= 0;
		$menus	    = JSite::getMenu();
		$menu       = $menus->getActive();
		$menuid     = $menu->id;
		$params     = $menus->getParams($menuid);
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_alphauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);

		// hide defined rules from list
		$hiderule = array();
		$hiderules = "";
		$hide_rules = $params->get('hide_rule_byid');
		if ( $hide_rules ) {
			$hiderule = explode( ",", $hide_rules);
			for ($i=0, $n=count($hiderule); $i < $n; $i++) {
				$hiderules .= "\nAND id!='" . trim($hiderule[$i]) . "'";
			}
		}

		$access = 2;//$params->get('show_access');
		$pub_state = "";
		$show_order = "";
		
		if (!$params->get('show_published', 0)) {
			$pub_state .= "\nAND published='1'";
		} else {
			$pub_state .= "";
		}

		if ( $params->get('order_by')==2) {
			$show_order .= "\nORDER BY points DESC, id ASC";
		} elseif ( $params->get('order_by')==1) {
			$show_order .= "\nORDER BY category ASC, points DESC";
		} else {
			$show_order .= "\nORDER BY id ASC";
		}
			$query = "SELECT *, '' AS numrules FROM #__alpha_userpoints_rules "
			. "\nWHERE access<='".$access."'"
			. $pub_state
			. $hiderules
			. $show_order;
		$total = @$this->_getListCount($query);
		$results = $this->_getList($query, $limitstart, $limit);

		for ($i=0, $n=count( $results ); $i < $n; $i++) {
			$row   = $results[$i];
			$query = "SELECT COUNT(*) FROM #__alpha_userpoints_rules "
			. "\nWHERE rule_name='".$row->id."'";
			$db->setQuery($query);
			$row->numrules = $db->loadResult();
		}
		
		return array($results, $total, $limit, $limitstart);
		
	}
}
?>