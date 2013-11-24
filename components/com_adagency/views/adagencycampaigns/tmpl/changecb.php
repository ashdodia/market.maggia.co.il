<?php
    defined ('_JEXEC') or die ("Go away.");
    $document = &JFactory::getDocument();
    $document->addStyleSheet( JURI::base() . "components/com_adagency/includes/css/changecb.css" );
    $document->addStyleSheet( "components/com_adagency/includes/css/adagency_template.css" );
    $document->addScript( JURI::base() . "components/com_adagency/includes/js/jquery.js" );
    $document->addScript( JURI::base() . "components/com_adagency/includes/js/jquery.adagency.js" );
    $camp = $this->camp;
    $banners = $this->banners;
    //echo "<pre>";var_dump($banners);die();
?>
<div id="adagency_container">
<div id="changecb_container">
<div id="ijadagencycampaigns2" class="componentheading">
<?php echo JText::sprintf('ADAG_CMP_SEL_ADS2', $camp->name); ?>
</div>

<form id="goform" action="index.php?option=com_adagency&controller=adagencyCampaigns&task=savechangecb" method="post">

<table class="content_table">
<thead>
	<tr>
	    <td class="sectiontableheader"><?php echo ucwords(JText::_('AD_NEW_CAMP_BAN_NAME')); ?></td>
   		<td class="sectiontableheader"><?php echo JText::_('ADAG_REMOVE'); ?></td>
	</tr>
</thead>

<tbody>

<?php
    $k = 0;
    if ( is_array($banners) )
    foreach ($banners as $banner) {
?>
    <tr class="row<?php echo $k; ?>">
        <td valign="top">
            <?php echo $banner->title; ?>
        </td>
        <td valign="top">
            <input type="checkbox" name="todel[]" value="<?php echo $banner->id; ?>" />
        </td>
	</tr>
	<tr style="height: 6px;">
		<td style="border-bottom: 1px solid rgb(0, 0, 0);" colspan="5"></td>
	</tr>
<?php
        $k = 1 - k;
    }
?>

</tbody>
</table>

<input type="hidden" name="campaignid" value="<?php echo $camp->id; ?>" />
<input type="hidden" name="component" value="com_adagency" />
<input type="hidden" name="controller" value="adagencyCampaigns" />
<input type="hidden" name="task" value="savechangecb" />

<input type="submit" name="submit" value="<?php echo JText::_('AD_SAVE'); ?>" />

</form>
</div>
</div>
