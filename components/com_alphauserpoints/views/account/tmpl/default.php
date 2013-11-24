<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2013 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined("_AUP_IMAGE_LIVE_PATH")) {
	define('_AUP_IMAGE_LIVE_PATH', JURI::base(true) . '/components/com_alphauserpoints/assets/images/');
}

// current user
$user =  JFactory::getUser();
$document = JFactory::getDocument();

$com_params = JComponentHelper::getParams( 'com_alphauserpoints' );
$_profilelink	= $com_params->get('linkToProfile');

$line=1; // use for alternate color
$db = JFactory::getDBO();
$nullDate = $db->getNullDate();
$nullDate2 = "0000-00-00";

// update profile views counter
if ( $this->referreid!=@$_SESSION['referrerid'] ) {	
	_updateProfileViews ( $this->referreid );
	$profilviews = $this->userinfo->profileviews+1;
} else $profilviews = $this->userinfo->profileviews;

//DEBUG
/*
		$userid 			= AlphaUserPointsHelper::getUserID( $referrerid );
		$authorizedLevels 	= JAccess::getAuthorisedViewLevels($user->id);		
		print_r($authorizedLevels);
		jimport('joomla.user.helper');
		echo '<br /><br />';
		$result = array_keys(JUserHelper::getUserGroups($user->id));
		print_r($result);
		echo '<br />';
		$result = JUserHelper::getUserGroups($user->id);
		$actualnamegroup = end($result);
		echo $actualnamegroup;
		//echo print_r($actualnamegroup);
*/

if ( $this->params->get( 'show_page_title', 1 ) ) { 
	?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->params->get( 'page_title' ); ?>
	</div>
	<?php
}

echo '<div id="profile-aup">';

if ( @$this->userrankinfo ) { 
	if ( $this->userrankinfo->image ) {
		$pathimage = JURI::root() . "components/com_alphauserpoints/assets/images/awards/large/";
		$userrankimg1 = $pathimage.$this->userrankinfo->image;
		$userrankimg = JURI::root(). "components/com_alphauserpoints/assets/phpThumb/phpThumb.php?src=".$userrankimg1."&amp;h=100";
		?>
		<div style="float:right;padding: 0 0 0 10px;">
		<img src="<?php echo $userrankimg ; ?>" alt=""  height="100" />
		</div>
		<?php 
		} 
}

if ( $this->avatar ) { 
	?>
		<div style="float:right;padding: 0 10px 0 10px;">
		<?php echo $this->avatar ; ?>
		</div>
	<?php
} 

echo '<h1 class="aboutme">' . $this->myname . '</h1>';
if ( $this->params->get( 'showSystemProfileComplete', 1) && $this->referreid==@$_SESSION['referrerid'] ) {
	echo _getProgressProfile( $this->userinfo, 'bar' );
}
echo "<b>" . JText::_('AUP_USERNAME') . " : " . $this->myusername . "</b><br />";
echo "<b>ID</b> : " . $this->referreid . "<br />";
echo "<b>" . JText::_('AUP_MYPOINTS') . "</b> : " . getFormattedPoints( $this->currenttotalpoints );
if ( @$this->userrankinfo->rank ) {
 	echo " (". $this->userrankinfo->rank . ")";
}
echo "<br />";
echo "<b>" . JText::_('AUP_LASTUPDATE') . "</b> : " . JHTML::_('date', $this->lastupdate, JText::_('DATE_FORMAT_LC2') ) . "<br />";
echo "<b>" . JText::_('AUP_MEMBER_SINCE') . "</b> : " . JHTML::_('date', $this->userinfo->registerDate, JText::_('DATE_FORMAT_LC3') ) . "<br />";
echo "<b>" . JText::_('AUP_LAST_ONLINE') . "</b> : " . nicetime( $this->userinfo->lastvisitDate ) . "<br />";
if ( $this->referraluser!='' ) {
	if ( $this->params->get( 'show_links_to_users', 1) ){
		$_user_info = AlphaUserPointsHelper::getUserInfo ( $this->referraluser );
		$linktoprofilreferral = getProfileLink( $_profilelink, $_user_info );

		$linktoprofilreferral =  "<a href=\"" . JRoute::_($linktoprofilreferral) . "\">" . $this->referralname . "</a>";
	} else $linktoprofilreferral = $this->referralname;
	echo "<b>" . JText::_('AUP_MYREFRERRALUSER') . "</b> : " ;
	echo $this->referraluser . " (" . $linktoprofilreferral . ")";
	echo "<br />";
}
if ( $this->params->get( 'show_links_to_users', 1) ){
	echo "<b>" . JText::_('AUP_PROFILE_VIEWS') . "</b> : " . $profilviews . "<br />";
}

$referrer_link = getLinkToInvite( $this->referreid, $this->cparams->get('systemregistration')  );
?><br />
<input type="text" size="42" name="referrer_link" id="referrer_link" onfocus="select();" readonly="readonly" class="inputbox" value="<?php echo $referrer_link; ?>" />
<br /><span class="small"><?php echo JText::_( 'AUP_INVITATION_LINK' ); ?></span>
<?php 
if( $this->params->get( 'showQRCode', 1) ) {
	echo '<div id="QRcodeInvite">'; 
	require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'assets'.DS.'barcode'.DS.'BarcodeQR.php');
	$pathQRtemp = JPATH_SITE.DS.'tmp'.DS.$this->referreid.".png";
	$qrcode = new BarcodeQR();
	$qrcode->url( $referrer_link );
	$qrcode->draw( 150, $pathQRtemp );
	echo '<img src="'.JURI::base().'tmp/'.$this->referreid.'.png" alt="" />';
	echo '</div>'; 
}

// Integration Uddeim
// ******************
if ( $this->referreid==@$_SESSION['referrerid'] && $this->enabledUDDEIM && $user->id==$this->userinfo->userid  ) {
	require_once (JPATH_SITE . DS . 'components' . DS . 'com_uddeim' . DS . 'uddeim.api.php');
	require_once (JPATH_COMPONENT.DS.'helpers'.DS.'uddeim_alert.php');
	require_once (JPATH_COMPONENT.DS.'helpers'.DS.'uddeim_mailbox.php');
}
// end integration Uddeim

// CROP AVATAR
if ( ($this->referreid==@$_SESSION['referrerid']) && ($user->id==$this->userinfo->userid) && $this->params->get( 'showCropAvatar') && $this->useAvatarFrom=='alphauserpoints' && $this->user_info->avatar ) {
?>
	<img id="cropAvatar" src="<?php echo JURI::root(). 'components/com_alphauserpoints/assets/images/avatars/' . $this->user_info->avatar ; ?>" alt="cropping" />
<?php
	echo '<p class="small">' . JText::_('AUP_CLICK_MASK'). '<br />'; echo JText::_('AUP_SHIFT_RESIZE'). '<br />'; echo JText::_('AUP_DOUBLE_CLICK_ON_RESIZER'). '</p>';
}

// start About Me information
if ( $_profilelink=='' )
{
	//echo '<h2 class="aboutme">' . JText::_('AUP_ABOUT_ME') . '</h2>' ;
	echo "<br />" . $this->slider->startPane("slider-pane");
		// basic information (used by rule for profile complete)
		echo $this->slider->startPanel(JText::_( 'AUP_BASIC_INFORMATION' ), "profile-slider-basic" );
					
			if ( $this->userinfo->birthdate!=$nullDate2 ) {
				if ( $this->params->get( 'showBirthday', 1) ){	
					echo "<b>" . JText::_('AUP_BIRTHDAY') . "</b> : " ;		
					echo JHTML::_('date', $this->userinfo->birthdate, JText::_('DATE_FORMAT_LC') );
				} else {
					echo "<b>" . JText::_('AUP_AGE') . "</b> : " ;		
					echo determine_age( $this->userinfo->birthdate );
				}
			} else echo "<b>" . JText::_('AUP_AGE') . "</b> : " . JText::_('AUP_THIS_INFORMATION_HAS_NOT_BEEN_PROVIDED');
			echo "<br />";
	
			echo "<b>" . JText::_('AUP_GENDER') . "</b> : " ;
			if ( $this->userinfo->gender ) {
				if ( $this->userinfo->gender== 1) {
					echo  JText::_('AUP_MALE');
				} elseif ( $this->userinfo->gender== 2) {
					echo  JText::_('AUP_FEMALE');
				}
			} else echo JText::_('AUP_THIS_INFORMATION_HAS_NOT_BEEN_PROVIDED');
			echo "<br />";
			
			if ( $this->userinfo->city ) {
				echo "<b>" . JText::_('AUP_LOCATION') . "</b> : " ;
				if ( $this->userinfo->city ) {
					echo JText::_( $this->userinfo->city );	
				}
				echo "<br />";
			}
			
			echo "<b>" . JText::_('AUP_ABOUT_ME') . "</b> : " ;
			if ( $this->userinfo->aboutme ) {
				echo JText::_( $this->userinfo->aboutme );	
			} else echo JText::_('AUP_THIS_INFORMATION_HAS_NOT_BEEN_PROVIDED');
			echo "<br />";
	
		echo $this->slider->endPanel();	
		
		// contact information
		if ( $this->params->get( 'showContactInformation', 1) && ( $this->userinfo->shareinfos || $this->referreid==@$_SESSION['referrerid'] ) ) {
			echo $this->slider->startPanel(JText::_( 'AUP_CONTACT_INFORMATION' ), "profile-slider-contact" );
			
				echo "<b>" . JText::_('AUP_MOBILE_PHONE') . "</b> : " ;	
				if ( $this->userinfo->phonemobile ) {		
					echo  $this->userinfo->phonemobile;
				}
				echo "<br />";
				
				echo "<b>" . JText::_('AUP_HOME_PHONE') . "</b> : " ;	
				if ( $this->userinfo->phonehome ) {		
					echo  $this->userinfo->phonehome;
				}
				echo "<br />";
				
				echo "<b>" . JText::_('AUP_ADDRESS') . "</b> : " ;	
				if ( $this->userinfo->address ) {		
					echo  $this->userinfo->address;
				}
				echo "<br />";
	
				echo "<b>" . JText::_('AUP_ZIPCODE') . "</b> : " ;	
				if ( $this->userinfo->zipcode ) {		
					echo  $this->userinfo->zipcode;
				}
				echo "<br />";
	
				echo "<b>" . JText::_('AUP_CITY') . "</b> : " ;	
				if ( $this->userinfo->city ) {		
					echo  $this->userinfo->city;
				} else echo JText::_('AUP_THIS_INFORMATION_HAS_NOT_BEEN_PROVIDED');
				echo "<br />";
	
				echo "<b>" . JText::_('AUP_COUNTRY') . "</b> : " ;
				if ( $this->userinfo->country ) {		
					echo  $this->userinfo->country;
				}
				echo "<br />";
	
				echo "<b>" . JText::_('AUP_WEBSITE') . "</b> : " ;	
				if ( $this->userinfo->website ) {		
					echo  $this->userinfo->website;
				}
				echo "<br />";
			
			echo $this->slider->endPanel();
		}
		
		// Education information
		if ( $this->params->get( 'showEducationInformation', 1) && ( $this->userinfo->shareinfos || $this->referreid==@$_SESSION['referrerid'] ) ) {
			echo $this->slider->startPanel(JText::_( 'AUP_EDUCATION_INFORMATION' ), "profile-slider-education" );
			
				echo "<b>" . JText::_('AUP_COLLEGE_UNIVERSITY') . "</b> : " ;
				if ( $this->userinfo->education ) {		
					echo  $this->userinfo->education;
				}
				echo "<br />";
				
				echo "<b>" . JText::_('AUP_CLASS_YEAR') . "</b> : " ;	
				if ( $this->userinfo->graduationyear ) {		
					echo  $this->userinfo->graduationyear;
				}
				echo "<br />";
				
				echo "<b>" . JText::_('AUP_JOB') . "</b> : " ;	
				if ( $this->userinfo->job ) {		
					echo  $this->userinfo->job;
				}
				echo "<br />";
				
				
			echo $this->slider->endPanel();
		}	
		
		// Social Networking Information
		if ( $this->params->get( 'showSocialInformation', 1) && ( $this->userinfo->shareinfos || $this->referreid==@$_SESSION['referrerid'] ) ) {
			echo $this->slider->startPanel(JText::_( 'AUP_SOCIAL_NETWORKING_INFORMATION' ), "profile-slider-social" );
				echo "<br />";
				if ( $this->userinfo->facebook ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/facebook.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->facebook;
					echo "<br /><br />";
				}
				
				if ( $this->userinfo->twitter ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/twitter.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->twitter;
					echo "<br /><br />";
				}
	
				if ( $this->userinfo->aim ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/aim.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->aim;
					echo "<br /><br />";
				}
				
				if ( $this->userinfo->yim ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/yim.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->yim ;
					echo "<br /><br />";
				}
				
				if ( $this->userinfo->msn ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/msn.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->msn ;
					echo "<br /><br />";
				}
	
				if ( $this->userinfo->icq ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/icq.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->icq ;
					echo "<br /><br />";
				}
				
				if ( $this->userinfo->gtalk ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/gtalk.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->gtalk ;
					echo "<br /><br />";
				}
				
				if ( $this->userinfo->skype ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/skype.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->skype ;
					echo "<br /><br />";
				}
	
				if ( $this->userinfo->xfire ) {
					echo '<img style="padding-right:10px;" src="components/com_alphauserpoints/assets/images/xfire.gif" alt="" align="absmiddle"/>';
					echo  $this->userinfo->xfire ;
					echo "<br /><br />";
				}
			echo $this->slider->endPanel();
		}
		
	echo $this->slider->endPane();
	// end Generale profile information
	
	// if profile not complete ->
	if ( $this->params->get( 'showSystemProfileComplete', 1) && $this->referreid==@$_SESSION['referrerid'] ) {
		echo _getProgressProfile( $this->userinfo, 'message' );
	}
	
	//echo $this->pane->startPane("content-pane");

}

echo $this->pane->startPane("content-pane");

// LATEST ACTIVITY
if ( $this->params->get( 'num_item_activities' )!='0' && $this->rowslastpoints ) {

	echo $this->pane->startPanel(JText::_( 'AUP_LASTACTIVITY' ), "account-panel-activities" );
	
	if ( $this->params->get( 'num_item_activities' )=='all' ) {
		echo '<p>'.$this->pagination->getResultsCounter().'</p>';
	}
	
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left" width="15%">
			<?php echo JText::_('AUP_DATE'); ?>
		</td>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left" width="25%">
			<?php echo JText::_('AUP_ACTION'); ?>
		</td>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="5%">
			<?php echo JText::_('AUP_POINTS'); ?>
		</td>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="15%">
			<?php echo JText::_('AUP_EXPIRE'); ?>
		</td>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left">
			<?php echo JText::_('AUP_DETAIL'); ?>
		</td>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="5%">
			<?php echo JText::_('AUP_APPROVED'); ?>
		</td>
	</tr>
	<?php
	if ( $this->rowslastpoints ) {
		$k = 0;
		foreach ($this->rowslastpoints as $lastpoints) { 	
		?>
			<tr>
				<td class="sectiontableentry<?php echo $k; ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left" width="15%">
					<?php echo JHTML::_('date',  $lastpoints->insert_date,  JText::_('DATE_FORMAT_LC2') ); ?>
				</td>
				<td class="sectiontableentry<?php echo $k; ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left" width="25%">
					<?php echo JText::_($lastpoints->rule_name); ?>
				</td>
				<td class="sectiontableentry<?php echo $k; ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="5%">
					<?php echo getFormattedPoints( $lastpoints->points ); ?>
				</td>
				<td class="sectiontableentry<?php echo $k; ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="15%">
					<?php			
						if ( $lastpoints->expire_date == $nullDate ) {
							echo '-';
						} else {
							echo JHTML::_('date',  $lastpoints->expire_date,  JText::_('DATE_FORMAT_LC') );
						}						 
					 ?>
				</td>
				<td class="sectiontableentry<?php echo $k; ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left">
					<?php				
					switch ( $lastpoints->plugin_function ) {
						case 'sysplgaup_dailylogin':
							echo JHTML::_('date', $lastpoints->datareference, JText::_('DATE_FORMAT_LC1') );
							break;
						case 'plgaup_getcouponcode_vm':
						case 'plgaup_alphagetcouponcode_vm':
						case 'sysplgaup_buypointswithpaypal':
							if ( $this->referreid!=@$_SESSION['referrerid'] ) {
								echo '';
							} else echo $lastpoints->datareference;
							break;
						default:
							echo $lastpoints->datareference;
					}				
					?>
				</td>
				<td class="sectiontableentry<?php echo $k; ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="5%">
					<?php
					$img = ( $lastpoints->approved )? 'tick.png' : 'publish_x.png' ;	
					$alt = ( $lastpoints->approved )? 'AUP_APPROVED' : 'AUP_PENDINGAPPROVAL' ;	 
					 ?>
					 <img src="components/com_alphauserpoints/assets/images/<?php echo $img; ?>" border="0" title="<?php echo JText::_( $alt ); ?>" alt="<?php echo JText::_( $alt ); ?>" />
				</td>
			</tr>
			
		<?php 
			$k = 1 - $k;
		} 
	}
	?>
	</table>
	<?php 
	if ( $this->params->get( 'num_item_activities' )=='all' ) {
		echo '<p align="center">';
		echo $this->pagination->getPagesLinks();
		echo '<br />' . $this->pagination->getPagesCounter();
		echo '</p>';
	}
	?>
	<?php
		// if activities -> allow to download CSV format for owner
		if ( $this->rowslastpoints && $this->referreid==@$_SESSION['referrerid'] && $user->id==$this->userinfo->userid ) {	
			//$linktodownloadactivity = "index.php?option=com_alphauserpoints&amp;view=account&amp;task=downloadactivity&amp;userid=".$this->referreid;
			$linktodownloadactivity = "index.php?option=com_alphauserpoints&amp;view=account&amp;task=downloadactivity&amp;userid=".$user->id;			
			$linktodownloadactivity =  "<img src=\"components/com_alphauserpoints/assets/images/icon_csv.gif\" alt=\"\" />&nbsp;&nbsp;<a href=\"" . JRoute::_($linktodownloadactivity) . "\">" . JText::_('AUP_DOWNLOAD_MY_FULL_ACTIVITY') . "</a>";
			echo  "<br /> " . $linktodownloadactivity;
		}


	echo $this->pane->endPanel();
	
} // end if show activities
	
	if ( $this->medalslistuser && $this->params->get( 'show_tab_medals' ) ) {
		echo $this->pane->startPanel(JText::_( 'AUP_MEDALS' ), "account-panel-medals" );
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">		
		<tr>			
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center" width="15%">&nbsp;
				
			</td>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left" width="20%">
				<?php echo JText::_('AUP_DATE'); ?>
			</td>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left" width="24%">
				<?php echo JText::_('AUP_MEDALS'); ?>
			</td>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="left">
				<?php echo JText::_('AUP_REASON_FOR_AWARD'); ?>
			</td>
		</tr>
		<?php foreach ( $this->medalslistuser as $medaluser ) { ?>
		<tr>
			<td>
			<?php			
				if ( $medaluser->image ) {
				$pathimage = JURI::root() . 'components/com_alphauserpoints/assets/images/awards/large/';
				?>
				<img src="<?php echo $pathimage.$medaluser->image ; ?>" alt="" />	
			<?php }  ?>
			</td>
			<td>
				<?php 
					echo JHTML::_('date',  $medaluser->medaldate,  JText::_('DATE_FORMAT_LC') );
				?>
			</td>
			<td>
				<?php 
					echo JText::_( $medaluser->rank );
				?>
			</td>
			<td>
				<?php 
					echo JText::_( $medaluser->reason );
				?>
			</td>
		</tr>
		<?php } ?>
		</table>		
		<?php	
		
		echo $this->pane->endPanel();
	}
	
	if ( $this->params->get( 'show_tab_statistics', 1 ) ) {
	
	echo $this->pane->startPanel(JText::_( 'AUP_STATISTICS' ), "account-panel-statistics" );
	
	?>
<table class="adminlist" cellpadding="1" width="100%">
	<tr>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="20%"><?php echo JText::_( 'AUP_GENERAL' ); ?></th>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="80%">&nbsp;</td>
	</tr>
	<?php if ( $this->params->get( 'show_community_points', 1 ) ) { ?>
	<tr>
	  <td><?php echo JText::_( 'AUP_TOTAL_COMMUNITY_POINTS' ); ?></td>
	  <td><?php echo getFormattedPoints( $this->totalpoints ); ?></td>
	</tr>
	<?php } ?>
	<?php if ( $this->params->get( 'show_average_age_commnunity', 1 ) && $this->average_age ) { ?>
	<tr>
	  <td><?php echo JText::_( 'AUP_AVERAGE_AGE_COMMUNITY' ); ?></td>
	  <td><?php echo $this->average_age . " " . JText::_( 'AUP_YEARS' ); ?></td>
	</tr>
	<?php } ?>		
	<tr>
	  <td><?php echo JText::_( 'AUP_MYPOINTS' ); ?></td>
	  <td><?php echo getFormattedPoints( $this->currenttotalpoints ); ?></td>
	</tr>	
	<?php if ( $this->params->get( 'show_percentage', 1 ) ) { ?>
	<tr>
	  <td><?php echo JText::_( 'AUP_MY_PERCENTAGE_OF_POINTS' ); ?></td>
	  <td>
	  <?php
	  	if ( $this->totalpoints > 0 ) {
	  		$percent = (($this->currenttotalpoints)/($this->totalpoints))*100;
		} else $percent = 0;
		if ( $percent >= 1 ) {
				echo number_format($percent,1,'.','')."%";
		} elseif ( $percent >= 0 && $percent < 1 ) {
				echo number_format($percent,3,'.','')."%";
		}
	  ?>
	  </td>
	</tr>
	<?php } ?>
</table>
<table class="adminlist" cellpadding="1" width="100%">
	<tr>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="20%"><?php echo JText::_( 'AUP_MY_STATISTICS' ); ?></td>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="40%"><?php echo JText::_( 'AUP_POINTS_EARNED' ); ?></td>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="40%" ><?php echo JText::_( 'AUP_POINTS_SPENT' ); ?></td>
	</tr>
	<tr>
	  <td><?php echo JText::_( 'AUP_SINCE_THE_BEGENNING' ); ?></th>
	  <td><?php echo getFormattedPoints( $this->mypointsearned ); ?></td>
	  <td><?php echo getFormattedPoints( $this->mypointsspent ); ?></td>
	</tr>
	<tr>
	  <td><?php echo JText::_( 'AUP_THIS_MONTH' ); ?></th>
	  <td><?php echo getFormattedPoints( $this->mypointsearnedthismonth ); ?></td>
	  <td><?php echo getFormattedPoints( $this->mypointsspentthismonth ); ?></td>
	</tr>
	<tr>
	  <td><?php echo JText::_( 'AUP_THIS_DAY' ); ?></th>
	  <td><?php echo getFormattedPoints( $this->mypointsearnedthisday ); ?></td>
	  <td><?php echo getFormattedPoints( $this->mypointsspentthisday ); ?></td>
	</tr>
</table>

<?php  if ( $this->params->get( 'show_top10', 1 )) { ?>	
<table class="adminlist" cellpadding="1" width="100%">
	<tr>
	<?php  if ( $this->params->get( 'show_name_cols' )) { ?>	
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="150"><?php echo JText::_( 'AUP_NAME' ); ?></td>
	 <?php } ?> 
	 <?php  if ( $this->params->get( 'show_username_cols', 1 ) || (!$this->params->get( 'show_name_cols' ) && !$this->params->get( 'show_username_cols' ) ) ) { ?>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="120" ><?php echo JText::_( 'AUP_USERNAME' ); ?></td>
	  <?php } ?> 	  
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="100%">&nbsp;</td>
	  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>"><div align="center"><?php echo JText::_( 'AUP_POINTS' ); ?></div></td>
	</tr>
	<?php		
		for ($i=0, $n=count( $this->pointsearned ); $i < $n; $i++)
		{
			$row 	= $this->pointsearned[$i];
			
			if ($i==0) {
				$maxpoints = $row->sumpoints;
				$barwidth = 100;
			}
			else {
				//if ( $maxpoints>0 ) 
				if ( $row->sumpoints > 0 )
				{
					$barwidth = @round(($row->sumpoints * 100) / $maxpoints);
				} 
				else
				{
					$barwidth = 0;
				}
			}
	?>
			<tr>
			<?php  if ( $this->params->get( 'show_name_cols' )) { ?>	
			  <td>
			  <?php 
			  if ( $this->params->get( 'show_links_to_users', 1) ){
				$_user_info = AlphaUserPointsHelper::getUserInfo ( $row->referreid );
				$linktoprofil = getProfileLink( $_profilelink, $_user_info );

				$linktoprofil =  "<a href=\"" . JRoute::_($linktoprofil) . "\">" . $row->name . "</a>";
			  } else $linktoprofil = $row->name;
				
			  	echo $linktoprofil;
			  ?> 
			  </td>
			<?php } ?>
			<?php  if ( $this->params->get( 'show_username_cols', 1 ) || (!$this->params->get( 'show_name_cols' ) && !$this->params->get( 'show_username_cols' ) ) ) { ?>	
			  <td>
			  <?php 
			  if ( $this->params->get( 'show_links_to_users', 1) ){
				$_user_info = AlphaUserPointsHelper::getUserInfo ( $row->referreid );
				$linktoprofil = getProfileLink( $_profilelink, $_user_info );

				$linktoprofil =  "<a href=\"" . JRoute::_($linktoprofil) . "\">" . $row->username . "</a>";
			  } else $linktoprofil = $row->username;
			  
			  echo $linktoprofil;			  
			  ?> 
			  </td>
			  <?php } ?>
			  <td bgcolor="#D0DCDD"><img style="margin-bottom:1px;" src="components/com_alphauserpoints/assets/images/bar.gif" alt="" height="15" width="<?php echo $barwidth;?>%" /></td>
			  <td><div align="right"><?php echo getFormattedPoints( $row->sumpoints );?></div></td>
			</tr>
			<?php
	}
	?>
</table>
<?php } ?>

	<?php
	echo $this->pane->endPanel();
	
	} // end if show statistics
	
	if ( $this->rowsreferrees && $this->params->get( 'show_tab_referrees', 1 )) {
		echo $this->pane->startPanel(JText::_( 'AUP_MYREFERREES' ), "account-panel-myreferrees" );
		?>
		<table class="adminlist" cellpadding="1" width="100%">
			<tr>
			  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="30%">ID</th>
			  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="30%"><?php echo JText::_( 'AUP_USERNAME' ); ?></td>
			  <?php  if ( $this->params->get( 'show_name_cols' )) { ?>	
			  	<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="40%" ><?php echo JText::_( 'AUP_NAME' ); ?></td>
			  <?php } ?>
			</tr>
		<?php
		foreach ( $this->rowsreferrees as $referrees ) {		
			echo "<tr><td>" . $referrees->referreid . "</td>";		
			if ( $this->params->get( 'show_links_to_users', 1) ){
				$_user_info = AlphaUserPointsHelper::getUserInfo ( $referrees->referreid );
				$linktoprofil = getProfileLink( $_profilelink, $_user_info );

				$linktoprofil =  "<a href=\"" . JRoute::_($linktoprofil) . "\">" . $referrees->username . "</a>";
			} else $linktoprofil = $referrees->username;
			echo "<td>" . $linktoprofil . "</td>";			
			if ( $this->params->get( 'show_name_cols' )) {
				echo "<td>" . $referrees->name . "</td>";
			}			
			echo "</tr>";
		}
		
		echo "</table>";
		
		echo $this->pane->endPanel();
	}
	
	// coupons code
	if ( $this->mycouponscode && $this->referreid==@$_SESSION['referrerid'] && $user->id==$this->userinfo->userid ) {
		echo $this->pane->startPanel(JText::_( 'AUP_MYCOUPONS' ), "account-panel-mycoupons" );
		?>
		<table class="adminlist" cellpadding="1" width="100%">
			<tr>
			  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="20%"><?php echo JText::_( 'AUP_DATE' ); ?></th>
			  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="15%"><?php echo JText::_( 'AUP_COUPONSCODE' ); ?></td>
			  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="50%" ><?php echo JText::_( 'AUP_DETAIL' ); ?></td>
			  <td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="15%" ><?php echo JText::_( 'AUP_POINTS' ); ?></td>
			</tr>
		<?php
		foreach ( $this->mycouponscode as $coupon ) {
			$pos = strpos($coupon->keyreference, '##');
			if ( $pos===false ) {
				$realcoupontitle = $coupon->keyreference; 
			} else {
				$realcoupontitle = substr( $coupon->keyreference, 0, $pos );
			}
			echo "<tr><td>" . JHTML::_('date',  $coupon->insert_date,  JText::_('DATE_FORMAT_LC2') ) . "</td>";
			echo "<td>" . $realcoupontitle . "</td>";	
			echo "<td>" . $coupon->datareference . "</td>";
			echo "<td>" . getFormattedPoints( $coupon->points ) . "</td>";
			echo "</tr>";
		}
		
		echo "</table>";

		echo $this->pane->endPanel();
		
	}
		
	// settings
	if ( ($this->referreid==@$_SESSION['referrerid']) && ($user->id==$this->userinfo->userid) && ($this->params->get( 'show_tab_profile')) ) {
		
		echo $this->pane->startPanel(JText::_( 'AUP_EDIT_PROFILE' ), "account-panel-editprofile" );
		?>
		<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="frmProfile" id="frmProfile" class="form-validate">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formu">
			  <tr> 
				<td colspan="2"><h2 class="aboutme"><?php echo JText::_( 'AUP_BASIC_INFORMATION' ) ; ?></h2></td>
			  </tr>
			  <tr> 
				<td width="20%"><?php echo JText::_('AUP_GENDER') ; ?></td>
				<td>
				<fieldset id="jform_methods" class="radio">
				<?php echo $this->lists['gender'] ; ?>
				</fieldset>
				</td>				
			  </tr>
			  <tr>
				<td width="20%"><?php echo  JText::_('AUP_BIRTHDAY') ; ?></td>
				<td>
				<?php 
					// Check format 
					if ( $this->params->get( 'formatBirthday', '%Y-%m-%d' )!='%Y-%m-%d'	)
					{	
						$valdat = $this->birthday;
						// french format
						$checkSeparator = substr($this->params->get( 'formatBirthday' ), -3, 1 ); //        -   or   /
						$cday = substr($valdat, -2, 2);
						$cmonth = substr($valdat, -5, 2);
						$cyear = substr($valdat, -10, 4);
						$birthdayFormated = $cday.$checkSeparator.$cmonth.$checkSeparator.$cyear;
					} else $birthdayFormated = $this->birthday;
					echo JHTML::_('calendar', $birthdayFormated, 'birthdate', 'birthdate', $this->params->get( 'formatBirthday', '%Y-%m-%d' ), array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); 
				?>
				</td>
			  </tr>
			  <tr> 
				<td width="20%"><?php echo  JText::_('AUP_ABOUT_ME') ; ?></td>
				<td><div id="counterAboutMe"><?php echo (250-strlen($this->user_info->aboutme)); ?></div>
				  <textarea name="aboutme" id="aboutme" rows="3" class="inputbox" style="width:100%;" onkeydown="countlength(this.form.aboutme, 'counterAboutMe', '250');" onkeyup="countlength(this.form.aboutme, 'counterAboutMe', '250');" onfocus="showInputField('counterAboutMe', this.form.aboutme, '250');"><?php echo $this->user_info->aboutme; ?></textarea>
				</td>				
			  </tr>			  
			  <?php
				// contact information
				if ( $this->params->get( 'showContactInformation', 1) ) {			  
			  ?>			  
			  <tr> 
				<td colspan="2"><h2 class="aboutme"><?php echo JText::_( 'AUP_CONTACT_INFORMATION' ) ; ?></h2></td>
			  </tr>
			  <tr> 
				<td width="20%"><?php echo  JText::_('AUP_MOBILE_PHONE') ; ?></td>
				<td>
                  <input name="phonemobile" type="text" id="phonemobile" class="inputbox" value="<?php echo $this->user_info->phonemobile; ?>" size="35" /></td>				
			  </tr>
			  <tr> 
				<td width="20%"><?php echo  JText::_('AUP_HOME_PHONE') ; ?></td>
				<td>
                  <input name="phonehome" type="text" id="phonehome" class="inputbox" value="<?php echo $this->user_info->phonehome; ?>" size="35" /></td>				
			  </tr>
			  <tr> 
				<td width="20%"><?php echo  JText::_('AUP_ADDRESS') ; ?></td>
				<td>
                  <input name="address" type="text" id="address" class="inputbox" value="<?php echo $this->user_info->address; ?>" size="35" /></td>				
			  </tr>
			  <tr>
                <td><?php echo  JText::_('AUP_ZIPCODE') ; ?></td>
                <td><input name="zipcode" type="text" id="zipcode" class="inputbox" value="<?php echo $this->user_info->zipcode; ?>" size="6" /></td>
		      </tr>
			  <tr>
                <td><?php echo  JText::_('AUP_CITY') ; ?></td>
                <td><input name="city" type="text" id="city" class="inputbox" value="<?php echo $this->user_info->city; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo  JText::_('AUP_COUNTRY') ; ?></td>
                <td><?php echo _getCountryList($this->user_info->country) ; ?></td>
		      </tr>
			  <tr> 
				<td width="20%"><?php echo  JText::_('AUP_WEBSITE') ; ?></td>
				<td>
                  <input name="website" type="text" id="website" class="inputbox" value="<?php echo $this->user_info->website; ?>" size="35" /></td>				
			  </tr>
			  <?php } ?>
			  <?php
				// contact information
				if ( $this->params->get( 'showEducationInformation', 1) ) {			  
			  ?>			  
			  <tr> 
				<td colspan="2"><h2 class="aboutme"><?php echo JText::_( 'AUP_EDUCATION_INFORMATION' ) ; ?></h2></td>
			  </tr>			  
			  <tr>
                <td><?php echo  JText::_('AUP_COLLEGE_UNIVERSITY') ; ?></td>
                <td><input name="education" type="text" id="education" class="inputbox" value="<?php echo $this->user_info->education; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo  JText::_('AUP_CLASS_YEAR') ; ?></td>
                <td><input name="graduationyear" type="text" class="inputbox" id="graduationyear" value="<?php echo $this->user_info->graduationyear; ?>" size="6" maxlength="4" /></td>
		      </tr>
			  <tr>
                <td><?php echo  JText::_('AUP_JOB_TITLE') ; ?></td>
                <td><input name="job" type="text" id="job" class="inputbox" value="<?php echo $this->user_info->job; ?>" size="35" /></td>
		      </tr>
			  <?php } ?>
			  <?php
				// contact information
				if ( $this->params->get( 'showSocialInformation', 1) ) {			  
			  ?>			  
			  <tr> 
				<td colspan="2"><h2 class="aboutme"><?php echo JText::_( 'AUP_SOCIAL_NETWORKING_INFORMATION' ) ; ?></h2></td>
			  </tr>			  
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'facebook.gif" border="0" alt="Facebook" title="Facebook" />' ; ?></td>
                <td><input name="facebook" type="text" id="facebook" class="inputbox" value="<?php echo $this->user_info->facebook; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'twitter.gif" border="0" alt="Twitter" title="Twitter" />' ; ?></td>
                <td><input name="twitter" type="text" id="twitter" class="inputbox" value="<?php echo $this->user_info->twitter; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'icq.gif" border="0" alt="icq" title="icq" />' ; ?></td>
                <td><input name="icq" type="text" id="icq" class="inputbox" value="<?php echo $this->user_info->icq; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'aim.gif" border="0" alt="AOL" title="AOL" />' ; ?></td>
                <td><input name="aim" type="text" id="aim" class="inputbox" value="<?php echo $this->user_info->aim; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'yim.gif" border="0" alt="Yahoo" title="Yahoo" />' ; ?></td>
                <td><input name="yim" type="text" id="yim" class="inputbox" value="<?php echo $this->user_info->yim; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'msn.gif" border="0" alt="MSN" title="MSN" />' ; ?></td>
                <td><input name="msn" type="text" id="msn" class="inputbox" value="<?php echo $this->user_info->msn; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'skype.gif" border="0" alt="skype" title="skype" />' ; ?></td>
                <td><input name="skype" type="text" id="skype" class="inputbox" value="<?php echo $this->user_info->skype; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'gtalk.gif" border="0" alt="gtalk" title="gtalk" />' ; ?></td>
                <td><input name="gtalk" type="text" id="gtalk" class="inputbox" value="<?php echo $this->user_info->gtalk; ?>" size="35" /></td>
		      </tr>
			  <tr>
                <td><?php echo '<img src="' . _AUP_IMAGE_LIVE_PATH . 'xfire.gif" border="0" alt="xfire" title="xfire" />' ; ?></td>
                <td><input name="xfire" type="text" id="xfire" class="inputbox" value="<?php echo $this->user_info->xfire; ?>" size="35" /></td>
		      </tr>
			  <?php } ?>
			  <tr> 
				<td colspan="2"><h2 class="aboutme"><?php echo JText::_( 'AUP_SHARE_INFO_WITH_COMMUNITY' ) ; ?></h2></td>
			  </tr>			  
			  <tr>
                <td><?php echo  JText::_('AUP_SHARE') ; ?></td>
                <td>
				<fieldset id="jform_methods" class="radio">
				<?php echo $this->lists['shareinfos'] ; ?>
				</fieldset>
				</td>
		      </tr>			  
			  <tr>
				<td width="15%">&nbsp;</td>
				<td>
				<input type="submit" name="Submit" value="<?php echo  JText::_('AUP_SAVE_CHANGE'); ?>" class="button" />			
				</td>
			  </tr>
			</table>
			<input type="hidden" name="option" value="com_alphauserpoints" />
			<input type="hidden" name="task" value="saveprofile" />
			<input type="hidden" name="referreid" value="<?php echo $this->referreid; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->user_info->rid; ?>" />			
			<input type="hidden" name="userid" value="<?php echo $this->user_info->userid; ?>" />
			<input type="hidden" name="upnid" value="<?php echo $this->user_info->upnid; ?>" />
			<input type="hidden" name="points" value="<?php echo $this->user_info->points; ?>" />
			<input type="hidden" name="max_points" value="<?php echo $this->user_info->max_points; ?>" />
			<input type="hidden" name="last_update" value="<?php echo $this->user_info->last_update; ?>" />
			<input type="hidden" name="referraluser" value="<?php echo $this->user_info->referraluser; ?>" />
			<input type="hidden" name="referrees" value="<?php echo $this->user_info->referrees; ?>" />
			<input type="hidden" name="blocked" value="<?php echo $this->user_info->blocked; ?>" />
			<input type="hidden" name="avatar" value="<?php echo $this->user_info->avatar; ?>" />
			<input type="hidden" name="levelrank" value="<?php echo $this->user_info->levelrank; ?>" />
			<input type="hidden" name="leveldate" value="<?php echo $this->user_info->leveldate; ?>" />
			<input type="hidden" name="profileviews" value="<?php echo $this->user_info->profileviews; ?>" />
			<input type="hidden" name="controller" value="account" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
		echo $this->pane->endPanel();
	}
	
	// settings
	if ( $this->referreid==@$_SESSION['referrerid'] && $this->useAvatarFrom=='alphauserpoints') {
	
		// AUP Link Profile
		$db	   = JFactory::getDBO();	
		$query = "SELECT id FROM #__menu WHERE `link`='index.php?option=com_alphauserpoints&view=account' AND `type`='component' AND `published`='1'";
		$db->setQuery( $query );
		$Itemid = $db->loadResult();
		$profileitemid  = '&Itemid=' . $Itemid;
	
		echo $this->pane->startPanel(JText::_( 'AUP_UPLOAD_AVATAR' ), "account-panel-uploadavatar" );
		?>
		<!-- File Upload Form -->
		<form action="<?php echo JURI::base(); ?>index.php?option=com_alphauserpoints&amp;task=uploadavatar&amp;tmpl=component&amp;<?php echo JUtility::getToken();?>=1" id="uploadForm" method="post" enctype="multipart/form-data" >
			<fieldset>
				<legend><?php echo  JText::_('AUP_AVATAR') . ": " . JText::_( 'Upload File' ); ?></legend>
				<fieldset class="actions">
					<input type="file" id="file-upload" name="Filedata" class="inputbox" />					
					<input type="submit" id="file-upload-submit" value="<?php echo JText::_('Start Upload'); ?>" class="button" />
					<span id="upload-clear"></span>
				</fieldset>
				<ul class="upload-queue" id="upload-queue">
					<li style="display: none" />
				</ul>
			</fieldset>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_alphauserpoints&view=account&userid='.@$_SESSION['referrerid'].$profileitemid); ?>" />
			<input type="hidden" name="option" value="com_alphauserpoints" />
			<input type="hidden" name="controller" value="account" />
			<input type="hidden" name="referrerid" value="<?php echo @$_SESSION['referrerid']; ?>" />
			<input type="hidden" name="userid" value="<?php echo @$_SESSION['referrerid']; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
		</form>		
		<?php
		echo $this->pane->endPanel();
	}
	
	// -------------------------------------------------------------------	
	// add new Panel module position
	$renderer = $document->loadRenderer( 'modules' );
	$options = array( 'style' => 'none' );
	echo $renderer->render( 'AlphaUserPoints Profile Tab', $options, null);
	// -------------------------------------------------------------------	
	
	echo $this->pane->endPane();
	
echo "</div>";

	/** 
	*
	*  Provide copyright on frontend
	*  If you remove or hide this line below,
	*  please make a donation if you find AlphaUserPoints useful
	*  and want to support its continued development.
	*  Your donations help by hardware, hosting services and other expenses that come up as we develop,
	*  protect and promote AlphaUserPoints and other free components.
	*  You can donate on http://www.alphaplug.com
	*
	*/	
	getCopyrightNotice ();
?>