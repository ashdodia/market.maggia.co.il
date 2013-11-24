<?php
/*
 * @component AlphaUserPoints, Copyright (C) 2008-2012 Bernard Gilly, http://www.alphaplug.com
 * @copyright Copyright (C) 2011 Mike Gusev (migus)
 * @license : GNU/GPL
 * @Website : http://migusbox.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );

class alphauserpointsViewRules extends JView
{
	function _displaylist($tpl = null) {
		$document	=  JFactory::getDocument();
		$this->assignRef( 'rules', $this->rules );
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );	
		$document->setTitle( $document->getTitle() . ' - ' . $pagination->getPagesCounter() );		
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'params',  $this->params );
		
		parent::display( $tpl) ;
	}
}
?>