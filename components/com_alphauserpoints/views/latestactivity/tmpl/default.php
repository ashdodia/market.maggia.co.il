<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

$user =  JFactory::getUser();

if ( $this->params->get( 'show_page_title', 1 ) ) { 
?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->params->get( 'page_title', JText::_( 'AUP_LASTACTIVITY' ) ); ?>
</div>
<?php
if ( $this->latestactivity ) {

		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');

		echo '<div class="pagination">';		
		echo $this->pagination->getPagesLinks();		
		echo '</div>';
		foreach ($this->latestactivity as $activity) {
		
			$_user_info = AlphaUserPointsHelper::getUserInfo ( $activity->referreid );
			$linktoprofil = getProfileLink( $this->linkToProfile, $_user_info );
			?>
			<div class="container-latest-activity">
			<div class="latest-activity">
			<?php
			if ( $this->useAvatarFrom && $this->params->get( 'showAvatar')) {				
				$avatar = getAvatar( $this->useAvatarFrom, $_user_info, $this->params->get( 'heightAvatar', 48 ) );							
				// add link to profil if need
				$startprofil = "";
				$endprofil   = "";
				echo '<div class="listing-latest-activity-avatar">';
				if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
					$startprofil =  "<a href=\"" . $linktoprofil . "\">";
					$endprofil   = "</a>";
				}					
				echo $startprofil.$avatar.$endprofil;
				echo '</div>';
			}			
			?>
				<div class="listing-latest-activity-info">
					<div class="listing-latest-activity-info-details">
						<div class="listing-latest-activity-rulename">
						<?php
						// insert icon category if exist
						if ( $this->params->get( 'showIconCategory', 1) && $activity->category!='' ) {
							echo _getIconCategoryRule( $activity->category ) . '&nbsp;' ;
						}
						echo JText::_( $activity->rule_name);						
						?>
						</div>
						<div class="listing-latest-activity-datareference">
						<?php
						switch ( $activity->plugin_function ) {
							case 'sysplgaup_dailylogin':
								echo JHTML::_('date', $activity->datareference, JText::_('DATE_FORMAT_LC1') );
								break;
							case 'plgaup_getcouponcode_vm':
							case 'plgaup_alphagetcouponcode_vm':
							case 'sysplgaup_buypointswithpaypal':
								echo  ''; 
								break;
							default:
								echo $activity->datareference;
						}									
						?>
						</div>
						<div>
							<span class="listing-latest-activity-name">
							<?php					
							if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
								$profil =  "<a href=\"" . $linktoprofil . "\">" . $activity->usrname . "</a>";
							} else $profil = $activity->usrname ;
							echo $profil;			 
							 ?>
							 </span>
							<span class="listing-latest-activity-date">
								<span class="editlinktip hasTip" title="<?php echo JText::_( $activity->rule_name); ?>::<?php echo JHTML::_('date', $activity->insert_date, JText::_('DATE_FORMAT_LC2') ); ?>">
								<?php echo nicetime($activity->insert_date); ?>
								</span>
							</span>
						</div>

					</div>
					
					<div class="listing-latest-activity-points">
						<div align="right">
						<?php echo getFormattedPoints( $activity->last_points ) . " " . JText::_( 'AUP_POINTS' ); ?>
						</div>
					</div>				

				</div>
			</div>
			<div class="listing-latest-activity-separate"></div>
			</div>	
			<div class="listing-latest-activity-separate2"></div>		
			<?php			
			}
		}
		echo '<div class="pagination">';		
		echo $this->pagination->getPagesCounter().'<br />';
		echo $this->pagination->getPagesLinks();
		echo '</div>';
}

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