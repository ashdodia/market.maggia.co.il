<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/
function getChannelInfo($bid = NULL){
    require_once(str_replace('administrator','',JPATH_BASE).'/components/com_adagency/helpers/geoipotherdata.php');
    require_once(str_replace('administrator','',JPATH_BASE).'/components/com_adagency/helpers/geoipregionvars.php');
    if (!function_exists('json_encode')) {
        require_once(str_replace('administrator','',JPATH_BASE).DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');
    }
    //echo "<pre>";var_dump($HELPER_GEOIP_CONTINENTS);die();
    $db = &JFactory::getDBO();
    if($bid == NULL) {
        $cid = JRequest::getInt('cid', 0);
        $cid2 = JRequest::getInt('id', 0);
    } else {
        $cid = $bid;
    }
    $output = "";
    if(($cid == 0)&&($cid2 != 0)) {$cid = $cid2;}
    if(isset($cid)&&($cid != 0)) {
        $sql = "SELECT id FROM `#__ad_agency_channels` WHERE banner_id = '".$cid."'";
        $db->setQuery($sql);
        $cid = $db->loadResult();
        $sql = "SELECT `type`, `logical`, `option`, `data` FROM `#__ad_agency_channel_set` WHERE channel_id = ".intval($cid)." ORDER BY id ASC";
        //echo "<pre>";var_dump($sql);die();
        $db->setQuery($sql);
        $result = $db->loadObjectList();
        if(isset($result)) {
            $counter = 0;
            foreach($result as $element){
                $counterREG = 0;$aux = NULL;$values = NULL;$not_any = NULL;
                $element->data = json_decode($element->data);
                if($counter>=1) {
                    $output.=", ";
                }
                if($element->type == 'continent') {
                    foreach($element->data as &$val){
                        $val = $HELPER_GEOIP_CONTINENTS[$val];
                    }
                    $values = implode(', ',$element->data);
                    $output.= JText::_('ADAG_CONTINENT').": ".$values;
                } elseif($element->type == 'country'){
                    foreach($element->data as &$val){
                        $val = $HELPER_GEOIP_COUNTRIES[$val];
                    }
                    $values = implode(', ',$element->data);
                    if($element->option != 'is') { $not_any = JText::_('ADAG_NOT_ANY'); }
                    $output.= JText::_('ADAG_COUNTRY').$not_any.": ".$values;
                } elseif($element->type == 'region'){
                    if(isset($element->data)){
                        foreach($element->data as &$val){
                            if($counterREG == 0){
                                $aux = $val;
                                $val = $HELPER_GEOIP_COUNTRIES[$val];
                            } else {
                                $val = $GEOIP_REGION_NAME[$aux][$val];
                            }
                            $counterREG++;
                        }
                    }
                    if($element->option != 'is') { $not_any = JText::_('ADAG_NOT_ANY'); }
                    $output.= JText::_('ADAG_COUNTRY').": ".$element->data[0];
                    $element->data[0] = NULL;unset($element->data[0]);
                    $values = implode(', ',$element->data);
                    $output.= ",".JText::_('ADAG_REGION').$not_any.": ".$values;
                } elseif($element->type == 'city'){
                    $output.= JText::_('ADAG_COUNTRY').": ".$HELPER_GEOIP_COUNTRIES[$element->data[0]];
                    $values = $element->data[1];
                    $output.= ", ".JText::_('ADAG_CITY').": ".$values;
                } elseif($element->type == 'dma'){
                    if($element->option != 'is') { $not_any = JText::_('ADAG_NOT_ANY'); }
                    foreach($element->data as &$val){
                        $val = $HELPER_GEOIP_DMA[$val];
                    }
                    $values = implode(', ',$element->data);
                    $output.= JText::_('ADAG_DMA2').$not_any.": ".$values;
                } elseif($element->type == 'latitude'){
                    if($element->option == '==') { $not_any = JText::_('ADAG_BETWEEN'); } else { $not_any = JText::_('ADAG_NOT_BETWEEN'); }
                    $output.= JText::_('ADAG_LATLONG').$not_any.": ";
                    $output.= $element->data->a." ".JText::_('ADAG_AND')." ".$element->data->b."; ";
                    $output.= $element->data->c." ".JText::_('ADAG_AND')." ".$element->data->d;
                } elseif($element->type == 'usarea'){
                    if($element->option == '==') { $not_any = JText::_('ADAG_IS_EQUAL'); }
                    elseif($element->option == '!=') { $not_any = JText::_('ADAG_IS_DIFFERENT'); }
                    elseif($element->option == '=~') { $not_any = JText::_('ADAG_CONTAINS'); }
                    elseif($element->option == '!~') { $not_any = JText::_('ADAG_NOT_CONTAINS'); }
                    $output.= JText::_('ADAG_USAREA').": ".$element->data[0];
                } elseif($element->type == 'postalcode'){
                    if($element->option == '==') { $not_any = JText::_('ADAG_IS_EQUAL'); }
                    elseif($element->option == '!=') { $not_any = JText::_('ADAG_IS_DIFFERENT'); }
                    elseif($element->option == '=~') { $not_any = JText::_('ADAG_CONTAINS'); }
                    elseif($element->option == '!~') { $not_any = JText::_('ADAG_NOT_CONTAINS'); }
                    $output.= JText::_('ADAG_POSTAL_COD').": ".$element->data[0];
                }
                $counter++;
            }
        }
        //echo "<pre>";var_dump($result);echo "<hr />";//die();
        //var_dump($output);die();
        return trim($output);
    }
}

    $db = &JFactory::getDBO();
    $data = JRequest::get('post');
    $user_n = &JFactory::getUser();
    if(!isset($data['geo_type'])) { return true; }

    if((!isset($data['id'])||($data['id'] == 0))&&($bid != NULL)){
        $data['id'] = $bid;
    }

    if(strpos(JPATH_BASE,'administrator')>0) {
        $status = 'B';
        $user_n->id = $this->getUIDbyAID($data['advertiser_id']);
    } else { $status = 'F'; }

    //echo "<pre>";var_dump($data);echo "<hr />";//die();
    if (!function_exists('json_encode')) {
        require_once(str_replace('administrator','',JPATH_BASE).DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');
    }

    if(!isset($data['id']) || !isset($data['advertiser_id'])) { return false; } else { $channel_id = $this->delete_geo($data['id'], $data['advertiser_id']); }

    if((intval($data['geo_type']) == 1)&&isset($data['limitation'])&&($data['limitation'] != '')) {
        if(!isset($channel_id) || intval($channel_id) < 0) {
            $sql = "INSERT INTO `#__ad_agency_channels` (`id`, `name`, `banner_id`, `advertiser_id`, `public`, `created`, `created_by`, `from`) VALUES (NULL, '".$data['title']."', '".$data['id']."', '".$data['advertiser_id']."', 'N', NOW(), '".$user_n->id."', '".$status."' );";
            $db->setQuery($sql);
            $db->query();
            $sqlz[] = $sql;
            $channel_id = @mysql_insert_id();
            if(!$channel_id) {
                $sql = "SELECT * FROM #__ad_agency_channels WHERE `name` = '".$data['title']."' ORDER BY id DESC LIMIT 1";
                $sqlz[] = $sql;
                $db->setQuery($sql);
                $channel_id = $db->loadResult();
            }
        } else {
            $sql = "UPDATE `#__ad_agency_channels` SET `name` = '".$data['title']."' WHERE `id` = ".$channel_id.";";
            $sqlz[] = $sql;
            $db->setQuery($sql);
            $db->query();
        }

        if(isset($data['limitation'])&&($data['limitation'] != '')) {
            $limitation_sql = "INSERT INTO `#__ad_agency_channel_set` (`id` ,`channel_id` ,`type` ,`logical` ,`option` ,`data`) VALUES ";
            $temp = NULL;
            $the_equals = array('usarea', 'city', 'latitude', 'postalcode');
            $the_is = array('country', 'region', 'dma');
            $region_city_exist = false;
            foreach($data['limitation'] as $element) {
                if(($element['type'] == 'region')||($element['type'] == 'city')) {
                    $region_city_exist = true;
                } elseif($element['type'] == 'country') {
                    $the_country = array($element['data'][0]);
                }
            }
            foreach($data['limitation'] as $element) {
                if(in_array($element['type'],$the_equals)) { $element['option'] = '==';}
                elseif (in_array($element['type'],$the_is)) { $element['option'] = 'is';}
                else { $element['option'] = ''; }
                if(($element['type'] == 'region')||($element['type'] == 'city')) {
                    $element['data'] = array_merge($the_country, $element['data']);
                } elseif (($element['type'] == 'country')&&($region_city_exist)) { continue; }
                $temp[] = "( NULL , '".$channel_id."', '".$element['type']."','AND','".$element['option']."', '".json_encode($element['data'])."' )";
            }
            $channel_sets = implode(', ',$temp);
            $sets_sql = $limitation_sql.$channel_sets;
            $sqlz[] = $sets_sql;
            $db->setQuery($sets_sql);
            $db->query();
        }
        $new_name = substr(getChannelInfo($bid),0,25).'...';
        $sql = "UPDATE `#__ad_agency_channels` SET `name` = '".$new_name."' WHERE `id` =".$channel_id.";";
        $db->setQuery($sql);
        $db->query();
    } elseif(intval($data['geo_type']) == 2){
        if(isset($data['limitation_existing'])&&($data['limitation_existing'] != 0)) {
            $channel_id = $data['limitation_existing'];
        }
    }

    $sql = "UPDATE `#__ad_agency_banners` SET `channel_id` = '".$channel_id."' WHERE `id` =".$bid.";";
    $db->setQuery($sql);
    $db->query();

    //echo "<pre>";var_dump($sqlz);die();
    /*echo "<pre>";var_dump($sets_sql);echo "<hr />";//die();
    var_dump($data);die();*/

    return true;
?>
