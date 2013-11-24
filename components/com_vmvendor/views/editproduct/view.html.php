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
class VMVendorViewEditproduct extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		// Assign data to the view
		$this->product_data			= $this->get('productdata');
		$this->product_images		= $this->get('productimages');
		$this->product_files		= $this->get('productfiles');
		$this->price_format			= $this->get('priceformat');
		
		$this->product_tags			= $this->get('producttags');
		$this->core_custom_fields	= $this->get('corecustomfields');
		$this->virtuemart_vendor_id		= $this->get('vendorid');
 
		// Display the view
		parent::display($tpl);
	}
}