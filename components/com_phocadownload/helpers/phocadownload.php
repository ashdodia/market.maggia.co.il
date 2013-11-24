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
jimport('joomla.application.component.controller');
jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );

class PhocaDownloadHelperFront
{	
	function download($fileData, $downloadId, $currentLink) {
			
		$app			= JFactory::getApplication();
		$params 		= $app->getParams();
		$directLink 	= $fileData['directlink'];// Direct Link 0 or 1
		$externalLink 	= $fileData['externallink'];
		$absOrRelFile	= $fileData['file'];// Relative Path or Absolute Path
		
		// NO FILES FOUND (abs file)
		$error 		= false;
		$error 		= preg_match("/COM_PHOCADOWNLOAD_ERROR/i", $absOrRelFile);
		
		
		if ($error) {
			$msg = JText::_('COM_PHOCADOWNLOAD_ERROR_WHILE_DOWNLOADING_FILE') . ' ' . JText::_($absOrRelFile);
			$app->redirect(JRoute::_($currentLink), $msg);
		} else {
			
			// Get extensions
			$extension = JFile::getExt($absOrRelFile);
			
			$aft = $params->get( 'allowed_file_types_download', PhocaDownloadHelper::getDefaultAllowedMimeTypesDownload() );
			$dft = $params->get( 'disallowed_file_types_download', '' );
			
			// Get Mime from params ( ext --> mime)
			$allowedMimeType = PhocaDownloadHelper::getMimeType($extension, $aft);
			$disallowedMimeType = PhocaDownloadHelper::getMimeType($extension, $dft);
			
			
			// NO MIME FOUND
			$errorAllowed 		= false;// !!! IF YES - Disallow Downloading
			$errorDisallowed 	= false;// !!! IF YES - Allow Downloading
			
			$errorAllowed 		= preg_match("/PhocaError/i", $allowedMimeType);
			$errorDisallowed	= preg_match("/PhocaError/i", $disallowedMimeType);
			
			if ($errorAllowed) {
				$msg = JText::_('COM_PHOCADOWNLOAD_WARNFILETYPE_DOWNLOAD');
				$app->redirect(JRoute::_($currentLink), $msg);
			} else if (!$errorDisallowed) {
				$msg = JText::_('COM_PHOCADOWNLOAD_WARNFILETYPE_DISALLOWED_DOWNLOAD');
				$app->redirect(JRoute::_($currentLink), $msg);
			
			} else {
				
				if ($directLink == 1) {
					// Direct Link on the same server
					
					$fileWithoutPath	= basename($absOrRelFile);
					$addHit	= PhocaDownloadHelperFront::_hit($downloadId);
					
					if ((int)$params->get('send_mail_download', 0) > 0) {
						PhocaDownloadHelper::sendPhocaDownloadMail((int)$params->get('send_mail_download', 0), $fileWithoutPath, 1);
					}
					
					// USER Statistics
					if ((int)$params->get('enable_user_statistics', 1) == 1) {
						$addUserStat = PhocaUserStatHelper::createUserStatEntry($downloadId);
					}
					
					$app->redirect ($absOrRelFile);
					exit;
				} else if ($directLink == 0 && $externalLink != '') {
					
					
					// External Link but with redirect
					// In case there is directLink the external Link does not go this way but directly to the external URL
					$addHit	= PhocaDownloadHelperFront::_hit($downloadId);
					
					if ((int)$params->get('send_mail_download', 0) > 0) {
						PhocaDownloadHelper::sendPhocaDownloadMail((int)$params->get('send_mail_download', 0), $externalLink, 1);
					}
					
					// USER Statistics
					if ((int)$params->get('enable_user_statistics', 1) == 1) {
						$addUserStat = PhocaUserStatHelper::createUserStatEntry($downloadId);
					}
					
					$app->redirect ($externalLink);
					exit;
				
				} else {
				
					// Clears file status cache
					clearstatcache();
					
					
				
					$fileWithoutPath	= basename($absOrRelFile);
					$fileSize 			= filesize($absOrRelFile);
					$mimeType			= '';
					$mimeType			= $allowedMimeType;
					
					// HIT Statistics
					$addHit	= PhocaDownloadHelperFront::_hit($downloadId);
					
					if ((int)$params->get('send_mail_download', 0) > 0) {
						PhocaDownloadHelper::sendPhocaDownloadMail((int)$params->get('send_mail_download', 0), $fileWithoutPath, 1);
					}
					
					// USER Statistics
					if ((int)$params->get('enable_user_statistics', 1) == 1) {
						$addUserStat = PhocaUserStatHelper::createUserStatEntry($downloadId);
					}
					
					if ($fileSize == 0 ) {
						die(JText::_('COM_PHOCADOWNLOAD_FILE_SIZE_EMPTY'));
						exit;
					}
					
					
					
					// Clean the output buffer
					ob_end_clean();
					
					// test for protocol and set the appropriate headers
				    jimport( 'joomla.environment.uri' );
				    $_tmp_uri 		= &JURI::getInstance( JURI::current() );
				    $_tmp_protocol 	= $_tmp_uri->getScheme();
					if ($_tmp_protocol == "https") {
						// SSL Support
						header('Cache-Control: private, max-age=0, must-revalidate, no-store');
				    } else {
						header("Cache-Control: public, must-revalidate");
						header('Cache-Control: pre-check=0, post-check=0, max-age=0');
						header("Pragma: no-cache");
						header("Expires: 0");
					} /* end if protocol https */
					header("Content-Description: File Transfer");
					header("Expires: Sat, 30 Dec 1990 07:07:07 GMT");
					header("Accept-Ranges: bytes");

					
					// HTTP Range
				/*	$httpRange = 0;
					if(isset($_SERVER['HTTP_RANGE'])) {
						list($a, $httpRange) = explode('=', $_SERVER['HTTP_RANGE']);
						str_replace($httpRange, '-', $httpRange);
						$newFileSize	= $fileSize - 1;
						$newFileSizeHR	= $fileSize - $httpRange;
						header("HTTP/1.1 206 Partial Content");
						header("Content-Length: ".(string)$newFileSizeHR);
						header("Content-Range: bytes ".$httpRange . $newFileSize .'/'. $fileSize);
					} else {
						$newFileSize	= $fileSize - 1;
						header("Content-Length: ".(string)$fileSize);
						header("Content-Range: bytes 0-".$newFileSize . '/'.$fileSize);
					}
					header("Content-Type: " . (string)$mimeType);
					header('Content-Disposition: attachment; filename="'.$fileWithoutPath.'"');
					header("Content-Transfer-Encoding: binary\n");*/
					
					// Modified by Rene
					// HTTP Range - see RFC2616 for more informations (http://www.ietf.org/rfc/rfc2616.txt)
					$httpRange   = 0;
					$newFileSize = $fileSize - 1;
					// Default values! Will be overridden if a valid range header field was detected!
					$resultLenght = (string)$fileSize;
					$resultRange  = "0-".$newFileSize;
					// We support requests for a single range only.
					// So we check if we have a range field. If yes ensure that it is a valid one.
					// If it is not valid we ignore it and sending the whole file.
					if(isset($_SERVER['HTTP_RANGE']) && preg_match('%^bytes=\d*\-\d*$%', $_SERVER['HTTP_RANGE'])) {
						// Let's take the right side
						list($a, $httpRange) = explode('=', $_SERVER['HTTP_RANGE']);
						// and get the two values (as strings!)
						$httpRange = explode('-', $httpRange);
						// Check if we have values! If not we have nothing to do!
						if(!empty($httpRange[0]) || !empty($httpRange[1])) {
							// We need the new content length ...
							$resultLenght	= $fileSize - $httpRange[0] - $httpRange[1];
							// ... and we can add the 206 Status.
							header("HTTP/1.1 206 Partial Content");
							// Now we need the content-range, so we have to build it depending on the given range!
							// ex.: -500 -> the last 500 bytes
							if(empty($httpRange[0]))
								$resultRange = $resultLenght.'-'.$newFileSize;
							// ex.: 500- -> from 500 bytes to filesize
							elseif(empty($httpRange[1]))
								$resultRange = $httpRange[0].'-'.$newFileSize;
							// ex.: 500-1000 -> from 500 to 1000 bytes
							else
								$resultRange = $httpRange[0] . '-' . $httpRange[1];
							//header("Content-Range: bytes ".$httpRange . $newFileSize .'/'. $fileSize);
						} 
					}
					header("Content-Length: ". $resultLenght);
					header("Content-Range: bytes " . $resultRange . '/' . $fileSize);
					
					header("Content-Type: " . (string)$mimeType);
					header('Content-Disposition: attachment; filename="'.$fileWithoutPath.'"');
					header("Content-Transfer-Encoding: binary\n");
					
					//@readfile($absOrRelFile);
					
					// Try to deliver in chunks
					@set_time_limit(0);
					$fp = @fopen($absOrRelFile, 'rb');
					if ($fp !== false) {
						while (!feof($fp)) {
							echo fread($fp, 8192);
						}
						fclose($fp);
					} else {
						@readfile($absOrRelFile);
					}
					flush();
					exit;
					
					/*
					http://www.phoca.cz/forum/viewtopic.php?f=31&t=11811
					
					$fp = @fopen($absOrRelFile, 'rb');
					// HTTP Range - see RFC2616 for more informations (http://www.ietf.org/rfc/rfc2616.txt)
					$newFileSize = $fileSize - 1;
					// Default values! Will be overridden if a valid range header field was detected!
					$rangeStart = 0;
					$rangeEnd = 0;
					$resultLength = $fileSize;
					// We support requests for a single range only.
					// So we check if we have a range field. If yes ensure that it is a valid one.
					// If it is not valid we ignore it and sending the whole file.
					if ($fp && isset($_SERVER['HTTP_RANGE']) && preg_match('%^bytes=\d*\-\d*$%', $_SERVER['HTTP_RANGE'])) {
						// Let's take the right side
						list($a, $httpRange) = explode('=', $_SERVER['HTTP_RANGE']);
						// and get the two values (as strings!)
						$httpRange = explode('-', $httpRange);
						// Check if we have values! If not we have nothing to do!
						if (sizeof($httpRange) == 2) {
							// Explictly convert to int
							$rangeStart = intval($httpRange[0]);
							$rangeEnd = intval($httpRange[1]); // Allowed to be empty == 0
							if (($rangeStart || $rangeEnd) // something actually set?
							&& $rangeStart < $fileSize // must be smaller
							&& $rangeEnd < $fileSize // must be smaller
							&& (!$rangeEnd || $rangeEnd > $rangeStart) // end > start, if end is set
							) {
								header("HTTP/1.1 206 Partial Content");
								if (!$rangeEnd) {
									$resultLength = $fileSize - $rangeStart;
									$range = $rangeStart . "-" . ($fileSize - 1) . "/" . $fileSize;
								} else {
									$resultLength = ($rangeEnd - $rangeStart 1);
									$range = $rangeStart . "-" . $rangeEnd . "/" . $fileSize;
								}
								header("Content-Range: bytes " . $range);
							} else {
								// Didn't validate: kill
								$rangeStart = 0;
								$rangeEnd = 0;
							}
						}
					}

					header("Content-Length: ". $resultLength);
					header("Content-Type: " . (string)$mimeType);
					header('Content-Disposition: attachment; filename="'.$fileWithoutPath.'"');
					header("Content-Transfer-Encoding: binary\n");
					@@ -211,13 +198,25 @@ class PhocaDownloadHelperFront

					// Try to deliver in chunks
					@set_time_limit(0);
					if ($fp !== false) {
						if ($rangeStart) {
							// Need to pass only part of the file, starting at $rangeStart
							fseek($fp, $rangeStart, SEEK_SET);
						}
						// If $rangeEnd is open ended (0, whole file from $rangeStart) try fpassthru,
						// else send in small chunks
						if ($rangeEnd || @!fpassthru($fp)) {
							while ($resultLength > 0 && !feof($fp)) {
								// 4 * 1460 (default MSS with ethernet 1500 MTU)
								// This is optimized for network packets, not disk access
								$bytes = min(5840, $resultLength);
								echo fread($fp, $bytes);
								$resultLength = $resultLength - $bytes;
							}
						}
						fclose($fp);
					} else {
						// Ranges are disabled at this point and were never set up
						@readfile($absOrRelFile);
					}
					flush();
					exit;
					*/
				}
			}
			
		}
		return false;
	
	}
	
	function getDownloadData($id, $return) {
	
		$outcome	= array();
		$wheres		= array();
		$db			= JFactory::getDBO();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user		= JFactory::getUser();
		$returnUrl  = 'index.php?option=com_users&view=login&return='.base64_encode($return);
		$userLevels	= implode (',', $user->authorisedLevels());
		
		$pQ			= $params->get( 'enable_plugin_query', 0 );
		
		$wheres[]	= " c.id = ".(int)$id;
		$wheres[] 	= " c.published = 1";
		$wheres[] 	= " c.approved 	= 1";
		$wheres[] 	= " c.catid = cc.id";
		$wheres[]   = " cc.access IN (".$userLevels.")";
		
		// Active
		$jnow		= JFactory::getDate();
		$now		= $jnow->toMySQL();
		$nullDate	= $db->getNullDate();
		$wheres[] 	= ' ( c.publish_up = '.$db->Quote($nullDate).' OR c.publish_up <= '.$db->Quote($now).' )';
		$wheres[] 	= ' ( c.publish_down = '.$db->Quote($nullDate).' OR c.publish_down >= '.$db->Quote($now).' )';
		
		if ($pQ == 1) {
			// GWE MOD - to allow for access restrictions
			JPluginHelper::importPlugin("phoca");
			$dispatcher	   =& JDispatcher::getInstance();
			$joins = array();
			$results = $dispatcher->trigger('onGetDownload', array (&$wheres, &$joins,$id,  $paramsC));	
			// END GWE MOD
		}
		
		/*$query = " SELECT c.filename, c.directlink, c.access"
				." FROM #__phocadownload AS c"
				. ($pQ == 1 ? ((count($joins)>0?( " LEFT JOIN " .implode( " LEFT JOIN ", $joins )):"")):"") // GWE MOD
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY c.ordering";*/
		
		
		$query = ' SELECT c.catid, c.filename, c.directlink, c.link_external, c.access, c.confirm_license, c.metakey, c.metadesc, cc.access as cataccess, cc.accessuserid as cataccessuserid '
				.' FROM #__phocadownload AS c, #__phocadownload_categories AS cc '
				. ($pQ == 1 ? ((count($joins)>0?( ' LEFT JOIN ' .implode( ' LEFT JOIN ', $joins )):'')):'') // GWE MOD
				. ' WHERE ' . implode( ' AND ', $wheres )
				. ' ORDER BY c.ordering';

		$db->setQuery( $query , 0, 1 );	
		$filename = $db->loadObjectList();
		
		
		//OSE Modified Start;
        if (!empty($filename[0])) {
			PhocaDownloadHelperFront::checkOSE($filename[0]);
        }
        //OSE Modified End; 
		

		// - - - - - - - - - - - - - - -
		// USER RIGHT - Access of categories (if file is included in some not accessed category) - - - - -
		// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
		$rightDisplay	= 0;
		if (!empty($filename[0])) {
			$rightDisplay = PhocaDownloadHelper::getUserRight('accessuserid', $filename[0]->cataccessuserid, $filename[0]->cataccess, $user->authorisedLevels(), $user->get('id', 0), 0);
		}
		// - - - - - - - - - - - - - - - - - - - - - -
		if ($rightDisplay == 0) {
			$app->redirect(JRoute::_($returnUrl), JText::_("COM_PHOCADOWNLOAD_NO_RIGHTS_ACCESS_CATEGORY_FILE"));
			exit;
		}
		
		
		if (empty($filename)) {
			$outcome['file'] 			= "COM_PHOCADOWNLOAD_ERROR_NO_DB_RESULT";
			$outcome['directlink']		= 0;
			$outcome['externallink']	= 0;
			return $outcome;
		} 
		
		if (isset($filename[0]->access)) {
			if (!in_array($filename[0]->access, $user->authorisedLevels())) {
				
				$app->redirect(JRoute::_($returnUrl), JText::_('COM_PHOCADOWNLOAD_PLEASE_LOGIN_DOWNLOAD_FILE'));
				exit;
			}
		} else {
			$outcome['file'] 			= "COM_PHOCADOWNLOAD_ERROR_NO_DB_RESULT";
			$outcome['directlink']		= 0;
			$outcome['externallink']	= 0;
			return $outcome;
		}
		// - - - - - - - - - - - - - - - -
		
		
		$this->_filename 		= $filename[0]->filename;
		$this->_directlink 		= $filename[0]->directlink;
		$this->_link_external 	= $filename[0]->link_external;
		$filePath				= PhocaDownloadHelper::getPathSet('file');
		
		if ($this->_filename !='') {
			
			// Important - you cannot use direct link if you have selected absolute path
			// Absolute Path defined by user
			$absolutePath	= $params->get( 'absolute_path', '' );
			if ($absolutePath != '') {
				$this->_directlink = 0;
			}
			
			if ($this->_directlink == 1 ) {
				$relFile = JURI::base(true).'/'.$params->get('download_folder', 'phocadownload' ).'/'.$this->_filename;
				$outcome['file'] 		= $relFile;
				$outcome['directlink']	= $this->_directlink;
				$outcome['externallink']= $this->_link_external;
				return $outcome;
			} else if ($this->_directlink == 0 && $this->_link_external != '' ) {
				$relFile = JURI::base(true).'/'.$params->get('download_folder', 'phocadownload' ).'/'.$this->_filename;
				$outcome['file'] 		= $relFile;
				$outcome['directlink']	= $this->_directlink;
				$outcome['externallink']= $this->_link_external;
				return $outcome;
			} else {
				$absFile = str_replace('/', DS, JPath::clean($filePath['orig_abs_ds'] . $this->_filename));
			}
	
			if (JFile::exists($absFile)) {
				$outcome['file'] 		= $absFile;
				$outcome['directlink']	= $this->_directlink;
				$outcome['externallink']= $this->_link_external;
				return $outcome;
			} else {
			
				$outcome['file'] 		= "COM_PHOCADOWNLOAD_ERROR_NO_ABS_FILE";
				$outcome['directlink']	= 0;
				$outcome['externallink']= $this->_link_external;
				return $outcome;
			}
		} else {
		
				$outcome['file'] 		= "COM_PHOCADOWNLOAD_ERROR_NO_DB_FILE";
				$outcome['directlink']	= 0;
				$outcome['externallink']= $this->_link_external;
				return $outcome;
		}
	}
	

	
	
	function _hit($id) {
		global $app;
		$table = & JTable::getInstance('PhocaDownload', 'Table');
		$table->hit($id);
		return true;
	}
	
	function getOrderingText ($ordering) {
		switch ((int)$ordering) {
			case 2:
				$orderingOutput	= 'ordering DESC';
			break;
			
			case 3:
				$orderingOutput	= 'title ASC';
			break;
			
			case 4:
				$orderingOutput	= 'title DESC';
			break;
			
			case 5:
				$orderingOutput	= 'date ASC';
			break;
			
			case 6:
				$orderingOutput	= 'date DESC';
			break;
			
			case 7:
				$orderingOutput	= 'id ASC';
			break;
			
			case 8:
				$orderingOutput	= 'id DESC';
			break;
			
			case 9:
				$orderingOutput	= 'hits ASC';
			break;
            
            case 10:
				$orderingOutput	= 'hits DESC';
			break;
		
			case 1:
			default:
				$orderingOutput = 'ordering ASC';
			break;
		}
		return $orderingOutput;
	}
	
	function renderOnUploadJS() {
		
		$tag = "<script type=\"text/javascript\"> \n"
		. "function OnUploadSubmitFile() { \n"
		. "if ( document.getElementById('catid').value < 1 ) { \n"
	    . "alert('".JText::_('COM_PHOCADOWNLOAD_PLEASE_SELECT_CATEGORY')."'); \n"
		. "return false; \n"
		. "} \n"
		. "document.getElementById('loading-label-file').style.display='block'; \n" 
		. "return true; \n"
		. "} \n"
		. "</script>";

		return $tag;
	}
	
	function renderDescriptionUploadJS($chars) {
		
		$tag = "<script type=\"text/javascript\"> \n"
		."function countCharsUpload() {" . "\n"
		."var maxCount	= ".$chars.";" . "\n"
		."var pdu 			= document.getElementById('phocadownload-upload-form');" . "\n"
		."var charIn		= pdu.phocadownloaduploaddescription.value.length;" . "\n"
		."var charLeft	= maxCount - charIn;" . "\n"
		."" . "\n"
		."if (charLeft < 0) {" . "\n"
		."   alert('".JText::_('COM_PHOCADOWNLOAD_MAX_LIMIT_CHARS_REACHED')."');" . "\n"
		."   pdu.phocadownloaduploaddescription.value = pdu.phocadownloaduploaddescription.value.substring(0, maxCount);" . "\n"
		."	charIn	 = maxCount;" . "\n"
		."  charLeft = 0;" . "\n"
		."}" . "\n"
		."pdu.phocadownloaduploadcountin.value	= charIn;" . "\n"
		."pdu.phocadownloaduploadcountleft.value	= charLeft;" . "\n"
		."}" . "\n"
		. "</script>";
		
		return $tag;
	}
	
	function userTabOrdering() {	
		$js  = "\t". '<script language="javascript" type="text/javascript">'."\n"
			 . 'function tableOrdering( order, dir, task )' . "\n"
			 . '{ ' . "\n"
			 . "\t".'var form = document.phocadownloadfilesform;' . "\n"
			 . "\t".'form.filter_order.value 		= order;' . "\n"
			 . "\t".'form.filter_order_Dir.value	= dir;' . "\n"
			 . "\t".'document.phocadownloadfilesform.submit();' . "\n"
			 . '}'. "\n"
			 . '</script>' . "\n";
			
		return $js;
	}
	
	function renderOverlibCSS($ol_fg_color, $ol_bg_color, $ol_tf_color, $ol_cf_color, $opacity = 0.8) {
		
		$opacityPer = (float)$opacity * 100;
		
		$css = "<style type=\"text/css\">\n"
		
		. ".bgPhocaClass{
			background:".$ol_bg_color.";
			filter:alpha(opacity=".$opacityPer.");
			opacity: ".$opacity.";
			-moz-opacity:".$opacity.";
			z-index:1000;
			}
			.fgPhocaClass{
			background:".$ol_fg_color.";
			filter:alpha(opacity=100);
			opacity: 1;
			-moz-opacity:1;
			z-index:1000;
			}
			.fontPhocaClass{
			color:".$ol_tf_color.";
			z-index:1001;
			}
			.capfontPhocaClass, .capfontclosePhocaClass{
			color:".$ol_cf_color.";
			font-weight:bold;
			z-index:1001;
			}"
		." </style>\n";
		
		return $css;
	}
	
	public static function checkOSE($fileName) {
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_osemsc'.DS.'init.php') 
		&& file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'define.php')) {
            require_once(JPATH_SITE.DS.'components'.DS.'com_osemsc'.DS.'init.php');
            oseRegistry :: call('content')->checkAccess('phoca', 'category', $fileName->catid);
        } else if (file_exists(JPATH_ADMINISTRATOR . DS . "components" . DS . "com_osemsc" . DS . "warehouse" . DS . "api.php")) {
            require_once (JPATH_ADMINISTRATOR . DS . "components" . DS . "com_osemsc" . DS . "warehouse" . DS . "api.php");
            $checkmsc = new OSEMSCAPI();
            $checkmsc->ACLCheck("phoca", "cat", $fileName->catid, true);
        }
	}
}


jimport('joomla.html.pagination');
class PhocaDownloadPagination extends JPagination
{

	function getLimitBox()
	{
		
		$app				= JFactory::getApplication();
		$paramsC 			= JComponentHelper::getParams('com_phocadownload') ;
		$pagination 		= $paramsC->get( 'pagination', '5,10,15,20,50,100' );
		$paginationArray	= explode( ',', $pagination );
		
		// Initialize variables
		$limits = array ();

		foreach ($paginationArray as $paginationValue) {
			$limits[] = JHTML::_('select.option', $paginationValue);
		}
		$limits[] = JHTML::_('select.option', '0', JText::_('COM_PHOCADOWNLOAD_ALL'));

		$selected = $this->_viewall ? 0 : $this->limit;

		// Build the select list
		if ($app->isAdmin()) {
			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $selected);
		} else {
			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}
	
}
?>