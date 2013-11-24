<?php
defined ('_JEXEC') or die ("Go away.");

function AdagencyBuildRoute(&$query) {

/*
    if( isset($query['task']) && ($query['task'] == 'click') ) {
        echo "<hr />";
        var_dump($query);
        echo "<hr />";die();
    }
*/
    
	$segments = array();
	if (isset($query['controller'])) {
		$segments[] = $query['controller'];
		unset($query['controller']);
	} 
    if (isset($query['task'])) {
		$segments[] = $query['task'];
        unset($query['task']);
	} 
    if (isset($query['cid'])) {
		if (is_array($query['cid'])) {
			$segments[] = $query['cid'][0];
		} else {
			$segments[] = $query['cid'];
		}
		unset($query['cid']);
	} 
	if (isset($query['aid'])) {
		if (is_array($query['aid'])) {
			$segments[] = $query['aid'][0];
		} else {
			$segments[] = $query['aid'];
		}
		unset($query['aid']);
	} 
    if (isset($query['bid'])) {
		if (is_array($query['bid'])) {
			$segments[] = $query['bid'][0];
		} else {
			$segments[] = $query['bid'];
		}
		unset($query['bid']);
	} 
    /*if (isset($query['lid'])) {
		if (is_array($query['lid'])) {
			$segments[] = $query['lid'][0];
		} else {
			$segments[] = $query['lid'];
		}
		unset($query['lid']);
	} */
    if (isset($query['tid'])) {
		if (is_array($query['tid'])) {
			$segments[] = $query['tid'][0];
		} else {
			$segments[] = $query['tid'];
		}
		unset($query['tid']);
	} 
	if (isset($query['tmpl'])) {
		if (is_array($query['tmpl'])) {
			$segments[] = $query['tmpl'][0];
		} else {
			$segments[] = $query['tmpl'];
		}
		unset($query['tmpl']);
    } 	
    if (isset($query['adid'])) {
		if (is_array($query['adid'])) {
			$segments[] = $query['adid'][0];
		} else {
			$segments[] = $query['adid'];
		}
		unset($query['adid']);
	} 	

    /*echo "<hr />"; 
    var_dump($segments);
    echo "<hr />";*/
    
    //print_r($segments); die;
	return $segments;
}

function AdagencyParseRoute($segments) {

    //echo "<hr />";
    //var_dump($segments);
    //echo "<hr />";die();

	$vars = array();
	//print_r($segments);echo "<hr />";
	$vars['controller'] = isset($segments[0])?$segments[0]:null;
	$vars['task'] = isset($segments[1])?$segments[1]:null;
	//$vars['cid'] = isset($segments[2])?$segments[2]:null;
	//$vars['aid'] = isset($segments[3])?$segments[3]:null;
	//$vars['bid'] = isset($segments[4])?$segments[4]:null;
	//$vars['lid'] = isset($segments[5])?$segments[5]:null;
	if ( isset($segments[5]) ) {		
		$vars['tid'] = $segments[5];	
	} else 
		if ($vars['task']=='order') {
			$vars['tid'] = isset($segments[2])?$segments[2]:null;	
		}
	
	//task preview adds
	if ($vars['controller']=='adagencyAds'){
		if($vars['task']=='preview') {
			$vars['tmpl'] = isset($segments[2])?$segments[2]:null;
			$vars['adid'] = isset($segments[3])?$segments[3]:null;	
		}
		else if($vars['task']=='click') {
			$vars['cid'] = isset($segments[2])?$segments[2]:null;
            $vars['aid'] = isset($segments[3])?$segments[3]:null;
			$vars['bid'] = isset($segments[4])?$segments[4]:null;			
		}
	}
	//task preview packages
	if($vars['controller']=="adagencyPackages"){
        if($vars['task'] == 'packs') {
            $vars['tmpl'] = isset($segments[2])?$segments[2]:null;
        } else {
            $vars['cid'] = isset($segments[2])?$segments[2]:null;
        }
	}
	if($vars['controller']=='adagencyCampaigns'){
		$vars['cid'] = isset($segments[2])?$segments[2]:null;
	}
	if(($vars['controller']=='adagencyStandard' || $vars['controller']=='adagencyAdcode' 
        || $vars['controller']=='adagencyPopup' || $vars['controller']=='adagencyFlash' 
        || $vars['controller']=='adagencyTextlink' || $vars['controller']=='adagencyTransition' 
        || $vars['controller']=='adagencyFloating') && $vars['task']=='edit' )
    {
		$vars['cid'] = isset($segments[2])?$segments[2]:null;
	}
	if($vars['controller']=='adagencyAdvertisers') {
		$vars['cid'] = isset($segments[2])?$segments[2]:null;
	}
	
	if($vars['controller']=='adagencyOrders') {
		if(count($segments) == 3){
			$vars['task'] = $segments["1"];
			$vars['tid'] = $segments["2"];
		}
	}
	
	return $vars;
}
?>