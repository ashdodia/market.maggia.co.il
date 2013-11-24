<?php
	header("Content-type: text/css");
	define( '_JEXEC', 1 );
	define('JPATH_BASE' , 1);
	define('DS', DIRECTORY_SEPARATOR);
	include_once("..".DS."..".DS."..".DS."..".DS."configuration.php");
	include_once("..".DS."..".DS."..".DS."..".DS."libraries".DS."joomla".DS."base".DS."object.php");
	include_once("..".DS."..".DS."..".DS."..".DS."libraries".DS."joomla".DS."database".DS."database.php");
	include_once("..".DS."..".DS."..".DS."..".DS."libraries".DS."joomla".DS."database".DS."database".DS."mysql.php");
	
	$config = new JConfig();
	$options = array ("host" => $config->host,"user" => $config->user,"password" => $config->password,"database" => $config->db,"prefix" => $config->dbprefix);
	$db = new JDatabaseMySQL($options);
	
	$id = (int)$_GET['bid'];
	$sql = "SELECT * FROM #__ad_agency_banners WHERE id='".$id."'";;
	$db->setQuery($sql);
	$_row = $db->loadObject();
	if(isset($_row->parameters)) {
		$_row->parameters = @unserialize($_row->parameters);
	}
	//echo "<pre>";var_dump($_row);die();
?>

.imgdiv2 {
<?php if(!isset($_row->image_url)||($_row->image_url=='')) {echo "	style='display: none;'";}?>
}

#textlink {
	overflow:hidden;
<?php
	if(isset($_row->parameters['border'])&&($_row->parameters['border']!='')){ 
		echo "	border: ".$_row->parameters['border']."px solid #".$_row->parameters['border_color']."; ";
	} 
	if(isset($_row->width)&&($_row->width!="")&&($_row->width!=0)){
		echo "	width: ".$_row->width;
		if(isset($_row->parameters['sizeparam'])&&($_row->parameters['sizeparam']!=0)) { 
			echo "%; ";
		} else { echo "px; ";}
	}
	if(isset($_row->height)&&($_row->height!="")&&($_row->height!=0)){
		echo "	height: ".$_row->height;
		if(isset($_row->parameters['sizeparam'])&&($_row->parameters['sizeparam']!=0)) { 
			echo "%; ";
		} else { echo "px; ";}
	}
	if(isset($_row->parameters['bg_color'])&&($_row->parameters['bg_color']!="")){
		echo "	background-color: #".$_row->parameters['bg_color']."; ";
	}
	if(isset($_row->parameters['padding'])&&($_row->parameters['padding']!="")){
		echo "	padding: ".$_row->parameters['padding']."px; ";
	}
	if(isset($_row->parameters['align'])&&($_row->parameters['align']!="")){
		echo "	text-align: ".$_row->parameters['align']."; ";
	}
?>
}

#tlink {
<?php 
	if(isset($_row->parameters['title_color'])&&($_row->parameters['title_color']!="")){								
		echo "	color: ".$_row->parameters['title_color'].";";
	}
?>
}

#ttitle {
<?php 
	if(isset($_row->parameters['font_size'])&&($_row->parameters['font_size']!="")){
		echo "	font-size: ".$_row->parameters['font_size']."px; ";
	}
	if(isset($_row->parameters['font_family'])&&($_row->parameters['font_family']!="")){
		echo "	font-family: ".$_row->parameters['font_family']."; ";
	}
	if(isset($_row->parameters['font_weight'])&&($_row->parameters['font_weight']!="")){
		echo "	font-weight: ".$_row->parameters['font_weight']."; ";
	}														
	if(isset($_row->parameters['title_color'])&&($_row->parameters['title_color']!="")){
		echo "	color: #".$_row->parameters['title_color']."; ";
	}	
?>
}

#imgdiv2 {
	<?php if(!isset($_row->image_url)||($_row->image_url=='')) { echo '	display: none;'; }?>
}

#rt_image {
<?php 
	if(isset($_row->parameters['ia'])){
		if($_row->parameters['ia'] == 'l') { echo "float:left;";echo "padding: 5px;"; }
		elseif($_row->parameters['ia'] == 'r') { echo "float:right;";echo "padding: 5px;"; }

		if(isset($_row->parameters['mxtype'])&&(isset($_row->parameters['mxsize']))){
			if($_row->parameters['mxtype'] == 'w') { 
				$rtp = 'width'; 
				if($img_w != NULL) { $img_w = $_row->parameters['mxsize']; }
			} else { $rtp = 'height'; }
			echo $rtp.":".$_row->parameters['mxsize']."px;";
		}

	}
?>
}

#tbody {
<?php
	if(isset($_row->parameters['align'])&&($_row->parameters['align']!="")){
		echo "	text-align: ".$_row->parameters['align']."; ";
	}
?>
}

#ttbody {
<?php 
	if(isset($_row->parameters['font_family_b'])&&($_row->parameters['font_family_b']!="")){
		$output_b = "font-family: ".$_row->parameters['font_family_b']."; ";
	}
	if(isset($_row->parameters['font_size_b'])&&($_row->parameters['font_size_b']!="")){
		$output_b.= "font-size: ".$_row->parameters['font_size_b']."px; ";
	}
	if(isset($_row->parameters['font_weight_b'])&&($_row->parameters['font_weight_b']!="")){
		$output_b.= "font-weight: ".$_row->parameters['font_weight_b']."; ";
	}
	if(isset($_row->parameters['body_color'])&&($_row->parameters['body_color']!="")){
		$output_b.= "color: #".$_row->parameters['body_color']."; ";
	}	
	if(isset($_row->parameters['wrap_img'])&&($_row->parameters['wrap_img'] == '0')){
		if(!isset($_row->parameters['ia'])) {$_row->parameters['ia'] = 't';}	
		if(isset($img_w)&&($img_w != NULL)&&($_row->parameters['ia'] != 't')){
			$img_w+=10;
			if($_row->parameters['ia'] == 'l') { $output_b.= "margin-left:".$img_w."px;"; }
			elseif($_row->parameters['ia'] == 'r') { $output_b.= "margin-right:".$img_w."px;"; }									
		}
	}
	echo $output_b;
?>
}

#taction {
<?php
	if(isset($_row->parameters['align'])&&($_row->parameters['align']!="")){
		echo " text-align: ".$_row->parameters['align']."; ";
	}
?>
}

#ttaction {
<?php
	if(isset($_row->parameters['font_family_a'])&&($_row->parameters['font_family_a']!="")){
		$output_a = "font-family: ".$_row->parameters['font_family_a']."; ";
	}
	if(isset($_row->parameters['font_size_a'])&&($_row->parameters['font_size_a']!="")){
		$output_a.= "font-size: ".$_row->parameters['font_size_a']."px; ";
	}
	if(isset($_row->parameters['font_weight_a'])&&($_row->parameters['font_weight_a']!="")){
		$output_a.= "font-weight: ".$_row->parameters['font_weight_a']."; ";
	}
	if(isset($_row->parameters['action_color'])&&($_row->parameters['action_color']!="")){
		$output_a.= "color: #".$_row->parameters['action_color']."; ";
	}		
	echo $output_a;
?>
}