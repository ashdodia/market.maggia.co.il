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
<?php include 'config.php' ?>
<div id="ad-gallery" class="ad-gallery">
    <div class="ad-image-wrapper">
    </div>
    <div class="ad-controls">
    </div>
    <div class="ad-nav">
        <div class="ad-thumbs">
            <ul class="ad-thumb-list">
                <?php foreach ($images as $img) { ?>
                    <li>
                        <a href="<?php echo $img[$this->settings['main']] ?>">
                            <img src="<?php echo $img[$this->settings['icon']] ?>">
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>

