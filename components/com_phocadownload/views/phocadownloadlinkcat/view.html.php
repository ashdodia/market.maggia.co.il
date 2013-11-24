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
jimport('joomla.application.component.view');
class phocaDownloadViewphocaDownloadLinkCat extends JView
{
	function display($tpl = null) {
		$app	= JFactory::getApplication();
		$db		=& JFactory::getDBO();
		//Frontend Changes
		$tUri = '';
		if (!$app->isAdmin()) {
			$tUri = JURI::base();
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocadownload'.DS.'helpers'.DS.'phocadownload.php' );
		}
		
		$document	=& JFactory::getDocument();
		$uri		=& JFactory::getURI();
		JHTML::stylesheet( 'administrator/components/com_phocadownload/assets/phocadownload.css' );
		
		$eName				= JRequest::getVar('e_name');
		$tmpl['ename']		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
		$tmpl['backlink']	= $tUri.'index.php?option=com_phocadownload&amp;view=phocadownloadlinks&amp;tmpl=component&amp;e_name='.$tmpl['ename'];
		
		$model 			= &$this->getModel();
		
		// build list of categories
		//$javascript 	= 'class="inputbox" size="1" onchange="submitform( );"';
		$javascript 	= 'class="inputbox" size="1"';
		$filter_catid	= '';
		
		$query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
		. ' FROM #__phocadownload_categories AS a'
		. ' WHERE a.published = 1'
		//. ' AND a.approved = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$phocadownloads = $db->loadObjectList();

		$tree = array();
		$text = '';
		$tree = PhocaDownloadHelper::CategoryTreeOption($phocadownloads, $tree, 0, $text, -1);
		array_unshift($tree, JHTML::_('select.option', '0', '- '.JText::_('COM_PHOCADOWNLOAD_SELECT_CATEGORY').' -', 'value', 'text'));
		$lists['catid'] = JHTML::_( 'select.genericlist', $tree, 'catid',  $javascript , 'value', 'text', $filter_catid );
		//-----------------------------------------------------------------------
		
		$this->assignRef('lists',	$lists);
		$this->assignRef('tmpl',	$tmpl);
		parent::display($tpl);
	}
}
?>