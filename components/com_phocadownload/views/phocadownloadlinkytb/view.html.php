<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
 
class phocaDownloadViewphocaDownloadLinkYtb extends JView
{
	function display($tpl = null) {
		$app	= JFactory::getApplication();
		$document	=& JFactory::getDocument();
		$uri		=& JFactory::getURI();
		//Frontend Changes
		$tUri = '';
		if (!$app->isAdmin()) {
			$tUri = JURI::base();
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocadownload'.DS.'helpers'.DS.'phocadownload.php' );
		}
		JHTML::stylesheet('administrator/components/com_phocadownload/assets/phocadownload.css' );
		
		$eName				= JRequest::getVar('e_name');
		$tmpl['ename']		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
		$tmpl['backlink']	= $tUri.'index.php?option=com_phocadownload&amp;view=phocadownloadlinks&amp;tmpl=component&amp;e_name='.$tmpl['ename'];
		
		$this->assignRef('tmpl',	$tmpl);
		parent::display($tpl);
	}
}
?>