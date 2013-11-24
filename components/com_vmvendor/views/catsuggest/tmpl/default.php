<?php
/*
 * @component VMVendor
 * @copyright Copyright (C) 2008-2013 Adrien Roussel
 * @license : GNU/GPL
 * @Website : http://www.nordmograph.com
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user 			= &JFactory::getUser();
$db 			= &JFactory::getDBO();
$app			= JFactory::getApplication();
$juri 			= JURI::base();
$doc 			= JFactory::getDocument();
$format = JRequest::getVar('format');
if (!class_exists( 'VmConfig' ))
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
$cparams 		=& JComponentHelper::getParams('com_vmvendor');
$cat_suggest 	= $cparams->getValue('cat_suggest',1);
$profileman		= $cparams->getValue('profileman',0);

$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/bootstrap.min.css');
$doc->addScript($juri.'components/com_vmvendor/assets/js/bootstrap.min.js');



$virtuemart_vendor_id = $this->virtuemart_vendor_id;

if($cat_suggest>0){
	echo  '<form id="cat_suggest" name="cat_suggest" onsubmit="validateForm();" method="post" >';
	echo ' <div class="modal-dialog">
	 <div class="modal-content">
	 <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
        
	if ($cat_suggest<=2)
		echo '<h4 class="modal-title">'.JText::_('COM_VMVENDOR_CATSUGGEST_SUGGESTFORM').'</h4>';
	else
		echo '<h4 class="modal-title"'.JText::_('COM_VMVENDOR_CATSUGGEST_ADDITIONFORM').'</h4>';
	echo '    </div>
	<div class="modal-body">';
		
	if($profileman == 2){
		require_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'window.php' );
		CWindow::load();
		$tooltip_class = 'jomTips';		
	}
	else{
		JHTML::_('behavior.tooltip');
		$tooltip_class = 'hasTip';
	}
	
	$forbidcatids 	= $cparams->getValue('forbidcatids');	
	$vmitemid 		= $cparams->getValue('vmitemid', '103');
	$to 			= $cparams->getValue('to');				
	echo '<script type="text/javascript">function validateForm(){
	var warning = "'.JText::_('COM_VMVENDOR_VMVENADD_JS_FIXTHIS').' \n";
	var same = warning;
	if (document.getElementById("cat_name").value==""){
		warning += " * '.JText::_('COM_VMVENDOR_CATNAME_REQUIRED').' \n";
		document.getElementById("cat_name").style.backgroundColor = \'#ff9999\';
	}
	if (document.getElementById("cat_parent").value==""){
		warning += " * '.JText::_('COM_VMVENDOR_CATPARENT_REQUIRED').' \n";
		document.getElementById("cat_parent").style.backgroundColor = \'#ff9999\';
	}';
				
	echo 'if (warning == same){
			return true;
		}
		else	{
			alert(warning);
			return false;
		}
	}
	</script>';
			
	
	echo  JText::_('COM_VMVENDOR_CATSUGGEST_MANDATORYFIELDS');
	echo  '<table class="table table-condensed">';
					
	echo  '<tr align="left" >';
	echo  '<td>'.JText::_('COM_VMVENDOR_CATSUGGEST_CATNAME').'</td>';
	echo  '<td>';
	echo '<input type="text" id="cat_name"  name="cat_name" size="50" onkeyup="this.style.backgroundColor = \'\'"  class="form-control"/>';
	echo  '</td>';
	echo  '</tr>';
			
	echo  '<tr align="left" >';
	echo  '<td>'.JText::_('COM_VMVENDOR_CATSUGGEST_CATDESCR').'</td>';
	echo  '<td>';
	echo '<textarea id="cat_descr"  name="cat_descr" cols="60" class="form-control">';
	
	echo '</textarea>';
	echo  '</td>';
	echo  '</tr>';
				
	echo  '<tr>';
	echo  '<td>';
	echo JText::_('COM_VMVENDOR_CATSUGGEST_CATPARENT');
	echo  '</td>';
	echo  '<td>';
	//////////////////////// Category select field
	echo '<select   id="cat_parent" name="cat_parent" onchange="this.style.backgroundColor = \'\'" class="form-control">';
	echo '<option value="" >'.JText::_('COM_VMVENDOR_CATSUGGEST_CHOOSECAT').'</option>';
	echo '<option value="0" >'.JText::_('COM_VMVENDOR_CATSUGGEST_ROOT').'</option>';
	$traverse = VmvendorModelCatsuggest::traverse_tree_down('',0,0,$forbidcatids,$virtuemart_vendor_id );
	echo '</select>';
	echo  '</td>';
	echo  '</tr>';
	echo  '</table>
	</div>
	<div class="modal-footer">';	
	
	
	if($user->id >0){		
		if ($cat_suggest==1){
			echo '<button type="submit" class="btn btn-lg btn-block btn-primary">'.JText::_('COM_VMVENDOR_CATSUGGEST_CATSUGGESTBUTTON').'</button>';
		}
		else{
			echo '<button type="submit" class="btn btn-lg btn-block btn-primary">'.JText::_('COM_VMVENDOR_CATSUGGEST_CATADDBUTTON').'</button>';
			
			if ($cat_suggest==2)
				echo '<input type="hidden" name="cat_published" value="0" />';
			if ($cat_suggest==3)
				echo '<input type="hidden" name="cat_published" value="1" />';
		}
	}
	else
		echo '<div class="alert alert-warning">'. JText::_('COM_VMVENDOR_VMVENADD_ONLYLOGGEDIN') .'</div>';
	
	echo  '</div>';
	
	echo '<input type="hidden" name="option" value="com_vmvendor" />
		<input type="hidden" name="controller" value="catsuggest" />';
	echo '<input type="hidden" name="task" value="addcat" />';
	
	
	echo '</div>
	</div>';
	echo  '</form>';
}
else{
	echo JText::_('COM_VMVENDOR_EDITTAX_TAX_MODE_DISABLED');
	//echo '<input type="button" name="cancel" id="cancelbutton" value="'.JText::_('COM_VMVENDOR_VMVENADD_BTTN_CANCEL').'" onclick="history.go(-1)">';
}
?>