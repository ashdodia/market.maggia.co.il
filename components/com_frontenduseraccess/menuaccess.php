<?php
/**
* @package Frontend-User-Access (com_frontenduseraccess)
* @version 4.1.7
* @copyright Copyright (C) 2008-2012 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//silly workaround for developers who install the trail version while totally ignoring 
//all warnings about that you need Ioncube installed or else it will criple the site
$fua_trial_version = 0;

if(!$fua_trial_version || ($fua_trial_version && extension_loaded('ionCube Loader'))){
	require_once(JPATH_BASE.DS.'components'.DS.'com_frontenduseraccess'.DS.'checkaccess2.php');
} 

?>