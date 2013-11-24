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

jimport ("joomla.application.component.model");


class adagencyModeladagencyConfig extends JModel {
	var $_configs = null;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$this->_id = 1;
	}

	function getConfigs() {
		if (empty ($this->_configs)) {
			$this->_configs =& $this->getTable("adagencyConfig");
			$this->_configs->load($this->_id);
		}
		return $this->_configs;
	}
	
	function getConf(){
		$db = & JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$result = $db->loadObject();
		
		$advertiser = $this->getCurrentAdvertiser();
		if(isset($advertiser->apr_ads)&&($advertiser->apr_ads!='G')){
			$approved = $advertiser->apr_ads;
		} else {
			$sql = "SHOW COLUMNS FROM `#__ad_agency_banners` WHERE field = 'approved'";
			$db->setQuery($sql);
			$res = $db->loadObject();
			$approved = $res->Default;
		}
		$result->ad_status = $approved;
		
		return $result;
	}
    
    function getItemid($controller){
        $db = &JFactory::getDBO();
        $controller = $db->getEscaped($controller);
        $sql =  "SELECT id FROM `#__menu` 
                    WHERE `menutype` = 'adagency' 
                    AND `link` LIKE '%index.php?option=com_adagency&view={$controller}%' ";
        //echo "** " . $sql . " **";die();
        $db->setQuery($sql);
        $res = (int)$db->loadResult();
        return ($res == 0) ? JRequest::getInt('Itemid', '0', 'get') :  $res;       
    }
    	
	function getCurrentAdvertiser(){
		$db = & JFactory::getDBO();
		$my = & JFactory::getUser();
		$sql = "SELECT * FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	
	function isWizzard(){
		$db = & JFactory::getDBO();	
		$sql = "SELECT `show` FROM #__ad_agency_settings ORDER BY id LIMIT 1";
		$db->setQuery($sql);
		$shown = $db->loadResult();
		$shown = explode(";",$shown);
		$isWizzard = false;
		foreach($shown as $element){
			if($element == "wizzard") { $isWizzard = true; }
		}
		return $isWizzard;
	}

	function store () {
		$database = & JFactory::getDBO();
		$item =& $this->getTable('adagencyConfig');

		$data = JRequest::get('post');
		if (isset($data['allowstand'])) $data['allowstand']=1; else $data['allowstand']=0;
		if (isset($data['allowadcode'])) $data['allowadcode']=1; else $data['allowadcode']=0;
		if (isset($data['allowpopup'])) $data['allowpopup']=1; else $data['allowpopup']=0;
		if (isset($data['allowswf'])) $data['allowswf']=1; else $data['allowswf']=0;
		if (isset($data['allowtxtlink'])) $data['allowtxtlink']=1; else $data['allowtxtlink']=0;
		if (isset($data['allowtrans'])) $data['allowtrans']=1; else $data['allowtrans']=0;
		if (isset($data['allowfloat'])) $data['allowfloat']=1; else $data['allowfloat']=0;
				
		if (!$item->bind($data)){
			return JError::raiseError( 500, $database->getErrorMsg() );
			return false;

		}
		if (!$item->check()) {
			return JError::raiseError( 500, $database->getErrorMsg() );
			return false;

		}
        
		if (!$item->store()) {
			return JError::raiseError( 500, $database->getErrorMsg() );
			return false;

		}
		
		$respathfe = JPATH_ROOT.DS."language".DS."en-GB".DS."en-GB.com_adagency.ini";
		$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS."en-GB".DS."en-GB.com_adagency.ini";
		
		/*Saving LANGUAGE*/
		$textfe = JRequest::getVar("filecontentFE");
		$f = fopen ($respathfe, "w");
		fwrite ($f, $textfe);
		fclose ($f);
		
		$textbe = JRequest::getVar("filecontentBE");
		$g = fopen ($respathbe, "w");
		fwrite ($g, $textbe);
		fclose ($g);
		
		/*Saving PLUGIN*/
		$plugin_handler = adagencyAdminModeladagencyConfig::makePluginList();
				
	    if (count($plugin_handler) > 0 )
	    foreach ($plugin_handler as $name => $plugin) {	    
	        $name = $plugin->name;
	        $value = JRequest::getVar($name."_account", array(0), 'array');
	        $value = implode (" ", $value);
	        $default = JRequest::getVar("default", null, 'post');
	        $sandbox    = JRequest::getVar($name."_sandbox", 0, 'post');
	        $reqhttps = JRequest::getVar($name."_reqhttps", 0, 'post');
	        $default = substr( $default, 0, strlen($default) - 4 ) ;
	        $value = $database->getEscaped($value);
	        if ($plugin->type == 'encoding' && file_exists($value)) {
	            $query = "update #__ad_agency_plugins set value='".$value."', sandbox='".intval($sandbox)."' where name='".addslashes(trim($name))."';";
	        }  else if ($plugin->type !='encoding'){
	            $query = "update #__ad_agency_plugins set value='".$value."', sandbox='".intval($sandbox)."', reqhttps='".intval($reqhttps)."' where name='".addslashes(trim($name))."';";  
	           } else {
	            $query = '';
	        }
	
	        $database->setQuery($query);
	        $database->query();
	
	        $query = "update #__ad_agency_plugins set def='' where def='default';"; 
	        $database->setQuery($query);
	        $database->query();
	        
	        $query = "update #__ad_agency_plugins set def='default' where name='".addslashes(trim($default))."';";    
	       
	        $database->setQuery($query);
	        $database->query();
       
	    }
	    
		return true;

	}	
	
	function makePluginList () {
		$database = JFactory::getDBO();	
		$plugin_path = JPATH_ROOT.'/administrator/components/com_adagency/plugins/';
		if ( is_dir ($plugin_path) ) {
			$plugin_dir = opendir($plugin_path);
			while ( ($plugin_file = readdir ($plugin_dir) ) ) {
				if (substr ($plugin_file, -3) == 'php' ) {
					//Select plugin data for current filename from database.
					$query = "select * from #__ad_agency_plugins where filename='".addslashes(trim($plugin_file))."'";
					$database->setQuery($query);
					$res = $database->loadObjectList();
					
					//If no entry for this file - remove it.
					if (!isset($res[0]) ) {
						@unlink (JPATH_ROOT."/administrator/components/com_adagency/plugins".$plugin_file);				
					} else {
						$plugins[$res[0]->name] = $res[0];
					}
				} //if substr
			} //while readdir
			closedir($plugin_dir);
		}// if plugin_path
		
		return $plugins;
	}
};
?>