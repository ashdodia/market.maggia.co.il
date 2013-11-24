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
<div id="tj_container" class="tj_container">
					<div class="tj_nav">
						<span id="tj_prev" class="tj_prev">Previous</span>
						<span id="tj_next" class="tj_next">Next</span>
					</div>
					<div class="tj_wrapper">
						<ul class="tj_gallery" id="SPLightbox">
							
						
        <?php foreach ($images as $img) { ?>
<li>
<a class="colorbox" href="<?php echo $img[$this->settings['main']] ?>">
<img src="<?php echo $img[$this->settings['icon']] ?>" />
</a>
</li>
        <?php } ?>
   </ul>
					</div>
				</div>
<div style="clear:both"></div>
