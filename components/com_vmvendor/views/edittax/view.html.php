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
class VmvendorViewEdittax extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		$this->taxdata			= $this->get('thistaxdata');
		$this->tax_cats			= $this->get('thistaxcats');
		$this->virtuemart_vendor_id		= $this->get('vendorid');
		$this->vendor_shoppergroups			= $this->get('vendorshoppergroups');
		//var_dump($this->vendor_shoppergroups);
		//echo count($this->vendor_shoppergroups);
 
		// Display the view
		parent::display($tpl);
	}
}