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
jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );

class PhocaDownloadViewFile extends JView
{

	protected $file;
	protected $category;

	function display($tpl = null){		
		
		$app			= JFactory::getApplication();
		$params 		= $app->getParams();
		$tmpl			= array();
		$tmpl['user'] 	= &JFactory::getUser();
		$uri 			= &JFactory::getURI();
		$model			= &$this->getModel();
		$document		= &JFactory::getDocument();
		$fileId			= JRequest::getVar('id', 0, '', 'int');
		$limitStart		= JRequest::getVar( 'start', 0, '', 'int');// we need it for category back link
		$tmplR			= JRequest::getVar( 'tmpl', '', '', 'string' );
		

		$tmpl['tmplr'] = 0;
		if ($tmplR == 'component') {
			$tmpl['tmplr'] = 1;
		}
		$tmpl['limitstart'] = $limitStart;
		if ($limitStart > 0 ) {
			$tmpl['limitstarturl'] = '&start='.$limitStart;
		} else {
			$tmpl['limitstarturl'] = '';
		}
		
		$this->category		= $model->getCategory($fileId);
		$this->file			= $model->getFile($fileId, $tmpl['limitstarturl']);
		
		$tmpl['licenseboxheight']	= $params->get( 'license_box_height', 300 );
		
		// Theme
		$theme		= $params->get( 'theme', 'phocadownload-grey' );
		JHTML::stylesheet('components/com_phocadownload/assets/phocadownload.css' );
		JHTML::stylesheet('components/com_phocadownload/assets/'.$theme.'.css' );
		JHTML::stylesheet('components/com_phocadownload/assets/phocadownloadbutton.css' );
		$buttonS	= $params->get( 'button_style', 'rc' );
		if ($buttonS == 'rc') {
			JHTML::stylesheet('components/com_phocadownload/assets/phocadownloadbuttonrc.css' );
		}
		JHTML::stylesheet('components/com_phocadownload/assets/phocadownloadrating.css' );
		$document->addCustomTag('<script type="text/javascript" src="'.JURI::root().'components/com_phocadownload/assets/overlib/overlib_mini.js"></script>');
	
		$js				= 'var enableDownloadButtonPD = 0;'
						 .'function enableDownloadPD() {'
						 .' if (enableDownloadButtonPD == 0) {'
						 .'   document.forms[\'phocadownloadform\'].elements[\'pdlicensesubmit\'].disabled=false;'
						 .'   enableDownloadButtonPD = 1;'
						 .' } else {'
						 .'   document.forms[\'phocadownloadform\'].elements[\'pdlicensesubmit\'].disabled=true;'
						 .'   enableDownloadButtonPD = 0;'
						 .' }'
						 .'}';
		$document->addScriptDeclaration($js);
		JHTML::stylesheet('components/com_phocadownload/assets/custom.css' );
		
		// PARAMS
		$tmpl['filename_or_name'] 		= $params->get( 'filename_or_name', 'filename' );
		$tmpl['display_up_icon'] 		= $params->get( 'display_up_icon', 1 );
		$tmpl['allowed_file_types']		= $params->get( 'allowed_file_types', '' );
		$tmpl['disallowed_file_types']	= $params->get( 'disallowed_file_types', '' );
		$tmpl['enable_user_statistics']	= $params->get( 'enable_user_statistics', 1 );
		$tmpl['display_file_comments'] 	= $params->get( 'display_file_comments', 0 );
		$tmpl['file_icon_size'] 		= $params->get( 'file_icon_size', 16 );
		$tmpl['display_file_view']		= $params->get('display_file_view', 0);
		$tmpl['download_metakey'] 		= $params->get( 'download_metakey', '' );
		$tmpl['download_metadesc'] 		= $params->get( 'download_metadesc', '' );
		$tmpl['display_downloads'] 		= $params->get( 'display_downloads', 0 );
		$tmpl['display_date_type'] 		= $params->get( 'display_date_type', 0 );
		$tmpl['displaynew']				= $params->get( 'display_new', 0 );
		$tmpl['displayhot']				= $params->get( 'display_hot', 0 );
		$tmpl['phoca_dwnld']			= PhocaDownloadHelper::getF();
		$tmpl['download_external_link'] = $params->get( 'download_external_link', '_self' );
		$tmpl['display_report_link'] 	= $params->get( 'display_report_link', 0 );
		$tmpl['send_mail_download'] 	= $params->get( 'send_mail_download', 0 );// not boolean but id of user
		//$tmpl['send_mail_upload'] 		= $params->get( 'send_mail_upload', 0 );
		$tmpl['display_rating_file'] 		= $params->get( 'display_rating_file', 0 );
		$tmpl['display_tags_links'] 	= $params->get( 'display_tags_links', 0 );
		$tmpl['display_mirror_links'] 	= $params->get( 'display_mirror_links', 0 );
		$tmpl['display_specific_layout']= $params->get( 'display_specific_layout', 0 );
		$tmpl['display_detail']			= $params->get( 'display_detail', 1);
	
		// Facebook Comments
		$tmpl['fb_comment_app_id']		= $params->get( 'fb_comment_app_id', '' );
		$tmpl['fb_comment_width']		= $params->get( 'fb_comment_width', '550' );
		$tmpl['fb_comment_lang'] 		= $params->get( 'fb_comment_lang', 'en_US' );
		$tmpl['fb_comment_count'] 		= $params->get( 'fb_comment_count', '' );
		
		
		
		// RATING
		if ($tmpl['display_rating_file'] == 2 || $tmpl['display_rating_file'] == 3 ) {
			JHTML::_('behavior.framework', true);
			PhocaDownloadRateHelper::renderRateFileJS(1);
			$tmpl['display_rating_file'] = 1;
		} else {
			$tmpl['display_rating_file'] = 0;
		}

		// DOWNLOAD
		// - - - - - - - - - - - - - - - 
		$download				= JRequest::getVar( 'download', array(0), '', 'array' );
		$licenseAgree			= JRequest::getVar( 'license_agree', '', 'post', 'string' );
		$downloadId		 		= (int) $download[0];

		if ($downloadId > 0) {
		
			if (isset($this->file[0]->id)) {
				$currentLink	= 'index.php?option=com_phocadownload&view=file&id='.$this->file[0]->id.':'.$this->file[0]->alias. $tmpl['limitstarturl'] . '&Itemid='. JRequest::getVar('Itemid', 0, '', 'int');
			} else {
				$currentLink	= 'index.php?option=com_phocadownload&view=sections&Itemid='. JRequest::getVar('Itemid', 0, '', 'int');
			}
		
			
			// Check Token
			$token	= JUtility::getToken();
			if (!JRequest::getInt( $token, 0, 'post' )) {
				//JError::raiseError(403, 'Request Forbidden');
				$app->redirect(JRoute::_('index.php', false), JText::_('COM_PHOCADOWNLOAD_INVALID_TOKEN'));
				exit;
			}
			
			// Check License Agreement
			if (empty($licenseAgree)) {
				$app->redirect(JRoute::_($currentLink, false), JText::_('COM_PHOCADOWNLOAD_WARNING_AGREE_LICENSE_TERMS'));
				exit;
			}
			
			
			$fileData		= PhocaDownloadHelperFront::getDownloadData($downloadId, $currentLink);
			PhocaDownloadHelperFront::download($fileData, $downloadId, $currentLink);
			
		}
		// - - - - - - - - - - - - - - - 
		
		// CSS Image Path
		$imagePath		= PhocaDownloadHelper::getPathSet('icon');
		$cssImagePath	= str_replace ( '../', JURI::base(true).'/', $imagePath['orig_rel_ds']);
		
		$filePath		= PhocaDownloadHelper::getPathSet('file');

		$this->assignRef('tmpl',			$tmpl);
		
		
		$this->assignRef('params',			$params);
		$this->assignRef('cssimagepath',	$cssImagePath);
		$this->assignRef('absfilepath',		$filePath['orig_abs_ds']);
		$this->assignRef('request_url',		$uri->toString());
		
		
		if (isset($this->category[0]) && is_object($this->category[0]) && isset($this->file[0]) && is_object($this->file[0])){
			$this->_prepareDocument($this->category[0], $this->file[0]);
		}
		parent::display($tpl);
		
	}
	
	protected function _prepareDocument($category, $file) {
		
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= &$app->getParams();
		$title 		= null;
		
		$this->tmpl['downloadmetakey'] 		= $this->params->get( 'download_metakey', '' );
		$this->tmpl['downloadmetadesc'] 	= $this->params->get( 'download_metadesc', '' );
		

		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		/*$title = $this->params->get('page_title', '');
		if (empty($title) || (isset($title) && $title == '')) {
			$title = $this->item->title;
		}
		if (empty($title) || (isset($title) && $title == '')) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		} else if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		//$this->document->setTitle($title);

		$this->document->setTitle($title);*/
		
		  // get page title
          $title = $this->params->get('page_title', '');
          // if the page title is set append the file title (if set!)
          if (!empty($title) && !empty($file->title)) {
             $title .= " - " . $file->title;
          }
          // if still is no title is set take the sitename only
          if (empty($title)) {
             $title = $app->getCfg('sitename');
          }
          // else add the title before or after the sitename
          elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
             $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
          }
          elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
             $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
          }
          $this->document->setTitle($title);

		
		if ($file->metadesc != '') {
			$this->document->setDescription($file->metadesc);
		} else if ($this->tmpl['downloadmetadesc'] != '') {
			$this->document->setDescription($this->tmpl['downloadmetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		} 

		if ($file->metakey != '') {
			$this->document->setMetadata('keywords', $file->metakey);
		} else if ($this->tmpl['downloadmetakey'] != '') {
			$this->document->setMetadata('keywords', $this->tmpl['downloadmetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->getCfg('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}
		
		// Breadcrumbs TODO (Add the whole tree)
		$pathway 		= $app->getPathway();
		if (isset($category->id)) {
			if ($category->id > 0) {
				$pathway->addItem($category->title, JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($category->id, $category->alias)));
			}
		}
		
		if (!empty($file->title)) {
			$pathway->addItem($file->title);
		}

	}
}
?>