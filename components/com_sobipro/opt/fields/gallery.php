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
SPLoader::loadClass('opt.fields.image');

class SPField_Gallery extends SPField_Image implements SPFieldInterface {

    protected $maxImages = 10;

    protected $maxImagesPublic = 10;

    protected static $loaded = array();

    protected $resizeWidth = 1000;

    protected $resizeHeight = 700;

    protected $thumbWidth = 150;

    protected $thumbHeight = 150;

    protected $tmplVCard = 'horizontal';

    protected $tmplDetails = 'horizontal';

    protected $specialLevel = array();

    protected $maxImagesSpecial = array();

    public function __construct(  &$field  ){
        parent::__construct($field);

        $this->maxImages = $this->maxImagesPublic;

        $user = JFactory::getUser();
        /*
        echo 'authorizedViewLevels: ' . $user->name;
        print_r(JAccess::getAuthorisedViewLevels($user->id));
        echo 'specialLevels';
        print_r($this->specialLevel);*/
        foreach (JAccess::getAuthorisedViewLevels($user->id) as $level => $value){
            $idx = array_search($level, $this->specialLevel);
            if($idx !== false){
                if($this->maxImagesSpecial[$idx] > $this->maxImages)
                    $this->maxImages = $this->maxImagesSpecial[$idx];
            }
        }
    }
    protected function getAttr()
    {
        return array( 'width', 'tmplDetails', 'tmplVCard', 'thumbHeight', 'thumbWidth', 'maxSize', 'resizeWidth', 'resizeHeight', 'maxImagesPublic', 'specialLevel', 'maxImagesSpecial' );
    }
    /**
     * Gets the data for a field and save it in the database
     * @param SPEntry $entry
     * @return bool
     */
    public function saveData(&$entry, $request = 'POST') {
        if (!( $this->enabled )) {
            return false;
        }

        static $store = null;
        $cache = false;
        if ($store == null) {
            $store = SPFactory::registry()->get('requestcache_stored');
        }
        if (is_array($store) && isset($store[$this->nid])) {
            $data = $store[$this->nid];
            $cache = true;
        } else {
            $data = SPRequest::arr($this->nid);
        }

        /* @var SPdb $db */
        $db = & SPFactory::db();
        $this->verify($entry, $request);

        $time = SPRequest::now();
        $IP = SPRequest::ip('REMOTE_ADDR', 0, 'SERVER');
        $uid = Sobi::My('id');

        /* if we are here, we can save these data */

        /* collect the needed params */
        $save = count($data) ? SPConfig::serialize($data) : null;
        $params = array();
        $params['publishUp'] = $entry->get('publishUp');
        $params['publishDown'] = $entry->get('publishDown');
        $params['fid'] = $this->fid;
        $params['sid'] = $entry->get('id');
        $params['section'] = Sobi::Reg('current_section');
        $params['lang'] = Sobi::Lang();
        $params['enabled'] = $entry->get('state');
        $params['baseData'] = $db->escape($save);
        $params['approved'] = $entry->get('approved');
        $params['confirmed'] = $entry->get('confirmed');
        /* if it is the first version, it is new entry */
        if ($entry->get('version') == 1) {
            $params['createdTime'] = $time;
            $params['createdBy'] = $uid;
            $params['createdIP'] = $IP;
        }
        $params['updatedTime'] = $time;
        $params['updatedBy'] = $uid;
        $params['updatedIP'] = $IP;
        $params['copy'] = !( $entry->get('approved') );

        /* save it */
        try {
            $db->insertUpdate('spdb_field_data', $params);
        } catch (SPException $x) {
            Sobi::Error($this->name(), SPLang::e('CANNOT_SAVE_FIELDS_DATA_DB_ERR', $x->getMessage()), SPC::WARNING, 0, __LINE__, __FILE__);
        }

        /* If it's a new entry, move pictures according to entry id */
        $tmpid = SPRequest::string($this->nid . 'id');
        if($tmpid){
            $base = SPLoader::dirPath('images/sobipro/galleries/', 'root');
            mkdir($base . $entry->get('id'));
            if(!rename($base . $tmpid . DS . $this->nid, $base . $entry->get('id') . DS . $this->nid)){
                Sobi::Error($this->name(), SPLang::e('CANNOT_RENAME_TEMP_DIR'));
            }
            unlink($base . $tmpid);
        }
    }

    /**
     * Shows the field in the edit entry or add entry form
     * @param bool $return return or display directly
     * @return string
     */
    public function field($return = false) {
        if (!( $this->enabled )) {
            return false;
        }

        //JHtml::_('script', 'media/system/js/progressbar.js', true, false);
        $header = SPFactory::header();

        if(!self::$loaded[__METHOD__]){
            SPLang::load('SpApp.gallery');
            // Include MooTools framework
            JHtmlBehavior::framework();

            $header->addJsFile('root.media.system.js.swf');
            $header->addJsFile('root.media.system.js.progressbar');
            $header->addJsFile('gallery.gallery-edit');
            $header->addCssCode('
                .spgallery{padding: 10px;clear:left;width:90%;}
                .sobigallery-image-list{padding:20px 10px;}
                .sobigallery-status{float:left;padding:2px 3px;}
                .sgimage img{width:80%;}
                .sgimage img.delete{opacity:0.5;}
                .sgimage{float:left;width:25%;position:relative;height:120px;overflow:hidden;}
                .sgimage.left{clear:left;}
            ');

            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILENAME');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_UPLOAD_COMPLETED');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_PROGRESS_OVERALL');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_TITLE');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE_TITLE');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_FILE');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_PROGRESS');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_ERROR');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_DUPLICATE');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMIN');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMAX');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTMAX');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTSIZEMAX');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_HTTPSTATUS');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_SECURITYERROR');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_IOERROR');
            JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');

            self::$loaded[__METHOD__] = true;
        }

        $class = $this->required ? $this->cssClass . ' required' : $this->cssClass;

        $field = "";
        $params = array('id' => $this->nid, 'class' => $class);
        if ($this->width) {
            $params['style'] = "width: {$this->width}px;";
        }
        //$params['onclick'] = $this->nid . "_uploader.start();";
        //$params['accept'] = 'image/*';

        $files = SPConfig::unserialize($this->getRaw());

        if(!is_array($files)) $files = array();

        $divid = $this->nid . "_gallery_adm";
        $statusid = $this->nid . "_status";
        if($this->notice) $field .= '  <p class="notice">' . $this->notice . '</p>';
        $field .= '  <div id="' . $divid . '" class="spgallery">';
        $field .= '  <button id="' . $this->nid . '_browse" style="display:block;float:left !important;" name="' . $this->nid . '_browse" type="button">' . SPLang::txt('FIELD_GAL_BROWSE_FILES') . '</button>';
        $field .= '  <div id="' . $statusid . '" class="sobigallery-status"><span id="' . $this->nid . '_progress" class="progress"></span><span id="' . $this->nid . '_text" class="text"></span></div>';
        $field .= '  <div style="clear:both;"></div>';
        $field .= '  <div class="sobigallery-image-list" style="clear:both;">';
        $field .= '    <div id="' . $this->nid . '_images">';
        foreach ($files as $img) {
            $imgurl = $this->getImagePath($img, 'thumb');
            $field .= '<div class="sgimage" >
                            ' . SPHtml_Input::checkbox($this->nid . '[]', $img, $this->name, null, true, array('class' => 'checkbox'), array('field', 'image')) . '
                            <img src="' . $imgurl . '" />
                       </div>';
        }
        $field .= '    </div>';
        $field .= '  </div>';
        $field .= '</div>';

        $uploadURL = JURI::root() . 'index.php?' . JURI::buildQuery(array('option' => 'com_sobipro', SOBI_TASK => 'gallery.upload', 'fid' => $this->fid, 'pid' => Sobi::Reg( 'current_section' ), 'tmpl' => 'component'));
        if($this->sid){
            $uploadURL .= '&sid=' . $this->sid;
        }else{
            $tmpid = 'tmp_' . rand(1000,99999);
            $uploadURL .= '&'.$this->nid.'id=' . $tmpid;

            $field .= '<input type="hidden" name="' . $this->nid . 'id" value="' . $tmpid . '" />';
        }

        $field .= SPHtml_Input::file($this->nid, 20, $params);

        $opt['multiple'] = true;
        $opt['url'] = $uploadURL;
        $opt['path'] = JURI::root() . 'media/system/swf/uploader.swf';
        $opt['target'] = '\\document.id(\'' . $this->nid . '_browse\')';
        $opt['instantStart'] = true;
        $opt['allowDuplicates'] = true;
        $opt['fileSizeMax'] = $this->maxSize;
        $opt['fileListMax'] = $this->maxImages - count($files);
        $opt['typeFilter'] = '\\{\'Images (*.jpg, *.jpeg, *.gif, *.png)\': \'*.jpg; *.jpeg; *.gif; *.png\'}';
        $opt['fieldName'] = $this->nid;

        //$opt['buttonImage'] = false; //'http://games.mochiads.com/c/g/diamond-adventure/_thumb_100x100.jpg';
        // Optional functions
        //$opt['createReplacement']	= (isset($params['createReplacement'])) ? '\\' . $params['createReplacement'] : null;
        //$opt['onFileComplete'] = (isset($params['onFileComplete'])) ? '\\' . $params['onFileComplete'] : null;
        //$opt['onBeforeStart'] = (isset($params['onBeforeStart'])) ? '\\' . $params['onBeforeStart'] : null;
        //$opt['onStart'] = (isset($params['onStart'])) ? '\\' . $params['onStart'] : null;
        //$opt['onComplete'] = (isset($params['onComplete'])) ? '\\' . $params['onComplete'] : null;

        $opt['onLoad'] = '\\
            function() {
                document.id(\'' . $divid . '\').removeClass(\'hide\');
                document.id(\'' . $this->nid . '\').destroy();

                this.target.addEvents({
                    click: function(e){
                        return false;
                    },
                    mouseenter: function() {
                        this.addClass(\'hover\');
                        this.blur();
                    },
                    mouseleave: function() {
                            this.removeClass(\'hover\');
                            this.blur();
                    },
                    mousedown: function() {
                            this.blur();
                    }
                });
            }';

        $options = self::_getJSObject($opt);

        // Attach tooltips to document
        $header->addJsCode("window.addEvent('domready', function(){
                this['{$this->nid}_uploader'] = new SPGallUpload('{$this->nid}', {$options} );

                this['{$this->nid}_sortables'] = new Sortables('{$this->nid}_images', {revert: true,
    opacity: 0.7});
            });");
        if (!$return) {
            echo $field;
        } else {
            return $field;
        }
    }

    protected static function _getJSObject($array = array()) {
        // Initialise variables.
        $object = '{';

        // Iterate over array to build objects
        foreach ((array) $array as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            if (is_bool($v)) {
                if ($k === 'fullScreen') {
                    $object .= 'size: { ';
                    $object .= 'x: ';
                    $object .= 'window.getSize().x-80';
                    $object .= ',';
                    $object .= 'y: ';
                    $object .= 'window.getSize().y-80';
                    $object .= ' }';
                    $object .= ',';
                } else {
                    $object .= ' ' . $k . ': ';
                    $object .= ($v) ? 'true' : 'false';
                    $object .= ',';
                }
            } elseif (!is_array($v) && !is_object($v)) {
                $object .= ' ' . $k . ': ';
                $object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1)  : "'" . $v . "'";
                $object .= ',';
            } else {
                $object .= ' ' . $k . ': ' . self::_getJSObject($v) . ',';
            }
        }

        if (substr($object, -1) == ',') {
            $object = substr($object, 0, -1);
        }

        $object .= '}';

        return $object;
    }

    private function getImagePath($img, $size = false) {
        $sPath = 'images/sobipro/galleries/' . $this->sid . '/' . $this->nid . '/';
        $path = SPLoader::dirPath($sPath, 'root', false);

        if ($size) {
            if (!is_array($size))
                $size = array($size);

            foreach ($size as $s) {
                if (file_exists($path . $s . '_' . $img)) {
                    return Sobi::Cfg('live_site') . $sPath . $s . '_' . $img;
                }
            }
        }

        return Sobi::Cfg('live_site') . $sPath . $img;
    }

    /**
     * Gets the data for a field, verify it and pre-save it.
     * @param SPEntry $entry
     * @param string $request
     * @return void
     */
    public function submit(&$entry, $tsid = null, $request = 'POST') {
        return array();
    }

    /**
     * @param SPEntry $entry
     * @param string $request
     * @return bool
     */
    private function verify($entry, $request) {
        if (strtolower($request) == 'post' || strtolower($request) == 'get') {
            $data = SPRequest::arr($this->nid);
        } else {
            $data = SPRequest::arr($this->nid, array(), $request);
        }

        $dexs = !$data || (strcmp(SPConfig::serialize($data), $this->getRaw()) != 0);

        /* check if there was an adminField */
        if ($this->adminField && $dexs) {
            if (!( Sobi:: Can('entry.adm_fields.edit') )) {
                throw new SPException(SPLang::e('FIELD_NOT_AUTH', $this->name));
            }
        }

        /* check if it was free */
        if (!( $this->isFree ) && $this->fee && $dexs) {
            SPFactory::payment()->add($this->fee, $this->name, $entry->get('id'), $this->fid);
        }

        /* check if it was editLimit */
        if ($this->editLimit == 0 && !( Sobi::Can('entry.adm_fields.edit') ) && $dexs) {
            throw new SPException(SPLang::e('FIELD_NOT_AUTH_EXP', $this->name));
        }

        /* check if it was editable */
        if (!( $this->editable ) && !( Sobi::Can('entry.adm_fields.edit') ) && $dexs && $entry->get('version') > 1) {
            throw new SPException(SPLang::e('FIELD_NOT_AUTH_NOT_ED', $this->name));
        }
        return true;
    }

    public function saveImage() {

        if (!( $this->enabled )) {
            throw new SPException(SPLang::e('FIELD_NOT_AUTH', $this->name));
        }
        $files = SPConfig::unserialize($this->getRaw());

        if (count($files) >= $this->maxImages) {
            throw new SPException(SPLang::e('FIELD_NOT_AUTH', $this->name));
        }
        /* check if there was an adminField */
        if ($this->adminField) {
            if (!( Sobi:: Can('entry.adm_fields.edit') )) {
                throw new SPException(SPLang::e('FIELD_NOT_AUTH', $this->name));
            }
        }

        /* check if it was editable */
        if (!( $this->editable ) && !( Sobi::Can('entry.adm_fields.edit') ) && count($files) && $entry->get('version') > 1) {
            throw new SPException(SPLang::e('FIELD_NOT_AUTH_NOT_ED', $this->name));
        }

        //$file = SPRequest::file($this->nid);
        //$pattern = '/(?:_\d*)?(\.[^\.]+)$/';

        $data = SPRequest::file($this->nid, 'tmp_name');

        /* if we have an image */
        if ($data) {
            $orgName = SPRequest::file($this->nid, 'name');
            if (preg_match('/^(.+)(?:_\d*)?\.([^\.]+)$/', $orgName, $matches)) {
                $name = $matches[1];
                $ext = $matches[2];
                //echo $name . $ext;
            } else {
                throw new SPException(SPLang::txt('FIELD_GAL_FILENAME_ERROR'));
            }

            $sPath = 'images/sobipro/galleries/' . $this->sid . '/' . $this->nid . '/';
            $path = SPLoader::dirPath($sPath, 'root', false);

            $fileSize = SPRequest::file($this->nid, 'size');
            if ($fileSize > $this->maxSize) {
                throw new SPException(SPLang::e('FIELD_IMG_TOO_LARGE', $this->name, $fileSize, $this->maxSize));
            }

            $tmp = $name;
            $i = 0;
            while (file_exists($path . $name . '.' . $ext)) {
                $name = $tmp . '_' . ++$i;
            }
            $name .= '.' . $ext;

            $images = array();
            try {
                $imgnames = array(
                    'baseurl' => Sobi::Cfg('live_site') . $sPath,
                    'original' => $name);

                $orgImage
                    = $images['original']
                    = SPFactory::Instance('base.fs.image');
                $orgImage->upload($data, $path . $imgnames['original']);

                $imgnames['image'] = 'img_' . $name;
                $image
                    = $images['image']
                    = clone $orgImage;
                $image->resample($this->resizeWidth, $this->resizeHeight, false);
                $image->saveAs($path . $imgnames['image']);

                $imgnames['thumb'] = 'thumb_' . $name;
                $thumb
                    = $images['thumb']
                    = clone $orgImage;
                $thumb->resample($this->thumbWidth, $this->thumbHeight, false);
                $thumb->saveAs($path . $imgnames['thumb']);

                $imgnames['ico'] = 'ico_' . $name;
                $ico
                    = $images['ico']
                    = clone $orgImage;
                $icoSize = explode(':', Sobi::Cfg('image.ico_size', '70:70'));
                $ico->resample($icoSize[0], $icoSize[1], false);
                $ico->saveAs($path . $imgnames['ico']);

                return $imgnames;
            } catch (Exception $ex) {
                // Delete all images
                array_walk($images, create_function('&$img', '$img->delete();'));
                throw $ex;
            }
        }

        return false;
    }

    /* (non-PHPdoc)
     * @see Site/opt/fields/SPFieldType#deleteData($sid)
     */
    /*
      public function deleteData($sid) {
      parent::deleteData($sid);
      $this->delImgs();
      }/*
      /*
      private function delImgs() {
      $files = SPConfig::unserialize($this->getRaw());
      if (is_array($files) && count($files)) {
      SPLoader::loadClass('cms.base.fs');
      foreach ($files as $file) {
      $file = Sobi::FixPath(SOBI_ROOT . "/{$file}");
      if (SPFs::exists($file)) {
      SPFs::delete($file);
      }
      }
      }
      } */

    /**
     * @return array
     */
    public function struct() {
        $images = SPConfig::unserialize($this->getRaw());
        if (is_array($images) && count($images)) {
            $this->cssClass = strlen($this->cssClass) ? $this->cssClass : 'spFieldsData';
            $this->cssClass = $this->cssClass . ' ' . $this->nid;
            $this->cleanCss();
            switch ($this->currentView) {
                default:
                case 'vcard':
                    $tmpl = $this->get('tmplVCard');
                    break;
                case 'details':
                    $tmpl = $this->get('tmplDetails');
                    break;
            }

            if (isset($tmpl)) {
                $this->cssClass .= ' ' . $tmpl;
                // Get gallery output from template
                $path = SPLoader::dirPath('opt.fields.gallery');
                $imgdirurl = Sobi::Cfg('live_site') . 'images/sobipro/galleries/' . $this->sid . '/' . $this->nid . '/';

                ob_start();
                include $path . 'tmpl/' . $tmpl . '.php';
                if(ob_get_length()){
                    $data = ob_get_clean();
                    if($this->description){
                        $data = '
                            <p class="description">
                                ' . $this->description . '
                            </p>
                            ' . $data;
                    }
                }
                ob_end_clean();

                if($data){
                    return array(
                        '_complex' => 1,
                        '_data' => $data);
                }
            }
        }
    }
}