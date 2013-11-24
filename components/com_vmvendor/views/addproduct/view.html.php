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
class VmvendorViewAddproduct extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		$this->price_format			= $this->get('priceformat');
		$this->core_custom_fields	= $this->get('corecustomfields');
		$this->virtuemart_vendor_id		= $this->get('vendorid');
 
		// Display the view
		parent::display($tpl);
	}
}