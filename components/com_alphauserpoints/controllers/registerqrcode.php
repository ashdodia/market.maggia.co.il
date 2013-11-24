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
class alphauserpointsControllerRegisterqrcode extends alphauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	function __construct()	{
		parent::__construct( );
	}
	
	//function registerQRcode()
	function display()
	{

		$model      = $this->getModel ( 'registerqrcode' );
		$view       = $this->getView  ( 'registerqrcode','html' );
		
		$couponCode = JRequest::getVar('QRcode', '', 'get', 'string');
		$trackIDNew = uniqid('', true);		
		$trackID = JRequest::getVar('trackID', $trackIDNew, 'get', 'string');
		$model->trackQRcode($trackID, $couponCode);
		
		$view->assign( 'couponCode', $couponCode );
		$view->assign( 'trackID', $trackID );
		
		$view->display();
	}
	
	function attribqrcode() 
	{

		$model      = $this->getModel ( 'registerqrcode' );
		$view       = $this->getView  ( 'registerqrcode','html' );
	
		$points = $model->attribPoints();
		
		$view->assign( 'points', $points );
		
		$view->displayResult();
	}


}
?>