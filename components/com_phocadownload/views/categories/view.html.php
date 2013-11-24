<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view');

class PhocaDownloadViewCategories extends JView
{

	function display($tpl = null)
	{		
		$app			= JFactory::getApplication();
		$params 		= &$app->getParams();
		$tmpl['user'] 	= &JFactory::getUser();
		$model			= &$this->getModel();
		$document		= &JFactory::getDocument();
		$categories		= $model->getCategoriesList();
		$mostViewedDocs	= $model->getMostViewedDocsList($params);
		$tmpl['phc']					= PhocaDownloadHelper::getF();
		$tmpl['displaynew']				= $params->get( 'display_new', 0 );
		$tmpl['displayhot']				= $params->get( 'display_hot', 0 );
		$tmpl['displaymostdownload']	= $params->get( 'display_most_download', 1 );
		$tmpl['displaynumdocsecs']		= $params->get( 'display_num_doc_secs', 0 );
		$tmpl['displaynumdocsecsheader']= $params->get( 'display_num_doc_secs_header', 1 );
		$tmpl['file_icon_size_md'] 		= $params->get( 'file_icon_size_md', 16 );
		$tmpl['download_metakey'] 		= $params->get( 'download_metakey', '' );
		$tmpl['download_metadesc'] 		= $params->get( 'download_metadesc', '' );
		$tmpl['description']			= $params->get( 'description', '' );
		$tmpl['displaymaincatdesc']		= $params->get( 'display_main_cat_desc', 0 );
		$tmpl['display_specific_layout']= $params->get( 'display_specific_layout', 0 );
		$css							= $params->get( 'theme', 'phocadownload-grey' );
		
		$theme		= $params->get( 'theme', 'phocadownload-grey' );
		JHTML::stylesheet('components/com_phocadownload/assets/phocadownload.css' );
		JHTML::stylesheet('components/com_phocadownload/assets/'.$theme.'.css' );
		//JHTML::stylesheet('components/com_phocadownload/assets/phocadownloadbutton.css' );
		//$buttonS	= $params->get( 'button_style', 'rc' );
		//if ($buttonS == 'rc') {
		//	JHTML::stylesheet('components/com_phocadownload/assets/phocadownloadbuttonrc.css' );
		//}
		//JHTML::stylesheet('components/com_phocadownload/assets/phocadownloadrating.css' );
		$document->addCustomTag("<!--[if lt IE 7]>\n<link rel=\"stylesheet\" href=\""
		.JURI::base(true)
		."/components/com_phocadownload/assets/".$theme."-ie6.css\" type=\"text/css\" />\n<![endif]-->");
		
		JHTML::stylesheet('components/com_phocadownload/assets/custom.css' );
		
		
		// CSS Image Path
		$imagePath		= PhocaDownloadHelper::getPathSet('icon');
		$cssImagePath	= str_replace ( '../', JURI::base(true).'/', $imagePath['orig_rel_ds']);
		$filePath		= PhocaDownloadHelper::getPathSet('file');
		
		
		
		$this->assignRef('cssimagepath',		$cssImagePath);
		$this->assignRef('absfilepath',			$filePath['orig_abs_ds']);
		$this->assignRef('categories',			$categories);
		$this->assignRef('mostvieweddocs',		$mostViewedDocs);
		$this->assignRef('params',				$params);
		$this->assignRef('tmpl',				$tmpl);
		
		$this->_prepareDocument();
		parent::display($tpl);
		
	}
	
	protected function _prepareDocument() {
		
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
		$title = $this->params->get('page_heading', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		} else if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		//$this->document->setTitle($title);

		if (empty($title) || (isset($title) && $title == '')) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);*/
		
		  // get page title
          $title = $this->params->get('page_title', '');
          // if no title is set take the sitename only
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

		
		if ($this->tmpl['downloadmetadesc'] != '') {
			$this->document->setDescription($this->tmpl['downloadmetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		} 

		if ($this->tmpl['downloadmetakey'] != '') {
			$this->document->setMetadata('keywords', $this->tmpl['downloadmetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->getCfg('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

	}
	
}
?>