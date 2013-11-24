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
SPLoader::loadController('controller');

class SPGallery extends SPController {

    /**
     * @var string
     */
    protected $_defTask = 'upload';
    protected $_type = 'gallery';

    protected function authorise($action = 'upload', $ownership = 'valid') {

        $action = SPRequest::sid() ? 'edit':'add';
        if (!( Sobi::Can('entry', $action, $ownership, Sobi::Section()) )) {
            if ($action == 'add' && Sobi::Cfg('redirects.entry_enabled', false) && strlen(Sobi::Cfg('redirects.entry_url', null))) {
                $redirect = Sobi::Cfg('redirects.entry_url', null);
                $msg = Sobi::Cfg('redirects.entry_msg', SPLang::e('UNAUTHORIZED_ACCESS', SPRequest::task()));
                $msgtype = Sobi::Cfg('redirects.entry_msgtype', 'message');
                if (!( preg_match('/http[s]?:\/\/.*/', $redirect) ) && $redirect != 'index.php') {
                    $redirect = Sobi::Url($redirect);
                }
                Sobi::Redirect($redirect, Sobi::Txt($msg), $msgtype, true);
            } else {
                Sobi::Error($this->name(), SPLang::e('UNAUTHORIZED_ACCESS_TASK', SPRequest::task()), SPC::ERROR, 403, __LINE__, __FILE__);
            }
            exit;
        }
        return true;
    }

    /**
     */
    public function execute() {
        SPLang::load('SpApp.gallery');
        $this->_task = strlen($this->_task) ? $this->_task : $this->_defTask;
        switch ($this->_task) {
            case 'upload':
                $this->saveImage();
                break;
        }
    }

    private function saveImage() {
        $response = array(
            'status' => 0,
            'message' => SPLang::txt('FIELD_GAL_ERROR')
        );

        $sid = SPRequest::int('sid');
        $fid = SPRequest::int('fid');

        if ($sid) {
            $entry = SPFactory::Entry($sid);
            $gallery = $entry->getField($fid);
        } else {
            $db = SPFactory::db();
            $db->select('*', 'spdb_field', array('fid' => $fid));
            $field = $db->loadObject();

            $fmod = SPLoader::loadModel('field', defined('SOBIPRO_ADM'));

            $gallery = new $fmod();
            $gallery->extend($field);

            $tmpid = SPRequest::string($gallery->get('nid') . 'id');
            $gallery->set('sid', $tmpid);
        }

        try {
            $images = $gallery->saveImage();
            if ($images) {
                $response = array(
                    'status' => 1,
                    'message' => SPLang::txt(SPLang::txt('FIELD_GAL_SUCCESS')),
                    'images' => $images
                );
            } else {
                $response['error'] = SPLang::txt('FIELD_GAL_ERROR');
            }
        } catch (Exception $ex) {
            $response['error'] = $ex->message;
        }

        SPFactory::mainframe()->cleanBuffer();
        echo json_encode($response);

        exit;
    }

}

