<?php
/**
 * ------------------------------------------------------------------------
 * Gallery Plugin For SobiPro
 * ------------------------------------------------------------------------
 * @copyright   Copyright (C) 2011-2012 Chartiermedia.com - All Rights Reserved.
 * @license     GNU/GPL, http://www.gnu.org/copyleft/gpl.html
 * @author:     Sebastien Chartier
 * @link:     http://www.chartiermedia.com
 * ------------------------------------------------------------------------
 *
 * @package	Joomla.Plugin
 * @subpackage  Gallery
 * @version     1.12
 * @since	1.7
 */
defined( 'SOBIPRO' ) || exit( 'Restricted access' );

$head =& SPFactory::header();
$head->addJsFile('gallery.imagebox');
$head->addCssFile('gallery.imagebox');
?>
<div class="<?php echo $this->cssClass; ?>">
    <?php foreach($images as $f){ ?>
    <a href="<?php echo $imgdirurl . $f; ?>" rel="imagebox[<?php echo $this->nid; ?>]"><img src="<?php echo $imgdirurl . 'thumb_' . $f; ?>" /></a>
    <?php } ?>
</div>