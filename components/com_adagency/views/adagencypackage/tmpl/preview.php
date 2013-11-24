<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/
defined ('_JEXEC') or die ("Go away.");
$get = $this->get;
$link = $this->link;
$link = JURI::base() . $this->link;

// echo $link;die();
if (function_exists('curl_init')) {
	// initialize a new curl resource
	$ch = curl_init();

	// set the url to fetch
	curl_setopt($ch, CURLOPT_URL, $link);

	// don't give me the headers just the content
	curl_setopt($ch, CURLOPT_HEADER, 0);

	// return the value instead of printing the response to browser
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// use a user agent to mimic a browser
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');

	$content = curl_exec($ch);
	// remember to always close the session and free all resources
	curl_close($ch);

	$html = $content;

	if(isset($get['cid'])&&($get['cid'] != NULL)){
	$new_css = '<style type="text/css">
		<!--
		#ijoomlazone'.$get['cid'].' {
			background: yellow; border: 4px dotted black;
			overflow:hidden;
		}
		-->
	  </style>
	';
	} else {
	$new_css = '<style type="text/css">
		<!--
		.mod_ijoomlazone {
			background: yellow; border: 4px dotted black;
		}
		-->
	  </style>
	';
	}

	$html = str_replace('</head>', $new_css.'</head>', $html);

	echo $html;
} else {
	echo "<div style='text-align:center;font-size:20px;font-weight:bold; margin-top: 25%;'>
		Please enable CURL extension in your PHP server configuration to view this page properly!<br />
	</div>";
}
?>
