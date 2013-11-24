<script language="javascript" type="text/javascript">
		<!--

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

		function checkZones(){
			var ok = true;
			ADAG('#banner_table tr:gt(0)').each(function(index){
				if((ADAG(this).find('.add_column input:checked').length>0)&&(ADAG(this).find('.w145[value=]').length>0)){
					ok = false;
				}
			});
			return ok;
		}

		ADAG(function(){

            ADAG('.modal').openDOMWindow({
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

			ADAG('.w145').change(function(){
   				if(ADAG(this).val()) {
    				ADAG(this).parent().parent().find('.add_column input').prop('checked','true');
			    }
			});

			if ("<?php echo JRequest::getVar('task','','post');	?>" == "refresh") {
				setTimeout("window.location.hash = 'refreshpackage'", 500);
			}

            ADAG('#banner_table input').click(function(event) {
                if (ADAG('#banner_table input:checked').length > '<?php echo $adslim; ?>') {
                    alert('<?php echo JText::sprintf('ADAG_CMP_LIMIT_AD_WARN', $adslim); ?>');
                    event.preventDefault();
                }
            });

		});


        function limitAds() {
            <?php 
				if(trim($adslim) == "-"){
					$adslim = 0;
				}
			?>
			
			return !!(ADAG('.add_column').find(':checked').length <= '<?php echo $adslim; ?>' );
        }

		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton=='save') {
				<?php
					if(!isset($get_data['cid'][0])||($get_data['cid'][0]==0)) {
				?>
				ok = false;
				if(typeof(ADAG('#countbids').get(0)) != 'undefined'){
					for(var i=1;i<=document.getElementById('countbids').value;i++){
						if(document.getElementById('bid['+i+']').checked == true) {
							ok = true;
						}
					}
				}
                <?php if ($camp_row->id<1) { ?>
                ok = true;
                <?php } ?>                
				if(ok==false) {
					alert('<?php echo JText::_('ADAG_NOCMPAD');?>');
					return true;
				}
				<?php
					}
				?>
				if (form['name'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_CMPNAME");?>" );
				<?php if ($camp_row->id<1) { ?>
				} else if (getSelectedValue2('adminForm','otid') < 1) {
					alert( "<?php echo JText::_("JS_SELECT_PACKAGE");?>" );
				<?php } ?>
				} else if (form['start_date'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_DATE");?>" );
				} else if (checkZones() == false){
					alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_EACH_AD"); ?>" );
				} else if (!limitAds()) {
                    alert( "<?php echo JText::_("ADS_LIM_WARN"); ?>" );
                } else {
					<?php if(isset($camp_row->id)&&($camp_row->id>0)&&isset($pstatus)&&($pstatus != 'Y')) { ?>
						var answer = confirm('<?php echo JText::_('ADAG_JS_CONFIRM_CAMP'); ?>');
					<?php } else { ?>
						var answer = true;
					<?php }	?>
					if(answer) {
						submitform( pressbutton );
					} else {
						return false;
					}
				}
			}
			if(pressbutton == 'refresh') { submitform(pressbutton); }
		}
		-->
		</script>
