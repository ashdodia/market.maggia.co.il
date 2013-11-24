<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$user =  JFactory::getUser();
$colspan = 4;
if ( $this->params->get( 'usrname' )=='' ) $colspan = 6;
if ( $this->useAvatarFrom ) $colspan++;

?>
<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="adminForm">
<?php if ($this->params->def('show_limit', 1)) { ?>
<div class="display-limit">
<?php
//echo JText::_('AUP_DISPLAY_NUM') .'&nbsp;';
//echo $this->pagination->getLimitBox();
?>
</div>
<?php
} 
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="category">
		<thead>
			<tr>
				<?php if ( $this->useAvatarFrom ) { ?>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>">&nbsp;
				</td>
				<?php } ?>				
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="20">&nbsp;																						
				</td>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='name') { ?>			
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="15%">
					<?php echo JText::_('AUP_NAME'); ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='username') { ?>	
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="15%">
					<?php echo JText::_( 'AUP_USERNAME' ); ?>
				</td>
				<?php } ?>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="30%">
					<?php echo JText::_('AUP_DATE'); ?>
				</td>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php echo JText::_( 'AUP_REASON_FOR_AWARD' ); ?>
				</td>
			</tr>
		</thead>
		<tbody>
		<?php
			$k = 0;			

			require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
			
			for ($i=0, $n=count( $this->detailrank ); $i < $n; $i++)			
			{
				$row 	=& $this->detailrank[$i];
				
				$_user_info = AlphaUserPointsHelper::getUserInfo ( $row->referreid );				
				$linktoprofil = getProfileLink( $this->linkToProfile, $_user_info );
				
				if ($row->icon ) {
					$pathicon = JURI::root() . 'components/com_alphauserpoints/assets/images/awards/icons/';
					$icone = '<img src="'.$pathicon . $row->icon.'" width="16" height="16" border="0" alt="" />';
				} else $icone = '';	
				
			?>
			<tr class="cat-list-row<?php echo $k; ?>">	
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
				<td class="list-title">
					<div align="center">
					<?php echo $icone; ?>
					</div>
				</td>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='name') { ?>			
				<td class="list-title">				
					<?php
					if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
						$profil =  "<a href=\"" . $linktoprofil . "\">" . $row->name . "</a>";
					} else $profil = $row->name ;
						echo $profil;			 
					 ?>
					
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='username') { ?>	
				<td class="list-title">
					<?php
					if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
						$profil =  "<a href=\"" . $linktoprofil . "\">" . $row->username . "</a>";
					} else $profil = $row->username ;
						echo $profil;			 
					 ?>					
				</td>
				<?php } ?>
				<td class="list-title">
					<?php 
					echo JHTML::_('date',  $row->dateawarded,  JText::_('DATE_FORMAT_LC') );
					?>
				</td>
				<td class="list-title">					
					<?php 
					echo JText::_( $row->rank );
					if ( $row->reason ) echo ' - ' . JText::_( $row->reason ); 
					?>
				</td>
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
	<input type="hidden" name="option" value="com_alphauserpoints" />
	<input type="hidden" name="controller" value="medals" />
	<input type="hidden" name="task" value="detailsmedal" />
	<input type="hidden" name="cid" value="<?php echo $row->cid ; ?>" />
</form>
<br /><br />