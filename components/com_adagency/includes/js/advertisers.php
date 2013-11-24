<script language="javascript" type="text/javascript">
<!--
var request_processed = 0;
function getSelectedValue2( frmName, srcListName ) {
    var form = eval( 'document.' + frmName );
    var srcList = form[srcListName];

    i = srcList.selectedIndex;
    if (i != null && i > -1) {
        return srcList.options[i].value;
    } else {
        return null;
    }
}

function randomString() {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 5;
    var randomstring = '';
    for (var i=0; i<string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum,rnum+1);
    }
    return randomstring;
}

ADAG(function(){
    ADAG('.cpanelimg').click(function(){
        document.location = '<?php echo  JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);?>';
    });
});

function refreshcaptcha(){
    var randstr = randomString();
    document.getElementById('cptka2').innerHTML = '<img align="absbottom" alt="code" id="cptka" src="<?php echo JURI::root()."components/com_adagency/views/adagencyadvertisers/tmpl/captcha.php?code=";?>'+randstr+'" />';
    document.getElementById('cptchd').value = randstr;
}

Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    var regexp = /[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/

    if (pressbutton=='save') {
        if(form['is_already_registered'].value == 0)
        {
            <?php if ( isset($configs->show) && (in_array('company',$configs->show)) &&
                isset($configs->mandatory) && (in_array('company',$configs->mandatory))) {
            ?>
                if (form['company'].value == "") {
                    alert( "<?php echo JText::_("JS_INSERT_COMPANY");?>" );
                    return false;
                }
                if (form['description'].value == "") {
                    alert( "<?php echo JText::_("JS_INSERT_COMPANY_DSC");?>" );
                    return false;
                }
            <?php } // end checking company fields [if necessary] ?>
            if (form['username'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_USERNAME");?>" );
                return false;
            }
            if (form['password'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_PASS");?>" );
                return false;
            }
            if (form['password'].value != form['password2'].value) {
                alert( "<?php echo JText::_("JS_MATCH_PASS");?>" );
                return false;
            }
            <?php if ( isset($configs->show) && (in_array('phone',$configs->show)) &&
                isset($configs->mandatory) && (in_array('phone',$configs->mandatory))) {
            ?>
            if (form['telephone'].value == '') {
                alert( "<?php echo JText::_("JS_INSERT_TELEPHONE");?>" );
                return false;
            }
            <?php } ?>
            <?php if ( isset($configs->show) && (in_array('url',$configs->show)) &&
                isset($configs->mandatory) && (in_array('url',$configs->mandatory))) {
            ?>
            if ((form['website'].value == 'http://')||(form['website'].value == '')) {
                alert( "<?php echo JText::_("JS_INSERT_URL");?>" );
                return false;
            }
            <?php } ?>
            if (!regexp.test(form['email'].value)) {
                alert( "<?php echo JText::_("JS_INSERT_VALIDMAIL");?>" );
                return false;
            }
            if (form['name'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_NAME");?>" );
                return false;
            }
            <?php if(isset($configs->show)&&(in_array('captcha',$configs->show))) {?>
                if (form['captcha'].value != document.getElementById('cptchd').value) {
                    alert( "<?php echo JText::_("JS_CODE");?>" );
                    return false;
                }
            <?php } ?>
            <?php if(isset($configs->show)&&(in_array('calculation',$configs->show))) {?>
                if (form['calculation'].value != <?php echo $configs->xx + $configs->yy;?>) {
                    alert( "<?php echo JText::_("JS_CALCULATION");?>" );
                    return false;
                }
            <?php } ?>
            <?php if(isset($configs->show)&&(in_array('address',$configs->show))&&isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) {?>
            if (getSelectedValue2('adminForm','country') < 1) {
                alert( "<?php echo JText::_("JS_SELECT_COUNTRY");?>" );
                return false;
            }
            if (form['address'].value == "") {
                alert( "<?php echo JText::_("ADAG_JS_STREET");?>" );
                return false;
            }
            if (form['zip'].value == "") {
                alert( "<?php echo JText::_("ADAG_JS_ZIP");?>" );
                return false;
            }
            <?php } //end check for address fields [if necessary]?>
            if (form['checkagreeterms'].value == 1 ){
                    if(form.agreeterms.checked != true && <?php echo $configs->askterms; ?> == 1 && ("<?php echo $my->id;?>" == "0" ))
                        {
                            alert('<?php echo JText::_("JS_ACCEPT_TERMS_CONDITIONS"); ?>');
                            return false;
                        }
            }

            form.submit();
                //return true;

        } // it's not registered so we have checked all fields
        else
        { // he is registered so we don't need all fields - start
            <?php if(isset($configs->show)&&(in_array('company',$configs->show))&&isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) {?>
            if (form['company'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_COMPANY");?>" );
                return false;
            }
            if (form['description'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_COMPANY_DSC");?>" );
                return false;
            }
            <?php } // end checking company fields [if necessary]?>
            <?php if ( isset($configs->show) && (in_array('phone',$configs->show)) &&
                isset($configs->mandatory) && (in_array('phone',$configs->mandatory))) {
            ?>
            if (form['telephone'].value == '') {
                alert( "<?php echo JText::_("JS_INSERT_TELEPHONE");?>" );
                return false;
            }
            <?php } ?>
            <?php if ( isset($configs->show) && (in_array('url',$configs->show)) &&
                isset($configs->mandatory) && (in_array('url',$configs->mandatory))) {
            ?>
            if ((form['website'].value == 'http://')||(form['website'].value == '')) {
                alert( "<?php echo JText::_("JS_INSERT_URL");?>" );
                return false;
            }
            <?php } ?>
            if (form['name'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_NAME");?>" );
                return false;
            }
            <?php if(isset($configs->show)&&(in_array('address',$configs->show))&&isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) {?>
            if (getSelectedValue2('adminForm','country') < 1) {
                alert( "<?php echo JText::_("JS_SELECT_COUNTRY");?>" );
                return false;
            }
            if (form['address'].value == "") {
                alert( "<?php echo JText::_("ADAG_JS_STREET");?>" );
                return false;
            }
            if (form['zip'].value == "") {
                alert( "<?php echo JText::_("ADAG_JS_ZIP");?>" );
                return false;
            }
            <?php } //end check for address fields [if necessary]?>
            if (form['checkagreeterms'].value == 1 ){
                if(form.agreeterms.checked != true && <?php echo $configs->askterms; ?> == 1)
                {
                    alert('<?php echo JText::_("JS_ACCEPT_TERMS_CONDITIONS"); ?>');
                    return false;
                }
            }

            /*if(form['newpswd'].value!="") {
                if(form['newpswd'].value!=form['newpswd2'].value) {
                    alert( "<?php echo JText::_("JS_MATCH_PASS");?>" );
                    return false;
                }
            }*/

            form.submit();
            //return true;

        } // he is registered so we don't need all fields - stop
    } else {
        return false;
    }
}

function populateShipping () {
    var names = Array ('address','zipcode', 'city');
    var i;
    for (i = 0; i < names.length; i++) {
        val = document.getElementById(names[i]).value;
        document.getElementById('ship' + names[i]).value = val;
    }
    idx = document.getElementById('country').selectedIndex;
    document.getElementById('shipcountry').selectedIndex = idx;

    changeProvince_ship();
    request_processed = 1;
}

-->
</script>
