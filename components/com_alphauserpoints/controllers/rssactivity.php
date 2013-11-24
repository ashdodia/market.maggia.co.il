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
class alphauserpointsControllerRssactivity extends alphauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	function __construct()	{
		parent::__construct( );
	}
	
	function display() 
	{
		$model      = &$this->getModel ( 'rssactivity' );
		$view       = $this->getView  ( 'rssactivity','html' );	

		$model->_showRSSAUPActivity();

	}

}
?>