<?php
	$document->addScript( JURI::root() . 'components/com_adagency/includes/js/jquery.js');
    $document->addScript( JURI::root() . 'components/com_adagency/includes/js/jquery.fcbkcomplete.js');
    $document->addScript( JURI::root() . "components/com_adagency/includes/js/jquery.adagency.js" );
    $document->addScript( JURI::root() . "components/com_adagency/includes/js/jquery.DOMWindow.js" );
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "'. $cpn_link .'";
			});
		});');
?>
<script language="javascript" type="text/javascript">
		<!--

        ADAG(function(){

            if (!ADAG('#campaign_table').find('input').length) {
                ADAG('#campaign_table').hide();
            }
        
            ADAG('#change_cb').openDOMWindow({
                height: 450,
                width: 600,
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
            ADAG('#close_cb').closeDOMWindow({eventType:'click'});

        <?php
            for ($i=0;$i<count($camps);$i++) {
                // store adslimit & totalbanners for each campaign
                // for later checks
        ?>
                $current_camp = ADAG('#adv_cmp<?php echo $i+1; ?>');
                if ($current_camp.length) {
                    ADAG.data($current_camp[0], 'adslim', '<?php
                        if ( isset( $camps[$i]->params['adslim'] ) ) {
                            $adslim = $camps[$i]->params['adslim'];
                        } else {
                            $adslim = '999';
                        }
                        echo $adslim;
                    ?>');
                    ADAG.data($current_camp[0], 'totalbanners', '<?php
                        if ( isset( $camps[$i]->totalbanners ) ) {
                            echo $camps[$i]->totalbanners;
                        } else {
                            echo '0';
                        }
                    ?>');
                    // check if campaign has reached max number of ads
                    // and don't allow banner assignment if so
                    $current_camp.click(function(event) {
                        var adslim = ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'adslim'),
                        totalbanners = ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners'),
                        availableads = adslim - totalbanners,
                        val = ADAG(this).val();

                        if ( !ADAG(this).prop('checked') ) {
                            newTotalbanners = parseInt(totalbanners,10) - 1;
                            ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners', newTotalbanners);
                        } else {
                            //alert("Available ads: " + adslim + " - " + totalbanners + " = " + availableads);

                            if ( (availableads <= 0) && (ADAG('#DOMWindow').length == 0) ) {
                                event.preventDefault();
                                window.setTimeout(function() {
                                    //alert(val);
                                    var answer = confirm('<?php echo JText::sprintf('ADAG_CMP_LIMIT_AD_WARN2', $adslim); ?>');
                                    if (answer) {
                                        ADAG('#change_cb').prop(
                                            'href',
                                            'index.php?option=com_adagency&controller=adagencyCampaigns&task=changecb&id=' + val + '&tmpl=component'
                                        ).click();
                                    }
                                    // ADAG('#close_cb').click();
                                }, 1);
                            } else if (availableads <= 0) {
                                event.preventDefault();
                            } else {
                                newTotalbanners = parseInt(totalbanners,10) + 1;
                                ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners', newTotalbanners);
                            }
                        }

                    });
                }
        <?php
            }
        ?>

        });

		function checkZones(){
			var ok = true;
			ADAG('#campaign_table').find('tr:gt(0)').each(function(index){
    			if((ADAG(this).find('.check_camp input.formField').prop('checked') == true)&&(ADAG(this).find('.check_ad .w145').val() == 0)){
    			    ok = false;
    			}
			});
			return ok;
		}

		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;

			<?php for($i=1;$i<=count($camps);$i++) { ?>
		if ( document.getElementById('adv_cmp<?php echo $i; ?>').checked == true) {
			document.getElementById('adv_cmp<?php echo $i; ?>').value=document.getElementById('adv_cmp<?php echo $i; ?>').value+"|yes|";
		} else {
			document.getElementById('adv_cmp<?php echo $i; ?>').value=document.getElementById('adv_cmp<?php echo $i; ?>').value+"|no|";
		}
		<?php } ?>

		if (pressbutton=='save') {
			if (form['title'].value == "") {
				alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
			} else if (!(form['width'].value>0)) {
				alert( "<?php echo JText::_("JS_BANNER_WIDTH") ?>" );
			} else if (!(form['height'].value>0)) {
				alert( "<?php echo JText::_("JS_BANNER_HEIGHT"); ?>" );
			} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
			} else { //else ONE
				<?php
					if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {

					//echo $editor1->save( 'transitioncode' );
				?>
					var title2 = ADAG.trim(ADAG('#floating_title').val());
					var description2 = ADAG.trim(ADAG('#floating_description').val());
					var the_code = <?php echo $editor1->getContent('transitioncode'); ?>;
					var code2 = ADAG.trim(the_code).replace(new RegExp( "\\n", "g" ),'');

					if((title2 != ADAG.trim('<?php echo $_row->title; ?>'))||(description2 != ADAG.trim('<?php echo $_row->description; ?>'))||(code2 != ADAG.trim('<?php echo str_replace("\r\n","",addslashes(stripslashes($_row->parameters['ad_code']))); ?>'))){
						var answer = confirm('<?php echo JText::_('ADAG_AT_PENDING'); ?>');
					} else {
						var answer = true;
					}

					if(answer){
				<?php
					}
				?>

					<?php if(!isset($configs->geoparams['allowgeo'])) { ?>
					submitform( pressbutton );
					<?php } else { ?>
					if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
					sanitizeAndSubmit(pressbutton);
					<?php } ?>

				<?php
					if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
				?>
					} else {
						return false;
					}
				<?php
					}
				?>

			}//else ONE
		} else
				submitform( pressbutton );
		}
		-->
		</script>
