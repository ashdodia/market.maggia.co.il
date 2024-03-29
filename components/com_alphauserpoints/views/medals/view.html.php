<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );

class alphauserpointsViewMedals extends JView
{
		
	function _displaylist($tpl = null) {
		
		$document	=  JFactory::getDocument();			
		
		$this->assignRef( 'levelrank', $this->levelrank );
		$this->assignRef( 'params',  $this->params );
		$this->assignRef( 'total', $this->total );
		$this->assignRef( 'limit', $this->limit );
		$this->assignRef( 'limitstart', $this->limitstart );

		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );	
		$this->assignRef( 'pagination', $pagination );
		
		// insert the page counter in the title of the window page
		$titlesuite =  ( $this->limitstart ) ? ' - ' . $pagination->getPagesCounter() : '';
		$document->setTitle( $document->getTitle() . $titlesuite );
		
		parent::display( $tpl) ;
	}
	
	function  _displaydetailrank($tpl = null) {

		$document	=  JFactory::getDocument();
		
		$this->assignRef( 'detailrank', $this->detailrank );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );
		
		// insert the page counter in the title of the window page
		$document->setTitle( $document->getTitle() . ' - ' . $pagination->getPagesCounter() );
		
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'params',  $this->params );
		$this->assignRef( 'useAvatarFrom',  $this->useAvatarFrom );
		$this->assignRef( 'linkToProfile', $this->linkToProfile );
		$this->assignRef( 'allowGuestUserViewProfil', $this->allowGuestUserViewProfil );		
		
		parent::display( "listing" );
	}
	
}
?>