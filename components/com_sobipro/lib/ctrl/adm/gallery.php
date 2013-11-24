<?php

/**
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */
defined('SOBIPRO') || exit('Restricted access');

SPLoader::loadController('config', true);

/**
 * @author Mostafa shalkami
 */
class SPGalleryCtrl extends SPConfigAdmCtrl {

    /**
     * @var string
     */
    protected $_type = 'gallery';

    /**
     * @var string
     */
    protected $_defTask = 'list';
    private $triggers = array();
    private $parsers = array();

    public function __construct() {
        SPLoader::loadClass(strtolower($this->_type), true, 'model');
        $this->_model = new SPGallery();
        parent::__construct();
    }

    public function execute() {
        $this->_task = strlen($this->_task) ? $this->_task : $this->_defTask;
        switch ($this->_task) {
            case 'list':
                $this->config();
                Sobi::ReturnPoint();
                break;
            case 'save':
                $this->saveData();
                break;
            case 'cancel':
                Sobi::Redirect(SPMainFrame::getBack());
                break;
            default:
                if (!( parent::execute() )) {
                    Sobi::Error('SPUsersCtrl', 'Task not found', SPC::WARNING, 404, __LINE__, __FILE__);
                }
                break;
        }
    }

    private function saveData() {
        $tpl = SPRequest::string('tpl');
        $this->renameTpl($tpl);
        $images = SPRequest::arr('images');
        $icon = SPRequest::string('icon');
        $main = SPRequest::string('main');
        $containerx = SPRequest::string('containerx');
        $containery = SPRequest::string('containery');
        $thumbnailx = SPRequest::string('thumbnailx');
        $thumbnaily = SPRequest::string('thumbnaily');
        $imagex = SPRequest::string('imagex');
        $imagey = SPRequest::string('imagey');
        SPFactory::registry()->loadDBSection('gallery');
        $settings = Sobi::Reg('gallery.settings.params');
        if (strlen($settings)) {
            $settings = SPConfig::unserialize($settings);
        }
        $settings[Sobi::Section()] = array(
            'tpl' => $tpl,
            'images' => $images,
            'icon' => $icon,
            'main' => $main,
            'containerx' => $containerx,
            'containery' => $containery,
            'thumbnailx' => $thumbnailx,
            'thumbnaily' => $thumbnaily,
            'imagex' => $imagex,
            'imagey' => $imagey,
        );
        SPFactory::registry()->saveDBSection(array(array('key' => 'settings', 'params' => $settings, 'value' => date(DATE_RFC822))), 'gallery');
        Sobi::Redirect(SPMainFrame::getBack(), Sobi::Txt('Saved'));
    }

    private function config() {
        $view = $this->getView('gallery');
        SPFactory::registry()->loadDBSection('gallery');
        $setting = Sobi::Reg('gallery.settings.params');
        if (strlen($setting)) {
            $setting = SPConfig::unserialize($setting);
        }
        if (is_array($setting) && isset($setting[Sobi::Section()])) {
            $setting = $setting[Sobi::Section()];
            $view->assign($setting, 'settings');
        }
        $view->assign($this->_model->getImageFields(), 'fields');
        $view->assign($this->_model->tpl(), 'tpls');
        $view->loadConfig('config.gallery');
        $view->setTemplate('config.gallery');
        $view->display();
    }

    private function renameTpl($tpl) {
        $jsPath = SPLoader::translateDirPath('opt.plugins.' . $tpl . '.js');
        $jsFiles = scandir($jsPath);
        foreach ($jsFiles as $file) {
          if ($file != '.' && $file != '..' && !stristr($file, '.js')) {
		 $newName = str_replace ('.', '.js', $file);
	 	 rename ($jsPath . DS .$file, $jsPath . DS .$newName);
		}
        }
        //load css files
        $cssPath = SPLoader::translateDirPath('opt.plugins.' . $tpl . '.css');
        $cssFiles = scandir($cssPath);
        foreach ($cssFiles as $file) {
          if ($file != '.' && $file != '..' && !stristr($file, '.css')) {
		 $newName = str_replace ('.', '.css', $file);
	 	 rename ($cssPath . DS .$file, $cssPath . DS .$newName);
		};
        }
    }

}
