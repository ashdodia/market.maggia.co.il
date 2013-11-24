<?php

defined('_JEXEC') or die('Restricted access'); 

echo '<div id="phoca-dl-categories-box" class="pd-categories-view'.$this->params->get( 'pageclass_sfx' ).'">';

if ( $this->params->get( 'show_page_heading' ) ) { 
	
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}

if ( $this->tmpl['description'] != '') {
	
	echo '<div class="pd-desc">'. $this->tmpl['description']. '</div>';
}


if (!empty($this->categories)) {
	$i = 1;
	foreach ($this->categories as $value) {
		
		// Categories
		$numDoc 	= 0;
		$numSubcat	= 0;
		$catOutput 	= '';
		
		foreach ($value->subcategories as $valueCat) {
			
			// USER RIGHT - Access of categories - - - - -
			// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
			$rightDisplay	= 0;
			if (!empty($valueCat)) {
				$rightDisplay = PhocaDownloadHelper::getUserRight('accessuserid', $valueCat->accessuserid, $valueCat->access, $this->tmpl['user']->authorisedLevels(), $this->tmpl['user']->get('id', 0), 0);
				
			}
			// - - - - - - - - - - - - - - - - - - - - - -
			
			if ($rightDisplay == 1) {
				
				$catOutput 	.= '<div class="pd-subcategory">';
				$catOutput 	.= '<a href="'. JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($valueCat->id, $valueCat->alias))
							.'">'. $valueCat->title.'</a>';
			
				if ($this->tmpl['displaynumdocsecs'] == 1) {
					$catOutput  .=' <small>('.$valueCat->numdoc .')</small>';
				}
				$catOutput 	.= '</div>' . "\n";
				$numDoc = (int)$valueCat->numdoc + (int)$numDoc;
				$numSubcat++;
			}
		}
		
		// Don't display parent category
		// - if there is no catoutput
		// - if there is no rigths for it
		
		// USER RIGHT - Access of parent category - - - - -
		// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
		$rightDisplay	= 0;
		if (!empty($value)) {
			$rightDisplay = PhocaDownloadHelper::getUserRight('accessuserid', $value->accessuserid, $value->access, $this->tmpl['user']->authorisedLevels(), $this->tmpl['user']->get('id', 0), 0);
				
		}
		// - - - - - - - - - - - - - - - - - - - - - -
		
		if ($rightDisplay == 1) {
		
// =====================================================================================		
// BEGIN LAYOUT AREA
// =====================================================================================
			
			$pdTitle = '<a href="'. JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($value->id, $value->alias)).'">'. $value->title.'</a>';
			
			if ($this->tmpl['displaynumdocsecsheader'] == 1) {
				$pdTitle .= ' <small>('.$numSubcat.'/' . $value->numdoc .')</small>';
			}
			
			
			$pdDesc = '';
			$pdSubcategories = '';
			if ($this->tmpl['displaymaincatdesc']	 == 1) {
				$pdDesc .= $value->description;
			} else {
				if ($catOutput != '') {
					$pdSubcategories .= $catOutput;
				} else {
					$pdSubcategories .= '<div class="pd-no-subcat">'.JText::_('COM_PHOCADOWNLOAD_NO_SUBCATEGORIES').'</div>';
				}
			}
			
			$pdClear = '';
			if ($i%3==0) {
				$pdClear .= '<div class="pd-cb"></div>';
			}
			$i++;



			
			// ---------------------------------------------------
			//Convert
			// ---------------------------------------------------
			if ($this->tmpl['display_specific_layout'] == 0) {
			
				echo '<div class="pd-categoriesbox categories'.$i.' ">' ;
				echo '<div class="pd-title">'.$pdTitle.'</div>';
				
				if ($pdDesc != '') { echo '<div class="pd-desc">'.$pdDesc.'</div>';}
				echo $pdSubcategories;
				echo '</div>';
				echo $pdClear;
				
			} else {
				
				$categoriesLayout = PhocaDownloadHelper::getLayoutText('categories');
				
				/*'<div class="pd-categoriesbox">
				<div class="pd-title">{pdtitle}</div>
				{pdsubcategories}
				{pdclear}
				</div>';
				//<div class="pd-desc">{pdDescription}</div>*/
				
				$categoriesLayoutParams 	= PhocaDownloadHelper::getLayoutParams('categories');
				
				$replace	= array($pdTitle, $pdDesc, $pdSubcategories, $pdClear);
				$output		= str_replace($categoriesLayoutParams['search'], $replace, $categoriesLayout);
				
				echo $output;
			}
		}		
	}
}
echo '</div>'
    .'<div class="pd-cb"></div>';

	
// - - - - - - - - - - 	
// Most viewed docs (files)
// - - - - - - - - - - 
$outputFile		= '';

if (!empty($this->mostvieweddocs) && $this->tmpl['displaymostdownload'] == 1) {
	
	foreach ($this->mostvieweddocs as $value) {
		
		// USER RIGHT - Access of categories (if file is included in some not accessed category) - - - - -
		// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
		$rightDisplay	= 0;
		if (!empty($value)) {
			$rightDisplay = PhocaDownloadHelper::getUserRight('accessuserid', $value->cataccessuserid, $value->cataccess, $this->tmpl['user']->authorisedLevels(), $this->tmpl['user']->get('id', 0), 0);
		}
		// - - - - - - - - - - - - - - - - - - - - - -
		
		if ($rightDisplay == 1) {
			
			// FILESIZE
			if ($value->filename !='') {
				$absFile = str_replace('/', DS, JPath::clean($this->absfilepath . $value->filename));
				if (JFile::exists($absFile)) {
					$fileSize = PhocaDownloadHelper::getFileSizeReadable(filesize($absFile));
				} else {
					$fileSize = '';
				}
			}
			
			// IMAGE FILENAME
			$imageFileName = '';
			if ($value->image_filename !='') {
				$thumbnail = false;
				$thumbnail = preg_match("/phocathumbnail/i", $value->image_filename);
				if ($thumbnail) {
					$imageFileName 	= '';
				} else {
					$imageFileName = 'style="background: url(\''.$this->cssimagepath.$value->image_filename.'\') 0 center no-repeat;"';
				}
			}
		
			$outputFile .= '<div class="pd-document'.$this->tmpl['file_icon_size_md'].'" '.$imageFileName.'>';
			$outputFile .= '<a href="'
						. JRoute::_(PhocaDownloadHelperRoute::getCategoryRoute($value->categoryid,$value->categoryalias))
						.'">'. $value->title.'</a>'
						.' <small>(' .$value->categorytitle.')</small>';
			
			$outputFile .= PhocaDownloadHelper::displayNewIcon($value->date, $this->tmpl['displaynew']);
			$outputFile .= PhocaDownloadHelper::displayHotIcon($value->hits, $this->tmpl['displayhot']);		

			$outputFile .= '</div>' . "\n";
		}
	}
	
	if ($outputFile != '') {
		
		echo '<div class="pd-hr" style="clear:both">&nbsp;</div>';
		echo '<div id="phoca-dl-most-viewed-box">';
		echo '<div class="pd-documents"><h3>'. JText::_('COM_PHOCADOWNLOAD_MOST_DOWNLOADED_FILES').'</h3>';
		echo $outputFile;
		echo '</div></div>';
	
	}
}
echo '<div class="pd-cb">&nbsp;</div>';
echo $this->tmpl['phc'];
?>
