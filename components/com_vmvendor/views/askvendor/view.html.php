<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class VmvendorViewAskvendor extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		
		// Assign data to the view
		//$this->msg = 'Plugin Map';
		$this->vendorcontacts 	= $this->get('vendorcontacts');
		$this->productname		= $this->get('productname');
		// Display the view
		
		parent::display($tpl);
	}
}