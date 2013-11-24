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
jimport('joomla.html.pane');
jimport( 'joomla.html.pagination' );

/**
 * HTML View class for the HelloWorld Component
 */
class VMVendorViewVendorprofile extends JView
{

	
	
	// Overwriting JView display method
	function display($tpl = null) 
	{
		JHTML::_('behavior.mootools');
		//$pane   =& JPane::getInstance('tabs');
		$slider =& JPane::getInstance('sliders');
		//$this->assignRef( 'pane', $pane );
		$this->assignRef( 'slider', $slider );
		
		$this->myproducts_array		= $this->get('myproducts');
		$this->main_currency		= $this->get('currency');

		$this->vendor_data		= $this->get('vendordata');
		$this->user_thumb		= $this->get('userthumb');
		
		
		
		
		$this->myproducts	= $this->myproducts_array[0];
		$this->total		= $this->myproducts_array[1];
 		$this->limit		= $this->myproducts_array[2];
		$this->limitstart	= $this->myproducts_array[3];
		// Assign data to the view


		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef('pagination', $pagination );
		
 
		// Display the view
		parent::display($tpl);
	}
}