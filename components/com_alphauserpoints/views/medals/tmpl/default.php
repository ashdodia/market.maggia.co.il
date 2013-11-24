<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

$heightforicon = '';
if ( !$this->params->get( 'showImage' ) ) $heightforicon='height="20"';
?>
<?php if ( $this->params->get( 'show_page_title', 1 ) ) { ?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->params->get( 'page_title' ); ?>
	</div>
<?php } ?>
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
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="5%" <?php echo $heightforicon; ?> align="center">
				<div align="center">
				<?php
					$pathicon = JURI::root() . 'components/com_alphauserpoints/assets/images/awards/icons/';
					echo '<img src="'.$pathicon . 'award_star_gold.gif" width="16" height="16" border="0" alt="" />';
				?>
				</div>
				</td>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="30%">
					<?php echo JText::_('AUP_MEDALS'); ?>
				</td>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="40%">
					<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>
				</td>
				<th class="list-title<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="10%">
				<div align="center">
					<?php echo JText::_( 'AUP_AWARDED' ); ?>
				</div>
				</td>
			</tr>
		</thead>
		<tbody>
		<?php			
			$k = 0;
			
			for ($i=0, $n=count( $this->levelrank ); $i < $n; $i++)			
			{
				$row 	=& $this->levelrank[$i];
				
				if ( $this->params->get( 'showImage' ) ) {				
					if ($row->image ) {
						$pathimage = JURI::root() . 'components/com_alphauserpoints/assets/images/awards/large/';
						$icone = '<img src="'.$pathimage . $row->image.'" height="'.$this->params->get( 'heightImage', 32 ).'" border="0" alt="" />';
					} else $icone ='';				
				} else {				
					if ($row->icon ) {
						$pathicon = JURI::root() . 'components/com_alphauserpoints/assets/images/awards/icons/';
						$icone = '<img src="'.$pathicon . $row->icon.'" width="16" height="16" border="0" alt="" />';
					} else $icone ='';					
				}					
				
			?>
			<tr class="cat-list-row<?php echo $k; ?>">
				<td class="list-title" align="center">
					<div align="center">
					<?php echo $icone; ?>
					</div>
				</td>
				<td class="list-title">
					<?php echo JText::_( $row->rank ); ?>
				</td>
				<td class="list-title">
					<div align="left">
					<?php echo JText::_( $row->description ); ?>
					</div>
				</td>
				<td class="list-title" align="center">
					<div align="center">
					<?php					
					if ( $row->nummedals ) {					
						$link = 'index.php?option=com_alphauserpoints&amp;view=medals&amp;task=detailsmedal&amp;cid='.$row->id;						
						echo '<b><a href="'.JRoute::_( $link ).'">'.$row->nummedals.'</a></b>';
					} else 	echo '-';
					?>
					</div>
				</td>
			</tr>
		</tbody>
		<?php
				$k = 1 - $k;
			}
		?>
	</table>
	<div class="pagination">
	<?php 
	echo $this->pagination->getPagesCounter() . "<br />" ;
	echo $this->pagination->getPagesLinks(); 
	?>
	</div>
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
