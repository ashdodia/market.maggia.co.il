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
jimport ("joomla.aplication.component.model");

class adagencyModeladagencyPlugin extends JModel {
	var $_plugins;
	var $_plugin;
	var $plugin_instances = array();
	var $_id = null;
	var $allowed_types = array("payment", "encoding");
	var $req_methods = array("getFEData", "getBEData");	
	var $_installpath; 
	var $plugins_loaded = 0;
    
	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
		$this->loadPlugins();
	}

	function setId($id) {
		$this->_id = $id;
		$this->_installpath = JPATH_COMPONENT_ADMINISTRATOR . DS . "plugins" . DS;
		$this->_plugin = null;
	}

	function getlistPlugins () {
		if (empty ($this->_plugins)) {
			$sql = "select * from #__ad_agency_plugins";
			$this->_plugins = $this->_getList($sql);			
		} 
		return $this->_plugins;
	}

	function loadPlugins () { 
		if ($this->plugins_loaded == 1) return;		
		$plugins = $this->getlistPlugins();

		foreach ($plugins as $plugin) {
			$this->registerPlugin($plugin->filename, $plugin->classname);
		        if ($plugin->published == '1') {
	        	//add published plugins to arrays respective to
	            //their types
			  	if ($plugin->type == 'payment') {
					$this->payment_plugins[$plugin->name] = $plugin;
					if ($plugin->def == 'default'){
						$this->default_payment = $plugin; //also select default gateway	 
					}
				}
				if ($plugin->type == 'encoding') {
					$this->encoding_plugins[$plugin->name] = $plugin;
				}
			} 
		}
		$this->plugins_loaded = 1;
		return;
	}


	function getPlugin() {	
		if (empty ($this->_plugin)) {        
			$this->_plugin =& $this->getTable("adagencyPlugin");
			$this->_plugin->load($this->_id);
			$this->_plugin->instance =& $this->registerPlugin($this->_plugin->filename, $this->_plugin->classname);
			$db = JFactory::getDBO();
	        $sql = "select setting,description,value from #__ad_agency_plugin_settings where pluginid='".intval($this->_plugin->id)."'";
			$db->setQuery($sql);
			$conf = $db->loadObjectList();
			$config = new stdClass;
			$config->headers = array();
			$config->values = array();
			foreach ($conf as $value) {
    			$config->headers[] = $value->setting;
				$config->values[] = $value->value;
				$config->descrs[] = $value->description;
				$config->data[$value->setting] = $value->value;            
			}
			$this->_plugin->config = $config;
		}                

    	return $this->_plugin;
	}

	function getEncoders() {
    	$db = JFactory::getDBO();
		$sql = "select id,name,classname from #__ad_agency_plugins where type='encoding'";
		$db->setQuery($sql);
		$encs = $db->loadObjectList();
        
		$this->_plugins = array();
		foreach ($encs as $enc) {
			$this->_id = $enc;
			$this->_plugin = null;
			$this->_plugins[] = $this->getPlugin();
		}
        return $this->_plugins;
	}

	function store () {
		$item =& $this->getTable('adagencyPlugin');
		$data = JRequest::get('post');
		$data['sandbox'] = JRequest::getVar("sandbox", 0, 'post');
		if (!$item->bind($data)){
			$item->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$item->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
			$item->setError($item->getErrorMsg());
			return false;
		}

		$db = JFactory::getDBO();
		$sql = "select setting from #__ad_agency_plugin_settings where pluginid='".intval($item->id)."'";
		$db->setQuery($sql);
		$conf = $db->loadObjectList();
		foreach ($conf as $i => $v) {
			$req = JRequest::getVar($v->setting, '', 'post');
			$sql = "update #__ad_agency_plugin_settings set value='".addslashes(trim($req))."' where pluginid='".intval($item->id)."' and setting='".addslashes(trim($v->setting))."'";
			$db->setQuery($sql);
			$db->query();
		}
		return true;
	}	
	
	function registerPlugin ($filename, $classname) {
    
		$install_path = $this->_installpath; 
		if (!file_exists($install_path.$filename)) {
			return 0;//_NO_PLUGIN_FILE_EXISTS;	
		}
		require_once ($install_path.$filename);
		$plugin = new $classname;//new $this->plugins[$classname];	// 
		if (!is_object($plugin) ) {
			return 0;
		}
		foreach ($this->req_methods as $method) {
			if (!method_exists ($plugin, $method) ) {
				return 0;
			}
		}
		if (isset($this->_plugins[$classname])) {
			$this->_plugins[$classname]->instance =& $plugin;
		} else {
			$this->_plugins[$classname] = new stdClass;
			$this->_plugins[$classname]->instance = &$plugin;
		}		
        
		return $plugin;
	}

	function upload() {
		$table_entry =& $this->getTable ("adagencyPlugin");
		jimport('joomla.filesystem.file');
		$file = JRequest::getVar('pluginfile', array(), 'files');	
		$install_path = JPATH_ROOT.DS."tmp".DS."adagencyplugin".DS;
		Jfolder::create ($install_path);

		if (JFile::copy($file['tmp_name'], $install_path.$file['name'], '')) {

			$res = $this->installPlugin($install_path, $file['name']);
			JFolder::delete ($install_path);

		} else {
			$res = JText::_('MODPLUGCOPYERR');
		}
		
		return $res;
	}

	function BEPluginHandler ($param = 0) {
		$result = array();
		if (!is_object($this->_plugin->instance) ) return;
		$i = 0;
		$plugin = $this->_plugin;
        $sbx = $plugin->sandbox;
        $pluginform = array(
        	    "header" => $plugin->name.(JText::_("MODPLUGCONFIG")),
	            "header1" => $plugin->config->headers,
		    "descriptions" => $plugin->config->descrs,
	            "value" => $plugin->config->values,
        	    "isdef"=> $plugin->def,
	            "sandbox" => $plugin->name."_sandbox",
        	    "sbx" => $sbx
	        );
		$result[$i] = $pluginform;
		$result[$i]["type"] = $plugin->type;
		$result[$i]["pluginname"] = $plugin->name;
		if ($plugin->type != 'encoding') {
			$result[$i]["reqhttps"] = $plugin->reqhttps;
			$result[$i]["reqhttps_name"] = $plugin->name."_reqhttps";
		}
		$result[$i]["published"] = $plugin->published;
		return $result;	
	}

	function delete () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item =& $this->getTable('adagencyPlugin');
		jimport('joomla.filesystem.file');
		foreach ($cids as $cid) {
			$sql = "select name from #__ad_agency_plugins where id = '".intval($cid)."'";
			$db->setQuery($sql);
			$plugname = $db->loadResult();
			$sql = "delete from #__ad_agency_currencies where plugname = '".addslashes(trim($plugname))."'";
			$db->setQuery($sql);
			$db->query();
			$item->load($cid);
			JFile::delete($this->_installpath.$item->filename);
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	function publish () {
		$db =& JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item =& $this->getTable('adagencyPlugin');
		if ($task == 'publish'){
			$sql = "update #__ad_agency_plugins set published='1' where id in ('".implode("','", $cids)."')";
			$res = 1;
		} else {
			$sql = "update #__ad_agency_plugins set published='0' where id in ('".implode("','", $cids)."')";
			$res = -1;
		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
	
		return $res;
	}

	function getPluginOptions ($selected = '') {
		$content = '<select name="payment_type">';
		if(isset($this->payment_plugins)){
			foreach($this->payment_plugins as $plugin){
				$content .= '<option value="'.str_replace(".php","",$plugin->filename).'"';
				if(strlen( $selected ) > 0 && $selected.".php" == $plugin->filename){
					$content.= '"selected"';
				}
				
				if(isset($plugin->display_name)){
					$plugin->name=$plugin->display_name;
				}
				else{
					$plugin->name=$plugin->classname;
				}
				$language = "ADAG_".strtoupper(JText::_($plugin->name))."_PAYMENT";
				$content .= '>'.JText::_($language).'</option>';
			}
		}
		return $content;
	}

	function getListPluginCurrency() {
		$db = JFactory::getDBO();
		$sql = "select c.*, p.name as pluginame from #__ad_agency_currencies c, #__ad_agency_plugins p where p.id=c.pluginid and p.published=1";

		$db->setQuery($sql);
		$plugs = $db->loadObjectList();
		$res = array();
		foreach ($plugs as $plug) {
			$res[$plug->pluginame][$plug->currency_name] = $plug->currency_full;
		}
		return $res;

	}
    
	function getEncoder ($method) {
		$handler = -1;
		if (count ($this->encoding_plugins) < 1 ) { 
				return (-1);//do not to forget place errorcode switch in calling file so user
							// may know if encoding was successful or not.
		} else {
			foreach ($this->encoding_plugins as $plugin) {
				if ($method == $plugin->name ) {
					$handler = $this->_plugin[$plugin->classname]->instance; //we found handler for encoding
					break;
				}
					
			}
			
		}
		return $handler;	
	}
	
	function encodeZipFile ( $method, $phpversion, $srcDir, $srcFile, $tmpDir, $dstDir, $files, $mainFile, $passphrase, $trial, $now, $licenseFile = "license.txt" ) {
		jimport("joomla.filesystem.file");
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");
		@unlink ($dstDir.$srcFile);
		@unlink ($dstDir.$mainFile);
        @unlink ($tmpDir.$mainFile);
		$srcZip = new PclZip($srcDir.$srcFile);
		$x = $srcZip->extract(PCLZIP_OPT_BY_NAME, $mainFile, PCLZIP_OPT_PATH, $tmpDir);
		if ($x == 0) {
			die ($srcZip->errorInfo(true));
		}
   		$mainZip = new PclZip($tmpDir.$mainFile); 
		if ($mainZip->extract(PCLZIP_OPT_PATH, $tmpDir."1/") == 0 ) {
		}
		if ($mainZip->extract(PCLZIP_OPT_PATH, $dstDir) == 0 ) {
		}
		$handler = $this->getEncoder($method);
		if (!is_object($handler)) return (-2); //no handler found - internal error or hijack attempt
	
		foreach ( $files as $file ) {
			$file = trim($file);
			$handler->performEncoding ($file, $licenseFile, $passphrase, $tmpDir."1/", $dstDir, $now, $this->encoding_plugins[$method]); // plugin does all required
        }                    
	    $i = 0;
		$curdir = getcwd ();
		chdir ( $dstDir  );
		foreach ($files as $file){
		    $file = trim ($file);
			$mainZip->delete (PCLZIP_OPT_BY_NAME, $file);
			$r = $mainZip->add ($dstDir.$file, PCLZIP_OPT_REMOVE_PATH, $dstDir);
	        ++$i;
		}
		JFolder::delete($dstDir);
		JFolder::create($dstDir);
		JFile::copy ($srcDir.$srcFile, $dstDir.$srcFile);
		$srcZip = new PclZip (	$dstDir.$srcFile);
		$v_list = $srcZip->delete (PCLZIP_OPT_BY_NAME, $mainFile);
		$res = $srcZip->add ($tmpDir.$mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
		if ($res == 0) $res = $srcZip->create ($tmpDir.$mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
		chdir($curdir);
		JFolder::delete($tmpDir);
	}

	/*
	 * License file generator - converted code from original adagency
	 */
	function genLicenseFile ( $method, $zipFile, $subZipFile, $domain="localhost", $devdomain="localhost", $subdomains, $licenseFile, $passphrase, $trial_period = "", $outDir ) {
	
		//gen the license file
		foreach ($subdomains as $i => $v) {
	
			if (isset($subdomains[$i]) && trim($subdomains[$i]) != '') {
				$subdomains[$i] = preg_replace ("/^www\./", "", $subdomains[$i]);
				$subdomains[$i] = str_replace (".".$domain, "", $subdomains[$i]);
				$subdomains[$i] = str_replace ($domain, "", $subdomains[$i]);
				
				if (trim($subdomains[$i]) != '') {
					$tmp = trim( $subdomains[$i] );
					
					if (strlen(trim($domain)) > 0 ){ 			
						$subdomains[$i] = trim( $subdomains[$i] ) . "." . $domain;
						$subdomains[] = "www.".trim( $subdomains[$i] );// . "." . $domain;
					} else unset ($subdomains[$i]);
					if (strlen(trim($devdomain)) > 0 ){ 			
						$subdomains[] = trim( $tmp ) . "." . $devdomain;
						$subdomains[] = "www." . trim( $tmp ) . "." . $devdomain;
					}
	
					
				} else unset ($subdomains[$i]);
			}
		}
	
		$subdomains[] = '127.0.0.1';
		if ( is_array( $subdomains ) ) {
			$subdomains_text = @implode( ",",$subdomains );
		} else {
			$subdomains_text = '';
		}
		      
		$handler = $this->getEncoder($method);

		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
		$handler->genLicense($domain, $devdomain, $subdomains_text, $licenseFile, $passphrase, $trial_period = "", $outDir, $this->encoding_plugins[$method]);
		
		//common routines - lets they stay in this form for a while
		$curdir = getcwd ();
		
		//extract the sub zip file
		chdir ( $outDir );
		jimport("joomla.filesystem.file");
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");
	
		$srcZip = new PclZip($zipFile);
		if ($srcZip->extract(PCLZIP_OPT_BY_NAME, $subZipFile) == 0) {
		}
		$subZip = new PclZip($subZipFile); 	
		$license_file_text = implode ('', file($licenseFile));
		$subZip->add ($licenseFile);
		$srcZip->delete(PCLZIP_OPT_BY_NAME, $subZipFile);
		$res = $srcZip->add ($subZipFile);
		if ($res == 0) $srcZip->create ($subZipFile);
	
		//remove temp files
		@unlink( $subZipFile );
		@unlink( $licenseFile );
		//done
		chdir( $curdir );
		return $license_file_text;	
	}

	/*
	 * function from original code... lets see what it does.
	 */
	function genLoaders ( $method, $phpVersions, $platforms, $outDir ) {
	
		//lets select plugin that handles choosen encoding method
		$handler = $this->getEncoder($method);
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$handler->genLoader ($phpVersions, $platforms, $outDir);
		
	}
	
	 /*
	 * this function performs the same actions as one before... i wonder if there are any reasons to keep it
	 * adds some strange files to encoded package... lets ommit it for now.
	 */
	function embbedLoader( $method, $zipFile, $subZipFile, $outDir ) {
	
		$handler = $this->getEncoder($method);
		
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$loaderFile = $handler->getLoader ();
	
		//insert the license file into the zip file
		jimport("joomla.filesystem.file");
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");

		$curdir = getcwd();
		chdir( $outDir );
		$srcZip = new PclZip($zipFile);	
		if ($srcZip->extract(PCLZIP_OPT_BY_NAME, $subZipFile) == 0) {
			die ($srcZip->errorInfo(true));
		}
	
		$subZip = new PclZip($subZipFile); 
		$subZip->add ($loaderFile);
		$srcZip->delete(PCLZIP_OPT_BY_NAME, $subZipFile);
		$res = $srcZip->add ($subZipFile);
		if ($res == 0) $srcZip->create ($subZipFile);
		@unlink ( $subZipFile );
		@unlink ( $loaderFile );
		//done
		chdir ( $curdir );
	}
	
	
	function getEncPlatformsForMethod ($method) {
		$handler = -1;
		//lets select plugin that handles choosen encoding method
		if (count ($this->encoding_plugins) < 1 ) { 
				return (-1);//do not to forget place errorcode switch in calling file so user
							// may know if encoding was successful or not.
		} else {
			foreach ($this->encoding_plugins as $plugin) {
				if ($method == $plugin->name ) {
					$handler = $this->_plugins[$plugin->classname]->instance; //we found handler for encoding
					break;
				}
		 }			
		}
		if ($handler == -1) return (-2); //no handler found - internal error or hijack attempt
	
		$platform_options = $handler->getPlatforms ();
	//print_r($platform_options); die;
		return ($platform_options);	
	}
	
	function constructplugin ($classname) {
		$result = new $classname();
		return $result;
	}
	
	function FEPluginHandler ($pay_type,  $item, $tax, $redir = 0, $profile) {
		$db = JFactory::getDBO();
		if(!isset($this->default_payment)) {$this->default_payment = NULL;}
		$result = array();
		$total = $tax['taxed'];
		$conf = $this->getInstance("adagencyconfig", "adagencyModel");	
		$configs = $conf->getConfigs();
		$orderpack=JRequest::getInt('tid');
		$getPaymentType=JRequest::getVar('payment_type');
		//echo "<pre>";var_dump($this->payment_plugins);//die();
		if (count($this->payment_plugins) < 1 ) return -1;
		$plugin_exists = 0;
		
		if (!isset($this->payment_plugins[$pay_type])) {    //if selected payment method is not available
			if (is_object($this->default_payment)) {    //try selected, or default payment gateway
				
				foreach ($this->payment_plugins as $plug) {//find selected plugin
					if ($plug->filename==$getPaymentType.'.php') {
							$plugin = $plug;
							break;	
						}
				}	
				
				if (!isset($plugin)) {$plugin = $this->default_payment;}	//try default payment gateway
			} elseif (!is_object($this->default_payment)) {//no default available
				if (!isset($plugin)) {
					foreach ($this->payment_plugins as $plug) {//select first gateway available
						$plugin = $plug;
						break;	
					}
				}
			} else {
				die (JText::_("DSPAYMENTCANTBEPROC"));	
			}
		} else { //all os ok - use normal gateway
			$plugin = $this->payment_plugins[$pay_type];
		}
		//echo "<pre>";
		//echo "<pre>";var_dump($plugin);//die();
		$this->_id = $plugin->id;
		$plugin = $this->getPlugin();
        
		if ($plugin->reqhttps != 0 && (getenv('HTTPS') != 'on' && getenv('HTTPS') != '1')) {//plugin requires https connection to perform checkout
            return "https";			
		}             
        
		$my =& JFactory::getUser();
		$order_date = date('Y-m-d');	
		$db->setQuery("SELECT aid FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id));
		$aid = $db->loadResult();
	
		$db->setQuery("SELECT * FROM #__ad_agency_order_type WHERE tid = ".intval($orderpack));
		$typerow = $db->loadObjectList();
		$type_row = $typerow[0];
		
		$notes = $db->getEscaped($type_row->description);
	    $quantity = $type_row->quantity;
	    $type = $type_row->type;
	    $cost = $type_row->cost;
		$tid = $type_row->tid;
		$payment_type=JRequest::getVar('payment_type');

		if(isset($_SESSION['LCC']) && isset($_SESSION['LCC2'])) {
			$confirm_data = $_SESSION['LCC'].";".$_SESSION['LCC2'];
			$_SESSION['LCC'] = NULL;$_SESSION['LCC2']=NULL;
			unset($_SESSION['LCC']);unset($_SESSION['LCC2']);
		} else {
			$confirm_data = NULL;
		}
		
		$sql = "SELECT currencydef FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$currencydef = $db->loadResult();
		
		$renew = JRequest::getVar("remove_action", "");
		$orderid = 0;
		
		if($renew == ""){
			$promocode = @$_SESSION["promocode"];
			$new_cost = @$_SESSION["new_cost"];
			
			$order_cost = $cost;
			if(isset($new_cost) && trim($new_cost) != ""){
				$order_cost = $new_cost;
			}
			
			$orderid_from_request = JRequest::getVar("orderid", "0");
			if(intval($orderid_from_request) == 0){
				$insersql = "INSERT INTO #__ad_agency_order (`oid`,`tid`,`aid`,`type`,`quantity`,`cost`,`order_date`,`payment_type`,`card_number`,`expiration`,`card_name`,`notes`,`status`,`pack_id`,`currency`, `promocodeid`) VALUES ('','".intval($tid)."','".intval($aid)."','".trim($type)."','".intval($quantity)."','".trim($order_cost)."','".trim($order_date)."','".addslashes(trim($payment_type))."','".trim($confirm_data)."','','','".addslashes(trim($notes))."','not_paid','0','".trim($currencydef)."', '".intval($promocode)."');";
				$db->setQuery($insersql);
				$db->query();
				$orderid = @mysql_insert_id();
			}
			else{
				$orderid = intval($orderid_from_request);
			}
		}
		elseif($renew == "renew"){
			$orderid = intval(JRequest::getVar("orderid", "0"));
			// not redirect to pypal if package is free
			if($cost == 0 || $cost == "0.0" || $cost == "0.00" || $cost == "0.000" || $cost == "0.0000"){
				$package_id = JRequest::getVar("otid", "0");
				$sql = "select * from #__ad_agency_order_type where `tid`=".intval($package_id);
				$db->setQuery($sql);
				$db->query();
				$package_content = $db->loadAssocList();
				if(isset($package_content) && count($package_content) > 0){
					$campaign_id = JRequest::getVar("campaign_id", "0");
					$cost = $package_content["0"]["cost"];
					$quantity = $package_content["0"]["quantity"];
					
					//$date_today = date("Y-m-d H:i:s");
					$jnow = &JFactory::getDate();
					$date_today = $jnow->toMySQL();
					
					$date_today_int = strtotime($date_today);
					$validity = '0000-00-00 00:00:00';
					
					if(trim($package_content["0"]["validity"]) != ""){
						$pack_validity_str = trim($package_content["0"]["validity"]);
						$pack_validity_array = explode("|", $pack_validity_str);
						$validity_date = strtotime("+".$pack_validity_array["0"]." ".$pack_validity_array["1"], $date_today_int);
						$validity = date("Y-m-d H:i:s", $validity_date);
					}
					
					$sql = "update #__ad_agency_campaign set `start_date` = '".trim($date_today)."', `quantity`='".intval($quantity)."', `validity`='".trim($validity)."', `cost`='".trim($cost)."' where id=".intval($campaign_id);
					$db->setQuery($sql);
					$db->query();
					
					$Itemid = JRequest::getInt('Itemid','0');
					$link = "index.php?option=com_adagency&controller=adagencyCampaigns".$Itemid;
					$app = JFactory::getAPPlication("site");
					$app->redirect(JRoute::_($link), JText::_("ADAG_RENEWED_SUCCESSFUL"));
				}
			}
		}
		
		if(!isset($orderid)||($orderid==0)){
			$sql = "SELECT oid FROM #__ad_agency_order ORDER BY oid DESC LIMIT 1";
			$db->setQuery($sql);
			$orderid = $db->loadResult();
		}
        
        $item = new stdClass();
		$item->oid = $orderid;
		$item->product_name = $item->name = $type_row->description; //$notes;
		$item->amount = $cost;
		$item->quantity = 1;
		$item->tid = $tid;        
        $items[] = $item;
		
		$tax = array(); 
        $tax['currency'] = $configs->currencydef; 
        $tax['value'] = $cost;
        $tax['shipping'] = 0;
        $tax['taxed'] = $cost;
        
        $profile = null;
        $sql = "SELECT * FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
        $db->setQuery($sql);
		$profile = $db->loadObject();  

		if (is_null($profile)) $profile = new stdClass;
        $profile->_sid = $orderid;
        $profile->_user_id = $my->id;
        $profile->_user = $my;
        
		$redirect = 1;
		$result = "";
		$autorenew = JRequest::getVar("aurorenew", "0");
		if($autorenew == 1){
			$result = $plugin->instance->getFEDataRenew($items, $tax, $redirect, $profile, $plugin, $configs);
		}
		else{
			if(isset($_SESSION["new_cost"]) && intval($_SESSION["new_cost"]) == 0){
				$this->goToSuccessURL($orderid);
			}
			else{
				$result = $plugin->instance->getFEData($items, $tax, $redirect, $profile, $plugin, $configs);
			}
		}	
	  	return $result;			
	}
	
	function performCheckout($customer) {
		$db = JFactory::getDBO();		
		$tid = JRequest::getInt('tid'); 
		$sql = "SELECT * FROM #__ad_agency_order_type WHERE `tid`='".intval($tid)."'";
		$db->setQuery($sql);
		$resz = $db->loadObjectList();	
		$item = $resz[0];
		$payment_type = '-NONE-';
		$tax = 0;
	
        $content = $this->FEPluginHandler($payment_type, $item, $tax, true , $customer);             
        if($content == "https"){
			return $content;
		}	
		if($content){
        	echo ($content); 
		    return;
		}
		else{
			echo ('<script> alert("'.(JText::_("DSPROCPAYMENTERR")).'"); history.go(-1);</script>');
	        return;
       	}
	}
    
	function getLiveSite() {
	    // Check if a bypass url was set
		$config =& JFactory::getConfig();
		$live_site = $config->getValue('config.live_site');

		// Determine if the request was over SSL (HTTPS)
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
			$https = 's://';
		} else {
			$https = '://';
		}
		$subdom = $_SERVER['PHP_SELF']	;
		$subdom = explode ("/", $subdom);
		$res = array();
		foreach ($subdom as $i => $v) {
			if (strtolower(trim($v)) != "index.php") $res[] = trim($v);
			else break;
		}
		$subdom = implode ("/", $res);
		/*
		* Since we are assigning the URI from the server variables, we first need
		* to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
		* are present, we will assume we are running on apache.
		*/
		if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI'])) {

		    /*
		     * To build the entire URI we need to prepend the protocol, and the http host
		     * to the URI string.
		     */
		    if (!empty($live_site)) {
		        $theURI = $live_site;// . $_SERVER['REQUEST_URI'];
		    } else {
		        $theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;// . $_SERVER['REQUEST_URI'];
		    }

		/*
		* Since we do not have REQUEST_URI to work with, we will assume we are
		* running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
		* QUERY_STRING environment variables.
		*/
		} else {
		    // IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			if (!empty($live_site)) {
			        $theURI = $live_site . $_SERVER['SCRIPT_NAME'];
			} else {
			        $theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;//. $_SERVER['SCRIPT_NAME'];
			}

		    // If the query string exists append it to the URI string
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			}
		}

		return $theURI;
	}        
    
	function io(){
		$plug = JRequest::getVar("plugin", "", "request");
		$task = JRequest::getVar("task", "", "request");
		$flag = 0;
		if ( count ($this->payment_plugins  ) >0 ){
			foreach ($this->payment_plugins as $plugin) {
				if ($plug == $plugin->classname && $task == 'notifyPayment') {
				$this->_id = $plugin->id;
				$plugin = $this->getPlugin($this->_id);
				$content = $this->payment_notify($plugin);
				echo ($content);
				$flag = 1;
				break;
				}
			}
		
			foreach ($this->payment_plugins as $plugin) {
				if ($plug == $plugin->classname && $task == "returnPayment") {
					$this->_id = $plugin->id;
					$plugin = $this->getPlugin($this->_id);
		
					$content = $this->payment_return($plugin);
					echo ($content);
					$flag = 1;
					break;
				}
			}
		
			foreach ($this->payment_plugins as $plugin) {
				if ($plug == $plugin->classname && $task == "failPayment") {
					$this->_id = $plugin->id;
					$plugin = $this->getPlugin($this->_id);
					$content = $this->payment_fail($plugin);
					echo $content;
					$flag = 1;
					break;
				}
			}
		}
	
		return $flag;	
	}

	/*
	 * After user returns from payment system we should receive responce on if transaction 
	 * was successfull, tell result to user and store info into db. Has to have
	 * real ip.
	 */
	
	function payment_notify ($plugin) {
		//	$result = array();
		//	global $database;
		$conf = $this->getInstance("adagencyconfig", "adagencyModel");		
		$configs = $conf->getConfigs();
		$result = $plugin->instance->notify($plugin, null, $configs, $this);
		return $result;	
	}
	
	
	function payment_return ($plugin) {
		$_SESSION['in_trans'] = 1;
		$conf = $this->getInstance("adagencyconfig", "adagencyModel");		
		$configs = $conf->getConfigs();
		$result = $plugin->instance->return1($plugin, null, $configs, $this);
		return $result;	
	}
	
	function payment_fail ($plugin) {
		$_SESSION['in_trans'] = 1;
		$conf = $this->getInstance("adagencyconfig", "adagencyModel");	
		$configs = $conf->getConfigs();
		$result = $plugin->instance->return2($plugin, null, $configs, $this);
		return $result;	
	}    

	function goToSuccessURL ($sid = 0, $msg = '', $orderid = 0) {
		global $Itemid, $mainframe;
        $database = & JFactory::getDBO();
        $plugin = $this->getPlugin($this->_id);
          
        /*if ($plugin->name == 'offline') $status = 'pending'; else $status = 'paid';*/
        $sql = "UPDATE #__ad_agency_order SET `status`='paid' WHERE `oid`='".intval($sid)."'"; 
        $database->setQuery($sql);
        $database->query();
		
		$sql = "SELECT * FROM #__ad_agency_order WHERE `oid`='" . $database->getEscaped($sid) . "'";
		$database->setQuery($sql);
		$currento = $database->loadObject();
		
		if(isset($currento->card_number) && ($currento->card_number!="") && ($currento->card_number != NULL) ) {
			$verify = explode(";", $currento->card_number);
			if(isset($verify[1]) && (intval($verify[1])>0) && ($verify[1] == $currento->tid)){
				if(isset($verify[0])&&(intval($verify[0])>0)){
					//change campaign date expiration
					$sql = "select `tid` from #__ad_agency_order WHERE `oid` = ".intval($sid);
					$database->setQuery($sql);
					$database->query();
					$tid = $database->loadResult();
					
					$sql = "select `quantity`, `validity` from #__ad_agency_order_type where `tid`=".intval($tid);
					$database->setQuery($sql);
					$database->query();
					$tid_details = $database->loadAssocList();
					
					if(isset($tid_details) && count($tid_details) > 0){
						$validity = $tid_details["0"]["validity"];
						$quantity = intval($tid_details["0"]["quantity"]);
						
						//$start_date = date("Y-m-d H:i:s");
						$jnow = &JFactory::getDate();
						$start_date = $jnow->toMySQL();
						
						$end_date = "0000-00-00 00:00:00";
						
						if(trim($validity) != ""){
							$validity_array = explode("|", $validity);
							$today = strtotime($start_date);
							$end_date_int = strtotime("+".$validity_array["0"]." ".$validity_array["1"], $today);
							$end_date = date("Y-m-d H:i:s", $end_date_int);
						}
						$sql = "update #__ad_agency_campaign set `start_date`='".trim($start_date)."', `quantity`=".intval($quantity).", `validity`='".trim($end_date)."' where id=".intval($verify["0"]);
						$database->setQuery($sql);
						$database->query();
					}
					//change campaign date expiration
					// add history
					$sql = "select `activities` from #__ad_agency_campaign WHERE `id`=".intval($verify["0"]);
					$database->setQuery($sql);
					$database->query();
					$activities = $database->loadResult();
					$sql = "";
					
					$user = JFactory::getUser();
					$user_id = $user->id;
					
					if(strpos($activities, "Purchased") === FALSE){
						$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, 'Purchased(new) - ".date("Y-m-d H:i:s")."', ' - ".intval($user_id)."', ';') WHERE `id` = '".intval($verify["0"])."' ";
					}
					else{
						$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, 'Purchased(renewal) - ".date("Y-m-d H:i:s")."', ' - ".intval($user_id)."', ';') WHERE `id` = '".intval($verify["0"])."' ";
					}
					$database->setQuery($sql);
					$database->query();
					// add history
					
					$sql = "SELECT * FROM #__ad_agency_campaign WHERE id = " . $database->getEscaped($verify[0]);
					$database->setQuery($sql);
					$currentCmp = $database->loadObject();
					if($currentCmp->approved != 'Y') {
						/*$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='approved'";
						$database->setQuery($query);
						$result = $database->loadRow();
						$approval['cmp']=$result[4];
						if($approval['cmp']=='Y') {
							$sql = "UPDATE `#__ad_agency_campaign` SET `approved` = 'Y' WHERE `id` = '".$currentCmp->id."' ";
							$database->setQuery($sql);
							$database->query();
							$_SESSION['cmp_pending_to_approved']='Y';
						} else {
							$_SESSION['cmp_pending_to_approved']='N';
						}
                        */
                        $_SESSION['cmp_pending_to_approved']='N';
					} else {
                        $_SESSION['cmp_pending_to_approved']='Y';
                    }
				}
			}
		}
		
		$conf = $this->getInstance("adagencyconfig", "adagencyModel");		
		$configs = $conf->getConfigs();
		$shown = explode(";",$configs->show);
		
		$page_itemid = JRequest::getInt('Itemid','0');
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		}
		else{
			$Itemid = NULL;
		}
		
		if(in_array("aftercamp2",$shown)) {
			if(!isset($Itemid)){
				$sql = "select `id` from #__menu where link='index.php?option=com_adagency&view=adagencycampaigns'";
				$database->setQuery($sql);
				$database->query();
				$itemid = intval($database->loadResult());
				
				if($itemid != "0"){
					$Itemid = "&Itemid=".$itemid;
				}
			}
			$success_url = JURI::base()."index.php?option=com_adagency&controller=adagencyCampaigns";//.$Itemid;
		}
		else{
			$success_url = JURI::base()."index.php?option=com_adagency&controller=adagencyOrders";//.$Itemid;
		}
		
		$msg = JText::_('ADAG_SUCC_PAY');
		
		if(isset($verify[0])){
			$sql = "SELECT `approved` FROM `#__ad_agency_campaign` WHERE `id` = '" . $database->getEscaped($verify[0]) ."'";
			$database->setQuery($sql);
			$aa_cmp = $database->loadResult();
			if($aa_cmp == 'P') {
				$msg = JText::_("ADAG_CMPMSG1");
			} elseif($aa_cmp == 'Y') {
				$msg = JText::_("ADAG_CMPMSG2");
			}
		}
		
        $success_url = str_replace ("https://", "http://", $success_url);
		$app = JFactory::getApplication('site');
		//$app->redirect($success_url, $msg);
		$app->redirect($success_url);
	}
	
	function goToFailedURL ($sid = 0, $msg = '') {
		global $Itemid, $mainframe;

		$page_itemid = JRequest::getInt('Itemid','0');
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		} else {
			$Itemid = NULL;
		}	
		
        $failed_url = JURI::base()."index.php?option=com_adagency&controller=adagencyCampaigns";//.$Itemid;
		
		$tid = "0";
		if(intval($sid) != "0"){
			$db = JFactory::getDBO();
			$sql = "select `tid` from #__ad_agency_order where `oid`=".intval($sid);
			$db->setQuery($sql);
			$db->query();
			$tid = intval($db->loadResult());
		}
		
		$plugin = JRequest::getVar("plugin", "");
		if($plugin == "authorizenet"){
			$creditCardNumber = JRequest::getVar("creditCardNumber", "");
			$expDateMonth = JRequest::getVar("expDateMonth", "");
			$expDateYear = JRequest::getVar("expDateYear", "");
			$cvv2Number = JRequest::getVar("cvv2Number", "");
			$firstName = JRequest::getVar("firstName", "");
			$lastName = JRequest::getVar("lastName", "");
			$email = JRequest::getVar("email", "");
			$_SESSION["creditCardNumber"] = $creditCardNumber;
			$_SESSION["expDateMonth"] = $expDateMonth;
			$_SESSION["expDateYear"] = $expDateYear;
			$_SESSION["cvv2Number"] = $cvv2Number;
			$_SESSION["firstName"] = $firstName;
			$_SESSION["lastName"] = $lastName;
			$_SESSION["email"] = $email;
			$failed_url = JURI::base()."index.php?option=com_adagency&controller=adagencyOrders&task=checkout&tid=".$tid."&payment_type=authorizenet_payment".$Itemid;
		}
		
		$failed_url = str_replace ("https://", "http://", $failed_url);
        $mainframe->redirect($failed_url, JText::_('ADAG_FAIL_PAY'));        
	}    
	
	function BackPluginHandler ($param = 0) {
		global $digistor_plugins, $plug_list_tmp;		
		$result = array();
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_plugins";
		$db->setQuery($sql);
		$this->plugins = $db->loadObjectList();
		if (count($this->plugins) < 1 ) return;
		$i = 0;
		foreach ($this->plugins as $plugin) {		
			if ($param == 0) {
				$database = & JFactory::getDBO();
		        $query = "select value from #__ad_agency_plugins where name='".addslashes(trim($plugin->name))."'";
		        $database->setQuery($query);
		        $val = $database->loadResult();
		        $query = "select sandbox from #__ad_agency_plugins where name='".addslashes(trim($plugin->name))."'";
		        $database->setQuery($query);
		        $sbx = $database->loadResult();
		        $pluginnname = strtoupper($plugin->name);
		        $textplugin = JText::_($pluginnname.'TEXTSETT');
		        $plugin_form = array(
		            "header" => $textplugin,
		            "header1" => array(''),
		            "name" => array($plugin->name."_account[]"),
		            "value" => explode(" ",$val),
		            "isdef"=> $plugin->name."_def",
		            "sandbox" => $plugin->name."_sandbox",
		            "sbx" => $sbx
		        );
		        $result[$i] = $plugin_form;
				$result[$i]["type"] = $plugin->type;
				$result[$i]["pluginname"] = $plugin->name;
				if ($plugin->type != 'encoding') {
					$result[$i]["reqhttps"] = $plugin->reqhttps;
					$result[$i]["reqhttps_name"] = $plugin->name."_reqhttps";
				}
				if ($plugin->published == 1) {
					$result[$i]["published"] = 1;
				} else {
	                $result[$i]["published"] = 0;
				}
			}
				++$i;
		}
		return $result;	
	}
};
?>