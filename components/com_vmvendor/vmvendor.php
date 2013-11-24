<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Vmvendor
$controller = JController::getInstance('Vmvendor');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();