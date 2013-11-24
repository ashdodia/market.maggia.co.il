<?php
	if(isset($configs->geoparams['allowgeo']) && ($configs->geoparams['allowgeo'] == '1')){
		$document->addStyleSheet("components/com_adagency/includes/css/geo.css");
		$document->addStyleSheet("components/com_adagency/includes/css/fcb.css");

		include_once(JPATH_BASE."/components/com_adagency/includes/js/geo.php");

		if(isset($current)) {
			$counter = 1;
			$output = "ADAG(document).ready(function(){";
			if(isset($current->sets)){
				foreach($current->sets as $element) {
					$out_p = NULL;$temp_set = NULL;
					$element->data = json_decode($element->data);
					if(is_array($element->data)) {
						$temp_set = "'".implode('|', $element->data)."'";
					} else { $temp_set = 'null'; }
					$output .= " ADAG('#limitation option[value=".$element->type."]').prop('selected','selected'); ";
					if(($element->type != 'region')&&($element->type != 'city')) {
						$output .= " var aux = ".$temp_set."; ";
						$output .= " selim('".$element->type."', aux);";
					} else {
						$output .= " selim('country', '".$element->data[0]."');";
						$output .= " ADAG('#country_container').show(); ";
						$temp_set = "'".implode('|', $element->data)."'";

						$output .= " var aux = ".$temp_set."; ";

						if($element->type == 'region') {
							$output .= " window.setTimeout(function(){ ADAG('#secondOption').prop('checked','checked');
										 ADAG('<table id=\"region_container\">').insertAfter(ADAG('#secondOption').next('label'));
										 ADAG('#city_container').remove();
										 selim('region', aux);
										 },300);
							 ";
						} else {
							for($i=1;$i<=count($element->data)-1;$i++){
								$ttemp2[] = $element->data[$i];
							}
							//echo "<pre>";var_dump($ttemp2);die();
							$ttemp2 = implode('|',$ttemp2);

							$output .= " window.setTimeout(function(){
											ADAG('#thirdOption').prop('checked','checked');
											 ADAG('<table id=\"city_container\">').insertAfter(ADAG('#thirdOption').next('label'));
											 ADAG('#region_container').remove();
											 selim('city','".$ttemp2."');
											 //ADAG('.city input').val('".$element->data[1]."');
										 },300);
										 ";

						}
					}
					switch($element->type) {
						case 'country':
							$output .= "ADAG('#country_container').show(); ADAG('#firstOption').prop('checked','checked');";
							break;
						case 'city':
							if(isset($element->data[0]) && isset($element->data[1])) {
								$output .= " ADAG('#limitation-".$counter."city').val('".$element->data[1]."');";
							}
							break;
						case 'latitude':
							$out_p.= "
								if(index == 0) { ADAG(this).val('".$element->data->a."'); }
								else if(index == 1) { ADAG(this).val('".$element->data->b."'); }
								else if(index == 2) { ADAG(this).val('".$element->data->c."'); }
								else if(index == 3) { ADAG(this).val('".$element->data->d."'); }
							";
							$output .= " ADAG('.latitude td:eq(0)').find('input').each(function(index){".$out_p."}); ";
							break;
						case 'postalcode':
							$output .= " ADAG('.postalcode td:eq(0)').find('input').val('".$element->data[0]."'); ";
							break;
						case 'usarea':
							//$output .= " ADAG('.usarea td:eq(0)').find('input').val('".$element->data[0]."'); ";
							break;
						default:
							break;
					}

					if($element->type == 'usarea') {
						//$output .= " window.setTimeout(function(){ADAG('.usarea .bit-input').hide();},1); ";
					} elseif($element->type == 'postalcode') {
						//$output .= " window.setTimeout(function(){ADAG('.postalcode .bit-input').hide();},1); ";
					}

					$counter++;
				}
			}
			$output .= "}); ";
			$document->addScriptDeclaration($output);
		}

		$output2 = "

			function checkChannel(makeRed){
				makeRed = makeRed || false;

				var geo_type = document.adminForm.getElementById('geo_type1').value;
				var limitation = document.adminForm.getElementById('limitation').value;

				var okContinent = true;
				ADAG('#opts').find('.continent').each(function() {
					var current = ADAG(this);
					if(!current.find('.holder .bit-box').length) {
						okContinent = false;
						if(makeRed) {
							current.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_CONT')."');
							return false;
						}
					} else {
						current.css('border','1px solid transparent');
					}
				});

				if(okContinent){
					var okCountry = true;
					ADAG('#opts').find('.country').each(function() {
						var current2 = ADAG(this);
						if(!current2.find('.holder:first .bit-box').length) {
							okCountry = false;
							if(makeRed) {
								current2.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_COUN')."');
								return false;
							}
						} else {
							current2.css('border','1px solid transparent');
						}
					});
				}

				if(okCountry){
					var okRegion = true;
					ADAG('#opts').find('.region').each(function() {
						var current3 = ADAG(this);
						if(!ADAG('.country .holder:first .bit-box').length) {
							okRegion = false;
							if(makeRed) {
								ADAG('.country').css('border','2px solid red');
								alert('".JText::_('ADAG_JS_COUN')."');
								return false;
							}
						} else if(!current3.find('.holder:eq(0) .bit-box').length) {
							okRegion = false;
							if(makeRed) {
								current3.find('td:first').css('border','2px solid red');
								alert('".JText::_('ADAG_JS_REGI')."');
								return false;
							}
						} else {
							current3.css('border','1px solid transparent');
						}
					});
				}

				if(okRegion){
					var okCity = true;
					ADAG('#opts').find('.city').each(function() {
						/*var current4 = ADAG(this);
						if(current4.find(':input[size=40]').val() == '') {
							okCity = false;
							if(makeRed) {
								current4.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_CITY')."');
								return false;
							}
						} else if (!current4.find('.holder .bit-box').length){
							okCity = false;
							if(makeRed) {
								current4.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_COUN')."');
								return false;
							}
						} else {
							current4.css('border','1px solid transparent');
						}*/

						var current4 = ADAG(this);
						if(!ADAG('.country .holder:first .bit-box').length) {
							okCity = false;
							if(makeRed) {
								ADAG('.country').css('border','2px solid red');
								alert('".JText::_('ADAG_JS_COUN')."');
								return false;
							}
						} else if(current4.find('.input20').val() == '') {
							okCity = false;
							if(makeRed) {
								current4.find('td:first').css('border','2px solid red');
								alert('".JText::_('ADAG_JS_CITY')."');
								return false;
							}
						} else {
							current4.css('border','1px solid transparent');
						}

					});
				}

				if(okCity){
					var okLatitude = true;
					ADAG('#opts').find('.latitude').each(function() {
						var current5 = ADAG(this);
						current5.find(':input[size=20]').each(function(index) {
							if(ADAG(this).val() == '') {
								okLatitude = false;
							}
						});
						if(!okLatitude) {
							if(makeRed) {
								current5.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_LATI')."');
								return false;
							}
						} else {
							current5.css('border','1px solid transparent');
						}
					});
				}

				if(okLatitude){
					var okDMA = true;
					ADAG('#opts').find('.dma').each(function() {
						var current6 = ADAG(this);
						if(!current6.find('.holder .bit-box').length) {
							okDMA = false;
							if(makeRed) {
								current6.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_DMA')."');
								return false;
							}
						} else {
							current6.css('border','1px solid transparent');
						}
					});
				}

				if(okDMA){
					var okUsarea = true;
					ADAG('#opts').find('.usarea').each(function() {
						var current7 = ADAG(this);
						if(!current7.find('.holder:first .bit-box').length) {
							okUsarea = false;
							if(makeRed) {
								current7.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_USAR')."');
								return false;
							}
						} else {
							current7.css('border','1px solid transparent');
						}
					});
				}

				if(okUsarea){
					var okPostal = true;
					ADAG('#opts').find('.postalcode').each(function() {
						var current8 = ADAG(this);
						if(current8.find(':input[size=40]').val() == '') {
							okPostal = false;
							if(makeRed) {
								current8.css('border','2px solid red');
								alert('".JText::_('ADAG_JS_POST')."');
								return false;
							}
						} else {
							current8.css('border','1px solid transparent');
						}
					});
				}

				return ((okContinent)&&(okLatitude)&&(okDMA)&&(okUsarea)&&(okPostal));
			}

			";
			$document->addScriptDeclaration($output2);
	}

	if(isset($configs->geoparams['allowgeoexisting']) && ($configs->geoparams['allowgeoexisting'] == '1') && ((!isset($current->sets))||(count($current->sets) == 0)) && (isset($_row->channel_id))&&(intval($_row->channel_id)>0) ) {
		//echo $_row->channel_id;die();
		$output3 = '
			ADAG(function(){
				ADAG("#geo_type2").prop("checked","checked");
				ADAG("#limitation_existing option[value='.$_row->channel_id.']").prop("selected","selected");
				ADAG("#limitation_existing").change();
			});
		';
		$document->addScriptDeclaration($output3);
	} else {
		$output3 = 'ADAG(function(){
				ADAG("#geo_type1").prop("checked","checked");
			});
		';
		$document->addScriptDeclaration($output3);
	}
?>
