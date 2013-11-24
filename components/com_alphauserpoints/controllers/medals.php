<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package AlphaUserPoints
 */
class alphauserpointsControllerMedals extends alphauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	function __construct()	{
		parent::__construct( );
	}
	
	function display() 
	{

		$model      = &$this->getModel ( 'medals' );
		$view       = $this->getView  ( 'medals','html' );		
	
		$menus	    = JSite::getMenu();
		$menu       = $menus->getActive();
		$menuid     = $menu->id;

		$params     = $menus->getParams($menuid);		
		
		$_levelrank = $model->_getMedalsList();
		
		$view->assign('params', $params );
		$view->assign('levelrank', $_levelrank[0] );
		$view->assign('total', $_levelrank[1] );
		$view->assign('limit', $_levelrank[2] );
		$view->assign('limitstart', $_levelrank[3] );
		
		$view->_displaylist();
	}
	
	function detailsmedal()
	{	
	
		$com_params = JComponentHelper::getParams( 'com_alphauserpoints' );
		$_useAvatarFrom = $com_params->get('useAvatarFrom');
		$_profilelink	= $com_params->get('linkToProfile');	
		$_allowGuestUserViewProfil = $com_params->get('allowGuestUserViewProfil', 1);
		
		$model =& $this->getModel( 'medals' );
		
		$view  = $this->getView  ( 'medals','html' );
		
		$menus	    = JSite::getMenu();
		$menu       = $menus->getActive();
		$menuid     = $menu->id;

		$params     = $menus->getParams($menuid);

		// load levelrank
		$_detailrank = $model->_getDetailsMedalsListUsers ();
		
		$view->assign('params', $params );
		$view->assign('detailrank', $_detailrank[0] );
		$view->assign('total', $_detailrank[1] );
		$view->assign('limit', $_detailrank[2] );
		$view->assign('limitstart', $_detailrank[3] );
		$view->assign('useAvatarFrom', $_useAvatarFrom );
		$view->assign('linkToProfile', $_profilelink );
		$view->assign('allowGuestUserViewProfil', $_allowGuestUserViewProfil );
		
		// Display
		$view->_displaydetailrank();				
	}

}
?>