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
jimport( 'joomla.application.component.view');

class PhocaDownloadViewPlay extends JView
{

	function display($tpl = null){		
		
		
		$app			= JFactory::getApplication();
		$params 		= $app->getParams();
		$tmpl			= array();
		$tmpl['user'] 	= &JFactory::getUser();
		$uri 			= &JFactory::getURI();
		$model			= &$this->getModel();
		$document		= &JFactory::getDocument();
		$fileId			= JRequest::getVar('id', 0, '', 'int');
		$file			= $model->getFile($fileId);
		
		$fileExt		= '';

		$filePath	= PhocaDownloadHelper::getPathSet('fileplay');
		$filePath	= str_replace ( '../', JURI::base(false).'', $filePath['orig_rel_ds']);
		if (isset($file[0]->filename_play) && $file[0]->filename_play != '') {
		
			$fileExt = PhocaDownloadHelper::getExtension($file[0]->filename_play);
			$canPlay	= PhocaDownloadHelper::canPlay($file[0]->filename_play);
			if ($canPlay) {
				$tmpl['playfilewithpath']	= $filePath . $file[0]->filename_play;
				//$tmpl['playerpath']			= JURI::base().'components/com_phocadownload/assets/jwplayer/';
				$tmpl['playerpath']			= JURI::base().'components/com_phocadownload/assets/flowplayer/';				
				$tmpl['playerwidth']		= $params->get( 'player_width', 328 ); 
				$tmpl['playerheight']		= $params->get( 'player_height', 200 );
			} else {
				echo JText::_('COM_PHOCADOWNLOAD_ERROR_NO_CORRECT_FILE_TO_PLAY_FOUND');exit;
			}
		} else {
			echo JText::_('COM_PHOCADOWNLOAD_ERROR_NO_FILE_TO_PLAY_FOUND');exit;
		}
		
		$tmpl['filetype']	= '';
		if ($fileExt == 'mp3') {
			$tmpl['filetype'] 		= 'mp3';
			$tmpl['playerheight']	= $params->get( 'player_mp3_height', 30 );
		}
	
		$this->assignRef('file',			$file);
		$this->assignRef('tmpl',			$tmpl);
		$this->assignRef('params',			$params);
		$this->assignRef('request_url',		$uri->toString());
		parent::display($tpl);
	}
}
?>