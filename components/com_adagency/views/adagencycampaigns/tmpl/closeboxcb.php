<?php
    defined ('_JEXEC') or die ("Go away.");
    //echo "<pre>";var_dump($this->vars);

    $document = &JFactory::getDocument();
    $document->addScript( JURI::base() . "components/com_adagency/includes/js/jquery.js" );
    $document->addScript( JURI::base() . "components/com_adagency/includes/js/jquery.adagency.js" );
    $document->addScriptDeclaration("
        ADAG(function() {
            current_camp = window.parent.ADAG('.camp" . $this->vars->cid . "');
            //console.log(current_camp);
            window.setTimeout(function() {
                window.parent.ADAG.data(current_camp[0], 'totalbanners', '" . $this->vars->totalads . "');
                window.parent.ADAG('#close_cb').click();
            }, 1300);
        });
    ");

    echo "<div style='font-weight: bold; font-size: 22px;margin: 190px 70px;'>"
    . JText::_('ADAG_REM_ADS_CMP') . "</div>";
?>
