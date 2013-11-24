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
SPLoader::loadModel('datamodel');
SPLoader::loadModel('dbobject');

class SPGallery extends SPDBObject implements SPDataModel {

    /**
     * @var string
     */
    protected $_type = 'gallery';

    /**
     * Returns an array with field object of field type which is possible to use it as entry name field
     *
     * @return array
     */
    public function getImageFields() {
        $types = array('image');
        /* @var SPdb $db */
        $db = & SPFactory::db();
        try {
            $db->select(array('fid', 'nid'), 'spdb_field', array('fieldType' => $types, 'section' => Sobi::Reg('current_section')));
            $fields = $db->loadAssocList();
        } catch (SPException $x) {
            Sobi::Error($this->name(), SPLang::e('CANNOT_GET_FIELD_FOR_NAMES', $x->getMessage()), SPC::WARNING, 0, __LINE__, __FILE__);
        }
        $result = array();
        foreach ($fields as $field) {
            $result[$field['nid']] = $field['nid'];
        }
        return $result;
    }

    public function tpl() {
        $db = & SPFactory::db();
        try {
            $db->select(array('pid'), 'spdb_plugin_task', array('onAction' => 'gallery'));
            $tpls = $db->loadResultArray();
        } catch (SPException $x) {
            Sobi::Error($this->name(), SPLang::e('CANNOT_GET_FIELD_FOR_NAMES', $x->getMessage()), SPC::WARNING, 0, __LINE__, __FILE__);
        }
        $tpls = array_combine($tpls, $tpls);
        return $tpls;
    }

}
