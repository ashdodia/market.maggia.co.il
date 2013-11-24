<script type="text/javascript">
ADAG(function(){
    ADAG('.cpanelimg').click(function(){
        document.location = '<?php echo JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn); ?>';
    });
    ADAG('.modal2').openDOMWindow({
        height: 450,
        width: 800,
        positionTop: 50,
        eventType: 'click',
        positionLeft: 50,
        windowSource: 'iframe',
        windowPadding: 0,
        loader: 1,
        loaderImagePath: '<?php echo JURI::root()."components/com_adagency/images/loading.gif"; ?>',
        loaderHeight: 31,
        loaderWidth: 31
    });
});
</script>
