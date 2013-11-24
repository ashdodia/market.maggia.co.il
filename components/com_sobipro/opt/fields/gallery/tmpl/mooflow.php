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
defined('SOBIPRO') || exit('Restricted access');

$head = & SPFactory::header();
$head->addJsFile(array(
    'gallery.mooflow'
));
$head->addCssFile(array(
    'gallery.mooflow'
));
?>
<div id="<?php echo $this->nid; ?>_mf" class="<?php echo $this->cssClass; ?>">
    <?php foreach ($images as $f) { ?>
        <a href="<?php echo $imgdirurl . $f; ?>">
            <img src="<?php echo $imgdirurl . 'thumb_' . $f; ?>" title="title" alt="alt" />
        </a>
    <?php } ?>
</div>
<script type="text/javascript">
        <?php echo $this->nid; ?>_mf = new JfMooFlow(document.id('<?php echo $this->nid; ?>_mf'), {
            startIndex: 0,
            useSlider: true,
            useAutoPlay: true,
            useCaption: true,
            useResize: true,
            useViewer: true,
            useWindowResize: true,
            useMouseWheel: true,
            useKeyInput: true,
            useViewer: true,
            onClickView: function(img){

                links = this.master.images;
                linkMapper = function(el) {
                    return [el.href, el.title];
                };

                //JfImageBox.open(links.map(linkMapper), links.indexOf(this.getCurrent()), {});
                return false;
            }
        });
</script>