<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2013 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')) {
   define('DS', DIRECTORY_SEPARATOR);
}

if (class_exists('plgSystemReReplacer')) { 
	echo "Extension doesn't not work if ReReplacer Plugin is enabled"; 
	exit; 
}
if (class_exists('plgSystemRedact')) { 
	echo "Extension doesn't not work if Redaction Plugin is enabled";
	exit;
}

// Import file dependencies
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested
if ( JRequest::getVar( 'task', 'display', 'default', 'cmd')=='showRSSAUPActivity') {
	JRequest::setVar( 'view', 'rssactivity');
}

//if( $controller = JRequest::getWord('controller')) {
if( $controller = JRequest::getVar('controller', '', 'default', 'cmd')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	}
} else {
	$controller = JRequest::getVar( 'view', '', 'default', 'cmd' );
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	}	
}

// Create the controller
$classname	= 'alphauserpointsController'.ucfirst($controller);
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task', 'display', 'default', 'cmd' ) );
$controller->redirect();
?>