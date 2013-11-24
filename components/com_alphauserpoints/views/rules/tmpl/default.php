<?php
/*
 * @component AlphaUserPoints, Copyright (C) 2008-2012 Bernard Gilly, http://www.alphaplug.com
 * @copyright Copyright (C) 2011 Mike Gusev (migus)
 * @license : GNU/GPL
 * @Website : http://migusbox.com
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

?>
<?php if ( $this->params->get( 'show_page_title', 1 ) ) { ?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->params->get( 'page_title' ); ?>
	</div>
<?php } ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
<?php if ( !$this->params->get( 'show_caticon', 0 ) ) { ?>
				<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="5%">
				<div align="center">
				</div>
				</td>
<?php } ?>
				<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="15%">
				<div align="center">
					<?php echo JText::_('AUP_RULE_NAME'); ?>
				</div>
				</td>
<?php if ( !$this->params->get( 'show_desc', 0 ) ) { ?>
				<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
				<div align="center">
					<?php echo JText::_( 'AUP_RULE_DESC' ); ?>
				</div>
				</td>
<?php } ?>
				<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="10%">
				<div align="center">
					<?php echo JText::_( 'AUP_POINTS' ); ?>
				</div>
				</td>
<?php	if ( $this->params->get( 'show_published', 0 ) ) {?>
				<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="10%">
				<div align="center">
					<?php echo JText::_( 'AUP_RULEACTIVITY' ); ?>
				</div>
				</td>

<?php } ?>
<?php	if ( !$this->params->get( 'show_approve', 0 ) ) {?>
		<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="10%">
				<div align="center">
					<?php echo JText::_( 'AUP_RULEAPPROVAL' ); ?>
				</div>
				</td>
<?php } ?>
			</tr>
		<?php 

			for ($i=0, $n=count( $this->rules ); $i < $n; $i++)			
			{
				$row 	=& $this->rules[$i];			

			?>
			<tr class="sectiontableentry2">
<?php if ( !$this->params->get( 'show_caticon', 0 ) ) { ?>
				<td>
					<div align="center">
					<?php echo _getIconCategoryRule( $row->category ); ?>
					</div>
				</td>
<?php } ?>
				<td>
					<div align="left">
					<?php echo JText::_( $row->rule_name ); ?>
					</div>
				</td>
<?php if ( !$this->params->get( 'show_desc', 0 ) ) { ?>
				<td>
					<div align="left">
					<?php echo JText::_( $row->rule_description ); ?>
					</div>
				</td>
<?php } ?>
				<td align="center">
					<div align="center">
					<?php					
					if ( $row->points ) {				
						$points = $row->points;
						if ($points>0)
						$points_color = '#00AA00';
						if ($points<0)
						$points_color = '#FF0000';
						if ($points==0)
						$points_color = '#000000';
					?>
					<b><font color="<?php echo $points_color; ?>">
					<?php switch ( $row->plugin_function ) {
						case 'sysplgaup_referralpoints':
							echo getFormattedPoints( $points ) . " %";
							break;						
						default:
						echo getFormattedPoints( $points ); ?>
					<?php } ?>
					</font></b>
					<?php
					} else 	echo '-';
					?>
					</div>
				</td>
<?php	if ( $this->params->get( 'show_published', 0 ) ) {?>
				<td align="center">
					<div align="center">
					<?php					
						$icon = ( $row->published )? 'tick.png' : 'publish_x.png' ;
						$alt = ( $row->published )? 'AUP_RULEACTIVE' : 'AUP_RULEINACTIVE' ;	 
						?>
						<img src="components/com_alphauserpoints/assets/images/<?php echo $icon; ?>" border="0" title="<?php echo JText::_( $alt ); ?>" alt="<?php echo JText::_( $alt ); ?>" />
					</div>
				</td>
<?php } ?>
<?php	if ( !$this->params->get( 'show_approve', 0 ) ) {?>
				<td align="center">
					<div align="center">
					<?php
						switch ( $row->plugin_function ) {
						case 'sysplgaup_excludeusers':
						case 'sysplgaup_emailnotification':
						case 'sysplgaup_winnernotification':
						case 'sysplgaup_changelevel1':		
						case 'sysplgaup_changelevel2':		
						case 'sysplgaup_changelevel3':										
						case 'sysplgaup_archive':		
							$imgA =  'publish_y.png';
							$altA = JText::_('AUP_NOTAPPROVEDRULE');
							break;						
						default:
							$imgA = $row->autoapproved ? 'publish_g.png' :'publish_r.png';
							$altA = $row->autoapproved ? JText::_( 'AUP_AUTOAPPROVEDRULE' ) : JText::_( 'AUP_ADMINAPPROVEDRULE' );
						}
						?>
						<img src="components/com_alphauserpoints/assets/images/<?php echo $imgA; ?>" border="0" title="<?php echo JText::_( $altA ); ?>" alt="<?php echo JText::_( $altA ); ?>" />
					</div>
				</td>
<?php } ?>
			</tr>
			<tr style="height:5px;"></tr>
			<?php
				}
			?>
			<tr>
				<td colspan="4" align="center">
					<?php echo "<br />" . $this->pagination->getPagesLinks(); ?>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</td>
			</tr>
	</table>
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