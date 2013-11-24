<?php
ob_start();
?>
div#SPLightbox img {
    width: <?php echo $this->settings['thumbnailx'] ?>px;
    height: <?php echo $this->settings['thumbnaily'] ?>px;
}
<?php
$css = ob_get_contents();
ob_end_clean();
SPFactory::header()->addCSSCode($css);
?>
