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
require_once("components/com_adagency/helpers/helper.php");

class adagencyModeladagencyReports extends JModel {
	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_installpath = JPATH_COMPONENT.DS."plugins".DS;
		$this->_plugin = null;
	}

	function getlistLanguages () {
		if (empty ($this->_plugins)) {
			$sql = "select * from #__adagency_languages";
			$this->_languages = $this->_getList($sql);			
		}
		return $this->_languages;
	}	

	function getLanguage () {
		$db = JFactory::getDBO();
		$sql = "select * from #__adagency_languages where id=".intval($this->_id);
		$db->setQuery($sql);
		$lang = $db->loadObjectList();
		$lang = $lang[0];
		$file = $lang->fefilename;
		$code = $lang->name;

		$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$file;
		$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS.$file;
		$task = JRequest::getVar("task", "", "request");
		if ($task == "editFE") $lang->path = $respathfe;
		else $lang->path = $respathbe;
		$lang->data = implode ("", file ($lang->path));
		$lang->type = $task;
		return $lang;
	}
	
	function store () {
		$db = JFactory::getDBO();
		$id = JRequest::getVar("id", "", "request");
		if (!$id) return false;
		$sql = "select * from #__adagency_languages where id=".intval($id);
		$db->setQuery($sql);
		$lang = $db->loadObjectList();
		$lang = $lang[0];
		$file = $lang->fefilename;
		$code = $lang->name;
		$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$file;
		$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS.$file;
		$type = JRequest::getVar("type", "", "request");
		if ($type == "editFE") $path = $respathfe;
		else $path = $respathbe;
		$text = JRequest::getVar("langfiledata", "", "post");
		$f = fopen ($path, "w");
		fwrite ($f, $text);
		fclose ($f);
		return true;
	}
	
	function rotator() {
		$database = &JFactory::getDBO();

		$banner_id = JRequest::getInt('banner_id');
		$advertiser_id = JRequest::getInt('advertiser_id');
		$campaign_id = JRequest::getInt('campaign_id');
		$type = JRequest::getVar('type');

		$sql = "SELECT limit_ip FROM #__ad_agency_settings LIMIT 1";
		$database->setQuery($sql);
		$limit_ip = $database->loadResult();

		//reduce table size
		$time_interval = date("Y-m-d");

		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// check if isset REMOTE_ADDR and != empty
		elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		// you're probably on localhost
		} else {
			$ip = "127.0.0.1";
		}

		$the_ipp = ip2long($ip);

		$sql = "SELECT how_many FROM #__ad_agency_stat WHERE substring(entry_date,1,10)='".$time_interval."' and ip_address='".trim($the_ipp)."' AND campaign_id='".intval($campaign_id)."' AND banner_id='".intval($banner_id)."' AND `type`='impressions' limit 1";

		$database->setQuery($sql);
		if (!$database->query()) {
			echo $database->stderr();
			return;
		}		

		if ($database->getNumRows()) {
			$how_many = $database->loadResult();
			$how_many++; 
			if($how_many <= $limit_ip) {
				$class_helper = new adagencyAdminHelper;
				$ip_is_private = $class_helper->ip_is_private($the_ipp);
				if(!$ip_is_private){
					$sql = "UPDATE #__ad_agency_stat SET `how_many` = '".intval($how_many)."' WHERE substring(entry_date,1,10)='".$time_interval."' AND ip_address='".trim($the_ipp)."' AND campaign_id='".intval($campaign_id)."' AND banner_id='".intval($banner_id)."' AND `type`='impressions' limit 1";
					$database->setQuery($sql);
					if (!$database->query()) {
						echo $database->stderr();
						return;
					}
				}
			}
		} else { 
			$how_many = 1;
			// insert is made only if from the same IP during current day are no records
			// v.1.5.3 update - user_agent in no longer kept as a record
			$class_helper = new adagencyAdminHelper;
			$ip_is_private = $class_helper->ip_is_private($the_ipp);
			if(!$ip_is_private){
				$sql = "INSERT INTO #__ad_agency_stat SET entry_date=NOW(), ip_address='".trim($the_ipp)."', advertiser_id='".intval($advertiser_id)."', campaign_id='".intval($campaign_id)."', banner_id='".intval($banner_id)."', `type`='impressions', `how_many`='1'";
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
			}
		}	

		if ('cpm'==$type) {
			if($how_many <= $limit_ip) {
				$sql = "UPDATE #__ad_agency_campaign SET quantity = quantity-1 WHERE quantity > 0 AND id=".intval($campaign_id);
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
			}

			$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".intval($campaign_id);
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
			$quantity = $database->loadResult();
			
			if (($quantity == 0)&&($type!='fr')) {
				$nowdatetime = date("Y-m-d H:i:s");
				$sql = "UPDATE #__ad_agency_campaign SET validity = '".trim($nowdatetime)."' WHERE id=".intval($campaign_id);
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
			}			
		}
	}

	function copyLangFile ($path, $type, $code, $file) {
		$respath = "";
		if ($type == "fe") {
			$respath = JPATH_ROOT.DS."language".DS.$code.DS;
		} elseif ($type == "be") {
			$respath = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
		}
		if (!file_exists($respath) ) return -2;
		if (file_exists($respath.$file)) return -1;
		JFile::copy($path, $respath.$file, '');	
		return 1;
	}

	function installLanguage($path, $language_file = '') {
		$db = JFactory::getDBO();
		$language_file = trim ($language_file);
		if (strlen($language_file) < 1) return JText::_('MODLANGNOUPLOAD');
		$ext = substr ($language_file, strrpos($language_file, ".") + 1);
		if ($ext != 'zip') return JText::_('MODLANGNOZIP');
		jimport('joomla.filesystem.archive');	
		if (!JArchive::extract($path.$language_file, $path)) {
			return JText::_('MODLANGERREXTRACT');
		}
		$dir = opendir ($path);
		if (!file_exists($path."install")) return JText::_("MODLANGMISSINSTALL");
		$install = parse_ini_file($path."install");
		if (count ($install) < 1) return JText::_("MODLANGCORRUPTEDINSTALL");
		$lang_code = explode (" ", $install['langcode']);
	    foreach ($lang_code as $code ) {
			$be = 0;
			$fe = 0;

			$fe_path = $path."fe".DS.$code.DS.$code.".com_adagency.ini";
			$lang_file = $code.".com_adagency.ini";
			$be_path = $path."be".DS.$code.DS.$code.".com_adagency.ini";
			if (!file_exists($fe_path)) $fe = 0; else $fe = 1;
			if (!file_exists($be_path)) $be = 0; else $be = 1;
			if ($be && $fe ) {
				$query = "select count(*) from #__adagency_languages where fefilename='".trim($lang_file)."' or befilename='".trim($lang_file)."'";
  				$db->setQuery($query);
		   		$isthere = $db->loadResult();
				if ($isthere) {
					return JText::_('MODLANGALLREDYEXIST');// 
				} else {
				        $fe = 0;
					$be = 0;
					$fe = $this->copyLangFile($fe_path, "fe", $code, $lang_file);
					if ($fe) {
						$be = $this->copyLangFile($be_path, "be", $code, $lang_file);		
					}
					if (!$fe || !$be) {
						return JText::_("MODLANGCOPYERR");
					} else if ($fe == -1 || $be == -1) {
						return JText::_("MODLANGCANTCOPY");
					} else if ($fe < 0 || $be < 0) {
						return JText::_("MODLANGFOLDERNOTEXITST");
					} else {
						$sql = "insert into #__adagency_languages(`name`, `fefilename`, `befilename`) values ('".trim($code)."', '".trim($lang_file)."', '".trim($lang_file)."')";
		  				$db->setQuery($sql);
				   		$db->query();
					}
				} 
			} else {
				return JText::_("MODLANGMISSLANGFILE");
			}
		}
		$install_path = $this->_installpath;
      		JFile::copy ($path.$install['filename'], $install_path.$install['filename']);      
       	return JText::_("MODLANGSUCCESSFUL");
	}

	function upload() {
		$table_entry =& $this->getTable ("adagencyPlugin");
		jimport('joomla.filesystem.file');
		$file = JRequest::getVar('langfile', array(), 'files');	
		$install_path = JPATH_ROOT.DS."tmp".DS."adagencylanguage".DS;
		Jfolder::create ($install_path);
		if (JFile::copy($file['tmp_name'], $install_path.$file['name'], '')) {
			$res = $this->installLanguage($install_path, $file['name']);
			JFolder::delete ($install_path);
		} else {
			$res = JText::_('MODLANGCOPYERR');
		}
		
		return $res;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();

		foreach ($cids as $cid) {
			$sql = "select name,fefilename from #__adagency_languages where id=".intval($cid);
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			$file = $tmp[0]->fefilename;
			$code = $tmp[0]->name;
			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS;
			$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
			$menufile = str_replace (".ini", ".menu.ini", $file);
			if ((JFile::delete($respathfe.$file)) && (JFile::delete($respathfe.$menufile))
				&& (JFile::delete($respathbe.$file)) && (JFile::delete($respathbe.$menufile))) {
	
				$sql = "delete from #__adagency_languages where id=".intval($cid);
				$db->setQuery($sql);
				$db->query();
			}
		}
		return true;
	}
	
	function getreportsAdvertisers () {
		if (empty ($this->_package)) {
			$db =& JFactory::getDBO();
			$sql = "SELECT a.aid, a.company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY a.company ASC";
			$db->setQuery($sql);
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$this->_package = $db->loadObjectList();
			
		}
		return $this->_package;

	}
};
?>