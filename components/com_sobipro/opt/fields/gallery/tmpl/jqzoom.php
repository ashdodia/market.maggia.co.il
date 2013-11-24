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
$head->addJsFile(array(
    'jquery',
    'jqnc',
    'gallery.jqzoom',
    'gallery.scrollgallery'
));
$head->addCssFile(array('gallery.jfzoom', 'gallery.scrollgallery'));

?>
<div class="<?php echo $this->cssClass; ?> jfzoom">
    <?php foreach($images as $f){ ?>
    <a href='javascript:void(0);' rel="{gallery: '<?php echo $this->nid; ?>', smallimage: '<?php echo $imgdirurl . 'thumb_' . $f; ?>', largeimage: '<?php echo $imgdirurl . 'img_' . $f; ?>'}"><img src='<?php echo $imgdirurl . 'ico_' . $f; ?>' /></a>
    <?php } ?>
    <div style="clear:both;"></div>
    <div style="position:relative;">
        <?php if(count($images)){ ?>
            <a href="<?php echo $imgdirurl . 'img_' . $images[0]; ?>" id="<?php echo $this->nid; ?>_jqz" class="JfZoom" rel='<?php echo $this->nid; ?>'  title="triumph" >
                <img src="<?php echo $imgdirurl . 'thumb_' . $images[0]; ?>"  title="triumph"  style="border: 4px solid #666;">
            </a>
        <?php } ?>
    </div>
    <div style="clear:both;"></div>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#<?php echo $this->nid; ?>_jqz').JfZoom({
                zoomType: 'standard',
                lens:true,
                preloadImages: false,
                alwaysOn:false,
                position:'left'
            });

        });
    </script>
</div>