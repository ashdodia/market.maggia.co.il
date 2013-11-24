<?php 
/**
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */

include 'config.php' 
?>
<div id="easy-slider-container">
    <div id="easy-slider">
        <ul>				
            <?php foreach ($images as $img) { ?>
                <li>
                    <a href="<?php echo $img[$this->settings['main']] ?>" target="_blank">
                        <img src="<?php echo $img[$this->settings['icon']] ?>"/>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

