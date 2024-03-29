<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 - Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for AlphaUserPoints Invite
 *
 * @package	AlphaUserPoints
 */
class alphauserpointsViewInvite extends JView
{

	function _display($tpl = null) {
		
		$document	=  JFactory::getDocument();
		$lang       = $document->getLanguage();	
		
		$displ = "view";
		$points = 0;
		
		$document->addStyleSheet(JURI::base(true).'/components/com_alphauserpoints/assets/css/alphauserpoints.css');
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');		
		$result = AlphaUserPointsHelper::checkRuleEnabled('sysplgaup_invite');
		if ( $result ) $points = $result[0]->points;		
	
		JHTML::_('behavior.formvalidation');
		
		// reCaptcha script
		if ( $this->params->get( 'userecaptcha', 1 ) ) {	
			if ( $this->params->get( 'recaptchaajax ', 0 ) ) {			
				$document->addScript( "http://api.recaptcha.net/js/recaptcha_ajax.js" );			
				$paramsReCaptcha = "
					window.onload = function () {
					Recaptcha.create('". $this->params->get( 'pubkey' )."',
					'recaptcha_div', {
					 theme: '". $this->params->get( 'themerecaptcha', 'red' )."',
					 callback: Recaptcha.focus_response_field
					});
					}"
				;
			} else {			
				$paramsReCaptcha = "
				var RecaptchaOptions = {
				   theme : '". $this->params->get( 'themerecaptcha', 'red' )."',
				   lang  : '". substr($lang, 0, 2)."'
				};
				";						
			}
			$document->addScriptDeclaration($paramsReCaptcha, '');
		}
		
		$document->addScript(JURI::base(true).'/media/system/js/mootools.js');
		$document->addStyleSheet(JURI::base(true).'/media/system/css/modal.css');
		$document->addScript(JURI::base(true).'/media/system/js/modal.js');
		
		JHTML::_('behavior.mootools');
		JHTML::_('behavior.modal');
		
		$setModal = "window.addEvent('domready', function() {
			SqueezeBox.initialize({});

			$$('a.modal').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
		});
		";
		
		$document->addScriptDeclaration($setModal);		

			
		$this->assignRef( 'params', $this->params );
		$this->assignRef( 'referreid', $this->referreid );		
		$this->assignRef( 'points', $points );		
		$this->assignRef( 'displ', $displ );
		$this->assignRef( 'referrer_link', $this->referrer_link );		
		
		parent::display($tpl);
		
	}		
	
	/*
	function _display_sent( $counter = 0, $tpl = null ) {		
		
		$displ = "sent";
		$message = "";

		switch ( $counter ) {		
			case '0':
				$message = JText :: _('AUP_NO_EMAIL_HAS_BEEN_SENT');				
				break;				
			case '1':
				$message = JText :: _('AUP_EMAIL_SENT');				
				break;			
			default:
				$message = JText :: _('AUP_EMAILS_SENT');
				$message = sprintf( $message, $counter);
				break;				
		}
		
		$this->assignRef( 'displ', $displ );	
		$this->assignRef( 'params', $this->params );	
		$this->assignRef( 'message', $message );
		
		parent::display($tpl);
		
	}
	*/
	
	
	function _display_addressbook($tpl = null) 
	{			
		parent::display('addressbook');
	}
	
}
?>