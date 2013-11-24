<?php
SPLang::load('SpApp.gallery');

SPFactory::header()->addJsFile(array(
    'jquery',
    'jqnc',
    'gallery.imagebox',
    'gallery.scrollgallery'
));

SPFactory::header()->addCssFile(array(
    'gallery.imagebox',
    'gallery.scrollgallery'
));
?>
<div style="float: right;" id="gallery">
    <div id="JfScrollGalleryFoot" style="margin:0px; padding-bottom:0px;">
        <div id="thumbarea">
            <div id="thumbareaContent">
                <?php
                foreach ($images as $img) {
                    ?>
                    <img alt="" title="" src="<?php echo $imgdirurl . 'thumb_' . $img; ?>" />
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div id="JfScrollGalleryHead">
        <div id="imagearea">
            <div id="imageareaContent">
                <?php
                foreach ($images as $img) {
                    ?>
                    <a rel="imagebox[<?php echo $this->nid; ?>]"
                       class="zoomin"
                       title="<?php echo SPLang::txt('FIELD_GAL_CLICK_ZOOMIN'); ?>"
                       href="<?php echo $imgdirurl . $img; ?>">

                        <img alt="<?php $img; ?>"
                             title="<?php echo SPLang::txt('FIELD_GAL_CLICK_ZOOMIN'); ?>"
                             src="<?php echo $imgdirurl . 'img_' . $img; ?>" />

                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            var JfScrollGalleryObj = new JfScrollGallery({
                clickable:false,
                start:0,
                autoScroll: true,
                speed: 0.25
            });
        });
    </script>
</div>