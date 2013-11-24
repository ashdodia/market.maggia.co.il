<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2012 - Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if ( $this->displ=='view' ) {
	$document	=  JFactory::getDocument();
	$lang       = $document->getLanguage();	
	
	if ( $this->params->get( 'useplaxoaddressbook' )=='1' ) {
		$document->addScript('http://www.plaxo.com/css/m/js/util.js');
		$document->addScript('http://www.plaxo.com/css/m/js/basic.js');
		$document->addScript('http://www.plaxo.com/css/m/js/abc_launcher.js');
		$onABCommComplete = "<!--
			function onABCommComplete(){
			  // OPTIONAL: do something here after the new data has been populated in your text area			  	  	
			}		
			//-->";
		$document->addScriptDeclaration( $onABCommComplete );
	}
	?>	
	<script type="text/javascript">
	<!--	
		function validateForm() {			
			if (document.inviteForm.sender.value=='') {
				alert( "<?php echo JText::_( 'AUP_YOURNAME', true ); ?>" );
			} else {
				document.inviteForm.submit();
			}
		}
	// -->
	</script>	
	<?php if ( $this->params->get( 'show_page_title', 1 ) ) { ?>
		<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
			<?php echo $this->params->get( 'page_title' ); ?>
		</div>
	<?php } ?>
	<?php echo JText::_( 'AUP_INVITEYOURFRIENDSTOSIGNUP' ); ?><br /><br />
	<div id="invite-form">
	<form action="<?php echo JRoute::_( 'index.php?Itemid='.JRequest::getVar('Itemid') );?>" method="post" name="inviteForm" id="inviteForm" class="form-validate">
	<table border="0" width="525">
	  <tr>
	  <?php if ( $this->params->get( 'showinformations' ) || $this->params->get( 'useplaxoaddressbook' ) ) { ?>
		<td valign="top">
		<?php if ( $this->params->get( 'showinformations' ) ) { ?>
			<span style="text-decoration: underline;"><?php echo JText::_( 'AUP_INFORMATION' ); ?></span><br/>
			<span class="small"><?php echo JText::_( 'AUP_MAXEMAILPERINVITE' ); ?> <b><?php echo $this->params->get( 'maxemailperinvite' ); ?></b></span><br/>
			<span class="small"><?php echo JText::_( 'AUP_DELAYBETWEENINVITES' ); ?> <b><?php echo $this->params->get( 'delaybetweeninvites' ); ?> <?php echo JText::_( 'AUP_SECONDS' ); ?></b></span><br/>
			<span class="small"><?php echo JText::_( 'AUP_MAXINVITESPERDAY' ); ?> <b><?php echo $this->params->get( 'maxinvitesperday' ); ?></b></span><br/>
			<?php if ( $this->referreid && $this->points ) { ?>
			<span class="small"><?php echo JText::_( 'AUP_POINTSEARNEDPERSUCCESSFULLINVITE' ); ?> <b><?php echo $this->points ; ?></b></span>
			<?php } ?>
		<?php } ?>
			</td>
		<td valign="top">&nbsp;</td>
		<td valign="top">	
		<?php 
		if ( $this->params->get( 'useplaxoaddressbook' )>=1 ) 
		{
			echo JText::_( 'AUP_IMPORTEDRECIPIENTS' ); 
			?>
			<br /><textarea class="inputbox" cols="36" rows="4" name="importedemails" id="importedemails" disabled="disabled" ></textarea><br />
			<?php		
				
			if ( $this->params->get( 'useplaxoaddressbook' )=='1' ) 
			{
				?>
				  <a href="#" onclick="showPlaxoABChooser('importedemails', 'components/com_alphauserpoints/views/invite/tmpl/plaxo_cb.html'); return false">
				<?php 
			} 
			elseif ( $this->params->get( 'useplaxoaddressbook' )=='2' ) 
			{ 			
				?>
				<a href="index.php?option=com_alphauserpoints&amp;view=addressbook&amp;tmpl=component" rel="{handler: 'iframe', size: {x: 520, y: 460}}" class="modal">			
				<?php
			}
		?>
		<img src="components/com_alphauserpoints/assets/images/add_button.gif" border="0" style="padding-top:4px;" alt="<?php echo JText::_( 'AUP_ADD_FROM_MY_ADDRESS_BOOK' ); ?>" /></a>
		<?php	
		}
		?>		
		</td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <?php } ?>	  
	  <?php if ( $this->params->get( 'useplaxoaddressbook' ) ) { ?>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <?php } ?>
	  <tr>
		<td width="224" valign="top" class="titleCell">
			<div align="left">
			<?php 
			$recipient_label =  ( $this->params->get( 'useplaxoaddressbook' ) ) ? JText::_( 'AUP_OTHERRECIPIENTS' ) : JText::_( 'AUP_RECIPIENTS' ); 
			$class_recipient = ( $this->params->get( 'useplaxoaddressbook' ) ) ? "inputbox" : "inputbox required" ; 
			echo $recipient_label;		
			?>
			</div>
		</td>
		<td width="10">&nbsp;</td>
		<td width="291"><input type="text" size="42" name="other_recipients" id="other_recipients" class="<?php echo $class_recipient; ?>" /><br />
			<span class="small"><?php echo JText::_( 'AUP_SEPERATEMULTIPLEEMAIL' ); ?></span></td>
	  </tr>
	  <tr>
		<td valign="top"><br /><?php echo JText::_( 'AUP_YOURNAME' ) .' ('.JText::_( 'AUP_REQUIRED' ).')'; ?></td>
		<td>&nbsp;</td>
		<td><br /><input type="text" size="42" name="sender" id="sender" class="inputbox required" value="<?php echo $this->user_name; ?>" /></td>
	  </tr>
	  <tr>
		<td valign="top"><div align="left"><br /><?php echo JText::_( 'AUP_CUSTOMMESSAGEOPTIONAL' ); ?></div></td>
		<td>&nbsp;</td>
		<td><br /><textarea class="inputbox" cols="36" rows="4" name="custommessage" id="custommessage"></textarea><br />
			<span class="small"><?php echo JText::_( 'AUP_THISMESSAGEWILLBEAPPEARINTHEEMAILSENT' ); ?></span></td>
	  </tr>
	  <?php if ( $this->params->get( 'userecaptcha' )=='1' || ($this->params->get( 'userecaptcha' )=='2' && $this->user_name=='') ) {  ?>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>
		<?php 
		if ( $this->params->get( 'recaptchaajax ', 0 ) ) {
			echo '<div id="recaptcha_div"></div>';
		} else {			
			// prevent recaptchalib already loaded
			if ( !function_exists('_recaptcha_qsencode') ) {
				require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'assets'.DS.'recaptcha'.DS.'recaptchalib.php');	
			}			
			// Get a key from http://recaptcha.net/api/getkey
			$publickey = $this->params->get( 'pubkey' );					
			// the response from reCAPTCHA
			$resp = null;
			// the error code from reCAPTCHA, if any
			$error = null;			
			echo recaptcha_get_html($publickey, $error);
		}
		?>
		</td>
	  </tr>
	<?php } ?>
	  <tr>
		<td align="left" valign="top"><br /></td>
		<td>&nbsp;</td>
		<td>
			<div id="status"><br/>            
				<input type="button" name="Submit" value="<?php echo JText::_('AUP_SEND'); ?>" class="button" onclick="javascript:document.getElementById('importedemails').disabled=false;validateForm();" /><br /><br />
			</div>			
		</td>
	  </tr>
	  <?php if ( $this->referrer_link ) { ?>
	  <tr>
	    <td align="left" valign="top">&nbsp;</td>
	    <td>&nbsp;</td>
	    <td><input type="text" size="42" name="referrer_link" id="referrer_link" onfocus="select();" readonly="readonly" class="inputbox" value="<?php echo $this->referrer_link; ?>" /><br />
			<span class="small"><?php echo JText::_( 'AUP_INVITATION_LINK' ); ?></span>
		</td>
      </tr>
	  <?php } ?>
	  <tr>
	    <td align="left" valign="top">&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
      </tr>
	</table>		
		<input type="hidden" name="option" value="com_alphauserpoints" />
		<input type="hidden" name="controller" value="invite" />
		<input type="hidden" name="referreid" value="<?php echo $this->referreid; ?>" />
		<input type="hidden" name="task" value="sendinvite" />
		<?php  if ( !$this->params->get( 'useplaxoaddressbook' ) ) { ?>
			<input type="hidden" name="importedemails" id="importedemails" value="" />
		<?php } ?>
	</form>
	</div>
<?php } else { 

		 if ( $this->params->def( 'show_page_title', 1 ) ) { ?>
			<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
				<?php echo $this->params->get( 'page_title' ); ?>
			</div>
		<?php 
		}
	 
		echo JText::_( 'AUP_INVITEYOURFRIENDSTOSIGNUP' );
		echo "<br /><br />";
		echo $this->message;
		echo "<br />";
	}
?>
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