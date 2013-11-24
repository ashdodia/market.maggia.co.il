 <?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author  iJoomla.com <webmaster@ijoomla.com>
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
defined ("_JEXEC") or die ("Go away.");

class TableadagencyConfig extends JTable {
	var $id = null;
	var $lastsend = null;
	var $adminemail = null;
	var $fromemail = null;
	var $fromname = null;
	var $imgfolder = null;
	var $maxchars = null;                       
	var $allowstand = null;
	var $allowadcode = null;
	var $allowpopup = null;
	var $allowswf = null;
	var $allowtxtlink = null;
	var $allowtrans = null;
	var $allowfloat = null;
	var $txtafterreg = null;
	var $bodyafterreg = null;
	var $sbafterreg = null;
	var $bodyactivation = null;
	var $sbactivation = null;
	var $bodyrep = null;
	var $sbrep = null;
	var $bodycmpappv = null;
	var $sbcmpappv = null;
	var $bodycmpdis = null;
	var $sbcmpdis = null;
	var $bodyadappv = null;
	var $sbadappv = null;
	var $bodyaddisap = null;
	var $sbaddisap = null;
	var $bodyadvdis = null;
	var $sbadvdis = null;
	var $bodynewad = null;
	var $sbnewad = null;
	var $bodynewcmp = null;
	var $sbnewcmp = null;
	var $bodycmpex = null;
	var $sbcmpex = null;
	var $bodynewuser = null;
	var $sbnewuser = null;
	var $currencydef = null;
	var $showpreview = null;
	var $params = null;
	var $sbafterregaa = null;
	var $bodyafterregaa = null;	
	var $payment = null;
	var $geoparams = null;
	var $limit_ip = null;
	var $sbcmpexpadm = null;
	var $bodycmpexpadm = null;

	function TableadagencyConfig (&$db) {
		parent::__construct('#__ad_agency_settings', 'id', $db);
		$sql = "select count(*) from #__ad_agency_settings where id=1";
		$db->setQuery($sql);
		$c = $db->loadResult();
		if ($c < 1) {
			$sql = "insert into #__ad_agency_settings(`id`) values (1)";
			$db->setQuery($sql);
			$db->query();
		}
	}
};
?>