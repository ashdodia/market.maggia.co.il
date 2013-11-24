<?php include 'config.php' ?>
<div id="SPLightbox">
            <?php foreach ($images as $img) { ?>
                <a class="modal" href="<?php echo $img[$this->settings['main']] ?>">
                    <img src="<?php echo $img[$this->settings['icon']] ?>" />
                </a>
            <?php } ?>
        </div>
