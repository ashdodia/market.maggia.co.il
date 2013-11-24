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


if($this->tmpl=='component'){
?>
<div style="text-align: center; margin-top: 300px;">
<?php
}
echo $this->no_access_message;
?>
<br /><a href="javascript:history.back()"><?php echo $this->back;?></a>
<?php
if($this->tmpl=='component'){
?>
</div>
<?php
}

?>