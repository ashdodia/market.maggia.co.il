<?php
/**
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */
ob_start();
?>
div.spacegallery {
    width: <?php echo $this->settings['containerx'] ?>px;
    height: <?php echo $this->settings['containery'] ?>px;
}
div.spacegallery img {
    max-width: <?php echo $this->settings['imagex'] ?>px;
    max-height: <?php echo $this->settings['imagey'] ?>px;
}
div.spacegallery a:hover {
    background: transparent !important;
}
<?php
$css = ob_get_contents();
ob_end_clean();
SPFactory::header()->addCSSCode($css);
?>
