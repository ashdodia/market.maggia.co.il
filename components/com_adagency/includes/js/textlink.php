<?php
	$document = &JFactory::getDocument();
	$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
    $document->addScript( JURI::root().'components/com_adagency/includes/js/jquery.fcbkcomplete.js');
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.DOMWindow.js" );
    $document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.adagency.js" );
    $cpn_link = JRoute::_("index.php?option=com_adagency&controller=adagencyCPanel" . $Itemid_cpn);
	$document->addScriptDeclaration('
		ADAG(function(){
			ADAG(\'.cpanelimg\').click(function(){
				document.location = "' . $cpn_link . '";
			});
		});');?>

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
		window.onload = function(){
			changeLinkTitle();
			changeBody();
			changeAction();
			setOverflow();
		}
		<!--
		var imagearray = [<?php if(isset($realimgs)) {echo $realimgs;} else {echo '';}?>];

		ADAG(function(){

            if (!ADAG('#campaign_table').find('input').length) {
                ADAG('#campaign_table').hide();
            }       
        
			ADAG('#remimg').click(function(event){
				ADAG('input[name=image_url]').val('');
				ADAG('img[name*=imagelib]').prop('src','');
				ADAG('#wxh').html('x');
				ADAG(this).hide();
				event.preventDefault();
			});

			if ("<?php echo JRequest::getVar('task','','post');	?>" == "upload") {
				setTimeout("window.location.hash = 'adimage'", 1000);
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

		function setOverflow(){
			document.getElementById("textlink").style.overflow = "hidden";
		}

		function changeLinkTitle(){
			document.getElementById("ttitle").innerHTML = document.getElementById("clinktitle").value;
		}

		function changeBody(){
			document.getElementById("ttbody").innerHTML = document.getElementById("linktext").value;
		}

		function changeAction(){
			document.getElementById("ttaction").innerHTML = document.getElementById("clinkaction").value;
		}

		function setImageAlign(ia){
			var image2 = ADAG('#imgdiv2 #tlink2 img');
			if((image2.get().length>0)&&(image2.prop('src') != 'images/blank.png')) {
				//var ia = ADAG('#parametersia').val();
				var ia = ia || null;
				if(ia == 'l') {
					image2.css({'float':'left', 'padding':'5px'});
				} else if (ia == 'r') {
					image2.css({'float':'right', 'padding':'5px'});
				} else if(ia == 't') {
					//image2.removeAttr('style');
					image2.css({ 'float':'none' });
				}
			}
			setImageWrap();
		}

		function setImageWrap(ia,wrap_img){
			var text = ADAG('#tbody');
			var ia = ia || null;
			var wrap_img = wrap_img || null;

			if((wrap_img == '0')&&(ADAG('#rt_image').width() != null)){
				//var ia = ADAG('#parametersia').val();
				var size = ADAG('#rt_image').width() + 10; // 5*2 px for padding
				switch(ia){
					case 't':
						text.css({'margin-left':'','margin-right':''});
						break;
					case 'l':
						text.css({'margin-left':size+'px', 'margin-right':''});
						break;
					case 'r':
						text.css({'margin-right':size+'px', 'margin-left':''});
						break;
				}
			} else if( wrap_img == '1') { //ADAG('#parameterswrap_img').val()
				text.css({'margin-left':'','margin-right':''});
			}
		}

		function updateWH(mxtype, mxsize){
			var mxsize = mxsize || null;
			var mxtype = mxtype || null;
			//var size = ADAG('#mxsize').val()+'px';
			var size = mxsize + 'px';
			if(mxtype == 'w'){//ADAG('#mxtype').val()
				var attrbt = 'width';
				var attrbt2 = 'height';
			} else {
				var attrbt = 'height';
				var attrbt2 = 'width';
			}
			ADAG('#rt_image').css(attrbt2,'auto').css(attrbt,size);
		}

		function changeSize(width,height){
			//if (ADAG("#parameterssizeparam").val() == 1) { param = "%";} else { param = "px";}
			ADAG("#textlink").css('width', width + 'px');//ADAG("#thwidth").val()+param
			ADAG("#textlink").css('height',height + 'px');//ADAG("#thheight").val()+param
		}

		function callZoneSettings(){
			var id = ADAG('#zoneId').val();
			if(id != 0){
				try {
					objectus = unserialize(ADAG('#z'+id).val());
					setImageAlign(objectus.ia);
					updateWH(objectus.mxtype, objectus.mxsize);
					changeSize(objectus.width,objectus.height);
					setImageWrap(objectus.ia,objectus.wrap_img);
				}
				catch(err){
					alert(err.toString());
				}
			} else {
				// revert to original
			}
		}

		<!--

		var imagearray = [<?php if(isset($realimgs)) {echo $realimgs;} else {echo '';}?>];

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
			//document.adminForm['parameters[alt_text]'].value = document.adminForm['linktext'].value;
			ADAG('input[name*="parameters[alt_text]"]').val(document.adminForm['linktext'].value);
			document.adminForm['parameters[alt_text_t]'].value = document.adminForm['linktitle'].value;
			document.adminForm['parameters[alt_text_a]'].value = document.adminForm['linkaction'].value;

		<?php
			$len = array();
			foreach($camps as $camp){
				if(!in_array($camp->id,$len)) { $len[] = $camp->id; }
			}
			for($i=1;$i<=count($len);$i++) { ?>
			if ( document.getElementById('adv_cmp<?php echo $i; ?>').checked == true) {
				document.getElementById('adv_cmp<?php echo $i; ?>').value=document.getElementById('adv_cmp<?php echo $i; ?>').value+"|yes|";
			} else {
				document.getElementById('adv_cmp<?php echo $i; ?>').value=document.getElementById('adv_cmp<?php echo $i; ?>').value+"|no|";
			}
			<?php } ?>

				if (pressbutton=='save') {
					if (form['title'].value == "") {
						alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
						} else if (form['target_url'].value == "" || form['target_url'].value == "http://") {
							alert( "<?php echo JText::_("JS_TARGETURL") ?>" );
						} else if (form['linktext'].value == "") {
							alert( "<?php echo JText::_("JS_FILL_TEXTLINK"); ?>" );
						} else if (form['linktitle'].value == "") {
							alert( "<?php echo JText::_("JS_FILL_HTMLTILE"); ?>" );
						} else if(checkZones() == false){
							alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>" );
						} else { // else ONE

						<?php
							if(isset($_row->id)&&($_row->id != 0)&&($configs->ad_status != 'Y')&&($_row->approved != 'P')) {
						?>
							var title2 = ADAG.trim(ADAG('#text_title').val());
							var description2 = ADAG.trim(ADAG('#text_description').val());
							var img2 = ADAG.trim(ADAG('#text_image').val());
							var url2 = ADAG.trim(ADAG('#text_url').val());
							//var alt2 = ADAG.trim(ADAG('#text_alt').val());
							//||(alt2 != ADAG.trim('<?php echo stripslashes(@$_row->parameters['img_alt']); ?>'))
							var linktitle2 = ADAG.trim(ADAG('#clinktitle').val());
							var linktext2 = ADAG.trim(ADAG('#linktext').val());
							var linkaction2 = ADAG.trim(ADAG('#clinkaction').val());

							if((title2 != ADAG.trim('<?php echo $_row->title; ?>'))||(description2 != ADAG.trim('<?php echo $_row->description; ?>'))||(img2 != ADAG.trim('<?php echo $_row->image_url; ?>'))||(url2 != ADAG.trim('<?php echo $_row->target_url; ?>'))||(linktitle2 != ADAG.trim('<?php echo stripslashes(@$_row->parameters['alt_text_t']); ?>'))||(linktext2 != ADAG.trim('<?php echo stripslashes(@$_row->parameters['alt_text']); ?>'))||(linkaction2 != ADAG.trim('<?php echo stripslashes(@$_row->parameters['alt_text_a']); ?>'))){
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

						} // else ONE
				}
				else
					submitform( pressbutton );
			}
			-->
		</script>
