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
class VMVendorViewDashboard extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		JHTML::_('behavior.mootools');
		$pane   =& JPane::getInstance('tabs');
		$this->assignRef( 'pane', $pane );
		
		$this->mysalearray		= $this->get('mysales');
		$this->myreviewsarray	= $this->get('myreviews');
		$this->mytaxes			= $this->get('mytaxes');
		$this->main_currency	= $this->get('currency');
		//$this->column_chart		= $this->get('columnchart');
		
		
		
		
		
		$this->mysales	= $this->mysalearray[0];
		$this->total	= $this->mysalearray[1];
 		$this->limit	= $this->mysalearray[2];
		$this->limitstart	= $this->mysalearray[3];
		
		$this->myreviews	= $this->myreviewsarray[0];
		$this->reviews_total	= $this->myreviewsarray[1];
 		$this->reviews_limit	= $this->myreviewsarray[2];
		$this->reviews_limitstart	= $this->myreviewsarray[3];

		// Assign data to the view
		
		//$this->myreviews		= $this->get('reviews');

		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef('pagination', $pagination );
		
 
		// Display the view
		parent::display($tpl);
	}
}