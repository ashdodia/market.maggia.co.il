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

class PhocaDownloadViewCategory extends JView
{

	protected $category;
	protected $subcategories;
	protected $files;
	
	function display($tpl = null) {		
		
		jimport( 'joomla.filesystem.folder' ); 
		jimport( 'joomla.filesystem.file' );
		$app				= JFactory::getApplication();
		$params 			= $app->getParams();
		$tmpl				= array();
		$tmpl['user'] 		= &JFactory::getUser();
		$uri 				= &JFactory::getURI();
		$model				= &$this->getModel();
		$document			= &JFactory::getDocument();
		$this->categoryId	= JRequest::getVar( 'id', 0, '', 'int' );
		$this->tagId		= JRequest::getVar( 'tagid', 0, '', 'int' );
		$limitStart			= JRequest::getVar( 'limitstart', 0, '', 'int' );
		
		$this->category		= $model->getCategory($this->categoryId);
		$this->subcategories= $model->getSubcategories($this->categoryId);
		$this->files		= $model->getFileList($this->categoryId, $this->tagId);
		$tmpl['pagination']	= $model->getPagination($this->categoryId, $this->tagId);

		
		// Limit start
		if ($limitStart > 0 ) {
			$tmpl['limitstarturl'] =  '&start='.$limitStart;
		} else {
			$tmpl['limitstarturl'] = '';
		}
		
		
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
		JHTML::stylesheet('components/com_phocadownload/assets/custom.css' );
		
		
		
		// PARAMS
		$tmpl['phca']					= PhocaDownloadHelper::getF();
		$tmpl['download_external_link'] = $params->get( 'download_external_link', '_self' );
		$tmpl['filename_or_name'] 		= $params->get( 'filename_or_name', 'filenametitle' );
		$tmpl['display_downloads'] 		= $params->get( 'display_downloads', 0 );
		$tmpl['display_description'] 	= $params->get( 'display_description', 3 );
		$tmpl['display_detail'] 		= $params->get( 'display_detail', 1 );
		$tmpl['display_play'] 			= $params->get( 'display_play', 0 );
		$tmpl['playerwidth']			= $params->get( 'player_width', 328 ); 
		$tmpl['playerheight']			= $params->get( 'player_height', 200 );
		$tmpl['playermp3height']		= $params->get( 'player_mp3_height', 30 );
		$tmpl['previewwidth']			= $params->get( 'preview_width', 640 ); 
		$tmpl['previewheight']			= $params->get( 'preview_height', 480 );
		$tmpl['display_preview'] 		= $params->get( 'display_preview', 0 );
		$tmpl['play_popup_window'] 		= $params->get( 'play_popup_window', 0 );
		$tmpl['preview_popup_window'] 	= $params->get( 'preview_popup_window', 0 );
		$tmpl['file_icon_size'] 		= $params->get( 'file_icon_size', 16 );
		$tmpl['displaynew']				= $params->get( 'display_new', 0 );
		$tmpl['displayhot']				= $params->get( 'display_hot', 0 );
		$tmpl['display_up_icon'] 		= $params->get( 'display_up_icon', 1 );
		$tmpl['allowed_file_types']		= $params->get( 'allowed_file_types', '' );
		$tmpl['disallowed_file_types']	= $params->get( 'disallowed_file_types', '' );
		$tmpl['enable_user_statistics']	= $params->get( 'enable_user_statistics', 1 );
		$tmpl['display_category_comments']= $params->get( 'display_category_comments', 0 );
		$tmpl['display_date_type'] 		= $params->get( 'display_date_type', 0 );
		$tmpl['display_file_view']		= $params->get('display_file_view', 0);
		$tmpl['download_metakey'] 		= $params->get( 'download_metakey', '' );
		$tmpl['download_metadesc'] 		= $params->get( 'download_metadesc', '' );
		$tmpl['display_rating_file'] 	= $params->get( 'display_rating_file', 0 );
		$tmpl['display_mirror_links'] 	= $params->get( 'display_mirror_links', 0 );
		$tmpl['display_report_link'] 	= $params->get( 'display_report_link', 0 );
		$tmpl['send_mail_download'] 	= $params->get( 'send_mail_download', 0 );// not boolean but id of user
		//$tmpl['send_mail_upload'] 		= $params->get( 'send_mail_upload', 0 );
		$tmpl['display_tags_links'] 	= $params->get( 'display_tags_links', 0 );
		$tmpl['display_specific_layout']= $params->get( 'display_specific_layout', 0 );

		// Facebook Comments
		$tmpl['fb_comment_app_id']		= $params->get( 'fb_comment_app_id', '' );
		$tmpl['fb_comment_width']		= $params->get( 'fb_comment_width', '550' );
		$tmpl['fb_comment_lang'] 		= $params->get( 'fb_comment_lang', 'en_US' );
		$tmpl['fb_comment_count'] 		= $params->get( 'fb_comment_count', '' );
		
		// RATING
		if ($tmpl['display_rating_file'] == 1 || $tmpl['display_rating_file'] == 3) {
			JHTML::_('behavior.framework', true);
			PhocaDownloadRateHelper::renderRateFileJS(1);
			$tmpl['display_rating_file'] = 1;
		} else {
			$tmpl['display_rating_file'] = 0;
		}
		


		// DOWNLOAD
		// - - - - - - - - - - - - - - - 
		$download	= JRequest::getVar( 'download', array(0), 'get', 'array' );
		$downloadId	= (int) $download[0];
		
		if ($downloadId > 0) {

			
			if (isset($this->category[0]->id) && (int)$this->category[0]->id > 0 ) {
				$currentLink	= 'index.php?option=com_phocadownload&view=category&id='.$this->category[0]->id.':'.$this->category[0]->alias.$tmpl['limitstarturl'] . '&Itemid='. JRequest::getVar('Itemid', 0, '', 'int');
			} else {
				$currentLink = $uri;
			}
			
			$fileData		= PhocaDownloadHelperFront::getDownloadData($downloadId, $currentLink);			
			PhocaDownloadHelperFront::download($fileData, $downloadId, $currentLink);
			
		}
		// - - - - - - - - - - - - - - - 
		
		// DETAIL
		// - - - - - - - - - - - - - - -
		if ($tmpl['display_detail'] == 2) {
			$buttonD = new JObject();
			$buttonD->set('methodname', 'modal-button');
			$buttonD->set('name', 'detail');
			$buttonD->set('modal', true);
			$buttonD->set('options', "{handler: 'iframe', size: {x: 600, y: 500}, overlayOpacity: 0.7, classWindow: 'phocadownloaddetailwindow', classOverlay: 'phocadownloaddetailoverlay'}");
		}
		
		JHTML::_('behavior.modal', 'a.pd-modal-button');
		// PLAY - - - - - - - - - - - -
		$windowWidthPl 		= (int)$tmpl['playerwidth'] + 50;
		$windowHeightPl 	= (int)$tmpl['playerheight'] + 50;
		$windowHeightPlMP3 	= (int)$tmpl['playermp3height'] + 50;
		if ($tmpl['play_popup_window'] == 1) {
			$buttonPl = new JObject();
			$buttonPl->set('methodname', 'js-button');
			$buttonPl->set('options', "window.open(this.href,'win2','width=".$windowWidthPl.",height=".$windowHeightPl.",scrollbars=yes,menubar=no,resizable=yes'); return false;");
			$buttonPl->set('optionsmp3', "window.open(this.href,'win2','width=".$windowWidthPl.",height=".$windowHeightPlMP3.",scrollbars=yes,menubar=no,resizable=yes'); return false;");
		} else {
			$document->addCustomTag( "<style type=\"text/css\"> \n"  
		." #sbox-window.phocadownloadplaywindow   {background-color:#fff;padding:2px} \n"
		." #sbox-overlay.phocadownloadplayoverlay  {background-color:#000;} \n"			
		." </style> \n");
			$buttonPl = new JObject();
			$buttonPl->set('name', 'image');
			$buttonPl->set('modal', true);
			$buttonPl->set('methodname', 'modal-button');
			$buttonPl->set('options', "{handler: 'iframe', size: {x: ".$windowWidthPl.", y: ".$windowHeightPl."}, overlayOpacity: 0.7, classWindow: 'phocadownloadplaywindow', classOverlay: 'phocadownloadplayoverlay'}");
			$buttonPl->set('optionsmp3', "{handler: 'iframe', size: {x: ".$windowWidthPl.", y: ".$windowHeightPlMP3."}, overlayOpacity: 0.7, classWindow: 'phocadownloadplaywindow', classOverlay: 'phocadownloadplayoverlay'}");
		}
		// - - - - - - - - - - - - - - -
		// PREVIEW - - - - - - - - - - - -
		$windowWidthPr 	= (int)$tmpl['previewwidth'] + 20;
		$windowHeightPr = (int)$tmpl['previewheight'] + 20;
		if ($tmpl['preview_popup_window'] == 1) {
			$buttonPr = new JObject();
			$buttonPr->set('methodname', 'js-button');
			$buttonPr->set('options', "window.open(this.href,'win2','width=".$windowWidthPr.",height=".$windowHeightPr.",scrollbars=yes,menubar=no,resizable=yes'); return false;");
		} else {
			$document->addCustomTag( "<style type=\"text/css\"> \n"  
		." #sbox-window.phocadownloadpreviewwindow   {background-color:#fff;padding:2px} \n"
		." #sbox-overlay.phocadownloadpreviewoverlay  {background-color:#000;} \n"			
		." </style> \n");
			$buttonPr = new JObject();
			$buttonPr->set('name', 'image');
			$buttonPr->set('modal', true);
			$buttonPr->set('methodname', 'modal-button');
			$buttonPr->set('options', "{handler: 'iframe', size: {x: ".$windowWidthPr.", y: ".$windowHeightPr."}, overlayOpacity: 0.7, classWindow: 'phocadownloadpreviewwindow', classOverlay: 'phocadownloadpreviewoverlay'}");
			$buttonPr->set('optionsimg', "{handler: 'image', size: {x: 200, y: 150}, overlayOpacity: 0.7, classWindow: 'phocadownloadpreviewwindow', classOverlay: 'phocadownloadpreviewoverlay'}");
		}
		// - - - - - - - - - - - - - - -
		
		$imagePath		= PhocaDownloadHelper::getPathSet('icon');
		$cssImagePath	= str_replace ( '../', JURI::base(true).'/', $imagePath['orig_rel_ds']);
		$filePath		= PhocaDownloadHelper::getPathSet('file');

		$tmpl['action']	= $uri->toString();
		
		
			
		$this->assignRef('tmpl',			$tmpl);
		$this->assignRef('params',			$params);
		$this->assignRef('cssimagepath',	$cssImagePath);
		$this->assignRef('absfilepath',		$filePath['orig_abs_ds']);
		$this->assignRef('buttonpl',			$buttonPl);
		$this->assignRef('buttonpr',			$buttonPr);
		$this->assignRef('buttond',			$buttonD);

		if (isset($this->category[0]) && is_object($this->category[0])){
			$this->_prepareDocument($this->category[0]);
		}

		parent::display($tpl);
		
	}
	
	protected function _prepareDocument($category) {
		
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

		/*
		$title = $this->params->get('page_title', '');
		
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
          // if no page title is set take the category title only
          if (empty($title)) {
             $title = $category->title;
          }
          // else append the category title
          else {
              $title .= " - " . $category->title;
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

		
		if ($category->metadesc != '') {
			$this->document->setDescription($category->metadesc);
		} else if ($this->tmpl['downloadmetadesc'] != '') {
			$this->document->setDescription($this->tmpl['downloadmetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		} 

		if ($category->metakey != '') {
			$this->document->setMetadata('keywords', $category->metakey);
		} else if ($this->tmpl['downloadmetakey'] != '') {
			$this->document->setMetadata('keywords', $this->tmpl['downloadmetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->getCfg('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}
		
		// Breadcrumbs TODO (Add the whole tree)
		/*$pathway 		= $app->getPathway();
		if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 0) {
				// $pathway->addItem( JText::_('COM_PHOCADOWNLOAD_CATEGORIES'), JRoute::_(PhocaDownloadHelperRoute::getCategoriesRoute()));
			} else if ($this->category[0]->parentid > 0) {
				$pathway->addItem($this->category[0]->parenttitle, JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias)));
			}
		}

		if (!empty($this->category[0]->title)) {
			$pathway->addItem($this->category[0]->title);
		}*/
		
		// Breadcrumbs TODO (Add the whole tree)
		$pathway 		= $app->getPathway();
		if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 0) {
				// $pathway->addItem( JText::_('COM_PHOCADOWNLOAD_CATEGORIES'), JRoute::_(PhocaDownloadHelperRoute::getCategoriesRoute()));
			} else if ($this->category[0]->parentid > 0) {
				$curpath = $pathway->getPathwayNames();
				if($this->category[0]->parenttitle != $curpath[count($curpath)-1]){
				 	$pathway->addItem($this->category[0]->parenttitle, JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias)));
				}
			}
		}

		if (!empty($this->category[0]->title)) {
			$curpath = $pathway->getPathwayNames();
			if($this->category[0]->title != $curpath[count($curpath)-1]){
				$pathway->addItem($this->category[0]->title);
			}
		}

	}
}
?>