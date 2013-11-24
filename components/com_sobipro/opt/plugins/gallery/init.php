<?php

/**
 * @package: SobiPro Gallery
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */
defined('SOBIPRO') || exit('Restricted access');

class SPGalleryApp extends SPApplication {

    private static $methods = array('CreateAdmMenu', 'EntryViewDetails');
    private $settings;

    public function __construct() {
        self::$methods = array_unique(self::$methods);
        SPFactory::registry()->loadDBSection('gallery');
        $setting = Sobi::Reg('gallery.settings.params');
        if (strlen($setting)) {
            $setting = SPConfig::unserialize($setting);
        }
        if (is_array($setting) && isset($setting[Sobi::Section()])) {
            $this->settings = $setting[Sobi::Section()];
        }
    }

    public function provide($action) {
        return in_array($action, self::$methods);
    }

    public function CreateAdmMenu(&$menu) {
        if (( Sobi::Section())) {
            $this->CreateMenu($menu);
        }
    }

    public function EntryViewDetails(&$entry) {
        $imgObj = array();
        foreach ($this->settings['images'] as $image) {
            $img = $entry['entry']['_data']['fields'][$image]['_data']['data']['_attributes'];
            if ($img)
                $imgObj[] = $img;
        }
        $tpl = $this->loadFiles();
        $entry['gallery'] = $this->galleryTpl($imgObj, $tpl);
    }

    private function galleryTpl($images, $tpl) {
        ob_start();
        include $tpl;
        $gallery = ob_get_contents();
        ob_end_clean();
        return $gallery;
    }

    private function loadFiles() {
        // load js files
        $jsPath = SPLoader::translateDirPath('lib.js.gallery.' . $this->settings['tpl'] );
        $jsFiles = scandir($jsPath);
        $js = array();
        foreach ($jsFiles as $file) {
            if (stristr($file, '.js'))
                $js[] = 'gallery.' . $this->settings['tpl'] .'.'.str_replace ('.js', '', $file);
        }
        SPFactory::header()->addJsFile($js);
        //load css files
        $cssPath = SPLoader::translateDirPath('gallery.' . $this->settings['tpl'], 'css' );
        $cssFiles = scandir($cssPath);
        $css = array();
        foreach ($cssFiles as $file) {
            if (stristr($file, '.css'))
                $css[] =  'gallery.' . $this->settings['tpl'] .'.'.str_replace ('.css', '', $file);
        }
        SPFactory::header()->addCssFile($css);
        $tplPath = SPLoader::translateDirPath('opt.plugins.' . $this->settings['tpl'] . '.');
        $tpl = $tplPath . $this->settings['tpl'] . '.tpl.php';
        return $tpl;
    }

    private function CreateMenu(&$menu) {
        if (isset($menu['AMN.APPS_SECTION_HEAD'])) {
            $gConf = $menu['AMN.APPS_SECTION_HEAD'];
            $ngConf = array();
            foreach ($gConf as $task => $entry) {
                $ngConf[$task] = $entry;
                if ($task == 'extensions.manage') {
                    $ngConf['gallery'] = 'Gallery Plugin';
                }
            }
            $menu['AMN.APPS_SECTION_HEAD'] = $ngConf;
        }
    }

}

?>
