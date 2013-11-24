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
$db 			= &JFactory::getDBO();
$doc 			= &JFactory::getDocument();
$juri 			= JURI::base();
$vendor_store_desc 			= $this->vendor_data[0];
$vendor_terms_of_service	= $this->vendor_data[1];
$vendor_legal_info			= $this->vendor_data[2];	
$vendor_store_name			= ucfirst($this->vendor_data[3]);
$vendor_phone				= $this->vendor_data[4];
$vendor_url					= $this->vendor_data[5];
$vendor_id 					= $this->vendor_data[6];
$vendor_thumb 				= $this->vendor_thumb;
$cparams 				=& JComponentHelper::getParams('com_vmvendor');
$profileman 			= $cparams->getValue('profileman');
$wysiwyg_prof 			= $cparams->getValue('wysiwyg_prof',1);
if($wysiwyg_prof)
	jimport( 'joomla.html.editor' );
	
	$allowed = 1;
$profiletypes_mode		= $cparams->getValue('profiletypes_mode', 0);
$profiletypes_ids		= $cparams->getValue('profiletypes_ids');

$doc->addStyleSheet($juri.'components/com_vmvendor/assets/css/bootstrap.min.css');

if($profiletypes_mode>0 && $profiletypes_ids!='' && $profileman ==2)
	$allowed = VmvendorModelEditprofile::getJSProfileallowed($profiletypes_ids);
	
	
if($allowed){
	?>
	<h1><?php echo JText::_('COM_VMVENDOR_EDITPRO_EDITYOURPROF'); ?></h1>
	
	
	<form method="POST" enctype="multipart/form-data" >
	<table class="table table-striped table-condensed">
    
	<tr>
	
	<td >
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORTITLE'); ?>
	</td>
	
	<td>
	<input type="text" name="vendor_title" value="<?php  echo $vendor_store_name;   ?>" size="50"  class="form-control" />
	</td>
	
	</tr>
	
	<tr>
	
	<td>
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORIMG'); ?>
	</td>
	<?php if(!$vendor_thumb)
			$vendor_thumb = 'components/com_vmvendor/assets/img/noimage.gif'; ?>
	<td>
   
    <img src="<?php echo $juri.$vendor_thumb;   ?>" height="25" style="vertical-align:middle;"/> <div class="col-lg-8"> <input type="file" name="vendor_image" size="40" class="form-control">
	</div></td>
	
	</tr>
	
	<tr>
	
	<td>
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORTEL'); ?>
	</td>
	
	<td><input type="text" name="vendor_telephone" value="<?php  echo $vendor_phone;   ?>" size="20" class="form-control" />
	</td>
	
	</tr>
	
	<tr>
	
	<td>
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORURL'); ?>
	</td>
	
	<td>
	<input type="text" name="vendor_url" value="<?php  echo $vendor_url;   ?>" size="60"  class="form-control" />
	</td>
	
	</tr>
	
	<tr>
	<td style="vertical-align:top;"><a name="desc" ></a>
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORDESC'); ?>
	</td>
	<td>
	<?php
	if($wysiwyg_prof){
		$editor = &JFactory::getEditor();
		$editorhtml = $editor->display("vendor_store_desc", $vendor_store_desc, "100%;", '150', '5', '30', false);
		echo $editorhtml;
	}
	else
	{
		echo '<textarea name="vendor_store_desc" cols="50" rows="10" class="form-control">';
		echo $vendor_store_desc;
		echo '</textarea>';
	}
	?>
	</td>
	</tr>
	<tr>
	<td style="vertical-align:top;"><a name="tos" ></a>
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORTOS'); ?>
	</td>
	<td>
	<?php
	if($wysiwyg_prof){
		$editor = &JFactory::getEditor();
		$editorhtml = $editor->display("vendor_terms_of_service", $vendor_terms_of_service, "100%;", '150', '5', '30', false);
		echo $editorhtml;
	}
	else
	{
		echo '<textarea name="vendor_terms_of_service" cols="50" rows="10" class="form-control" >';
		echo $vendor_terms_of_service;
		echo '</textarea>';
	}
	?>
	</td>
	</tr>
	<tr>
	<td style="vertical-align:top;"><a name="legal" ></a>
	<?php echo JText::_('COM_VMVENDOR_EDITPRO_VENDORLEGALINFO'); ?>
	</td>
	<td>
	<?php 
	if($wysiwyg_prof){
		$editor = &JFactory::getEditor();
		$editorhtml = $editor->display("vendor_legal_info", $vendor_legal_info, "100%;", '150', '5', '30', false);
		echo $editorhtml;
	}
	else
	{
		echo '<textarea name="vendor_legal_info" cols="50" rows="10" class="form-control">';
		echo $vendor_legal_info;
		echo '</textarea>';
	}
	?>
	
	</td>
	</tr>
	<?php
	if($profileman==2){
		?>
		<tr>
		<td style="text-align:center">
	   
		</td>
		<td>
        <div class="checkbox">
		 <input type="checkbox" name="activity_stream" id="activity_stream" > <label for="activity_stream"><?php echo JText::_('COM_VMVENDOR_EDITPRO_ACTIVITYSTREAM'); ?></label>
		</div></td>    
		</tr>
	<?php } ?>
	<tr>
	<td>
	</td>
	<td>
	<button type="submit" name="Submit" id="submit" value="<?php  echo JText::_('COM_VMVENDOR_EDITPRO_SUBMIT');   ?>"  class="btn btn-primary"  ><?php  echo JText::_('COM_VMVENDOR_EDITPRO_SUBMIT');   ?></button>
	<input type="button" name="cancel" id="cancelbutton" value="<?php echo JText::_('COM_VMVENDOR_VMVENADD_BTTN_CANCEL'); ?>" onclick="history.go(-1)" class="btn btn-default" />
	</td>
	</tr>
	</table>
	 <input type="hidden" name="option" value="com_vmvendor" />
				<input type="hidden" name="controller" value="vendorprofile" />
				<input type="hidden" name="task" value="updateprofile" />
	</form>
  <?php
  
 }
else
		JError::raiseWarning( 100, JText::_('COM_VMVENDOR_JSPROFILE_NOTALLOWED') );