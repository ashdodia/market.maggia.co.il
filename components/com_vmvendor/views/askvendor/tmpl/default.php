<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2012 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user 			= &JFactory::getUser();
$juri 			= JURI::base();

$doc 					= &JFactory::getDocument();
$cparams 					=& JComponentHelper::getParams('com_vmvendor');
$profileman 				= $cparams->getValue('profileman');
$load_jquery			= $cparams->getValue('load_jquery', 1);
//$jquery_url			= $cparams->getValue('jquery_url','https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
//if($jquery_url) 	$doc->addScript($jquery_url);

echo ' <link rel="stylesheet" href="'.$juri.'components/com_vmvendor/assets/css/bootstrap.min.css" type="text/css" />';
echo ' <link rel="stylesheet" href="'.$juri.'components/com_vmvendor/assets/css/askvendor.css" type="text/css" />';
//$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/askvendor.css');
//$doc->addScript($juri.'components/com_vmvendor/assets/js/bootstrap.min.js');


$naming 				= $cparams->getValue('naming', 'username');
				
$yourname = 	'';
$youremail=		'';
if($user->id>0){
	$yourname = 	$user->$naming;
	$youremail=		$user->email;
}
$product_url = JRequest::getVar('href');
		
		
$emailto = $this->vendorcontacts[1];
$productname = $this->productname;

$sent = JRequest::getVar('sent');

if($sent>0 ){
	if($sent==1 )
		echo '<div class="alert alert-success">'.JText::_('COM_VMVENDOR_ASKVENDOR_SENT');	
	else
		echo '<div class="alert alert-danger">'.JText::_('COM_VMVENDOR_ASKVENDOR_EMAILFAILED');	
	echo '</div>';	

	echo '<div style="text-align:center;padding:200px 0 0 0 ;">
	<input type="button" value="'.JText::_('COM_VMVENDOR_ASKVENDOR_CLOSE').'"   class="btn btn-lg btn-primary"  onclick="window.parent.SqueezeBox.close();" />
	</div>';
}	
else{

	echo '	 <div class="modal-content">';
	echo '	 <div class="modal-header">';
	echo '<h4 class="modal-title">'.JText::_('COM_VMVENDOR_ASKVENDOR_TITLE1');
	if($productname!='')
		echo ' '.JText::_('COM_VMVENDOR_ASKVENDOR_TITLE2');
	echo '</h4>';
	echo '<script type="text/javascript">function validate(it){
			var warning = "'.JText::_('COM_VMVENDOR_VMVENADD_JS_FIXTHIS').' \n";
			var same = warning;';			
	echo 'if (it.formname.value==""){ 
		warning += " * '.JText::_('COM_VMVENDOR_ASKVENDOR_JS_NAMEREQUIRED').' \n";
		it.formname.style.backgroundColor = \'#ff9999\';
		}';
	echo 'if (it.formemail.value==""){
		warning += " * '.JText::_('COM_VMVENDOR_ASKVENDOR_JS_EMAILREQUIRED').' \n";
		it.formemail.style.backgroundColor = \'#ff9999\';
	}';
	echo 'if (it.formsubject.value==""){ 
		warning += " * '.JText::_('COM_VMVENDOR_ASKVENDOR_JS_SUBJECTREQUIRED').' \n";
		it.formsubject.style.backgroundColor = \'#ff9999\';
		}';
	echo 'if (it.formmessage.value==""){ 
			warning += " * '.JText::_('COM_VMVENDOR_ASKVENDOR_JS_MESSAGEREQUIRED').' \n";
			it.formmessage.style.backgroundColor = \'#ff9999\';
			}';
	echo 'if (warning == same) {return true;}
			else{ alert(warning); return false;}
	}</script>';
	echo '</div>';

	echo ' <div class="modal-body">
	<form method=POST onsubmit="return validate(this)" >';
	echo '<input type="hidden" name="formhref" value="'.JRequest::getVar('href').'">';
	echo '<table class="table table-condensed">';
	echo '<tr><td>'.JText::_('COM_VMVENDOR_ASKVENDOR_YOURNAME').':</td>';
	echo '<td><input type="text" name="formname" id="formname" size="50" value="'.ucfirst($yourname).'" class="form-control" onkeyup="this.style.backgroundColor = \'\'" /></td></tr>';
	echo '<tr><td>'.JText::_('COM_VMVENDOR_ASKVENDOR_YOURMAIL').':</td>';
	echo '<td><input type="text" name="formemail" id="formemail"  size="50" value="'.$youremail.'"class="form-control" onkeyup="this.style.backgroundColor = \'\'" /></td></tr>';
	echo '<tr><td>'.JText::_('COM_VMVENDOR_ASKVENDOR_SUBJECT').':</td>';
	$subject_value ='';
	if($productname!='')
		$subject_value = JText::_('COM_VMVENDOR_ASKVENDOR_ABOUTYOURPRODUCT').' '.ucfirst($productname);
	echo '<td><input type="text" name="formsubject" id="formsubject" size="50" value="'.$subject_value.'" class="form-control" onkeyup="this.style.backgroundColor = \'\'" /></td></tr>';
	echo '<tr><td colspan=2>'.JText::_('COM_VMVENDOR_ASKVENDOR_MESSAGE').':<br>';
	echo '<textarea COLS="50" ROWS="3" name="formmessage" class="form-control" onkeyup="this.style.backgroundColor = \'\'" ></textarea>';
	echo '</td></tr>';
	echo '</table>';
	if($productname!='')
		echo JText::_('COM_VMVENDOR_ASKVENDOR_URLWILLBEADDED');
	echo '</div>';
	echo '<div class="modal-footer">';
	echo '<input type="submit" value="'.JText::_('COM_VMVENDOR_ASKVENDOR_SEND').'"   class="btn btn-lg btn-primary"  /> ';
	echo '<input type="reset" value="'.JText::_('COM_VMVENDOR_ASKVENDOR_RESET').'" class="btn btn-sm btn-default"/>';
	echo '<input type="hidden" name="emailto" value="'.$emailto.'" />
			<input type="hidden" name="option" value="com_vmvendor" />
			<input type="hidden" name="controller" value="askvendor" />';
	echo '<input type="hidden" name="task" value="askvendor" />';
	echo '</form>';
	echo '</div>';
	echo '</div>';
}