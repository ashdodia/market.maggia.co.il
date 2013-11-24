<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

$colspan = 2;
$user =  JFactory::getUser();

if ( $this->useAvatarFrom ) $colspan++;

?>
<script language="javascript" type="text/javascript">
	function tableOrdering( order, dir, task ) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit( task );
}
</script>
<?php if ( $this->params->get( 'show_page_title', 1 ) ) { ?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->params->get( 'page_title' ); ?>
	</div>
<?php } ?>
<form action="<?php echo JFilterOutput::ampReplace($this->action); ?>" method="post" name="adminForm">
<?php if ($this->params->def('show_limit', 1)) { ?>
<div class="display-limit">
<?php
echo JText::_('AUP_DISPLAY_NUM') .'&nbsp;';
echo $this->pagination->getLimitBox();
?>
</div>
<?php
} 
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="category">
		<thead>
			<?php if ( $this->params->def( 'show_headings', 1 ) ) { ?>
			<tr>
				<?php if ( $this->params->get( 'show_num_cols' ) ) { 
				$colspan++;
				?>			
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="5%" align="center">
					<?php echo JText::_( 'AUP_NUM' ); ?>
				</td>
				<?php } ?>		
				<?php if ( $this->useAvatarFrom ) { ?>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
				<?php echo JText::_( 'AUP_AVATAR' ); ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_rank_icon_cols' ) ) { 
				$colspan++;
				?>				
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="5%" align="center">
				<?php echo JText::_( 'AUP_RANK' ); ?>
				</td>
				<?php } ?>				
				<?php if ( $this->params->get( 'show_nummedals_cols' ) ) { 
				$colspan++;
				?>				
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="5%">
					<?php echo JText::_( 'AUP_MEDALS' ); ?>
				</td>
				<?php } ?>					
				<?php if ( $this->params->get( 'show_name_cols' ) ) { 
				$colspan++;
				?>				
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="18%">
					<?php echo JHTML::_('grid.sort', 'AUP_NAME', 'u.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_username_cols' ) ) { 
				$colspan++;
				?>
					<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="15%">
						<?php echo JHTML::_('grid.sort', 'AUP_USERNAME', 'u.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_referreid_cols' ) ) { 
				$colspan++;
				?>				
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="22%">
					<?php echo JHTML::_('grid.sort', 'AUP_REFERREID', 'aup.referreid', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td>
				<?php } ?>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="10%" align="center">
					<?php echo JHTML::_('grid.sort', 'AUP_POINTS_UPPER', 'aup.points', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td>				
				<?php if ( $this->params->get( 'show_referral_user_cols' ) ) { 
				$colspan++;
				?>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php echo JHTML::_('grid.sort', 'AUP_REFERRALUSER', 'aup.referraluser', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</td>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php } ?>
		<?php
			$k = 0;

			require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
			
			$db = JFactory::getDBO();
			$nullDate 		= $db->getNullDate();

			for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
			{
				$row 	=& $this->rows[$i];
				
				$_user_info   = AlphaUserPointsHelper::getUserInfo ( $row->referreid );
				$linktoprofil = getProfileLink( $this->linkToProfile, $_user_info );
				
				$pathicon = JURI::root() . 'components/com_alphauserpoints/assets/images/awards/icons/';
				$icone = "";
				$nummedals = "";
				
				if ( $row->levelrank && $this->params->get( 'show_rank_icon_cols' ) ) {
					
					$queryicon = "SELECT icon FROM #__alpha_userpoints_levelrank WHERE id=".$row->levelrank;
					$db->setQuery( $queryicon );
					$icon = $db->loadResult();
					if ( $icon ) {
						$icone = '<img src="'.$pathicon . $icon .'" width="16" height="16" border="0" style="vertical-align:middle" alt="" /> ';
					}
				}
				
				if ( $this->params->get( 'show_nummedals_cols' ) ) {
				
					$querymedals = "SELECT COUNT(*) FROM #__alpha_userpoints_medals WHERE rid=".$row->rid;
					$db->setQuery( $querymedals );
					$medals = $db->loadResult();
					if ( $medals ) {
						$nummedals = $medals;
					}
				
				} 			
			?>
			<tr class="cat-list-row<?php echo $k; ?>">
				<?php if ( $this->params->get( 'show_num_cols' ) ) { ?>
				<td class="list-title" align="center">
					<?php echo $i+1+$this->pagination->limitstart; ?>
				</td>
				<?php } ?>
				<?php
					// load avatar if need
					if ( $this->useAvatarFrom ) {
						$avatar = getAvatar( $this->useAvatarFrom, $_user_info, $this->params->get( 'heightAvatar' ) );							
						// add link to profil if need
						$startprofil = "";
						$endprofil   = "";
						if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
							$startprofil =  "<a href=\"" . $linktoprofil . "\">";
							$endprofil   = "</a>";
						}					
						echo "<td>".$startprofil.$avatar.$endprofil."</td>";
					}			
				?>				
				<?php if ( $this->params->get( 'show_rank_icon_cols' ) ) { ?>		
				<td class="list-title" align="center">
					<?php echo $icone ; ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_nummedals_cols' ) ) { ?>		
				<td class="list-title" align="center">
					<?php echo $nummedals ; ?>
				</td>
				<?php } ?>			
				<?php if ( $this->params->get( 'show_name_cols' ) ) { ?>		
				<td class="list-title">
					<?php
					if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
						$profil =  "<a href=\"" . $linktoprofil . "\">" . $row->usr_name . "</a>";
					} else $profil = $row->usr_name ;
					echo $profil;			 
					 ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_username_cols' ) ) { ?>
					<td>					
						<?php
						if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
							$profil =  "<a href=\"" . $linktoprofil . "\">" . $row->usr_username . "</a>";
						} else $profil = $row->usr_username ;						
						 echo $profil;				 
						 ?>			
					</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_referreid_cols' ) ) { ?>
				<td class="list-title">
					<?php echo $row->referreid; ?>
				</td>
				<?php } ?>
				<td class="list-title" align="center">				
					<?php echo getFormattedPoints( $row->points ); ?>
				</td>
				<?php if ( $this->params->get( 'show_referral_user_cols' ) ) { ?>
				<td class="list-title">				
					<?php echo $row->referraluser; ?>
				</td>
				<?php } ?>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>	
	<div class="pagination">
	<?php 
	echo $this->pagination->getPagesCounter() . "<br />" ;
	echo $this->pagination->getPagesLinks(); 
	?>
	</div>
	<input type="hidden" name="filter_order" value="<?php echo @$this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->lists['order_Dir']; ?>" />
	<input type="hidden" name="controller" value="users" />
</form>
<?php
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
