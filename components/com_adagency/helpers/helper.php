<?php

/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
class adagencyAdminHelper {
/**
 * Build the select list for parent menu item
 */
	function getParent( &$row ) {
		$db =& JFactory::getDBO();
		// If a not a new item, lets set the menu item id
		$where = array();
		if ( $row->id ) {
			$where[] = ' id != '.(int) $row->id;
		} else {
			$id = null;
		}

		// In case the parent was null
		if (!$row->parent_id) {
			$row->parent_id = 0;
		}

		// get a list of the menu items
		// excluding the current menu item and its child elements
		$query = 'SELECT m.*' .
				' FROM #__ad_agency_categories m' .
				(count($where) > 0?" where ".implode(" and ", $where):"") .
				' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		// establish the hierarchy of the menu
		$children = array();

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$v->parent = $v->parent_id;
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}


		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		// assemble menu items to the array
		$mitems 	= array();
		$mitems[] 	= JHTML::_('select.option',  '0', JText::_( 'HELPERTOP' ) );
		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename );
		}

		$output = JHTML::_('select.genericlist',   $mitems, 'parent_id', 'class="inputbox" size="10"', 'value', 'text', $row->parent_id );
		return $output;
	}

	function getCatListProd( &$row, $citems) {
        
		$id = '';
        
		// establish the hierarchy of the menu
		$children = array();
        
		if ( $citems ) {
			// first pass - collect children
			foreach ( $citems as $v ) {
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		foreach ($children as $i => $v) {
			foreach ($children[$i] as $j => $vv) {
		                $children[$i][$j]->parent = $vv->parent_id;
			}
		}
		// second pass - get an indent list of the items


		$list = JHTML::_('menu.treerecurse', 0, "&nbsp;", array(), $children, 20, 0, 0);
        
		// assemble menu items to the array
		$mitems 	= array();
		$msg = JText::_('HELPERCATSEL');


                $mitems[]       = JHTML::_('select.option',  $msg );//mosHTML::makeOption( '-1', $msg, 'id', 'name' );
		 	//= mosHTML::makeOption( '0', 'Top' );
        
		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, "&nbsp;&nbsp;&nbsp;".$item->treename );//mosHTML::makeOption( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename,'id', 'name' );
		}
		$output = JHTML::_('select.genericlist',  $mitems, 'catid[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $row->selection);

        
		return $output;
	}

	function get_topcountries_option ($configs) {
	        $db =& JFactory::getDBO();
		$sql = "select distinct country from #__ad_agency_states order by country";
		$db->setQuery($sql);
		$countries = $db->loadObjectList();

		$mitems = array();
		$topcountries = explode (",", $configs->topcountries);        
		foreach ($countries as $country) {

			if ($country != '0') {
				$mitems[] = JHTML::_('select.option',  $country->country, $country->country );
			} else {

			}
		}
		$output = JHTML::_('select.genericlist',  $mitems, 'topcountries[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $topcountries);

		return $output;
	}

	function get_country_options ($profile, $ship = false, $configs) {
		$db =& JFactory::getDBO();	
		$country_word = 'country';
	        if ($ship) $country_word = 'ship'.$country_word;
		if (!isset($profile->$country_word)) $profile->$country_word = '';
		$query = "SELECT country FROM #__ad_agency_states GROUP BY country ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();
		global $topcountries;
	
		$country_option = "<select name='".$country_word."' id='".$country_word."' onChange='changeProvince".($ship?'_ship':'')."();'>";
		$country_option .= '<option value="" ';
		if (!$profile->$country_word) $country_option .= 'selected';
		$country_option .= '>'.(JText::_('HELPERCOUNSEL')).'</option>';             
		$country_option .= '<option value="" ></option>';   
		$configs->topcountries = '';
		$topcountries = explode (",", $configs->topcountries);

        	if (count ($topcountries) > 0) {
			foreach ($topcountries as $topcountry){
				if ($topcountry != '0') {
					$country_option .= '<option value="'.$topcountry.'" ';
					if ( $profile->$country_word == $topcountry && strlen (trim ($topcountry)) > 0) {
					        $country_option .= 'selected';  
        				}
					$country_option .= ' >'.$topcountry.'</option>';  
				}
			
			}
        		
		} else {
		    $country_option .= '<option value="United-States" ';
		    if ( $profile->$country_word == 'United-States') {
		        $country_option .= 'selected';  
		    }
        	    $country_option .= ' >United-States</option>';  
		    
        	    $country_option .= '<option value="Canada" ';
		    if ($profile->$country_word == 'Canada') {
		        $country_option .= 'selected';  
		
		    	$country_option .= '  >Canada</option>';                
        	
			}
	
        	}                
		$country_option .= '<option value=""  >-------</option>';               
		foreach( $countries as $country ) {
        		if (($country->country != 'United-States' && $country->country != 'Canada' && count($topcountries) < 1) || (count($topcountries) > 0 && !in_array($country->country, $topcountries))) {
	        		$country_option .= "<option value='" . $country->country ."' ";
        	
				if ($country->country == $profile->$country_word) $country_option .= "selected";
    
	        	    	$country_option .=  " >" . $country->country . "</option>"; 
		        }
		}
		$country_option .= "</select>"; 
		
		//echo "<pre>";var_dump($profile->$country_word);die();
		
		return $country_option; 

	}

	function get_store_province ($configs, $ship = 0) {
		$db =& JFactory::getDBO();
		$province_word = "province";
	        if ($ship) $province_word = 'ship'.$province_word;
	        if (isset($configs->state)){
         		$query = "select state FROM #__ad_agency_states where country='".$configs->country."'";
            		$db->setQuery($query);
		        $res = $db->loadObjectList();
		        $output = '
                		<div id="'.$province_word.'">
		                    <select name="state">';
			foreach ($res as $i => $v ) {
                        	$output .= '<option value="'.$v->state.'" '; 
		                if ($v->state == $configs->state) $output .= 'selected';
				$output .= '>'.$v->state.'</option>';


		        }

		        $output .= '</select></div>';
	       	} else {
			$output = '<div id="'.$province_word.'">
         	           <select><option>'.(JText::_('HELPERSELECTCOUNTY')).'</option></select>
                	</div>
		            ';
            
	       	}
		return $output;
		
	}

	function parseDate($format, $date) {
		$format = explode ("-", $format);
		$date = explode ("-", $date);
		$res = 0;
		foreach ($format as $i => $v) {
			switch ($v) {
				case "YYYY":
				case "Y":
					$year = $date[$i];
					break;
					
				case "MM":
				case "m":
					$month = $date[$i];
					break;
				
				case "DD":
				case "d":
					$day = $date[$i];
					break;
			}
		}
//echo $configs['time_format'];
//echo $day." ".$month." ".$year."<br/>";
		if ((int )$day > 0 && (int )$month > 0 && (int )$year > 0) {
			$res = mktime (0,0,0, (int)$month, (int)$day, (int)$year);
		} else {
			$res = 0;
		}
		return $res;
	}

	function publishAndExpiryHelper(&$img, &$alt, &$times, &$status, $timestart, $timeend, $published, $configs) {
		$now = time();
		$nullDate = 0;

		if ( $now <= $timestart && $promo->publishing == "1" ) {
	                $img = "tick.png";
        	        $alt = JText::_('HELPERPUBLISHED');
	        } else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
        	        $img = "tick.png";
                	$alt = JText::_('HELPERPUBLISHED');
	        } else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
        	        $img = "publish_r.png";
                	$alt = JText::_('HELPEREXPIRED');
	        } elseif ( $published == "0" ) {
        	        $img = "publish_x.png";
                	$alt = JText::_('HELPERUNPUBLICHED');
	        }       
  	        $times = '';
//echo $timestart;
          	if (isset( $timestart)) {
          		if ( $timestart == $nullDate) {
                		$times .= "<tr><td>".(JText::_("HELPERALWAWSPUB"))."</td></tr>";
	                } else {
        		        $times .= "<tr><td>".(JText::_("HELPERSTARTAT"))." ".date($configs->time_format, $timestart)."</td></tr>";
	                }
        	}
	        if ( isset( $timeend ) ) {
        	        if ( $timeend == $nullDate) {
                		$times .= "<tr><td>".(JText::_("HELPERNEVEREXP"))."</td></tr>";
	                } else {
        		        $times .= "<tr><td>".(JText::_("HELPEXPAT"))." ".date($configs->time_format, $timeend)."</td></tr>";
	                }
        	}
//print_r($configs);
//echo $timeend; 
//echo $times;

                $status = '';
		if (!isset ($promo->codelimit)) {
			$promo->codelimit = 0;
		}
		if (!isset ($promo->used)) {
			$promo->used = 0;
		}

		$remain = $promo->codelimit - $promo->used;
		if (($timeend > $now || $timeend == $nullDate )&& ($remain > 0 || $promo->codelimit == 0)) {
			$status = JText::_("HELPERACTIVE");
		} else if ($timeend != $nullDate && $timeend < $now && ($remain < 1 && $promo->codelimit > 0)) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Date"))." ,".(JText::_("Amount")).")</span>";
		} else if ($remain < 1 && $promo->codelimit > 0) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Amount")).")</span>";
		} else if ($timeend < $now && $timeend != $nullDate){
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Date")).")</span>";
		} else {
			$status = "<span style='color:red'>".(JText::_("HELPERPROMOERROR"))."</span>";
		}

	}

	function format_price ($amount, $ccode, $add_sym = true, $configs) {
		$db = JFactory::getDBO();
		
		
		$code = 0;

		$price_format = '%'.$configs->totaldigits.'.'.$configs->decimaldigits.'f';
		$res =  sprintf($price_format,$amount) ;//. " " . $tax['currency'] . '<br>';
		$sql = "select id, csym from #__ad_agency_currency_symbols where ccode='".strtoupper($ccode)."'";
		$db->setQuery($sql);
		$codea = $db->loadObjectList();
		if (count($codea) > 0) {
			$code = $codea[0]->id;
		} else { 
			$code = 0;
		}
		if ($code > 0 && $configs->usecimg == '1') {
			$ccode = '<img height="14px" src="index2.php?option=com_adagency&task=get_cursym&no_html=1&symid='.$code.'" />';
		} else if ($code > 0) { 
			$ccode = $codea[0]->csym;
			$ccode = explode (",", $ccode);
			foreach ($ccode as $i => $code) {
				$ccode[$i] = "&#".trim($code).";";	
				
			}
			$ccode = implode("", $ccode);
		} else {
			$ccode = "";	
		}
		if ($add_sym) $res = $ccode. " " . $res;
		return $res; 	
	}
	
	function get_cursym () {
		$db = JFactory::getDBO();
		$symid = JRequest::getVar('symid', 0, 'request');
		$sql = "select cimg from #__ad_agency_currency_symbols where id='".$symid."'";
		$database->setQuery($sql);
		$sym = $database->loadResult();
		ob_clean();
		ob_start();
 
		header("Content-Type: image/gif; name=\"".$sym."\"" );
		readfile(JPATH_SITE."/administrator/components/com_adagency/images/csym/".$sym);
	
	
	
	}

	function getCSym ($ccode) {
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_settings";
		$db->setQuery($sql);
		$configs = $db->loadObjectList();
//print_r($configs);
		$configs = $configs[0];

		$sql = "select id, csym from #__ad_agency_currency_symbols where ccode='".strtoupper($ccode)."'";
		$db->setQuery($sql);
		$codea = $db->loadObjectList();
		if (count($codea) > 0) {
			$code = $codea[0]->id;
		} else { 
			$code = 0;
		}
		if ($code > 0 && $configs->usecimg == '1') {
			$ccode = '<img height="14px" src="index2.php?option=com_adagency&task=get_cursym&no_html=1&symid='.$code.'" />';
		} else if ($code > 0) { 
			$ccode = $codea[0]->csym;
			$ccode = explode (",", $ccode);
			foreach ($ccode as $i => $code) {
				$ccode[$i] = "&#".trim($code).";";	
				
			}
			$ccode = implode("", $ccode);
		} else {
			$ccode = "";	
		}
		return $ccode; 	


	}
	
	function formatime($time,$option = 1){
		$date_time = explode(" ",$time);
		$tdate = explode("-",$date_time[0]);
		$output = NULL;
		if(!isset($date_time[1])) {$date_time[1] = NULL;}
		switch($option){
			case "1":
				$output = $tdate[2]."-".$tdate[1]."-".$tdate[0]." ".$date_time[1];
				break;
			case "2":
				$output = $tdate[2]."/".$tdate[1]."/".$tdate[0]." ".$date_time[1];
				break;
			case "3":
				$output = $tdate[1]."-".$tdate[2]."-".$tdate[0]." ".$date_time[1];
				break;
			case "4":	
				$output = $tdate[1]."/".$tdate[2]."/".$tdate[0]." ".$date_time[1];
				break;
			case "5":
				$output = $time;
				break;
			case "6":
				$output = str_replace("-","/",$time);
				break;
			case "7":
				$output = $tdate[2]."-".$tdate[1]."-".$tdate[0];
				break;
			case "8":
				$output = $tdate[2]."/".$tdate[1]."/".$tdate[0];			
				break;
			case "9":
				$output = $tdate[1]."-".$tdate[2]."-".$tdate[0];
				break;
			case "10":
				$output = $tdate[1]."/".$tdate[2]."/".$tdate[0];
				break;
			case "11":
				$output = $date_time[0];
				break;
			case "12":
				$output = str_replace("-","/",$date_time[0]);
				break;
			default:
				$output = $time;
				break;
			
		}
		return $output;
	}

	function ip_is_private($ip){
        $pri_addrs = array(
                          '10.0.0.0|10.255.255.255',
                          '172.16.0.0|172.31.255.255',
                          '192.168.0.0|192.168.255.255',
                          '169.254.0.0|169.254.255.255',
                          '127.0.0.0|127.255.255.255'
                         );

        $long_ip = ip2long($ip);
        if($long_ip != -1) {
            foreach($pri_addrs AS $pri_addr){
                list($start, $end) = explode('|', $pri_addr);
                 // IF IS PRIVATE
                 if($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
                 return (TRUE);
            }
		}
		return (FALSE);
	}

};
?>