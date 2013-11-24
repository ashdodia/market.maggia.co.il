<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !@$this->points ) {
?>
<script type="text/javascript">
<!--
	function validateForm( frm ) {
		var valid = document.formvalidator.isValid(frm);
		if (valid == false) {
			// do field validation
			if (frm.login.invalid) {
				alert( "<?php echo JText::_( 'AUP_USERNAME', true ); ?>" );
			}
			return false;
		} else {
			frm.submit();
		}
	}
// -->
</script>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="registerQRcode" id="registerQRcode" class="form-validate">
		<table class="contentpaneopen">
		<tr>
			<td>
				<?php echo JText::_( 'AUP_USERNAME' ); ?>
			</td>
			<td>
				<input class="inputbox required" type="text" name="login" id="login" size="30" maxlength="255" value="" /> <span id="statusUSR"></span>
			</td>
		</tr>
		</table>
		<button class="button validate" type="submit"><?php echo JText::_('AUP_SEND'); ?></button>
	<input type="hidden" name="option" value="com_alphauserpoints" />
	<input type="hidden" name="view" value="registerqrcode" />
	<input type="hidden" name="couponCode" value="<?php echo $this->couponCode; ?>" />
	<input type="hidden" name="trackID" value="<?php echo $this->trackID; ?>" />
	<!--<input type="hidden" name="menuid" value="<?php echo $this->menuid; ?>" />-->
	<input type="hidden" name="task" value="attribqrcode" />
</form>
<?php 
}
else
{
	$app = JFactory::getApplication();
	if( $this->points > 0 )
	{	
		$app->enqueueMessage( sprintf ( JText::_('AUP_CONGRATULATION'), getFormattedPoints( $this->points ) ) );
	} elseif( $this->points < 0 ) {
		$app->enqueueMessage( sprintf ( JText::_('AUP_X_POINTS_HAS_BEEN_DEDUCTED_FROM_YOUR_ACCOUNT'), getFormattedPoints( abs($this->points) ) ) );
	}
}
?>