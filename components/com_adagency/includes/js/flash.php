<?php
	$document = &JFactory::getDocument();
	$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
	$document->addScript( JURI::root().'components/com_adagency/includes/js/jquery.fcbkcomplete.js' );
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.adagency.js" );
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
		var flashFolder = '<?php echo $lists['flash_directory'] ?>';

		function changeDisplayFlash() {
			var imgSrc = document.adminForm['swf_url'].value;
			var pic_span = document.getElementById("swf_file");
			if (imgSrc != '') {
				imgsrc = flashFolder + imgSrc;

				pic_span.innerHTML="<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" ID=\"banner\"><PARAM NAME=\"movie\" VALUE=\""+imgsrc+"?link=&window=_self\"><param name=\"wmode\" value=\"transparent\"><PARAM NAME=\"quality\" VALUE=\"high\"><EMBED SRC=\""+imgsrc+"?link=&window=_self\"  QUALITY=\"high\" wmode=\"transparent\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED></OBJECT>";

			}
			else {
				pic_span.innerHTML="";
			}

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

		ADAG(function(){

            // attach keyup event for width & height
			ADAG('input[name=width]').keyup(function(event){
				alterSize(event);
			});
			ADAG('input[name=height]').keyup(function(event){
				alterSize(event);
			});

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
                    $current_camp.live('click', function(event) {
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

		function alterSize(event){
			// only if we type in a number take action
			//alert(event.keyCode);
			if(((event.keyCode >= 48)&&(event.keyCode <= 57))||((event.keyCode >= 96)&&(event.keyCode <= 105))) {
				ADAG('#flashCampaigns').remove();
				var width = parseInt(ADAG('input[name=width]').val());
				var height = parseInt(ADAG('input[name=height]').val());
				if(!isNaN(width) && !isNaN(height)) {
					ADAG('#flash_ad_obj').prop({'width' : width, 'height' : height});
					ADAG('#flash_ad_embed').prop({'width' : width, 'height' : height});
					ADAG.ajax({
					   type: "GET",
					   url: "<?php echo JURI::root();?>index.php?option=com_adagency&controller=adagencyAds&task=getCampsByParams",
					   data: "type=flash&width="+ width +"&height=" + height,
					   success: function(msg){
					     ADAG('#flashCampaigns').remove();
					   	 msg = String(ADAG.trim(msg));
						 if(msg.length>=1){
						 	var splitEm = msg.split("|");
							var campaigns = '<table id="flashCampaigns" width="100%" class="adminform"><tbody><tr><td width="100%" class="sectiontableheader" colspan="2"><?php echo JText::_('VIEWADCAMPAIGNS'); ?></td></tr>';
							for(var i=0;i<=splitEm.length-1;i++){
								var cmp = splitEm[i].split("@"); var id = i+1;
								var zz = cmp[1].split("*");
								if(typeof(zz[1]) != 'undefined') {
									var selectbx = '<select class="w145" id="czones_'+cmp[0]+'" name="czones['+cmp[0]+']">';
									selectbx += '<option value="0"><?php echo JText::_('ADAG_ZONE_FOR_AD'); ?></option>';
									var z2 = zz[1].split(";");
									for(var j=0;j<=z2.length-1;j++){
										var z3 = z2[j].split("=");
										selectbx += '<option value="'+ z3[0] +'">'+ z3[1] +'</option>';
									}
									selectbx += '</select>';
								}
                                campaigns += '<tr><td class="check_camp"><input type="checkbox" value="' + cmp[0] + '" name="adv_cmp['+ id +']" id="adv_cmp'+ id +'" class="formField adv_cmp camp' + cmp[0] + '">&nbsp;<label>' + zz[0] + '</label></td><td class="check_ad">'+ selectbx +'</td></tr>';
							}
							campaigns += '</tbody></table>';
							ADAG(campaigns).insertAfter('#flashMarker');
                            window.setTimeout(updateCampLimits, 300);
					   }
					   }
					 });
				}
			}
		}

        function updateCampLimits() {
            var bid = '<?php if (isset($_row->id) && ($_row->id > 0))  { echo "&bid=" . $_row->id; } ?>';
            ADAG.ajax({
                type: "GET",
                url: "<?php echo JURI::root();?>index.php?option=com_adagency&controller=adagencyAds&task=getCampLimInfo" + bid,
                data: "aid="+ <?php echo (int)$advertiser_id; ?>,
                dataType: 'json',
                success: function (responseObject) {
                    if (typeof(responseObject) !== 'undefined') {
                        for(var id in responseObject) {
                            var $current_camp = ADAG('.camp' + id);
                            if ($current_camp.length) {
                                ADAG.data($current_camp[0], 'adslim', responseObject[id].adslim + "");
                                ADAG.data($current_camp[0], 'totalbanners', responseObject[id].occurences + "");
                            }
                        }
                    }
                }
            });
        }

		function checkZones(){
			var ok = true;
			ADAG('#flashCampaigns').find('tr:gt(0)').each(function(index){
				if((ADAG(this).find('.check_camp .adv_cmp').prop('checked') == true)&&(ADAG(this).find('.check_ad .w145').val() == 0)){
				   ok = false;
				}
			});
			return ok;
		}

		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;

			if (pressbutton=='save') {
				if (form['title'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
					} else if (form['target_url'].value == "" || form['target_url'].value == "http://") {
						alert( "<?php echo JText::_("JS_TARGETURL"); ?>" );
					} else if (form['swf_url'].value == "") {
						alert( "<?php echo JText::_("JS_SELECT_SWF");?>" );
					} else if (!(form['width'].value>0)) {
						alert( "<?php echo JText::_("JS_BANNER_WIDTH") ?>" );
					} else if (!(form['height'].value>0)) {
						alert( "<?php echo JText::_("JS_BANNER_HEIGHT"); ?>" );
					} else if(checkZones() == false){
						alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
						return false;
					} else {// else ONE

						<?php
							if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
						?>
							var title2 = ADAG.trim(ADAG('#flash_title').val());
							var description2 = ADAG.trim(ADAG('#flash_description').val());
							var width2 = ADAG.trim(ADAG('#flash_width').val());
							var height2 = ADAG.trim(ADAG('#flash_height').val());
							var swf2 = ADAG.trim(ADAG('#flash_swf').val());
							var url2 = ADAG.trim(ADAG('#flash_url').val());

							if((title2 != ADAG.trim('<?php echo $_row->title; ?>'))||(description2 != ADAG.trim('<?php echo $_row->description; ?>'))||(swf2 != ADAG.trim('<?php echo $_row->swf_url; ?>'))||(width2 != ADAG.trim('<?php echo $_row->width; ?>'))||(height2 != ADAG.trim('<?php echo $_row->height; ?>'))||(url2 != ADAG.trim('<?php echo $_row->target_url; ?>'))){
								var answer = confirm('<?php echo JText::_('ADAG_AT_PENDING'); ?>');
							} else {
								var answer = true;
							}

							if(answer){
						<?php
							}
						?>

								<?php if(!isset($configs->geoparams['allowgeo'])&&!isset($configs->geoparams['allowgeoexisting'])) { ?>
								submitform( pressbutton );
								<?php } else {?>
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

					} // ELSE ONE
			}
			else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
