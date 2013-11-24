<?php
/**
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */
 include 'config.php'; ?>
<div id="space-gallery" class="spacegallery">		
<?php foreach ($images as $img) { ?>
	<img src="<?php echo $img[$this->settings['main']] ?>" alt="" />
<?php } ?>
</div>
