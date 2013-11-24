<?php
defined('_JEXEC') or die('Restricted access'); 

echo '<div id="phoca-dl-category-box" class="pd-category-view'.$this->params->get( 'pageclass_sfx' ).'">';

if ( $this->params->get( 'show_page_heading' ) ) { 
	
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}
// Search by tags - the category rights must be checked for every file
$this->checkRights = 1;
// -------------------------------------------------------------------
if ((int)$this->tagId > 0) {
	
	echo $this->loadTemplate('files');
	$this->checkRights = 1;
	
	// Pagination - - - - -
	$this->tmpl['action'] = str_replace('&amp;', '&', $this->tmpl['action']);
	//$this->tmpl['action'] = str_replace('&', '&amp;', $this->tmpl['action']);
	$this->tmpl['action'] = htmlspecialchars($this->tmpl['action']);
	echo '<form action="'.htmlspecialchars($this->tmpl['action']).'" method="post" name="adminForm">'. "\n";

	if (count($this->files)) {
		echo '<div class="pgcenter"><div class="pagination">';
		
		if ($this->params->get('show_pagination_limit')) {
			
			echo '<div class="pginline">'
				.JText::_('COM_PHOCADOWNLOAD_DISPLAY_NUM') .'&nbsp;'
				.$this->tmpl['pagination']->getLimitBox()
				.'</div>';
		}
		
		if ($this->params->get('show_pagination')) {
		
			echo '<div style="margin:0 10px 0 10px;display:inline;" class="sectiontablefooter'.$this->params->get( 'pageclass_sfx' ).'" id="pg-pagination" >'
				.$this->tmpl['pagination']->getPagesLinks()
				.'</div>'
			
				.'<div style="margin:0 10px 0 10px;display:inline;" class="pagecounter">'
				.$this->tmpl['pagination']->getPagesCounter()
				.'</div>';
		}
		echo '</div></div>'. "\n";

	}
	//echo '<input type="hidden" name="controller" value="category" />';
	echo JHTML::_( 'form.token' );
	echo '</form>';
	// - - - - - - - - - - - -
	
	
	
} else {
	if (!empty($this->category[0])) {
		
		echo '<div class="pd-category">';
		if ($this->tmpl['display_up_icon'] == 1) {
			
			if (isset($this->category[0]->parentid)) {
				if ($this->category[0]->parentid == 0) {
					
					$linkUp = JRoute::_(PhocaDownloadHelperRoute::getCategoriesRoute());
					$linkUpText = JText::_('COM_PHOCADOWNLOAD_CATEGORIES');
				} else if ($this->category[0]->parentid > 0) {
					$linkUp = JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias));
					$linkUpText = $this->category[0]->parenttitle;
				} else {
					$linkUp 	= '#';
					$linkUpText = ''; 
				}
				echo '<div class="pdtop">'
					.'<a title="'.$linkUpText.'" href="'. $linkUp.'" >'
					.JHTML::_('image', 'components/com_phocadownload/assets/images/up.png', JText::_('COM_PHOCADOWNLOAD_UP'))
					.'</a></div>';
			}
		}
	} else {
		echo '<div class="pd-category"><div class="pdtop"></div>';
	}



	if (!empty($this->category[0])) {
		
		// USER RIGHT - Access of categories (if file is included in some not accessed category) - - - - -
		// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
		$rightDisplay	= 0;
		if (!empty($this->category[0])) {
			$rightDisplay = PhocaDownloadHelper::getUserRight('accessuserid', $this->category[0]->cataccessuserid, $this->category[0]->cataccess, $this->tmpl['user']->authorisedLevels(), $this->tmpl['user']->get('id', 0), 0);
		}
		// - - - - - - - - - - - - - - - - - - - - - -
		if ($rightDisplay == 1) {
			
			$this->checkRights = 0;
			$l = new PhocaDownloadLayout();
			
			echo '<h3 class="pd-ctitle">'.$this->category[0]->title. '</h3>';

			// Description
			/*if ($l->isValueEditor($this->category[0]->description)) {
				echo '<div class="pd-cdesc">'.$this->category[0]->description.'</div>';
			}*/
			
			// Description
			 if ($l->isValueEditor($this->category[0]->description)) {
				
				echo '<div class="pd-cdesc">';
				echo JHTML::_('content.prepare', $this->category[0]->description);
				echo '</div>';
			 }

			// Subcategories
			
			if (!empty($this->subcategories)) {	
				foreach ($this->subcategories as $valueSubCat) {
					
					echo '<div class="pd-subcategory">';
					echo '<a href="'. JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($valueSubCat->id, $valueSubCat->alias))
						 .'">'. $valueSubCat->title.'</a>';
					echo ' <small>('.$valueSubCat->numdoc.')</small></div>' . "\n";
					$subcategory = 1;
				}
				
				echo '<div class="pd-hr-cb"></div>';
			}
			
			// =====================================================================================		
			// BEGIN LAYOUT AREA
			// =====================================================================================

			echo $this->loadTemplate('files');

			// =====================================================================================		
			// END LAYOUT AREA
			// =====================================================================================
			
			

			// Pagination - - - - -
			$this->tmpl['action'] = str_replace('&amp;', '&', $this->tmpl['action']);
			//$this->tmpl['action'] = str_replace('&', '&amp;', $this->tmpl['action']);
			$this->tmpl['action'] = htmlspecialchars($this->tmpl['action']);
			
			echo '<form action="'.$this->tmpl['action'].'" method="post" name="adminForm">'. "\n";

			if (count($this->category[0])) {
				
				echo '<div class="pd-cb">&nbsp;</div>';
				echo '<div class="pgcenter"><div class="pagination">';
				
				if ($this->params->get('show_pagination_limit')) {
					
					echo '<div class="pginline">'
						.JText::_('COM_PHOCADOWNLOAD_DISPLAY_NUM') .'&nbsp;'
						.$this->tmpl['pagination']->getLimitBox()
						.'</div>';
				}
				
				if ($this->params->get('show_pagination')) {
				
					echo '<div style="margin:0 10px 0 10px;display:inline;" class="sectiontablefooter'.$this->params->get( 'pageclass_sfx' ).'" id="pg-pagination" >'
						.$this->tmpl['pagination']->getPagesLinks()
						.'</div>'
					
						.'<div style="margin:0 10px 0 10px;display:inline;" class="pagecounter">'
						.$this->tmpl['pagination']->getPagesCounter()
						.'</div>';
				}
				echo '</div></div>'. "\n";

			}
			//echo '<input type="hidden" name="controller" value="category" />';
			echo JHTML::_( 'form.token' );
			echo '</form>';
			// - - - - - - - - - - - -
			
			
			if ($this->tmpl['display_category_comments'] == 1) {
				if (JComponentHelper::isEnabled('com_jcomments', true)) {
					include_once(JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
					echo JComments::showComments($this->category[0]->id, 'com_phocadownload', JText::_('COM_PHOCADOWNLOAD_CATEGORY') .' '. $this->category[0]->title);
				}
			}
			
			if ($this->tmpl['display_category_comments'] == 2) {
				echo '<div class="pd-fbcomments">'.$this->loadTemplate('comments-fb').'</div>';
			}
			
		} else {
			echo '<h3>'.JText::_('COM_PHOCADOWNLOAD_CATEGORY'). '</h3>';
			echo '<div class="pd-error">'.JText::_('COM_PHOCADOWNLOAD_NO_RIGHTS_ACCESS_CATEGORY').'</div>';
		}
		
		echo '</div>';
	} else {
		
		//echo '<h3>&nbsp;</h3>';
		echo '</div>';
	}
}
echo '</div><div class="pd-cb">&nbsp;</div>';
echo $this->tmpl['phca'];
?>
