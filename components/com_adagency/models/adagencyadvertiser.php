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

class adagencyModeladagencyAdvertiser extends JModel {
	var $_customers;
	var $_customer;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');

        $my = &JFactory::getUser();
        $cids[0] = (int)$my->id;

		$this->setId($cids[0]);
		global $mainframe, $option;

		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);

	}

	function getConf(){
		$db = &JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id DESC LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadObject();
		return $res;
	}

	function getShowZInfo(){
		$db = &JFactory::getDBO();
		$sql = "SELECT `show` FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($sql);
		$shown = $db->loadResult();
		if(strpos(" ".$shown,"zinfo") > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getPublishedPacks(){
		$db =& JFactory::getDBO();

        $my = & JFactory::getUser();
        $sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
        $db->setQuery($sql);
        $advid = $db->loadResult();

		$sql = "SELECT o.*,z.zoneid,z.banners,z.banners_cols,z.z_title,z.rotatebanners,z.adparams 
					FROM `#__ad_agency_order_type` AS o
					LEFT JOIN `#__ad_agency_zone` AS z 
					ON o.location = z.zoneid
					WHERE o.`published`=1 AND o.`visibility`<>0 
                    AND o.tid NOT IN (
                        SELECT p.tid
                        FROM `#__ad_agency_order_type` AS p, `#__ad_agency_order` AS oo
                        WHERE p.visibility <>0
                        AND p.cost = '0.00'
                        AND p.hide_after =1
                        AND p.tid = oo.tid
                        AND oo.aid = ".intval($advid)."
                    )
					ORDER BY ordering";
		$db->setQuery($sql);
		$packages = $db->loadObjectList();
		return $packages;	
	}

	function getAID(){
		$my =& JFactory::getUser();
		$db =& JFactory::getDBO();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."'";
		$db->setQuery($sql);
	    $advertiserid = $db->loadResult();
		return $advertiserid;
	}

	function getZonesForPacks($packs){
		$db = &JFactory::getDBO();
		if(isset($packs)&&(is_array($packs))){
			foreach($packs as $pack){
				$sql = "SELECT m.title, m.id, z.banners as rows, z.banners_cols as cols, z.adparams, z.rotatebanners
						FROM #__ad_agency_package_zone AS pz
						LEFT JOIN #__modules AS m ON pz.zone_id = m.id
						LEFT JOIN #__ad_agency_zone AS z ON z.zoneid = m.id
						WHERE pz.package_id = ".intval($pack->tid);
				$db->setQuery($sql);
				$pack->location = $db->loadObjectList();
			}
		}
		return $packs;
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getlistAdvertisers();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;
		$this->_customer = null;
	}

	function getlistAdvertisers () {
		if (empty ($this->_customers)) {
			$sql = "SELECT user.id, advertis.aid, advertis.company, advertis.approved, user.name, user.email, user.block, count(c.id) count FROM #__ad_agency_advertis as advertis LEFT OUTER JOIN #__users as user on user.id=advertis.user_id LEFT JOIN #__ad_agency_campaign as c on c.aid=advertis.aid where 1=1 GROUP BY advertis.aid";
			$this->_total = $this->_getListCount($sql);

			$this->_customers = $this->_getList($sql);
		}
		return $this->_customers;
	}

	function getAdvertiser() {
        //echo "<pre>";var_dump($this->_id);die();
		if (empty ($this->_customer)) {
			$this->_customer =& $this->getTable("adagencyAdvertiser");
			$this->_customer->load($this->_id);
		}
        //echo "<pre>";var_dump($this->_customer);die();
		return $this->_customer;
	}

	function getAdvertiserByUserId($uid) {
		$db = &JFactory::getDBO();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id =".intval($uid);
		$db->setQuery($sql);
		return $db->loadResult();
	}

	function getRealUserType($id){
		$db = &JFactory::getDBO();
		$sql = "SELECT usertype,gid FROM #__users WHERE id = ".intval($id)." LIMIT 1";
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function store (&$error) {
		global $mainframe;
		// the_user_status will have 3 values:
		// 0 - it's not a registered user and also the username doesn't exists
		// 1 - it's not a registered user but the username exists
		//              - we display a message forcing him to login first to activate the advertiser status
		// 2 - it's a registered user that will activate it's status
		$the_user_status = 0;
		$item_id = JRequest::getInt('Itemid','0','get');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		$existing_user =& JFactory::getUser();
		if(isset($existing_user->id)&&($existing_user->id>0)) {
			$existing_user->usertype = $this->getRealUserType($existing_user->id)->usertype;
			$existing_user->gid = $this->getRealUserType($existing_user->id)->gid;
		}
		//echo "<pre>";var_dump($existing_user);echo "</pre><hr />";
		if($existing_user->id > 0) {
			$the_user_status = 2;
		} else {
			JRequest::checkToken() or die( 'Invalid Token' );
		}

		jimport("joomla.database.table.user");
		$db = JFactory::getDBO();
		$user = new JUser();
        $my = new stdClass;
   		$data = JRequest::get('post');
		if(isset($data['email'])&&($data['email'] != NULL)) {
			$data['email'] = trim($data['email']);
		}

		// See if there is a wizzard or not
		$sql = "SELECT COUNT(id) FROM `#__ad_agency_settings` WHERE `show` LIKE '%wizzard%'";
		$db->setQuery($sql);
		$is_wizzard = intval($db->loadResult());

		$data['paywith'] = NULL;
		$post_name=$data['name'];
		$item =& $this->getTable('adagencyAdvertiser');

		if($the_user_status == 0)
		{
			$sql = "SELECT `id` FROM #__users WHERE username='".mysql_escape_string($data['username'])."'";
			$db->setQuery($sql);
			$user_id_byname = $db->loadResult();

			if(isset($user_id_byname) && $user_id_byname > 0)
				$the_user_status = 1;
		}

		// setting the reports values - start
		$item->email_daily_report = 'N';
		$item->email_weekly_report = 'N';
		$item->email_month_report = 'N';
		$item->email_campaign_expiration = 'N';

		if(isset($data['email_daily_report']) && $data['email_daily_report'] == 'Y')
			$item->email_daily_report = 'Y';
		if(isset($data['email_weekly_report']) && $data['email_weekly_report'] == 'Y')
			$item->email_weekly_report = 'Y';
		if(isset($data['email_month_report']) && $data['email_month_report'] == 'Y')
			$item->email_month_report = 'Y';
		if(isset($data['email_campaign_expiration']) && $data['email_campaign_expiration'] == 'Y')
			$item->email_campaign_expiration = 'Y';
		// setting the reports values - stop

		$configs = $this->getInstance("adagencyConfig", "adagencyModel");
		$configs = $configs->getConfigs();

		// we determine what case we have - actual SAVE or REDIRECT - start
		$res = true;
		if($the_user_status == 1)
			{
				$err_msg = JText::_("VIEWADVERTISER_ERR_MSG");
				$err_msg = str_replace('{username}', mysql_escape_string($data['username']), $err_msg);

			$_SESSION['ad_company'] = $data['company'];
			$_SESSION['ad_description'] = $data['description'];
			$_SESSION['ad_approved'] = $data['approved'];
			$_SESSION['ad_enabled'] = $data['enabled'];
			$_SESSION['ad_username'] = $data['username'];
			$_SESSION['ad_email'] = $data['email'];
			$_SESSION['ad_name'] = $data['name'];
			$_SESSION['ad_website'] = $data['website'];
			$_SESSION['ad_address'] = $data['address'];
			$_SESSION['ad_country'] = $data['country'];
			$_SESSION['ad_state'] = $data['state'];
			$_SESSION['ad_city'] = $data['city'];
			$_SESSION['ad_zip'] = $data['zip'];
			$_SESSION['ad_telephone'] = $data['telephone'];

				$mainframe->redirect('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0', $err_msg);
			}
		elseif($the_user_status == 0)
			{
				$query = 'SELECT id FROM #__users WHERE email = "'.addslashes(trim($data['email'])).'"';
				$db->setQuery($query);
				$exists_email = $db->loadResult($query);
				if($exists_email != '') {
					$_SESSION['ad_company'] = $data['company'];
					$_SESSION['ad_description'] = $data['description'];
					$_SESSION['ad_approved'] = $data['approved'];
					$_SESSION['ad_enabled'] = $data['enabled'];
					$_SESSION['ad_username'] = $data['username'];
					$_SESSION['ad_email'] = $data['email'];
					$_SESSION['ad_name'] = $data['name'];
					$_SESSION['ad_website'] = $data['website'];
					$_SESSION['ad_address'] = $data['address'];
					$_SESSION['ad_country'] = $data['country'];
					$_SESSION['ad_state'] = $data['state'];
					$_SESSION['ad_city'] = $data['city'];
					$_SESSION['ad_zip'] = $data['zip'];
					$_SESSION['ad_telephone'] = $data['telephone'];
					$mainframe->redirect('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0', JText::_('ADAG_EMAILINUSE'));
				}
				if(isset($configs->show)&&(strpos(" ".$configs->show, 'calculation')>0)) {
					if(!isset($_SESSION['ADAG_CALC']) || ($_SESSION['ADAG_CALC'] != $data['calculation'])) {
						$_SESSION['ad_company'] = $data['company'];
						$_SESSION['ad_description'] = $data['description'];
						$_SESSION['ad_approved'] = $data['approved'];
						$_SESSION['ad_enabled'] = $data['enabled'];
						$_SESSION['ad_username'] = $data['username'];
						$_SESSION['ad_email'] = $data['email'];
						$_SESSION['ad_name'] = $data['name'];
						$_SESSION['ad_website'] = $data['website'];
						$_SESSION['ad_address'] = $data['address'];
						$_SESSION['ad_country'] = $data['country'];
						$_SESSION['ad_state'] = $data['state'];
						$_SESSION['ad_city'] = $data['city'];
						$_SESSION['ad_zip'] = $data['zip'];
						$_SESSION['ad_telephone'] = $data['telephone'];
						$mainframe->redirect('index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=0', JText::_('JS_CALCULATION'));
					}
				}
				$pwd = $data['password'];
				if (!$data['user_id'])	{$data['password2'] = $data['password'];}

				$sql = "SELECT `id` FROM #__usergroups WHERE `title`='Registered'";
				$db->setQuery($sql);
				$advgroup = $db->loadResult();

				if (!isset($user->registerDate)) $user->registerDate = date( 'Y-m-d H:i:s' );

				$user->usertype = 'Registered';

				$user->gid = $advgroup;
				if ($data['user_id']>0) $data['id'] = $data['user_id'];

				$user->bind($data);
				$usersConfig = &JComponentHelper::getParams( 'com_users' );

				// If user activation is turned on, we need to set the activation information

				// $user->usertype = 'Registered';

				$query = "SHOW columns FROM #__ad_agency_advertis WHERE field='approved'";
				$db->setQuery($query);
				$autoapprove = $db->loadRow();

				if(isset($autoapprove[4])&&($autoapprove[4]=='Y')){
					$user->block = 0; $user->activation='';
					$data['approved'] = 'Y';
				} else {
					$data['approved'] = 'P';
					$useractivation = $usersConfig->get( 'useractivation' );
					if ($useractivation == '1')
					{
						jimport('joomla.user.helper');
						$user->activation = md5( JUserHelper::genRandomPassword());
						$user->block = 1;
					}
				}

				if($is_wizzard > 0){
					$user->block = 0;
					$user->activation = NULL;
					$user->params = NULL;
				}

				if (!$user->save()) {
					$error = $user->getError();
					echo $error;
					$res = false;
				} else {

                    $sql = "INSERT INTO `#__user_usergroup_map` (`user_id` ,`group_id`)
                            VALUES ('" . intval($user->id) . "', '" . intval($advgroup) . "');";
                    $db->setQuery($sql);
                    $db->query($sql);

					$name 		= $user->name;
					$email 		= $user->email;
					$username 	= $user->username;
					$mosConfig_live_site     = JURI::base();

					if($data['approved']=='Y') {
						$subject=$configs->sbafterregaa;
						$message=$configs->bodyafterregaa;
					} else {
						$subject=$configs->sbactivation;
						$message=$configs->bodyactivation;
					}

					$subject =str_replace('{name}',$name,$subject);
					$subject =str_replace('{login}',$username,$subject);
					$subject =str_replace('{email}',$email,$subject);

					$subject =str_replace('{password}',$pwd,$subject);

					$message =str_replace('{name}',$name,$message);
					$message =str_replace('{login}',$username,$message);
					$message =str_replace('{email}',$email,$message);
					$message =str_replace('{password}',$pwd,$message);

					$configs->txtafterreg = str_replace('{name}',$name,$configs->txtafterreg);
					$configs->txtafterreg = str_replace('{login}',$username,$configs->txtafterreg);
					$configs->txtafterreg = str_replace('{password}',$pwd,$configs->txtafterreg);


					$message =str_replace('{activate_url}','<a href="'.$mosConfig_live_site.'index.php?option=com_users&task=registration.activate&token='.$user->activation.'" target="_blank">'.$mosConfig_live_site.'index.php?option=com_users&task=registration.activate&token='.$user->activation.'</a>',$message);
					$message = html_entity_decode($message, ENT_QUOTES);
					JUtility::sendMail( $configs->fromemail, $configs->fromname, $email, $subject, $message, 1 );
				}

				$ask = "SELECT `id` FROM `#__users` ORDER BY `id` DESC LIMIT 1 ";
				$db->setQuery( $ask );
				$where = $db->loadResult();
				$user->id = $where;
				if (!$data['user_id']) $data['user_id'] = $user->id;
                
                $sql = "SHOW tables";
                $db->setQuery($sql);
                $res_tables=$db->loadResultArray();
                $jconfigs = & JFactory::getConfig();
                $dbprefix = $jconfigs->getValue( 'config.dbprefix' );
                
                //echo "<pre>";var_dump($dbprefix );die();

                if(in_array($dbprefix . "comprofiler", $res_tables) && $data['user_id']) {
                    $sql = "INSERT INTO `#__comprofiler` (`id`, `user_id`) VALUES ('".intval($data['user_id'])."', '".intval($data['user_id'])."');";
                    $db->setQuery($sql);
                    $db->query();
                }
                
				$data['key'] = md5(rand(1000,9999));
				$sql = "SELECT params FROM `#__ad_agency_settings` LIMIT 1";
				$db->setQuery( $sql );
				$cpr = @unserialize($db->loadResult());
				if(!isset($cpr['timeformat'])) { $data['fax'] = 10; } else { $data['fax'] = intval($cpr['timeformat']); }

				if (!$item->bind($data)){
					$res = false;
				}

				if (!$item->check()) {
					$res = false;
				}

				if (!$item->store()) {
					$res = false;
				}

				// Send notification to administrator below
				//if(!isset($user->block)||($user->block==0)){
				if(isset($data['approved'])&&($data['approved']=='Y')){
					$approval_msg = JText::_('NEWADAPPROVED');
				} else {
					$approval_msg = JText::_('ADAG_PENDING');
				}

				if(!isset($data['address'])||($data['address']=='')){$data['address'] = "N/A";}
				if(!isset($data['state'])||($data['state']=='')){$data['state'] = "N/A";}
				if(!isset($data['website'])||($data['website']=='')){$data['website'] = "N/A";}
				if(!isset($data['company'])||($data['company']=='')){$data['company'] = "N/A";}
				if(!isset($data['country'])||($data['country']=='')){$data['country'] = "N/A";}
				if(!isset($data['description'])||($data['description']=='')){$data['description'] = "N/A";}
				if(!isset($data['telephone'])||($data['telephone']=='')){$data['telephone'] = "N/A";}
				if(!isset($data['zip'])||($data['zip']=='')){$data['zip'] = "N/A";}

				$eapprove = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=approve&key=".$data['key']."&cid=".$data['user_id']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=approve&key=".$data['key']."&cid=".$data['user_id']."</a>";
				$edecline = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=decline&key=".$data['key']."&cid=".$data['user_id']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=decline&key=".$data['key']."&cid=".$data['user_id']."</a>";

				$message2 = str_replace('{name}',$name,$configs->bodynewuser);
				$message2 = str_replace('{email}',$email,$message2);
				$message2 = str_replace('{approval_status}',$approval_msg,$message2);
				$message2 = str_replace('{street}',$data['address'],$message2);
				$message2 = str_replace('{state}',$data['state'],$message2);
				$message2 = str_replace('{company}',$data['company'],$message2);
				$message2 = str_replace('{zipcode}',$data['zip'],$message2);
				$message2 = str_replace('{country}',$data['country'],$message2);
				$message2 = str_replace('{description}',$data['description'],$message2);
				$message2 = str_replace('{url}',$data['website'],$message2);
				$message2 = str_replace('{username}',$username,$message2);
				$message2 = str_replace('{phone}',$data['telephone'],$message2);
				$message2 = str_replace('{approve_advertiser_url}',$eapprove,$message2);
				$message2 = str_replace('{decline_advertiser_url}',$edecline,$message2);

				$subject2 = str_replace('{name}',$name,$configs->sbnewuser);
				$subject2 = str_replace('{email}',$email,$subject2);
				$subject2 = str_replace('{description}',$data['description'],$subject2);
				$subject2 = str_replace('{company}',$data['company'],$subject2);
				$subject2 = str_replace('{url}',$data['website'],$subject2);
				$subject2 = str_replace('{street}',$data['address'],$subject2);
				$subject2 = str_replace('{state}',$data['state'],$subject2);
				$subject2 = str_replace('{zipcode}',$data['zip'],$subject2);
				$subject2 = str_replace('{country}',$data['country'],$subject2);
				$subject2 = str_replace('{username}',$username,$subject2);
				$subject2 = str_replace('{approval_status}',$approval_msg,$subject2);
				$subject2 = str_replace('{phone}',$data['telephone'],$subject2);
				$subject2 = str_replace('{approve_advertiser_url}',$eapprove,$subject2);
				$subject2 = str_replace('{decline_advertiser_url}',$edecline,$subject2);

				$subject2 = html_entity_decode($subject2, ENT_QUOTES);
				$message2 = html_entity_decode($message2, ENT_QUOTES);

				JUtility::sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject2, $message2, 1 );

				if(stripslashes($_GET['task']) != 'edit')
					{

						$advertiser_id = mysql_insert_id();
						if ($advertiser_id==0) {
							$ask = "SELECT `id` FROM `#__ad_agency_advertis` ORDER BY `id` DESC LIMIT 1 ";
							$db->setQuery( $ask );
							$advertiser_id = $db->loadResult();
						}

						$query = "SELECT `lastreport` FROM #__ad_agency_advertis WHERE `aid`=".intval($advertiser_id);
						$db->setQuery( $query );
						$lastreport = $db->loadResult();
						$secs=time();
						if (!empty($lastreport)) {
							$querry = "UPDATE #__ad_agency_advertis SET `lastreport` = ".intval($secs)." WHERE `aid`=".intval($advertiser_id);

							$db->setQuery( $querry );
							$db->query() or die( $db->stderr() );
						}

					}

			}
		elseif($the_user_status == 2)
			{
				//die('here - us 2');
				if(isset($data['newpswd'])&&($data['newpswd']!="")) {
					$sql = "UPDATE `#__users` SET `password` = '".md5($data['newpswd'])."' WHERE `id` =".intval($existing_user->id)." LIMIT 1";
					$db->setQuery($sql);
					$db->query();
				}
				//echo "<pre>";var_dump($sql);die();
				$data['user_id'] = $existing_user->id;

				$new_name = stripslashes($post_name);

				$querry = "UPDATE #__users SET `name` = '".addslashes(trim($new_name))."' WHERE `id`=".intval($existing_user->id);
				$db->setQuery( $querry );
				$db->query();

				if (!$data['user_id']) $data['user_id'] = $existing_user->id;

				$query = "SHOW columns FROM #__ad_agency_advertis WHERE field='approved'";
				$db->setQuery($query);
				$autoapprove = $db->loadRow();

				$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id='".intval($existing_user->id)."' LIMIT 1;";
				$db->setQuery($sql);
				$aiduser = $db->loadResult();

                if (!$aiduser) {
                    $data['key'] = md5(rand(1000,9999));
                }

				if (!$item->bind($data)){
					$res = false;
				}

				if (!$item->check()) {
					$res = false;
				}

				if (!$item->store()) {
					$res = false;
				}

                if (!$aiduser) {
                    $sql = "SELECT * FROM #__users WHERE id = ".intval($item->user_id);
                    $db->setQuery($sql);
                    $theUser = $db->loadObject();
                    $name = $theUser->name;
                    $email = $theUser->email;
                    $username = $theUser->username;

                    // Send notification to administrator below
                    //if(!isset($user->block)||($user->block==0)){
                    if ($autoapprove[4]=='Y') {
                        $approval_msg = JText::_('NEWADAPPROVED');
                    } else {
                        $approval_msg = JText::_('ADAG_PENDING');
                    }

                    if(!isset($data['address'])||($data['address']=='')){$data['address'] = "N/A";}
                    if(!isset($data['state'])||($data['state']=='')){$data['state'] = "N/A";}
                    if(!isset($data['website'])||($data['website']=='')){$data['website'] = "N/A";}
                    if(!isset($data['company'])||($data['company']=='')){$data['company'] = "N/A";}
                    if(!isset($data['country'])||($data['country']=='')){$data['country'] = "N/A";}
                    if(!isset($data['description'])||($data['description']=='')){$data['description'] = "N/A";}
                    if(!isset($data['telephone'])||($data['telephone']=='')){$data['telephone'] = "N/A";}
                    if(!isset($data['zip'])||($data['zip']=='')){$data['zip'] = "N/A";}

                    $eapprove = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=approve&key=".$data['key']."&cid=".$data['user_id']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=approve&key=".$data['key']."&cid=".$data['user_id']."</a>";
                    $edecline = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=decline&key=".$data['key']."&cid=".$data['user_id']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAdvertisers&task=manage&action=decline&key=".$data['key']."&cid=".$data['user_id']."</a>";

                    $message2 = str_replace('{name}',$name,$configs->bodynewuser);
                    $message2 = str_replace('{email}',$email,$message2);
                    $message2 = str_replace('{approval_status}',$approval_msg,$message2);
                    $message2 = str_replace('{street}',$data['address'],$message2);
                    $message2 = str_replace('{state}',$data['state'],$message2);
                    $message2 = str_replace('{company}',$data['company'],$message2);
                    $message2 = str_replace('{zipcode}',$data['zip'],$message2);
                    $message2 = str_replace('{country}',$data['country'],$message2);
                    $message2 = str_replace('{description}',$data['description'],$message2);
                    $message2 = str_replace('{url}',$data['website'],$message2);
                    $message2 = str_replace('{username}',$username,$message2);
                    $message2 = str_replace('{phone}',$data['telephone'],$message2);
                    $message2 = str_replace('{approve_advertiser_url}',$eapprove,$message2);
                    $message2 = str_replace('{decline_advertiser_url}',$edecline,$message2);

                    $subject2 = str_replace('{name}',$name,$configs->sbnewuser);
                    $subject2 = str_replace('{email}',$email,$subject2);
                    $subject2 = str_replace('{description}',$data['description'],$subject2);
                    $subject2 = str_replace('{company}',$data['company'],$subject2);
                    $subject2 = str_replace('{url}',$data['website'],$subject2);
                    $subject2 = str_replace('{street}',$data['address'],$subject2);
                    $subject2 = str_replace('{state}',$data['state'],$subject2);
                    $subject2 = str_replace('{zipcode}',$data['zip'],$subject2);
                    $subject2 = str_replace('{country}',$data['country'],$subject2);
                    $subject2 = str_replace('{username}',$username,$subject2);
                    $subject2 = str_replace('{approval_status}',$approval_msg,$subject2);
                    $subject2 = str_replace('{phone}',$data['telephone'],$subject2);
                    $subject2 = str_replace('{approve_advertiser_url}',$eapprove,$subject2);
                    $subject2 = str_replace('{decline_advertiser_url}',$edecline,$subject2);

                    $subject2 = html_entity_decode($subject2, ENT_QUOTES);
                    $message2 = html_entity_decode($message2, ENT_QUOTES);

                    //echo "<pre>";var_dump($subject2);echo "<hr />";var_dump($message2);echo "<hr />";die();
                    JUtility::sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject2, $message2, 1 );
                }

				if((!isset($aiduser)||($aiduser<1))&&($autoapprove[4]=='Y')){
					$mainframe->redirect("index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid,JText::_('ADVSAVED2'));
				}
			}

		// we determine what case we have - actual SAVE or REDIRECT - stop

		if(($the_user_status == 0)&&($autoapprove[4]=='Y')){
			if(isset($user->id)&&(intval($user->id)>0)) {
				$this->autoLogin($user->id);
				$mainframe->redirect("index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid,JText::_('ADVSAVED2'));
			}
		} elseif(($the_user_status == 0)&&($autoapprove[4]!='Y')&&($is_wizzard > 0)){
			if(isset($user->id)&&(intval($user->id)>0)) {
				$this->autoLogin($user->id);
				$mainframe->redirect("index.php?option=com_adagency&controller=adagencyAds&task=addbanners".$Itemid);//,JText::_('ADAG_PENDING_ADS2')
			}
		}

		return $res;
	}

	function manage($key,$action,$cid){
		global $mainframe;
		$db	= & JFactory::getDBO();
		$sql = "SELECT approved FROM `#__ad_agency_advertis` WHERE `user_id`='".intval($cid)."' AND `key`='".trim($key)."' LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		if(isset($res)&&($res!=NULL)){
			if($action == "approve"){
				$sql = "UPDATE `#__users` SET `block` = 0,
`activation` = '' WHERE `id` ='".intval($cid)."';";
				$db->setQuery($sql);
				$sql = "UPDATE `#__ad_agency_advertis` SET `approved` = 'Y' WHERE `user_id` ='".intval($cid)."';";
				$db->setQuery($sql);
				if($db->query()){
					echo "<img src='".JURI::root()."components/com_adagency/images/tick.png' />".JText::_('ADAG_AAMSG');
				}

				$sql = "SELECT id, name, username, email FROM #__users WHERE id = '".intval($cid)."'";
				$db->setQuery($sql);
				$user = $db->loadObject();

				$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
				$db->setQuery($sql);
				$configs = $db->loadObject();

				$name 		= $user->name;
				$email 		= $user->email;
				$username 	= $user->username;

				$subject=$configs->sbafterreg;
				$message=$configs->bodyafterreg;
				//$subject=$configs->sbadvdis;
				//$message=$configs->bodyadvdis;

				$subject = str_replace('{name}',$name,$subject);
				$subject = str_replace('{login}',$username,$subject);
				$subject = str_replace('{email}',$email,$subject);
				$message = str_replace('{name}',$name,$message);
				$message = str_replace('{login}',$username,$message);
				$message = str_replace('{email}',$email,$message);

				$subject = html_entity_decode($subject, ENT_QUOTES);
				$message = html_entity_decode($message, ENT_QUOTES);
				// mail publish advertiser  // Send email to user
				JUtility::sendMail( $configs->fromemail, $configs->fromname, $email, $subject, $message, 1 );

			} elseif ($action == "decline"){
				$sql = "UPDATE `#__ad_agency_advertis` SET `approved` = 'N' WHERE `user_id` ='".intval($cid)."';";
				$db->setQuery($sql);
				if($db->query()){
					echo "<img src='".JURI::root()."components/com_adagency/images/publish_x.png' />".JText::_('ADAG_ADMSG');
				}

				$sql = "SELECT id, name, username, email FROM #__users WHERE id = '".intval($cid)."'";
				$db->setQuery($sql);
				$user = $db->loadObject();

				$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
				$db->setQuery($sql);
				$configs = $db->loadObject();

				$name 		= $user->name;
				$email 		= $user->email;
				$username 	= $user->username;

				//$subject=$configs->sbafterreg;
				//$message=$configs->bodyafterreg;
				$subject=$configs->sbadvdis;
				$message=$configs->bodyadvdis;

				$subject = str_replace('{name}',$name,$subject);
				$subject = str_replace('{login}',$username,$subject);
				$subject = str_replace('{email}',$email,$subject);
				$message = str_replace('{name}',$name,$message);
				$message = str_replace('{login}',$username,$message);
				$message = str_replace('{email}',$email,$message);

				$subject = html_entity_decode($subject, ENT_QUOTES);
				$message = html_entity_decode($message, ENT_QUOTES);
				// mail publish advertiser  // Send email to user
				JUtility::sendMail( $configs->fromemail, $configs->fromname, $email, $subject, $message, 1 );

			} else {
				$mainframe->redirect("index.php");
			}
		} else {
			$mainframe->redirect("index.php");
		}
	}

	function autoLogin($userid){
		global $mainframe;
		$db =& JFactory::getDBO();
		$item_id = JRequest::getInt('Itemid','0','post');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		$query = "SELECT * FROM #__users WHERE id='".intval($userid)."'";
		$vect[] = $query;
		$db->setQuery( $query );
		$user = $db->loadObject();
		//echo "<pre>";var_dump($user);die();
		if(isset($user->id)){
			$sql = "UPDATE `#__users` SET `password` = '".md5('randompsw')."' WHERE `id` ='".intval($user->id)."' LIMIT 1 ;";
			$db->setQuery($sql);
			$db->query();
			$vect[] = $sql;

			$options['remember'] = false;
			$options['return'] = 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=register'.$Itemid;

			$credentials = array();
			$credentials['username'] = $user->username;
			$credentials['password'] = "randompsw";

			$mainframe->login($credentials, $options);

			$sql = "UPDATE `#__users` SET `password` = '".trim($user->password)."' WHERE `id` ='".intval($user->id)."' LIMIT 1 ;";
			$vect[] = $sql;
			$db->setQuery($sql);
			$db->query();
		}

		//echo "<pre>";var_dump($vect);die();
		return true;
	}

	function getLastAdvertiser(){
		$db =& JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_advertis ORDER BY aid DESC LIMIT 1";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		return $result;
	}

};
?>
