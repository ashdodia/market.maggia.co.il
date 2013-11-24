<?php 
/**
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */
?>
<?php
ob_start();
?>
div.ad-gallery {
width: <?php echo $this->settings['containerx'] ?>px;
height: <?php echo $this->settings['containery'] ?>px;
}
div.ad-gallery div.ad-image-wrapper img {
width: <?php echo $this->settings['imagex'] ?>px;
height: <?php echo $this->settings['imagey'] ?>px;
}
div.ad-gallery div.ad-thumbs img {
width: <?php echo $this->settings['thumbnailx'] ?>px;
height: <?php echo $this->settings['thumbnaily'] ?>px;
}
.ad-gallery .ad-image-wrapper {
    height: <?php echo $this->settings['imagey'] + 20 ?>px;
    }
<?php
$css = ob_get_contents();
ob_end_clean();
SPFactory::header()->addCSSCode($css);
?>
