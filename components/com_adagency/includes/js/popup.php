<?php
	$document = &JFactory::getDocument();
	// $document->addScript('media/system/js/mootools.js');
	$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
    $document->addScript( JURI::root().'components/com_adagency/includes/js/jquery.fcbkcomplete.js');
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.adagency.js" );
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	$document->addScriptDeclaration('
	ADAG(function(){
		ADAG(\'.cpanelimg\').click(function(){
			document.location = "' . $cpn_link . '";
		});
	});');

	// $document->addScript('components/com_adagency/includes/js/jquery.fcbkcomplete.js');
	//$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.filestyle.mini.js" );
?>

<script language="javascript" type="text/javascript">
/*	ADAG(function() {
	 ADAG("input[type=file]").filestyle({
		 image: "<?php echo JURI::root()."components/com_adagency/images/";?>browse_button.png",
		 imageheight : 22,
		 imagewidth : 82,
		 width : '99'
	 });
	});*/
</script>
		<script language="javascript" type="text/javascript">
		<!--
		var imagearray = [<?php echo $realimgs?>];

	function changeDisplayImage() {
		var imageFolder = '<?php echo $lists['image_directory'] ?>';
		var imgSrc = document.adminForm['image_url'].value;
		foundimg = false;
		mydiv = document.getElementById("imgdiv");
		for(i = 0; i < imagearray.length; i ++)
			if(imagearray[i][2] == imgSrc)
			{
				foundimg = true;
				document.adminForm['width'].value = imagearray[i][0];
				document.adminForm['height'].value = imagearray[i][1];
  				mydiv.innerHTML = '<img alt="preview" src="'+imageFolder+'/'+imgSrc+'" id="imgs" />';
			}
	if(!foundimg)
	{
	  mydiv.innerHTML = '';
	  document.adminForm['width'].value = "";
	  document.adminForm['height'].value = "";
	}

	return;
	}

		function getSelectedValue2( frmName, srcListName ) {
			var form = eval( 'document.' + frmName );
			var srcList = form[srcListName];
			//alert(srcList);

			i = srcList.selectedIndex;
			if (i != null && i > -1) {
				return srcList.options[i].value;
			} else {
				return null;
			}
		}

		function Change() {
			document.adminForm.task.value = "addpopup";
			document.adminForm.submit();
			return true;
		}

		function confirmPopup(){
			var popup_type = ADAG.trim(ADAG('#parameterspopup_type').val());
			var popup_title = ADAG.trim(ADAG('#popup_title').val());
			var popup_description = ADAG.trim(ADAG('#popup_description').val());
			<?php
				if(!isset($_row2->parameters['popup_type'])) { $_row2->parameters['popup_type'] = NULL; }
				if(!isset($_row2->parameters['html'])) { $_row2->parameters['html'] = NULL; }
				if(!isset($_row2->title)) { $_row2->title = NULL; }
				if(!isset($_row2->description)) { $_row2->description = NULL; }
				if(!isset($_row->parameters['page_url'])) { $_row->parameters['page_url'] = NULL; }
			?>

			if((popup_type != ADAG.trim('<?php echo $_row2->parameters['popup_type']; ?>'))||(popup_title != ADAG.trim('<?php echo $_row2->title; ?>'))||(popup_description != ADAG.trim('<?php echo $_row2->description; ?>'))) {
    			var answer = confirm('<?php echo JText::_('ADAG_AT_PENDING'); ?>');
			} else {
				if(popup_type == 'html') {
					var the_code = <?php echo $editor1->getContent('parameters[html]'); ?>;
					var code2 = ADAG.trim(the_code).replace(new RegExp( "\\n", "g" ),'');
					if(code2 != ADAG.trim('<?php echo str_replace("\r\n","",addslashes(html_entity_decode(stripslashes($_row2->parameters['html'])))); ?>')){
						var answer = confirm('<?php echo JText::_('ADAG_AT_PENDING'); ?>');
					} else {
						var answer = true;
					}
				} else if(popup_type == 'image') {
					var popup_targeturl_img = ADAG.trim(ADAG('#popup_targeturl_img').val());
					var popup_imageurl = ADAG.trim(ADAG('#popup_imageurl').val());

					if((popup_targeturl_img != ADAG.trim('<?php echo $_row->target_url; ?>'))||(popup_imageurl != ADAG.trim('<?php echo $_row->image_url; ?>'))){
						var answer = confirm('<?php echo JText::_('ADAG_AT_PENDING'); ?>');
					} else {
						var answer = true;
					}
				} else if(popup_type == 'webpage'){
					var popup_pageurl = ADAG.trim(ADAG('#popup_pageurl').val());

					if(popup_pageurl != ADAG.trim('<?php echo $_row->parameters['page_url']; ?>')){
						var answer = confirm('<?php echo JText::_('ADAG_AT_PENDING'); ?>');
					} else {
						var answer = true;
					}
				}
			}
    		return answer;
		}

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
			if (pressbutton=='save') {
				<?php if ($_row->parameters['popup_type']=="webpage") { ?>
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
					} else if (form['parameters[page_url]'].value == "" || form['parameters[page_url]'].value == "http://") {
						alert( "<?php echo JText::_("JS_TARGETURL") ?>" );
					} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
					} else {
						<?php if(!isset($configs->geoparams['allowgeo'])) { ?>
							<?php if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) { ?>
							var ans = confirmPopup();
							<?php } else { ?>
							var ans = true;
							<?php } ?>
							if(ans) {
								submitform( pressbutton );
							} else {
								return false;
							}
						//submitform( pressbutton );
						<?php } else { ?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
							<?php if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) { ?>
							var ans = confirmPopup();
							<?php } else { ?>
							var ans = true;
							<?php } ?>
							if(ans) {
								sanitizeAndSubmit(pressbutton);
							} else {
								return false;
							}

						//sanitizeAndSubmit(pressbutton);
						<?php } ?>
					}
				<?php } else if	($_row->parameters['popup_type']=="image") { ?>
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
					} else if (form['target_url'].value == "" || form['target_url'].value == "http://") {
						alert( "<?php echo JText::_("JS_TARGETURL") ?>" );
					} else if (form['image_url'].value == "") {
						alert( "<?php echo JText::_("JS_SELECT_IMG");?>" );
					} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
					} else {
						<?php if(!isset($configs->geoparams['allowgeo'])) { ?>
							<?php if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) { ?>
							var ans = confirmPopup();
							<?php } else { ?>
							var ans = true;
							<?php } ?>
							if(ans) {
								submitform( pressbutton );
							} else {
								return false;
							}
						//submitform( pressbutton );
						<?php } else { ?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
							<?php if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) { ?>
							var ans = confirmPopup();
							<?php } else { ?>
							var ans = true;
							<?php } ?>
							if(ans) {
								sanitizeAndSubmit(pressbutton);
							} else {
								return false;
							}

						//sanitizeAndSubmit(pressbutton);
						<?php } ?>
					}
				<?php } else if	($_row->parameters['popup_type']=="html") { ?>
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
				} else if(checkZones() == false){
					alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
				} else {
						<?php echo $editor1->save( 'parameters[html]' );?>
						<?php if(!isset($configs->geoparams['allowgeo'])) { ?>
							<?php if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) { ?>
							var ans = confirmPopup();
							<?php } else { ?>
							var ans = true;
							<?php } ?>
							if(ans) {
								submitform( pressbutton );
							} else {
								return false;
							}
						//submitform( pressbutton );
						<?php } else { ?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
							<?php if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) { ?>
							var ans = confirmPopup();
							<?php } else { ?>
							var ans = true;
							<?php } ?>
							if(ans) {
								sanitizeAndSubmit(pressbutton);
							} else {
								return false;
							}

						//sanitizeAndSubmit(pressbutton);
						<?php } ?>
					}
				<?php } ?>
			}

		}
		-->
		</script>
