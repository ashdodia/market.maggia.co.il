<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * extension menu created by Mike Gusev (migus)
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package AlphaUserPoints
 */
class alphauserpointsControllerRules extends alphauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	function __construct()	{
		parent::__construct( );
	}
	
	function display() 
	{

		$model      = &$this->getModel ( 'rules' );
		$view       = $this->getView  ( 'rules','html' );		
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_ADMINISTRATOR );
	
		$menus	    = JSite::getMenu();
		$menu       = $menus->getActive();
		$menuid     = $menu->id;

		$params     = $menus->getParams($menuid);		
		
		$_rules = $model->_getRulesList();
		
		$view->assign('params', $params );
		$view->assign('rules', $_rules[0] );
		$view->assign('total', $_rules[1] );
		$view->assign('limit', $_rules[2] );
		$view->assign('limitstart', $_rules[3] );
		
		$view->_displaylist();
	}

}
?>