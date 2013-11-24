<?php defined('_JEXEC') or die('Restricted access');

$db			= &JFactory::getDBO();
$user 		= &JFactory::getUser();
$config		= &JFactory::getConfig();
$nullDate 	= $db->getNullDate();
$now		= &JFactory::getDate();

echo '<div id="phocadownload-upload">'.$this->tmpl['iepx'];

if ($this->tmpl['displayupload'] == 1) {


?>
<script type="text/javascript">
Joomla.submitbutton = function(task, id)
{
	if (id > 0) {
		document.getElementById('phocadownloadfiles-form').actionid.value = id;
	}
	Joomla.submitform(task, document.getElementById('phocadownloadfiles-form'));
	
}
</script>


<fieldset>
<legend><?php echo JText::_( 'COM_PHOCADOWNLOAD_UPLOADED_FILES' ); ?></legend>
<form action="<?php echo htmlspecialchars($this->tmpl['action']);?>" method="post" name="phocadownloadfilesform" id="phocadownloadfiles-form">
<table>
	<tr>
		<td align="left" width="100%"><?php echo JText::_( 'COM_PHOCADOWNLOAD_FILTER' ); ?>:
		<input type="text" name="search" id="pdsearch" value="<?php echo $this->listsfiles['search'];?>" onchange="document.phocadownloadfilesform.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'COM_PHOCADOWNLOAD_SEARCH' ); ?></button>
		<button onclick="document.getElementById('pdsearch').value='';document.phocadownloadfilesform.submit();"><?php echo JText::_( 'COM_PHOCADOWNLOAD_RESET' ); ?></button></td>
		<td nowrap="nowrap"><?php echo $this->listsfiles['catid'];?></td>
	</tr>
</table>
		
<table class="adminlist">
<thead>
	<tr>
	<th class="title" width="50%"><?php echo JHTML::_('grid.sort',  'COM_PHOCADOWNLOAD_TITLE', 'a.title', $this->listsfiles['order_Dir'], $this->listsfiles['order'], 'image'); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort',  'COM_PHOCADOWNLOAD_PUBLISHED', 'a.published', $this->listsfiles['order_Dir'], $this->listsfiles['order'], 'image' ); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo JText::_('COM_PHOCADOWNLOAD_DELETE'); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo JText::_('COM_PHOCADOWNLOAD_ACTIVE'); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_PHOCADOWNLOAD_APPROVED', 'a.approved', $this->listsfiles['order_Dir'], $this->listsfiles['order'], 'image' ); ?></th>

	<th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_PHOCADOWNLOAD_DATE_UPLOAD', 'a.date', $this->listsfiles['order_Dir'], $this->listsfiles['order'], 'image' ); ?></th>
	
	
	<th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_PHOCADOWNLOAD_CATEGORY', 'a.catid', $this->listsfiles['order_Dir'], $this->listsfiles['order'], 'image' ); ?></th>

</thead>
			
<tbody><?php
$k 		= 0;
$i 		= 0;
$n 		= count( $this->tmpl['filesitems'] );
$rows 	= &$this->tmpl['filesitems'];

if (is_array($rows)) {
	foreach ($rows as $row) {
	
	// USER RIGHT - Delete (Publish/Unpublish) - - - - - - - - - - -
	// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
	// because we cannot check the access and delete in one time
	$user = JFactory::getUser();
	$rightDisplayDelete	= 0;
	$catAccess	= PhocaDownloadHelper::getCategoryAccessByFileId((int)$row->id);

	if (!empty($catAccess)) {
		$rightDisplayDelete = PhocaDownloadHelper::getUserRight('deleteuserid', $catAccess->deleteuserid, 2, $user->authorisedLevels(), $user->get('id', 0), 0);
	}
	// - - - - - - - - - - - - - - - - - - - - - -

	?><tr class="<?php echo "row$k"; ?>">

	<td><?php echo $row->title; ?></td>
	
	<?php 

	// Publish Unpublish
	echo '<td align="center">';
	if ($row->published == 1) {
		if ($rightDisplayDelete) {
			echo '<a href="javascript:void(0)" onclick="javascript:Joomla.submitbutton(\'unpublish\', '.(int)$row->id.');" >';
			echo JHTML::_('image', $this->tmpl['pi'].'icon-publish.png', JText::_('COM_PHOCADOWNLOAD_PUBLISHED'));
			echo '</a>';
		} else {
			echo JHTML::_('image', $this->tmpl['pi'].'icon-publish-g.png', JText::_('COM_PHOCADOWNLOAD_PUBLISHED'));
		}
	}
	if ($row->published == 0) {
		if ($rightDisplayDelete) {
			echo '<a href="javascript:void(0)" onclick="javascript:Joomla.submitbutton(\'publish\', '.(int)$row->id.');" >';
			echo JHTML::_('image', $this->tmpl['pi'].'icon-unpublish.png', JText::_('COM_PHOCADOWNLOAD_UNPUBLISHED'));
			echo '</a>';
		} else {
			echo JHTML::_('image', $this->tmpl['pi'].'icon-unpublish-g.png', JText::_('COM_PHOCADOWNLOAD_UNPUBLISHED'));
		}
	}
	echo '</td>';
	
	echo '<td align="center">';
	if ($rightDisplayDelete) {
		echo '<a href="javascript:void(0)" onclick="javascript: if (confirm(\''.JText::_('COM_PHOCADOWNLOAD_WARNING_DELETE_ITEMS').'\')) {Joomla.submitbutton(\'delete\', '.(int)$row->id.');}" >';
		echo JHTML::_('image', $this->tmpl['pi'].'icon-trash.png', JText::_('COM_PHOCADOWNLOAD_DELETE'));
		echo '</a>';
	} else {
		echo JHTML::_('image', $this->tmpl['pi'].'icon-trash-g.png', JText::_('COM_PHOCADOWNLOAD_DELETE'));
	}
	echo '</td>';
	
	echo '<td align="center">';
	// User should get info about active/not active file (if e.g. admin change the active status)			
	$publish_up 	= &JFactory::getDate($row->publish_up);
	$publish_down 	= &JFactory::getDate($row->publish_down);
	$publish_up->setOffset($config->getValue('config.offset'));
	$publish_down->setOffset($config->getValue('config.offset'));
	if ( $now->toUnix() <= $publish_up->toUnix() ) {
		$text = JText::_( 'COM_PHOCADOWNLOAD_PENDING' );
	} else if ( ( $now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate ) ) {
		$text = JText::_( 'COM_PHOCADOWNLOAD_ACTIVE' );
	} else if ( $now->toUnix() > $publish_down->toUnix() ) {
		$text = JText::_( 'COM_PHOCADOWNLOAD_EXPIRED' );
	}

	$times = '';
	if (isset($row->publish_up)) {
		if ($row->publish_up == $nullDate) {
			$times .= JText::_( 'COM_PHOCADOWNLOAD_START') . ': '.JText::_( 'COM_PHOCADOWNLOAD_ALWAYS' );
		} else {
			$times .= JText::_( 'COM_PHOCADOWNLOAD_START') .": ". $publish_up->toFormat();
		}
	}
	if (isset($row->publish_down)) {
		if ($row->publish_down == $nullDate) {
			$times .= "<br />". JText::_( 'COM_PHOCADOWNLOAD_FINISH'). ': '. JText::_('COM_PHOCADOWNLOAD_NO_EXPIRY' );
		} else {
			$times .= "<br />". JText::_( 'COM_PHOCADOWNLOAD_FINISH') .": ". $publish_down->toFormat();
		}
	}
	
	if ( $times ) {
		echo '<span class="editlinktip hasTip" title="'. JText::_( 'COM_PHOCADOWNLOAD_PUBLISH_INFORMATION' ).'::'. $times.'">'
			.'<a href="javascript:void(0);" >'. $text.'</a></span>';
	}
	
	
	echo '</td>';
	
	// Approved
	echo '<td align="center">';
	if ($row->approved == 1) {
		echo JHTML::_('image', $this->tmpl['pi'].'icon-publish.png', JText::_('COM_PHOCADOWNLOAD_APPROVED'));
	} else {	
		echo JHTML::_('image', $this->tmpl['pi'].'icon-unpublish.png', JText::_('COM_PHOCADOWNLOAD_NOT_APPROVED'));	
	}
	echo '</td>';
	
	echo '<td align="center">'. $row->date .'</td>';
	

	echo '<td align="center">'. $row->categorytitle .'</td>'
	//echo '<td align="center">'. $row->id .'</td>'
	.'</tr>';

		$k = 1 - $k;
		$i++;
	}
}
?></tbody>
<tfoot>
	<tr>
	<td colspan="7" class="footer"><?php 
	
//$this->tmpl['filespagination']->setTab($this->tmpl['currenttab']['files']);
if (count($this->tmpl['filesitems'])) {
	echo '<div><center>';
	echo '<div style="margin:0 10px 0 10px;display:inline;">'
		.JText::_('COM_PHOCADOWNLOAD_DISPLAY_NUM') .'&nbsp;'
		.$this->tmpl['filespagination']->getLimitBox()
		.'</div>';
	echo '<div class="sectiontablefooter'.$this->params->get( 'pageclass_sfx' ).'" style="margin:0 10px 0 10px;display:inline;" >'
		.$this->tmpl['filespagination']->getPagesLinks()
		.'</div>';
	echo '<div class="pagecounter" style="margin:0 10px 0 10px;display:inline;">'
		.$this->tmpl['filespagination']->getPagesCounter()
		.'</div>';
	echo '</center></div>';
}




?></td>
	</tr>
</tfoot>
</table>


<?php echo JHTML::_( 'form.token' ); ?>

<input type="hidden" name="controller" value="user" />
<input type="hidden" name="task" value=""/>
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="actionid" value=""/>
<input type="hidden" name="tab" value="<?php echo $this->tmpl['currenttab']['files'];?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->tmpl['filespagination']->limitstart;?>" />
<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid', 0, '', 'int') ?>"/>
<input type="hidden" name="filter_order" value="<?php echo $this->listsfiles['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />

</form>
</fieldset>
<?php

// Upload		
$currentFolder = '';
if (isset($this->state->folder) && $this->state->folder != '') {
	$currentFolder = $this->state->folder;
}
?><fieldset>
<legend><?php 
	echo JText::_( 'COM_PHOCADOWNLOAD_UPLOAD_FILE' ).' [ '. JText::_( 'COM_PHOCADOWNLOAD_MAX_SIZE' ).':&nbsp;'.$this->tmpl['uploadmaxsizeread'].']';
?></legend>	
				
<?php 
if ($this->tmpl['errorcatid'] != '') {
	echo '<div class="error">' . $this->tmpl['errorcatid'] . '</div>';
} ?>
				
<form onsubmit="return OnUploadSubmitFile();" action="<?php echo $this->tmpl['actionamp'] ?>task=upload&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1" name="phocadownloaduploadform" id="phocadownload-upload-form" method="post" enctype="multipart/form-data">
<table>
	<tr>
		<td><strong><?php echo JText::_('COM_PHOCADOWNLOAD_FILENAME');?>:</strong></td><td>
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_PHOCADOWNLOAD_START_UPLOAD'); ?>"/>
			<span id="upload-clear"></span></td>
		</tr>
		
		<?php
		if ($this->tmpl['errorfile'] != '') {
			echo '<tr><td></td><td><div class="error">' . $this->tmpl['errorfile'] . '</div></td></tr>';
		} ?>
					
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_FILE_TITLE' ); ?>:</strong></td>
			<td><input type="text" id="phocadownload-upload-title" name="phocadownloaduploadtitle" value="<?php echo $this->formdata->title ?>"  maxlength="255" class="comment-input" /></td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_DESCRIPTION' ); ?>:</strong></td>
			<td><textarea id="phocadownload-upload-description" name="phocadownloaduploaddescription" onkeyup="countCharsUpload();" cols="30" rows="10" class="comment-input"><?php echo $this->formdata->description ?></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo JText::_('COM_PHOCADOWNLOAD_CHARACTERS_WRITTEN');?> <input name="phocadownloaduploadcountin" value="0" readonly="readonly" class="comment-input2" /> <?php echo JText::_('COM_PHOCADOWNLOAD_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocadownloaduploadcountleft" value="<?php echo $this->tmpl['maxuploadchar'];?>" readonly="readonly" class="comment-input2" />
			</td>
		</tr>
		
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_AUTHOR' ); ?>:</strong></td>
			<td><input type="text" id="phocadownload-upload-author" name="phocadownloaduploadauthor" value="<?php echo $this->formdata->author ?>"  maxlength="255" class="comment-input" /></td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_AUTHOR_EMAIL' ); ?>:</strong></td>
			<td><input type="text" id="phocadownload-upload-email" name="phocadownloaduploademail" value="<?php echo $this->formdata->email ?>"  maxlength="255" class="comment-input" /></td>
		</tr>
		
		<?php
		if ($this->tmpl['erroremail'] != '') {
			echo '<tr><td></td><td><div class="error">' . $this->tmpl['erroremail'] . '</div></td></tr>';
		} ?>
		
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_AUTHOR_WEBSITE' ); ?>:</strong></td>
			<td><input type="text" id="phocadownload-upload-website" name="phocadownloaduploadwebsite" value="<?php echo $this->formdata->website ?>"  maxlength="255" class="comment-input" /></td>
		</tr>
		
		<?php
		if ($this->tmpl['errorwebsite'] != '') {
			echo '<tr><td></td><td><div class="error">' . $this->tmpl['errorwebsite'] . '</div></td></tr>';
		} ?>
		
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_LICENSE' ); ?>:</strong></td>
			<td><input type="text" id="phocadownload-upload-license" name="phocadownloaduploadlicense" value="<?php echo $this->formdata->license ?>"  maxlength="255" class="comment-input" /></td>
		</tr>
		
		<tr>
			<td><strong><?php echo JText::_( 'COM_PHOCADOWNLOAD_VERSION' ); ?>:</strong></td>
			<td><input type="text" id="phocadownload-upload-version" name="phocadownloaduploadversion" value="<?php echo $this->formdata->version ?>"  maxlength="255" class="comment-input" /></td>
		</tr>
		
	</table>
	
	<ul class="upload-queue" id="upload-queue"><li style="display: none" ></li></ul>

	<?php /*<input type="hidden" name="controller" value="user" /> */ ?>
	<input type="hidden" name="viewback" value="user" />
	<input type="hidden" name="view" value="user"/>
	<input type="hidden" name="task" value="upload"/>
	<input type="hidden" name="tab" value="<?php echo $this->tmpl['currenttab']['files'];?>" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid', 0, '', 'int') ?>"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->listsfiles['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="catidfiles" value="<?php echo $this->tmpl['catidfiles'] ?>"/>
</form>
<div id="loading-label-file"><center><?php echo JHTML::_('image', $this->tmpl['pi'].'icon-loading.gif', '') . JText::_('COM_PHOCADOWNLOAD_LOADING'); ?></center></div>
</fieldset>
	<?php
}
echo '</div>';

?>
