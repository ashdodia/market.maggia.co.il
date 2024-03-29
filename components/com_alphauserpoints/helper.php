<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2013 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')) {
   define('DS', DIRECTORY_SEPARATOR);
}

jimport('joomla.plugin.plugin');

class AlphaUserPointsHelper 
{

	public function newpoints ( $plugin_function='', $referrerid='', $keyreference='', $datareference='', $randompoints=0, $feedback=false, $force=0, $frontmessage='' ) 
	{		
		if ( $plugin_function!='' ) 
		{
			AlphaUserPointsHelper::userpoints ( $plugin_function, $referrerid, 0, $keyreference, $datareference, $randompoints, $feedback, $force, $frontmessage );
		} 
		else 
		{
			return;
		}	
	}	
	
	public function userpoints ( $plugin_function , $referrerid='', $referraluserpoints=0, $keyreference='', $datareference='', $randompoints=0, $feedback=false, $force=0, $frontmessage='' ) 
	{	
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		$allowNegativeAccount = $params->get( 'allowNegativeAccount', 0 );
		
		$checkReference = 0;

		if ( $plugin_function=='sysplgaup_recommend' && $referrerid!='' && $keyreference!='' && $datareference!='' )
		{
			$force = 1;		
		}		
		if ( !$referraluserpoints )
		{
			if ( !$referrerid )
			{
				$referrerid = @$_SESSION['referrerid'];
			}
		}
		if ( !$referrerid ) return;		
		if ( !AlphaUserPointsHelper::checkExcludeUsers( $referrerid) ) return ;		
		$result = AlphaUserPointsHelper::checkRuleEnabled( $plugin_function, $force, $referrerid );
		
		// check reference if not exist
		if ( $keyreference!='' && $result ) $checkReference = AlphaUserPointsHelper::checkReference( $referrerid, $keyreference, $result[0]->id );		
		if ( $result && !$checkReference )
		{
			// force specific points -> overwriting rule points or if rule points is 0			
			// This points can be negative (example : single rule in backend rule manager for several products on frontend, each products with different prices...)
			if ( $randompoints ) $result[0]->points = $randompoints;
			
			// check if limit daily points
			if ( $result[0]->points > 0 && $referraluserpoints==0 ) 
			{
				$result[0]->points = AlphaUserPointsHelper::checkMaxDailyPoints( $referrerid, $result[0]->points, $plugin_function );
			}
			
			if ( AlphaUserPointsHelper::operationIsFeasible ( $referrerid, $result[0]->points ) || $allowNegativeAccount )
			{   
			
				// check account
				AlphaUserPointsHelper::insertUserPoints( $referrerid, $result[0], $referraluserpoints, $keyreference, $datareference, $frontmessage );
				if ( $feedback==true ) return true;
			}
			else
			{
				// used for negative operation, 				
				$error = JText::_('AUP_YOUDONOTHAVEENOUGHPOINTSTOPERFORMTHISOPERATION' );
				JError::raiseNotice(0, $error );
				
				if ( $feedback==true )
				{
					return false;
				} 
				else
				{
					return;
				}
			}
		} else return true;
		
	}

	public function insertUserPoints( $referrerid, $result, $referraluserpoints=0, $keyreference='', $datareference='', $frontmessage='' ) 
	{	
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toMySQL();		
				
		$points = $result->points;
		$rule_expire = AlphaUserPointsHelper::checkRuleExpireDate( $result->rule_expire, $result->type_expire_date );
		$rule_id = $result->id;
		$autoapproved = $result->autoapproved;
		$rule_plugin = $result->plugin_function;
		$rule_name = $result->rule_name;
		$noupdate  = 0;
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		$insertAllActivities = $params->get( 'insertAllActivities', 0 );
		
		if ( $insertAllActivities )
		{	
			if ( !$referrerid ) return;
		}	
		else 
		{		
			if ( !$referrerid || $points==0 ) return;
		}			
		
		$points = ( $referraluserpoints>0 ) ? $referraluserpoints : $points ;	
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('alphauserpoints');
		$results = $dispatcher->trigger( 'onBeforeInsertUserActivityAlphaUserPoints', array(&$result, $points, $referrerid, $keyreference, $datareference) );		
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');
		// save new points into alpha_userpointsdetails table
		$row = JTable::getInstance('userspointsdetails');
		$row->id				= NULL;
		$row->referreid			= $referrerid;
		$row->points			= $points;
		$row->insert_date		= $now;
		$row->expire_date 		= $rule_expire;		
		$row->rule				= $rule_id;
		$row->approved			= $autoapproved;
		$row->status			= $autoapproved;
		$row->keyreference		= $keyreference;
		$row->datareference		= $datareference;

		if ( !$row->store() )
		{
			JError::raiseError(500, $row->getError());
		}
		
		if ( $noupdate ) return;		
		
		if ( $referrerid!='GUEST' || $referraluserpoints>0 )
		{
			if ( $rule_plugin=='' ) $rule_plugin = AlphaUserPointsHelper::getPluginFunction( $rule_id ) ;
			if ( $rule_name==''   ) $rule_name   = AlphaUserPointsHelper::getNameRule( $rule_plugin );
			AlphaUserPointsHelper::updateUserPoints( $result, $referrerid, $points, $now, $referraluserpoints, $autoapproved, $rule_plugin, $rule_id, $rule_name, $datareference, $frontmessage );
		}
	}

	public function updateUserPoints( $result, $referrerid, $assignpoints, $now, $referraluserpoints, $autoapproved, $rule_plugin='', $rule_id='', $rule_name='', $datareference='', $frontmessage='' ) 
	{
		$app = JFactory::getApplication();		
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );	
		
		$user =  JFactory::getUser();	
		$username = ( $user->id ) ? $user->username : '' ;
		
		$displaymsg = $result->displaymsg;
		$msg = str_replace('{username}', $username, $result->msg);
		$method = $result->method;
		
		$db	   = JFactory::getDBO();	
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		
		$query = "SELECT id FROM #__alpha_userpoints WHERE `referreid`='$referrerid'";
		$db->setQuery( $query );
		$referrerUser = $db->loadResult();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');				
		$row = JTable::getInstance('userspoints');
			
		// update points into alpha_userpoints table
		$row->load( intval($referrerUser) );
		
		$referraluser = $row->referraluser;
		
		$newtotal = ( !$referraluserpoints ) ? ($row->points + $assignpoints) : $row->points + $referraluserpoints ;		
		$row->last_update = $now;	
		
		$checkWinner = 0;		
		
		if ( $row->max_points >=1 && ( $newtotal > $row->max_points ) )
		{
			// Max total was reached !
			$newtotal = $row->max_points;

			if ( AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_winnernotification', 0, $referrerid ) )
			{
				// get email admins in rule
				$query = "SELECT `content_items` FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_winnernotification'";
				$db->setQuery( $query );
				$emailadmins = $db->loadResult();
				if ( $autoapproved || $referraluserpoints )
				{
					AlphaUserPointsHelper::sendwinnernotification ( $referrerid, $assignpoints, $newtotal, $emailadmins );
					
					// Uddeim notification integration
					if ( $params->get( 'sendMsgUddeim', 0 ) )
					{
						AlphaUserPointsHelper::sendUddeimWinnerNotification ( $referrerid, $assignpoints, $newtotal );
					}			
					
					$checkWinner = 1;
				}
			}
		}	
		
		if ( $autoapproved )  
		{
			if ( $rule_plugin=='sysplgaup_invitewithsuccess' )
			{
				$row->referrees = $row->referrees+1;		
			}
			
			$row->points = $newtotal;
			
			$db->updateObject( '#__alpha_userpoints', $row, 'id' );
		}
		
		if ( $displaymsg && !$referraluserpoints )			
		{
			$realcurrentreferrerid = AlphaUserPointsHelper::getAnyUserReferreID( $user->id );
			switch ( $rule_plugin )
			{
				case 'sysplgaup_bonuspoints':	
				case 'sysplgaup_recommend':	
				case 'sysplgaup_reader2author':
				case 'sysplgaup_buypointswithpaypal':
				case 'sysplgaup_invite':		
					// No need congratulation...								
					break;					
				case 'sysplgaup_invitewithsuccess':
					// number points in message = assign points to new user
					$numpoints = AlphaUserPointsHelper::getPointsRule( 'sysplgaup_newregistered' );
					if ( $numpoints && $user->id ) 
					{
							if ( $msg!='' )
							{								
								$msg =  str_replace ( '{points}', AlphaUserPointsHelper::getFPoints($numpoints), JText::_( $msg ) );
								$msg =  str_replace ( '{newtotal}', AlphaUserPointsHelper::getFPoints($newtotal), $msg );
								$app->enqueueMessage( $msg );
							} else {
								$app->enqueueMessage( sprintf ( JText::_('AUP_CONGRATULATION'), AlphaUserPointsHelper::getFPoints($numpoints) ));
							} 
					}
					break;
				
				default:
					if ( ($referrerid == $realcurrentreferrerid) && $user->id )
					{
						if ( $assignpoints>0  ) 
						{		
							if ( $msg!='' )
							{
								$msg =  str_replace ( '{points}', AlphaUserPointsHelper::getFPoints($assignpoints), JText::_( $msg ) );
								$msg =  str_replace ( '{newtotal}', AlphaUserPointsHelper::getFPoints($newtotal), $msg );
								$app->enqueueMessage( $msg );
							} else {
								$app->enqueueMessage( sprintf ( JText::_('AUP_CONGRATULATION'), AlphaUserPointsHelper::getFPoints($assignpoints) ));
								if ( $rule_plugin=='sysplgaup_happybirthday' ) $frontmessage = JText::_('AUP_HAPPYBIRTHDAY');
							}
							
						} 
						elseif ( $assignpoints<0 )
						{
							if ( $msg!='' )
							{
								$msg =  str_replace ( '{points}', AlphaUserPointsHelper::getFPoints(abs($assignpoints)), JText::_( $msg ) );
								$msg =  str_replace ( '{newtotal}', AlphaUserPointsHelper::getFPoints($newtotal), $msg );
								$app->enqueueMessage( $msg );
							} else {
								$app->enqueueMessage( sprintf ( JText::_('AUP_X_POINTS_HAS_BEEN_DEDUCTED_FROM_YOUR_ACCOUNT'), AlphaUserPointsHelper::getFPoints(abs($assignpoints)) ) );
							}
							
						}
					}
			}
		}		
		
		if ( $rule_plugin=='sysplgaup_custom' && $datareference ) $rule_name = JText::_( $datareference );
		
		// email notification
		if ( $result->notification && !$checkWinner ) 
		{
			AlphaUserPointsHelper::sendnotification ( $referrerid, $assignpoints, $newtotal, $result );
			// load external plugins
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('alphauserpoints');
			$results = $dispatcher->trigger( 'onSendNotificationAlphaUserPoints', array(&$result, $rule_name, $assignpoints, $newtotal, $referrerid, $user->id ) );
		}
		
		// Uddeim notification integration
		if ( $params->get( 'sendMsgUddeim', 0 ) && !$checkWinner )
		{
			AlphaUserPointsHelper::sendUddeimNotification ( $referrerid, $assignpoints, $newtotal, $rule_name );
		}		
	
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('alphauserpoints');
		$results = $dispatcher->trigger( 'onUpdateAlphaUserPoints', array(&$result, $rule_name, $assignpoints, $referrerid, $user->id ) );
				
		// checking rank and medals and update if necessary
		if ( $rule_id=='' ) $rule_id = AlphaUserPointsHelper::getRuleID( $rule_plugin );
		AlphaUserPointsHelper::checkRankMedal( $referrerid, $rule_id );
		
		// referral points rule
		if ( $referraluser!='' 
				&& $rule_plugin!='sysplgaup_buypointswithpaypal' // not assigned for this rule
					&& $rule_plugin!='sysplgaup_raffle' // not assigned for this rule
						&& $assignpoints>0 )
		{ // if not already assigned
			$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_referralpoints' AND `published`='1' AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
			$db->setQuery( $query );
			$referralpoints = $db->loadObjectList();
			if ( $referralpoints )
			{
				$referraluserpoints = round(($assignpoints*$referralpoints[0]->points)/100, 2) ;
				if ( $referraluserpoints>0 ) AlphaUserPointsHelper::userpoints( 'sysplgaup_referralpoints', $referraluser, $referraluserpoints );
			}
		}
		
		// check change user group rule 
		if ( $rule_plugin!='sysplgaup_changelevel1' && $rule_plugin!='sysplgaup_changelevel2' && $rule_plugin!='sysplgaup_changelevel3' ) 
		{
			AlphaUserPointsHelper::checkChangeLevel( $referrerid, AlphaUserPointsHelper::getCurrentTotalPoints ( $referrerid ) ); 
		}
		
		if ( $frontmessage!='' ) AlphaUserPointsHelper::displayMessageSystem($frontmessage);
	}
	
	public function checkRuleExpireDate( $rule_expire, $type_expire_date )	
	{	
		if ( $rule_expire!='0000-00-00 00:00:00' || $type_expire_date ) 
		{
			if ( $type_expire_date )
			{
				switch ( $type_expire_date )
				{
					case (($type_expire_date >1) && ($type_expire_date < 30)) :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+'.intval($type_expire_date).' days');
						break;
					case $type_expire_date==1 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+1 day');
						break;
					case $type_expire_date==30 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+1 month');
						break;
					case $type_expire_date==60 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+2 months');
						break;
					case $type_expire_date==90 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+3 months');
						break;
					case $type_expire_date==180 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+6 months');
						break;
					case $type_expire_date==360 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+1 year');						
						break;
				}
				$expire_date = $date->format("Y-m-d");
				$expire_date .= " 23:59:59";
			} else $expire_date = $rule_expire;
		
		 } else $expire_date = '0000-00-00 00:00:00';
	
		return $expire_date;
	}
	
	public function checkChangeLevel( $referrerid, $newtotal )
	{
		$app = JFactory::getApplication();
		$db	   = JFactory::getDBO();
		
		$ok = 0;
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE);	
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');
		
		$resultChangeLevel1 = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_changelevel1', 0, $referrerid );
		$resultChangeLevel2 = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_changelevel2', 0, $referrerid );
		$resultChangeLevel3 = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_changelevel3', 0, $referrerid );		
		
		if ($resultChangeLevel1) $checkAlreadyDone1  = explode(',', $resultChangeLevel1[0]->exclude_items);
		if ($resultChangeLevel2) $checkAlreadyDone2  = explode(',', $resultChangeLevel2[0]->exclude_items);
		if ($resultChangeLevel3) $checkAlreadyDone3  = explode(',', $resultChangeLevel3[0]->exclude_items);
		
		$userid 			= AlphaUserPointsHelper::getUserID( $referrerid );
	
		// get actual group fot this user
		jimport('joomla.user.helper');
		
		$authorizedLevels = JAccess::getAuthorisedViewLevels($userid);		

		$result = array_keys(JUserHelper::getUserGroups($userid));
		$actualgroup = end($result);
		
		if ( $resultChangeLevel1 && $newtotal >= $resultChangeLevel1[0]->points2 && !in_array(intval($resultChangeLevel1[0]->content_items), $authorizedLevels) && !in_array($userid, $checkAlreadyDone1) )
		{			
			// delete old group
			$query = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`='$userid'";
			$db->setQuery( $query );
			$db->query();
	
			JUserHelper::addUserToGroup($userid, intval($resultChangeLevel1[0]->content_items));
			$user = JUser::getInstance((int) $userid);

			$ok = 1;
			$resultChangeLevel = $resultChangeLevel1;
			$result = JUserHelper::getUserGroups($userid);
			$actualnamegroup = end($result);
			
			// insert done for this user in this rule			
			if ( $resultChangeLevel1[0]->exclude_items!='' )
			{
				$insertUserId = $resultChangeLevel1[0]->exclude_items . ',' . $userid;
			} else {
				$insertUserId = $userid;
			}
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel1[0]->id) );
			$row->exclude_items = $insertUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );			
		}
		
		if ( $resultChangeLevel2 && $newtotal >= $resultChangeLevel2[0]->points2 && !in_array(intval($resultChangeLevel2[0]->content_items), $authorizedLevels) && !in_array($userid, $checkAlreadyDone2) )
		{			
			$query = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`='$userid'";
			$db->setQuery( $query );
			$db->query();	
			JUserHelper::addUserToGroup($userid, intval($resultChangeLevel2[0]->content_items));
			$user = JUser::getInstance((int) $userid);
			$ok = 1;
			$resultChangeLevel = $resultChangeLevel2;
			$result = JUserHelper::getUserGroups($userid);
			$actualnamegroup = end($result);
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel2[0]->id) );
			$row->exclude_items = $insertUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );			
		}

		if ( $resultChangeLevel3 && $newtotal >= $resultChangeLevel3[0]->points2 && !in_array(intval($resultChangeLevel3[0]->content_items), $authorizedLevels) && !in_array($userid, $checkAlreadyDone3) )
		{			
			$query = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`='$userid'";
			$db->setQuery( $query );
			$db->query();	
			JUserHelper::addUserToGroup($userid, intval($resultChangeLevel3[0]->content_items));
			$user = JUser::getInstance((int) $userid);
			$ok = 1;
			$resultChangeLevel = $resultChangeLevel3;
			$result = JUserHelper::getUserGroups($userid);
			$actualnamegroup = end($result);
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel3[0]->id) );
			$row->exclude_items = $insertUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );
		}
		
		if ( $ok )
		{
			// refresh session if user online
			$temp = JFactory::getUser((int) $userid);
			$temp->groups = $user->groups;
			$temp = JFactory::getUser((int) $userid);
			if ($temp->id == $userid) {
				$temp->groups = $user->groups;
			}
		}

		// show message only for current user and if frontend site
		if ( $referrerid == @$_SESSION['referrerid'] && $app->isSite() && $ok )
		{
			// display message for the current user
			if ( $resultChangeLevel[0]->displaymsg && $resultChangeLevel[0]->msg!='' )
			{
				$msg = str_replace('{username}',$user->username,$resultChangeLevel[0]->msg);
				$msg = str_replace('{points}',AlphaUserPointsHelper::getFPoints($resultChangeLevel[0]->points),$msg);
				$msg = str_replace('{newtotal}',AlphaUserPointsHelper::getFPoints($newtotal),$msg);
				AlphaUserPointsHelper::displayMessageSystem( $msg );
			} 
			elseif ( $resultChangeLevel[0]->displaymsg && $resultChangeLevel[0]->msg=='' )		
			{
				AlphaUserPointsHelper::displayMessageSystem( sprintf (JText::_('AUP_MSG_YOUHAVENEWUSERRIGHTS'), AlphaUserPointsHelper::getFPoints($resultChangeLevel[0]->points2), $actualnamegroup) );
			} 
		}
		
		if ( $ok ) 	
		{		
			// insert this new activity in database			
			$datareference = sprintf (JText::_('AUP_DESCRIPTIONACTIVITYONCHANGELEVEL'), AlphaUserPointsHelper::getFPoints($resultChangeLevel[0]->points2), $actualnamegroup);
			AlphaUserPointsHelper::insertUserPoints( $referrerid, $resultChangeLevel[0], 0, '', $datareference );
			
			// load external plugins
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('alphauserpoints');
			$results = $dispatcher->trigger( 'onChangeLevelAlphaUserPoints', array(&$resultChangeLevel[0], $actualnamegroup, $userid, $referrerid ) );
		}
	}
	
	public function getUserID( $referrerid )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `userid` FROM #__alpha_userpoints WHERE `referreid`='".$referrerid."'";
		$db->setQuery( $query );
		$userID = $db->loadResult();
		return	$userID;	
	}
	
	public function getRuleID( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `id` FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function'";
		$db->setQuery( $query );
		$rule_id = $db->loadResult();
		return intval($rule_id);
	}
	
	public function getPointsRule( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `points` FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function'";
		$db->setQuery( $query );
		$numpoints = $db->loadResult();
		return	$numpoints;	
	}
	
	public function getPointsRule2( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `points2` FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function'";
		$db->setQuery( $query );
		$numpoints = $db->loadResult();
		return	$numpoints;	
	}
	
	public function getNameRule( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `rule_name` FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function'";
		$db->setQuery( $query );
		$rule_name = $db->loadResult();
		return	$rule_name;	
	}
	
	public function getPluginFunction( $id_rule )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `plugin_function` FROM #__alpha_userpoints_rules WHERE `id`='$id_rule'";
		$db->setQuery( $query );
		$plugin_function = $db->loadResult();
		return	$plugin_function;	
	}
	
	public function getDatareference( $idActivity )
	{
		if ( $idActivity ) return;		
		$db	   = JFactory::getDBO();
		$query = "SELECT `datareference` FROM #__alpha_userpoints_details WHERE `id`='$idActivity'";
		$db->setQuery( $query );
		$datareference = $db->loadResult();
		return	$datareference;
	}
	
	public function getMethod( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT `method` FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function' AND `published`='1'";
		$db->setQuery( $query );
		$method = $db->loadResult();

		return $method;	
	}	
	
	public function buildKeyreference( $plugin_function, $spc='', $userID=0 )
	{		
		$user =  JFactory::getUser();
		
		$method = AlphaUserPointsHelper::getMethod( $plugin_function );
		
		$jnow	= JFactory::getDate();		
		$date 	= $jnow->format('%Y-%m-%d');		
		$today 	= getdate();
				
		$week	= $today['wday'];
		$month	= $jnow->format('%m');
		$year	= $jnow->format('%Y');
		
		$keyreference = '';
		
		if (!$userID) $userID = $user->id;
		
		if ( $spc!='' ) $spc .= '|';
		
		switch ( $method )
		{
			case '1':		// ONCE PER USER
				$keyreference = $spc . $userID;
				break;
			case '2':		// ONCE PER DAY AND PER USER'		
				$keyreference = $spc . $userID .'|'.$date;
				break;
			case '3':		// ONCE A DAY FOR A SINGLE USER ON ALL USERS
				$keyreference = $spc . $date;
				break;
			case '5':       // ONCE PER USER PER WEEK
				$keyreference = $spc . $userID .'|'.$week.'|'.$year;
				break;
			case '6':       // ONCE PER USER PER MONTH
				$keyreference = $spc . $userID .'|'.$month.'|'.$year;
				break;
			case '7':       // ONCE PER USER PER YEAR
				$keyreference = $spc . $userID .'|'.$year;
				break;
			case '4':       // WHENEVER
			case '0':
			default:
				$keyreference = $spc . uniqid ( '', false );
		}
		
		return $keyreference;
		
	}
	
	public function checkExcludeUsers($referreID)
	{	
		$db	   = JFactory::getDBO();

		$query = "SELECT referreid FROM #__alpha_userpoints WHERE `referreid`='$referreID' AND `published`='1'";
		$db->setQuery( $query );
		$result  = $db->loadResult();		
		if ( $result )
		{		
			return 1;
		}		
		return 0;
	}
	
	public function checkRuleEnabled( $plugin_function='', $force=0, $referrerid='' )
	{	
		if ( !$plugin_function ) return false;	
		$user =  JFactory::getUser();	
		$jnow		= JFactory::getDate();
		$now		= $jnow->toMySQL();
		$userID		= 0;
		$accessrule = "";
		
		if ( !$referrerid )
		{
			$referrerid = @$_SESSION['referrerid'];
			if ( !$referrerid ) {
				$userID = $user->id;
			} else {
				$userID = AlphaUserPointsHelper::getUserID( $referrerid );
			}
		} else {
			$userID = AlphaUserPointsHelper::getUserID( $referrerid );
		}

		$authorizedLevels = JAccess::getAuthorisedViewLevels( $userID );
		
		switch ( $plugin_function )
		{		
			case 'sysplgaup_referralpoints': // assign to all referral users level
			case 'sysplgaup_emailnotification':
			case 'sysplgaup_winnernotification':
			case 'sysplgaup_newregistered':
			case 'sysplgaup_invitewithsuccess':
			case 'sysplgaup_archive':
			case 'sysplgaup_changelevel1':
			case 'sysplgaup_changelevel2':
			case 'sysplgaup_changelevel3':
				$accessrule = "";
				break;
			default:
				$accessrule = "AND `access` IN (" . implode ( ",", $authorizedLevels ) . ")";
				break;		
		}		
		if ( $force )
		{
			$accessrule = "";
		} 
		$db	   = JFactory::getDBO();		
		$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function' AND `published`='1' $accessrule AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
		$db->setQuery( $query );
		$result  = $db->loadObjectList();
		return $result;
	}
	
	public function checkReference( $referrerid, $keyreference, $ruleid )
	{	
		$db	   = JFactory::getDBO();
		
		$plugin_function = AlphaUserPointsHelper::getPluginFunction( $ruleid );
		$method = AlphaUserPointsHelper::getMethod( $plugin_function );
		
		switch ( $method )
		{
			case '3':
				$query = "SELECT count(*) FROM #__alpha_userpoints_details WHERE `keyreference`='$keyreference' AND `rule`='$ruleid'";
				break;			
			default:
				$query = "SELECT count(*) FROM #__alpha_userpoints_details WHERE `referreid`='$referrerid' AND `keyreference`='$keyreference' AND `rule`='$ruleid'";		
		}		
		$db->setQuery( $query );
		$resultKeyReference = $db->loadResult();
		return $resultKeyReference;	
	}
	
	public function getReferreid( $userID )
	{	
		if ( !$userID ) return;	
		// get referre ID on login	
		$db	   = JFactory::getDBO();
		$query = "SELECT referreid FROM #__alpha_userpoints WHERE `userid`='$userID'";
		$db->setQuery( $query );
		$referreid = $db->loadResult();
		if ( $referreid )
		{		
			@session_start('alphauserpoints');	
			$_SESSION['referrerid'] = $referreid;
			$expire=time()+60*60*24*30; //expires in 30 days
			setcookie("referrerid", $referreid, $expire);
		}	
	}
	
	public function getAnyUserReferreID( $userID )
	{	
		// get referre ID for an author etc...
		$db	   = JFactory::getDBO();
		$query = "SELECT referreid FROM #__alpha_userpoints WHERE `userid`='".intval($userID)."'";
		$db->setQuery( $query );
		$referreid = $db->loadResult();		
		return  $referreid;		
	}
	
	public function getUserInfo ( $referrerid='', $userid='' )
	{	
		if ( !$referrerid && !$userid ) return;
		
		$db	   = JFactory::getDBO();
		
		if ( $referrerid ) {
			$where = "a.referreid='$referrerid'";		
		} elseif ( $userid ){		
			$where = "a.userid='$userid'";
		}		
		
		$query = "SELECT a.userid, a.referreid, a.upnid, a.points, a.max_points, a.last_update, " .
				 "a.referraluser, a.referrees, a.blocked, a.birthdate, a.avatar, a.levelrank, a.leveldate, " .
				 "a.gender, a.aboutme, a.website, a.phonehome, a.phonemobile, a.address, a.zipcode, a.city, a.country, " .
				 "a.education, a.graduationyear, a.job, a.facebook, a.twitter, a.icq, a.aim, a.yim, a.msn, a.skype, a.gtalk, " .
				 "a.xfire, a.profileviews, a.shareinfos, a.id AS rid, u.* " .
				 "FROM #__alpha_userpoints AS a, #__users AS u " .
				 "WHERE $where AND a.userid=u.id";
		$db->setQuery( $query );
		$userinfo = $db->loadObjectList();
		return @$userinfo[0];
	}	
	
	public function sendnotification ( $referrerid, $assignpoints, $newtotal, $result, $force=0 )
	{
		$app = JFactory::getApplication();	
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );	
		
		if ( !$referrerid || $referrerid=='GUEST') return;	
		
		$db	   = JFactory::getDBO();
		
		jimport( 'joomla.mail.helper' );		
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		
		$SiteName 	= $app->getCfg('sitename');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		$sef		= $app->getCfg('sef');		
		$userinfo   = AlphaUserPointsHelper::getUserInfo( $referrerid );
		$email	    = $userinfo->email;
		
		$rule_name	= $result->rule_name;
		$subject	= $result->emailsubject;
		$body		= $result->emailbody;
		$formatMail	= $result->emailformat;
		$bcc2admin	= $result->bcc2admin;
			
		if ( !$userinfo->block || $force )
		{		
		
			if ( $subject!='' && $body!='' ) {
				
				$subject = str_replace('{username}', $userinfo->username, $subject);
				$subject = str_replace('{points}', AlphaUserPointsHelper::getFPoints(abs($assignpoints)), $subject);
				$subject = str_replace('{newtotal}', AlphaUserPointsHelper::getFPoints($newtotal), $subject);
				$body 	 = str_replace('{username}', $userinfo->username, $body);
				$body 	 = str_replace('{points}', AlphaUserPointsHelper::getFPoints(abs($assignpoints)), $body);
				$body 	 = str_replace('{newtotal}', AlphaUserPointsHelper::getFPoints($newtotal), $body);
				
			} else { 
				// default message
				if ( $assignpoints>0 ) 
				{
					$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT');
					$body = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG'), $SiteName, AlphaUserPointsHelper::getFPoints($assignpoints), AlphaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );	
				}
				 elseif ( $assignpoints<0 )
				{
					$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT_ACCOUNT_UPDATED');
					$body = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_REMOVE_POINTS'), $SiteName, AlphaUserPointsHelper::getFPoints(abs($assignpoints)), AlphaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );
				}
			}
			
			$subject = JMailHelper::cleanSubject($subject);		
			$body    = JMailHelper::cleanBody($body);
			
			$mailer = JFactory::getMailer();
			$mailer->setSender( array( $MailFrom, $FromName ) );
			$mailer->setSubject( $subject);
			$mailer->isHTML((bool) $formatMail);
			$mailer->CharSet = "utf-8";
			$mailer->setBody($body);
			$mailer->addRecipient( $email );
			if ( $bcc2admin ) 
			{			
				// get all users allowed to receive e-mail system
				$query = "SELECT email" .
						" FROM #__users" .
						" WHERE sendEmail='1' AND block='0'";
				$db->setQuery( $query );
				$rowsAdmins = $db->loadObjectList();		
				
				foreach ( $rowsAdmins as $rowsAdmin ) {
					$mailer->addBCC( $rowsAdmin->email );
				}
			}		
			$send = $mailer->Send();
			
		}		
	}
	
	public function sendUddeimNotification ( $referrerid, $assignpoints, $newtotal, $rule_name )
	{
		$app = JFactory::getApplication();
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );	
		
		// integration Uddeim		
		if ( !$referrerid || $referrerid=='GUEST') return;
		
		// check if component installed
		$uddeim_exist = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_uddeim'.DS.'admin.uddeimlib15.php';
		if ( !file_exists ($uddeim_exist) ) return;
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		
		$SiteName	= $app->getCfg('sitename');			
		$userinfo   = AlphaUserPointsHelper::getUserInfo( $referrerid );		
		$fromIdUddeim = intval($params->get('fromIdUddeim'));
			
		if ( !$userinfo->block && $fromIdUddeim>0 )
		{
			require_once( JPATH_SITE .DS . "components" . DS . "com_uddeim" . DS . "uddeim.api.php" );
			
			if ( $assignpoints>0 ) 
			{
				$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG'), $SiteName, AlphaUserPointsHelper::getFPoints($assignpoints), AlphaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );	
			}
			 elseif ( $assignpoints<0 )
			{
				$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_REMOVE_POINTS'), $SiteName, AlphaUserPointsHelper::getFPoints(abs($assignpoints)), AlphaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );
			}
			$uddeimapi = new uddeIMAPI();
			$uddeimapi->sendNewMessage( $fromIdUddeim, $userinfo->userid , $message );
		}		
	}
	
	public function sendUddeimWinnerNotification ( $referrerid, $assignpoints, $newtotal )
	{
		$app = JFactory::getApplication();
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );	
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		
		if ( !$referrerid || $referrerid=='GUEST') return;
		
		// check if component installed
		$uddeim_exist = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_uddeim'.DS.'admin.uddeimlib15.php';
		if ( !file_exists ($uddeim_exist) ) return;
			
		$FromName	= $app->getCfg('fromname');		
		$userinfo 	= AlphaUserPointsHelper::getUserInfo( $referrerid );
		$name 		= $userinfo->name;
		$fromIdUddeim = intval($params->get('fromIdUddeim'));

		if ( !$userinfo->block && $fromIdUddeim>0 )
		{	
			require_once( JPATH_SITE .DS . "components" . DS . "com_uddeim" . DS . "uddeim.api.php" );
				
			// send notification to winner
			$message = sprintf ( JText::_('AUP_EMAILWINNERNOTIFICATION_MSG_USER'), $name, AlphaUserPointsHelper::getFPoints($newtotal) );		
			$uddeimapi = new uddeIMAPI();
			$uddeimapi->sendNewMessage( $fromIdUddeim, $userinfo->userid , $message );
		}
	
	}

	
	public function sendwinnernotification ( $referrerid, $assignpoints, $newtotal, $emailadmins='' )
	{
		$app = JFactory::getApplication();
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );	
		
		if ( !$referrerid || $referrerid=='GUEST') return;		
		$MailFrom	= $app->getCfg('mailfrom'); 	
		$FromName	= $app->getCfg('fromname');		
		$userinfo 	= AlphaUserPointsHelper::getUserInfo( $referrerid );
		$name 		= $userinfo->name;
		$email	 	= $userinfo->email;
		if ( !$userinfo->block )
		{				
			// send notification to winner
			$subject = JText::_('AUP_EMAILWINNERNOTIFICATION_SUBJECT_MSG_USER');
			$message = sprintf ( JText::_('AUP_EMAILWINNERNOTIFICATION_MSG_USER'), $name, AlphaUserPointsHelper::getFPoints($newtotal) );			
			JUtility::sendMail( $MailFrom, $FromName, $email, $subject, $message );			
			// send notification to administrators...		
			if ( $emailadmins )
			{
				$subject = JText::_('AUP_EMAILWINNERNOTIFICATION_SUBJECT_MSG_ADMIN');
				$message = sprintf ( JText::_('AUP_EMAILWINNERNOTIFICATION_MSG_ADMIN'), $name, AlphaUserPointsHelper::getFPoints($newtotal) );				
				JUtility::sendMail( $MailFrom, $FromName, $emailadmins, $subject, $message );
			}
		}
	
	}

	public function getCurrentTotalPoints ( $referreid='', $userid=0, $formatted=0 )
	{	
	
		$db	   = JFactory::getDBO();
		$currenttotalpoints = 0;

		if ( $referreid!='' ) 
		{
			$query = "SELECT points FROM #__alpha_userpoints WHERE `referreid`='".$referreid."' AND `blocked`='0'";
			$db->setQuery( $query );
			$currenttotalpoints = $db->loadResult();
		} elseif ( $userid>=1 ) {
			$query = "SELECT points FROM #__alpha_userpoints WHERE `userid`='".intval($userid)."' AND `blocked`='0'";
			$db->setQuery( $query );
			$currenttotalpoints = $db->loadResult();
		} else {
			return;
		}
		
		if ( $formatted ) $currenttotalpoints = AlphaUserPointsHelper::getFPoints($currenttotalpoints);
		
		return $currenttotalpoints;		
	}
	
	public function operationIsFeasible ( $referreid='', $numpoints )
	{
		if ( !$referreid ) return;
		$currentpoints = AlphaUserPointsHelper::getCurrentTotalPoints ( $referreid );
		$newtotal = $currentpoints + $numpoints;
		if ( $newtotal >=0 ) 
		{
			return true;
		}
		else
		{
			return false;
		}	
	}
	
	public function checkMaxDailyPoints( $referreid, $numpoints, $plugin_function='' )
	{	
		if ( !$referreid ) return;
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );
		
		// except for raffles, coupons code, bonus points, custom rule, profile etc...
		switch ( $plugin_function ) {
			case 'sysplgaup_raffle':
			case 'sysplgaup_couponpointscodes':
			case 'sysplgaup_buypointswithpaypal':
			case 'sysplgaup_bonuspoints':
			case 'sysplgaup_custom':
			case 'sysplgaup_happybirthday':
			case 'sysplgaup_profilecomplete':
			case 'sysplgaup_uploadavatar':
				return $numpoints;
				break;			
			default:
				$maxdailypoints = $params->get('limit_daily_points');		
		}
		
		if ( $maxdailypoints )
		{
			$dateofday = date("Y-m-d"); 
			
			$db	   = JFactory::getDBO();
				
			// Except Raffle rule...
			$query = "SELECT id FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_raffle'";
			$db->setQuery( $query );
			$idraffle = $db->loadResult();
			
			// select only positive points earned per day
			$query = "SELECT SUM(points) FROM #__alpha_userpoints_details WHERE `referreid`='".$referreid."' AND `points` >= '1' AND `insert_date` LIKE '".$dateofday."%' AND `rule`!='".$idraffle."'";
			$db->setQuery( $query );
			$totaldailypoints = $db->loadResult();
			
			$sptotal = $totaldailypoints + $numpoints;
			
			if ( $sptotal > $maxdailypoints ) 
			{
				$sbtotal = $sptotal - $maxdailypoints;
				$numpoints = $numpoints - $sbtotal;
			}
			if ($numpoints<0) $numpoints=0;
		}		
		
		return $numpoints;
	
	}
	
	public function getUserRank ( $referrerid='', $userid=0 )
	{	
		if ( !$referrerid && !$userid ) return;	
		$db	   = JFactory::getDBO();		
		$query = "SELECT lv.*, aup.leveldate FROM #__alpha_userpoints_levelrank AS lv, #__alpha_userpoints AS aup WHERE lv.id=aup.levelrank AND (aup.referreid='".$referrerid."' OR aup.userid='".$userid."')";
		$db->setQuery( $query );
		$userrankinfo = $db->loadObjectList();
		return @$userrankinfo[0];
		
		// detail/explain
		// return $userrankinfo->id
		// return $userrankinfo->rank (name of rank)
		// return $userrankinfo->description (description of rank)		
		// return $userrankinfo->levelpoints (level points to reach this rank)
		// return $userrankinfo->typerank (return 0)
		// return $userrankinfo->icon (name file icon)
		// return $userrankinfo->image (name file image)
	}
	
	public function getUserMedals ( $referrerid='', $userid=0 )
	{	
		if ( !$referrerid && !$userid ) return;	
		$db	   = JFactory::getDBO();
		$medals = "SELECT m.id, m.medaldate, m.reason, lv.rank, lv.description, lv.icon, lv.image "
				. "\nFROM #__alpha_userpoints_medals AS m, #__alpha_userpoints_levelrank AS lv, #__alpha_userpoints AS aup "
				. "\nWHERE m.medal=lv.id AND aup.id=m.rid AND (aup.referreid='".$referrerid."' OR aup.userid='".$userid."') "
				. "\nORDER BY m.medaldate DESC";
		$db->setQuery( $medals );
		$medalslistuser = $db->loadObjectList();
		
		return @$medalslistuser;
		
		// detail/explain
		// sample -> for each ( $medalslistuser as medallistuser ) {
		// return $medallistuser->id
		// return $medallistuser->rank (name of medal)
		// return $medallistuser->description (reason for awarded)		
		// return $medallistuser->icon (name file icon)
		// return $medallistuser->image (name file image)
	}
	
	public function checkRankMedal( $referrerid='', $rule_id=0 ) 
	{		
		$db	   = JFactory::getDBO();
		
		$jnow		= JFactory::getDate();
		$nowdate	= $jnow->toMySQL();
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('alphauserpoints');
		
		$query = "SELECT COUNT(*) FROM #__alpha_userpoints_levelrank WHERE `levelpoints`>'0'";
		$db->setQuery( $query );
		$rankmedalexist = $db->loadResult();
		
		// check level/rank/ medals by level points
		if ( !$rankmedalexist || !$referrerid ) return;
		
		$userinfo = AlphaUserPointsHelper::getUserInfo ( $referrerid );
		// $userinfo->rid = id of table #__alpha_userpoints
		$rid = $userinfo->rid;

		$now = $jnow->toFormat('%Y-%m-%d');
		
		// check if current rule is assigned to a rank or medal
		$currentpoints = $userinfo->points;		

		// check current rank to reuse after	
		$query = "SELECT levelrank FROM #__alpha_userpoints WHERE referreid = '".$userinfo->referreid."'";
		$db->setQuery( $query );
		$oldrank = $db->loadResult();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');				
		$row = JTable::getInstance('userspoints');

		// check level/rank
		$query = "SELECT * FROM #__alpha_userpoints_levelrank " .
		         "WHERE `levelpoints`>0 AND `typerank`='0' ORDER BY `levelpoints` ASC";
		$db->setQuery( $query );
		$ranks = $db->loadObjectList();
		
		$previousrank = 0;
		
		for ($i=0, $n=count( $ranks ); $i < $n; $i++)
		{
			$rank 	= $ranks[$i];
			
			if ( $rank->ruleid>0 ) {
				// recalculate actual total points for this rule
				$query = "SELECT SUM(points) FROM #__alpha_userpoints_details " .
						 "WHERE rule='".$rank->ruleid."' AND approved='1' AND referreid='$referrerid' AND (`expire_date`>'$nowdate' OR `expire_date`='0000-00-00 00:00:00')";
				$db->setQuery( $query );
				$currentpoints = $db->loadResult();
			} else $currentpoints = $userinfo->points;
			
			if ( $currentpoints < $rank->levelpoints )
			{
				// update user rank into alpha_userpoints table
				$row->load( $rid );
				$row->levelrank = $previousrank;
				$row->leveldate = $now;				
				$db->updateObject( '#__alpha_userpoints', $row, 'id' );
				break;
			}
			 else
			{
				// update new user rank into alpha_userpoints table
				$row->load( $rid );
				$row->levelrank = $rank->id;
				$row->leveldate = $now;				
				$db->updateObject( '#__alpha_userpoints', $row, 'id' );	
			}			
			
			$previousrank = $rank->id;
		}
	
		// check medal(s)
		$query = "SELECT * FROM #__alpha_userpoints_levelrank " .
				 "WHERE `levelpoints`>0 AND `typerank`='1' ORDER BY `levelpoints` DESC";
		$db->setQuery( $query );
		$medals = $db->loadObjectList();

		for ($i=0, $n=count( $medals ); $i < $n; $i++)
		{
			$medal = $medals[$i];
			
			if ( $medal->ruleid>0 ) {
				// recalculate
				$query = "SELECT SUM(points) FROM #__alpha_userpoints_details " .
						 "WHERE rule='".$medal->ruleid."' AND approved='1' AND referreid='$referrerid' AND (`expire_date`>'$nowdate' OR `expire_date`='0000-00-00 00:00:00')";
				$db->setQuery( $query );
				$currentpoints = $db->loadResult();
			} else $currentpoints = $userinfo->points;
		
			if ( $medal->levelpoints <= $currentpoints )
			{			
				// check if user medal not already awarded
				$query = "SELECT COUNT(*) FROM #__alpha_userpoints_medals WHERE `rid`='".$rid."' AND `medal`='".$medal->id."'";
				$db->setQuery( $query );
				$alreadyexist = $db->loadResult();
				if ( !$alreadyexist ) 
				{
					// add medal
					$query = "INSERT INTO #__alpha_userpoints_medals (`id`, `rid`, `medal`, `medaldate`, `reason`) "
							. "VALUES ( '', '".$rid."', '".$medal->id."', '".$now."', '".$medal->description."');";
					$db->setQuery( $query );
					$db->query();					
					
					// load external plugins
					$results = $dispatcher->trigger( 'onUnlockMedalAlphaUserPoints', array(&$userinfo, $medal->rank, $medal->image, $medal->description ) );
		
					break;
				}				
			}
			elseif ( $medal->levelpoints > $currentpoints )
			{			
				// check if user medal not already awarded
				$query = "SELECT COUNT(*) FROM #__alpha_userpoints_medals WHERE `rid`='".$rid."' AND `medal`='".$medal->id."'";
				$db->setQuery( $query );
				$allreadyexist = $db->loadResult();
				if ( $allreadyexist ) //if he has, we delete it as he should not have it
				{
					// delete medal					
					$query = "DELETE FROM #__alpha_userpoints_medals WHERE `rid`='".$rid."' AND `medal`='".$medal->id."'";
					$db->setQuery( $query );
					$db->query();
					break;
				}
			}			
			
		}
		
		// check new rank for JomSocial Activity Stream
		$query = "SELECT levelrank FROM #__alpha_userpoints WHERE referreid = '$userinfo->referreid'";
		$db->setQuery( $query );
		$newrrank = $db->loadResult();

		$query = "SELECT rank, description, image, levelpoints FROM #__alpha_userpoints_levelrank WHERE id = '".$newrrank."'";
		$db->setQuery( $query );
		//$rankdetails = $db->loadObjectList();			
		$rankdetails = $db->loadObject();		
		
		if( $oldrank !== $newrrank )
		{		
			// load external plugins
			$results = $dispatcher->trigger( 'onGetNewRankAlphaUserPoints', array(&$userinfo, $rankdetails->rank, $rankdetails->image, $rankdetails->description ) );
		}
	
	}
	
	public function getNextUserRank ( $referrerid='', $userid=0, $currentrank )
	{	
		if ( !$referrerid && !$userid ) return;
		//get all ranks info order by levelpoints	
		$db	   = JFactory::getDBO();		
		$query = "SELECT * FROM #__alpha_userpoints_levelrank WHERE `typerank`=0 ORDER BY `levelpoints` ASC;";
		$db->setQuery( $query );
		$nextrankinfo = $db->loadObjectList();
		//create a single level index array
		$rankindex = array();
		foreach ($nextrankinfo as $onerank) {
			//copy just rank to search for index
			$rankindex[]=$onerank->rank;
		}
		//find the index of the current rank
		$index = array_search( $currentrank, $rankindex );
		//increase index to next
		$index = $index + 1;

		return @$nextrankinfo[$index];
		

		// detail/explain
		// return $nextrankinfo->id
		// return $nextrankinfo->rank (name of rank)
		// return $nextrankinfo->description (description of rank)		
		// return $nextrankinfo->levelpoints (level points to reach this rank)
		// return $nextrankinfo->typerank (return 0)
		// return $nextrankinfo->icon (name file icon)
		// return $nextrankinfo->image (name file image)
	}
	
	public function getAupLinkToProfil( $userid, $Itemid='', $xhtml=true )
	{
		if ( !$userid ) return;
		
		($Itemid!='') ? $theItemid = "&amp;Itemid=".$Itemid : $theItemid = AlphaUserPointsHelper::getItemidAupProfil();
		
		$profile 	  = AlphaUserPointsHelper::getUserInfo('', $userid);
		$linktoprofil = "index.php?option=com_alphauserpoints&amp;view=account&amp;userid=" . $profile->referreid . $theItemid;
		$linktoprofil = JRoute::_($linktoprofil, $xhtml);
		return $linktoprofil;
		
		// USAGE
		// $linktoAUPprofil = AlphaUserPointsHelper::getAupLinkToProfil($userid);   OR  $linktoAUPprofil = AlphaUserPointsHelper::getAupLinkToProfil($userid, $YourItemid); 
		// Think to call and include this API helper.php in your script
	}
	
	public function getAupUsersURL($xhtml = true)
    {
        $db       = JFactory::getDBO();
       
        $query = "SELECT id FROM #__menu WHERE `link`='index.php?option=com_alphauserpoints&view=users' AND `type`='component' AND `published`='1'";
        $db->setQuery( $query );
        $AupItemidUsers = $db->loadResult();
           
        $AupUsersURL = "index.php?option=com_alphauserpoints&amp;view=users&amp;Itemid=" . $AupItemidUsers;
        $AupUsersURL = JRoute::_($AupUsersURL, $xhtml);
           
        return $AupUsersURL;
   
    }
	
	public function getAvatarPath( $userid )
	{
		if(!defined("_AUP_AVATAR_PATH")) {
			define('_AUP_AVATAR_PATH', JURI::root() . 'components'.DS.'com_alphauserpoints'.DS.'assets'.DS.'images'.DS.'avatars'.DS);
		}
		$profile 	 = AlphaUserPointsHelper::getUserInfo('', $userid);
		$avatarPath  = ( $profile->avatar!='' ) ? _AUP_AVATAR_PATH . $profile->avatar : _AUP_AVATAR_PATH . 'generic_gravatar_grey.png' ;

		return avatarPath ;
	}
	
	public function getAvatarLivePath( $userid )
	{
		if(!defined("_AUP_AVATAR_LIVE_PATH")) {
			define('_AUP_AVATAR_LIVE_PATH', JURI::base(true) . '/components/com_alphauserpoints/assets/images/avatars/');
		}		
		$avatarLivePath  = ( $profile->avatar!='' ) ? _AUP_AVATAR_LIVE_PATH . $profile->avatar : _AUP_AVATAR_LIVE_PATH . 'generic_gravatar_grey.png' ;
		return $avatarLivePath;
	}
	
	public function getAupAvatar( $userid, $linktoprofil=0, $width='', $height='', $class='', $otherprofileurl='' )
	{
		if ( !$userid ) return;
		if(!defined("_AUP_AVATAR_PATH")) {
			// prevent call in administrator backend 
			$juriroot = str_replace( DS .'administrator', '', JURI::root());
			define('_AUP_AVATAR_PATH', JURI::root() . 'components'.DS.'com_alphauserpoints'.DS.'assets'.DS.'images'.DS.'avatars'.DS);
		} else $juriroot = $juriroot = str_replace( DS .'administrator', '', JURI::root());
		
		if(!defined("_AUP_AVATAR_LIVE_PATH")) {
			// prevent call in administrator backend 
			$juribase = str_replace('/administrator', '', JURI::base());
			define('_AUP_AVATAR_LIVE_PATH', $juribase . 'components/com_alphauserpoints/assets/images/avatars/');
		} else $juribase = str_replace('/administrator', '', JURI::base());
		
		$startprofil = "";
		$endprofil = "";
		$setwidth  = ( $width !='' ) ? ' width="'.$width.'"'   : '';
		$setheight = ( $height!='' ) ? ' height="'.$height.'"' : '';
		
		$setclass  = ( $class!='' )  ? ' class="'.$class.'"'   : '';
		
		$profile  = AlphaUserPointsHelper::getUserInfo('', $userid);
				
		$avatar 			= ( $profile->avatar!='' ) ? _AUP_AVATAR_LIVE_PATH . $profile->avatar : _AUP_AVATAR_PATH . 'generic_gravatar_grey.png' ;
		
		$avatar 			= JURI::root() . "components/com_alphauserpoints/assets/phpThumb/phpThumb.php?src=".$avatar."&amp;w=" . $width ."&amp;h=" . $height;
		
		$avatar				= '<img src="' . $avatar . '" border="0" alt=""' . $setwidth . $setheight . $setclass . ' />';
		$profileitemid		= '';
		if ( $linktoprofil ) {
			$profileitemid  = '&amp;Itemid=' . AlphaUserPointsHelper::getItemidAupProfil();	
			$profil = ( $otherprofileurl ) ? $otherprofileurl : "index.php?option=com_alphauserpoints&amp;view=account&amp;userid=".$profile->referreid.$profileitemid ;
			$startprofil	= "<a href=\"" . JRoute::_($profil) . "\">";
			$endprofil   	= "</a>";
			$avatar		 	= $startprofil . $avatar . $endprofil;
		}		
		return $avatar;
		
		// USAGE
		// $avatar = AlphaUserPointsHelper::getAupAvatar($userid, [int $linktoprofil], [int $width], [int $height], [string $class], [string $otherprofileurl]);
		// if $linktoprofil set to 1, display avatar with the link to the AUP profil of this user
		// Think to call and include this API helper.php in your script
	}
	
	public function getItemidAupProfil()
	{
		$db	   = JFactory::getDBO();
		
		$query = "SELECT id FROM #__menu WHERE `link`='index.php?option=com_alphauserpoints&view=account' AND `type`='component' AND `published`='1'";
		$db->setQuery( $query );
		$AupItemidProfile = $db->loadResult();
			
		return $AupItemidProfile;
	
	}
	
	public function displayMessageSystem($message) 
	{
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_($message ) );
		return true;
	}
	
	public function getAupVersion()
	{
		require_once ( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'assets' . DS . 'includes' . DS . 'version.php' );
		return _ALPHAUSERPOINTS_NUM_VERSION;
	}
	
	// get list of activities 
	// $params  $type = all | positive | negative
	// $params  $userid = all or unique userID
	// $params  $limit = int (0 by default)  
	// example-1 -> --------------------------------------------------------------------------
	// example-1 -> $listActivity = AlphaUserPointsHelper::getListActivity('all', 'all');
	// example-1 SAME AS -> $listActivity = AlphaUserPointsHelper::getListActivity();
	// example-1 -> show all acivities with pagination, positive and negative points of activity for all users
	// ------------------------------------------------------------------------------------
	// example-2 -> --------------------------------------------------------------------------
	// example-2 -> $user =  JFactory::getUser();
	// example-2 -> $userID = $user->id;
	// example-2 -> $listActivity = AlphaUserPointsHelper::getListActivity('positive', $userID, 20);
	// example-2 -> show only positive points of activity for the current user logged in and show only 20 rows of recent activities
	//
	// return an array or $rows
	//
	// list of available fields :
	// insert_date
	// referreid
	// points (of this activity)
	// datareference
	// rule_name
	// plugin_function
	// category	
	
	public function getListActivity($type='all', $userid='all', $numrows=0)
	{
		$app = JFactory::getApplication();
		$db	 = JFactory::getDBO();
		
		$nullDate	= $db->getNullDate();
		$date 		= JFactory::getDate();
		$now  		= $date->toMySQL();
		
		$typeActivity = "";
		$userID = "";
		
		switch ( $type )
		{			
			case 'positive':
				$typeActivity = " AND a.points > 0";		
			case 'negative':
				$typeActivity = " AND a.points <= 0";
				break;
			case 'all':
			default:
				$typeActivity = "";
		}
		
		switch ( $userid )
		{	
			case ( $userid>0 && $userid!='all' ):
				$userID = " AND aup.userid='".$userid."'";
				break;
			case ( $userid=0 ):
				return;
			case 'all':
			default:
				$userID = "";
		}
		
		$query = "SELECT a.insert_date, a.referreid, a.points, aup.points AS last_total_points, a.datareference, r.rule_name, r.plugin_function, r.category"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r"
			   . " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND (a.expire_date>='".$now."' OR a.expire_date='0000-00-00 00:00:00') AND r.id=a.rule"
			   . $typeActivity
			   . $userID 
			   . " ORDER BY a.insert_date DESC"
			   ;

		
		switch ( $numrows )
		{				
			case ($numrows>0):
				$query .= " LIMIT ". intval($numrows);
				$db->setQuery( $query );
				$result = $db->loadObjectList();
				return $result;
				break;			
			case 0:
			default:
				jimport( 'joomla.application.component.model' );
				// Get the pagination request variables
				$limit = JRequest::getVar('limit', $app->getCfg('list_limit'), '', 'int');
				$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
				// In case limit has been changed, adjust limitstart accordingly
				$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);		
				$total = @JModel::_getListCount($query);
				$result = JModel::_getList($query, $limitstart, $limit);
				return array($result, $total, $limit, $limitstart);
		}
	
	}
	
	 // Output format for Points
	 public function getFPoints( $points )
	 {
	 
		// get params definitions
		$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
		$formatPoints = $params->get( 'formatPoints', '0' );
		
		switch( $formatPoints ){
			case "1":
				$fpoints = number_format($points, 2, '.', ','); // english format
				break;
			case "2":
				$fpoints = number_format($points, 2, ',', '');
				break;
			case "3":
				$fpoints = number_format($points, 2, ',', ' '); // french format
				break;
			case "4":
				$fpoints = number_format($points, 0);
				break;
			case "5":
				$fpoints = number_format($points, 0, '', ' ');
				break;
			case "6":
				$fpoints = number_format($points, 0, '', ',');
				break;
			case "7":				
				$fpoints = number_format(floor($points), 0);
				break;				
			case "0":
			default:
				$fpoints = $points; 
		}		
	
	 	return $fpoints;
	}

	
}
?>