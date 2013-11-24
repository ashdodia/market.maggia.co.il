<?php
	defined ('_JEXEC') or die ("Go away.");
	if (function_exists('curl_init')) {

		// initialize a new curl resource
		$ch = curl_init();
		$link = JURI::root()."index.php?option=com_adagency&controller=adagencyPackages&tmpl=component";

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

		$new_css = "
		<style>
			div.ijadagencypackage {
				padding: 15px;
			}
            #main {
                font-size: 12px;
            }
		</style>
		";
		$html = $content;

		$re = "#<div class=\"below_packs\">(.*)</div>#Uis";
		$html = preg_replace($re,"",$html);
		$re = "#<p class=\"start_buy\">(.*)</p>#Uis";
		$html = preg_replace($re,"",$html);
		$html = str_replace('</head>', $new_css.'</head>', $html);

		echo $html;
	} else {
		echo "<div style='text-align:center;font-size:20px;font-weight:bold; margin-top: 25%;'>Please enable CURL extension in your PHP server configuration to view this page properly!</div>";
	}
?>
