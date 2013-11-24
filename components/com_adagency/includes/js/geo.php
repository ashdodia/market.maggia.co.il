<script type="text/javascript">

	// Initialise properties when adding new limitations
	function initAll(){
		ADAG('#geo_type1').prop('checked','checked');
		window.setTimeout(function(){removeTyper();},100);
	}

	<?php if(isset($configs->geoparams['allowgeo']) && ($configs->geoparams['allowgeo'] == '1')){ ?>
	ADAG(document).ready(function() {
		if(!existsLim()) {
			document.getElementById('geo_container').style.display = 'none';
		}
	});
	<?php } ?>

	// Define trim function
	String.prototype.trim = function() {
		a = this.replace(/^\s+/, '');
		return a.replace(/\s+$/, '');
	};

	// Function to check if there is at least one limitation
	function existsLim() {
		var nodelist = document.getElementById("opts");
		var tr = nodelist.tBodies[0].firstChild;
		var existsTr = false;
		while(tr){
			if(tr.nodeName == "TR") {
				existsTr = true;
			}
			tr = tr.nextSibling;
		}
		return existsTr;
	}

	// This function will remove all options [with removeChild]
	function removeAll(){
		var container = document.getElementById("tbdy");
		var nodelist = document.getElementById("opts");
		var tr = nodelist.tBodies[0].firstChild;
		var toRemove = new Array();
		i = 0;
		while(tr){
			if(tr.id) {
				toRemove[i] = tr.id;
				i++;
			}
			tr = tr.nextSibling;
		}

		for(var j=0;j<toRemove.length;j++){
			container.removeChild(document.getElementById(toRemove[j]));
		}
		ADAG('#limitation option').removeProp('disabled');
		document.getElementById('geo_container').style.display = "none";
	}

	function get_previoussibling(n)
	{
		//check if the previous sibling node is an element node
		x=n.previousSibling;
		while (x.nodeType!=1) {
  			x=x.previousSibling;
  		}
		return x;
	}

	function get_firstchild(n)
	{
		//check if the next sibling node is an element node
		x=n.firstChild;
		while (x.nodeType!=1)
  		{
  			x=x.nextSibling;
  		}
		return x;
	}

	// This function will delete the option by id [using removeChild]
	function deletelim(x){
		var container = document.getElementById("tbdy");
		var del = document.getElementById(""+x+"");
		ADAG('#limitation option:gt(0)').removeProp('disabled');	//[value='+del.className+']
		container.removeChild(del);
		if(existsLim()){
			var firstChild = get_firstchild(container);
			//document.getElementById(firstChild.id +'logical').style.display='none';
			updateColors();
		} else {
			document.getElementById("geo_container").style.display = "none";
		}
	}

	// Function to update the background color of the rows
	function updateColors(){
		var nodelist = document.getElementById("opts");
		var tr = nodelist.tBodies[0].firstChild;
		var i=1;
		var existsTr = 0;

		while(tr){
			if(tr.nodeName == "TR") {
				if(i%2==1){
					tr.style.backgroundColor = "#e6e6e6";
				} else {
					tr.style.backgroundColor = "#FFFFFF";
				}
				existsTr = 1;
			}

			i++;
			tr = tr.nextSibling;
		}
	}

	function selim(val, aux){
		<?php
			//echo "<pre>";var_dump($configs->geoparams);
			if(isset($configs->geoparams['c2'])&&($configs->geoparams['c2'] == '1')) { echo "var c2 = true;"; } else { echo "var c2 = 0;"; }
			if(isset($configs->geoparams['c3'])&&($configs->geoparams['c3'] == '1')) { echo "var c3 = true;"; } else { echo "var c3 = 0;"; }
		?>
		var val = val || document.getElementById("limitation").value;
		var aux = aux || null;

		if((val != 'region')&&(val != 'city')) {
			ADAG('#tbdy').html('');
		}
		if (val == 'continent') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','continent');
			myrow.style.border = '1px solid transparent';
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			mycellthree.innerHTML = '';
			var continents_set = 'AS,Asia|AF,Africa|EU,Europe|OC,Australia/Oceania|CA,Caribbean|SA,South America|NA,North America';
			var output = '<select id="limitation_select_'+currentid+'" name="limitation['+currentid+'][data][]" class="autocomplets">';
			var mySplitResult = continents_set.split("|");
			if(aux != null){
				temp_aux = aux.split('|');
			}

			for(var i=0;i<=mySplitResult.length-1;i++){
				var second = mySplitResult[i].split(",");
				var current_selected = '';
				if(aux != null) {
					for(var j=0;j<=temp_aux.length-1;j++){
						if(temp_aux[j] == second[0]) {
							current_selected = ' class="selected" ';
						}
					}
				}
				output += '<option value="'+second[0]+'"'+current_selected+'>'+second[1]+'</option>';
			}
			output += '</select>';
			mycellthree.innerHTML = output;

			mycellthree.align = "left";
			mycellthree.style.verticalAlign = "top";
			mycellthree.style.padding = "15px 0 15px 5px";
			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="continent" />';	//<a href="#" onclick="javascript:deletelim(\''+myrow.id+'\');return false;" style="text-decoration: underline;" ><?php echo JText::_('ADAG_REMOVE');?></a>

			mycellfour.align = "left";
			mycellfour.width = "100";
			mycellfour.style.padding = "15px 0px";
			mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#limitation_select_"+currentid).fcbkcomplete({ filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false });
				}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo JText::_('ADAG_GEO_TYPE_CONT');?>';
					ADAG(".continent .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if(val == 'country') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.id = 'limitation-'+currentid;
			myrow.style.border = '1px solid transparent';
			myrow.setAttribute('class','country');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();
			if(aux != null){
				temp_aux = aux.split('|');
			}
			var outputs = "";
			var mySplitResult = myString.split("|");
			for(var i=0;i<=mySplitResult.length-1;i++){
				var second = mySplitResult[i].split(",");
				var current_selected = '';
				if(aux != null) {
					for(var j=0;j<=temp_aux.length-1;j++){
						if(temp_aux[j] == second[0]) {
							current_selected = ' class="selected" ';
						}
					}
				}
				outputs += '<option value="'+second[0]+'"'+current_selected+'>'+second[1]+'</option>';
			}
			var output = '<select id="limitation_select_'+currentid+'" name="limitation['+currentid+'][data][]" class="autocomplets">'+outputs+'</select>';

			mycellthree.innerHTML = output;
			mycellthree.align = "left";
			mycellthree.style.verticalAlign = "top";
			mycellthree.style.padding = "15px 0 15px 5px";
			if(c2 || c3) { var firstOption = '<br /><input type="radio" name="coptions" id="firstOption" /><label for="firstOption"><?php echo JText::_('ADAG_GEO_EVERYWH');?></label>'; } else { var firstOption = '';}
			if(c2) { var secondOption = '<br /><input type="radio" name="coptions" id="secondOption" /><label for="secondOption"><?php echo JText::_('ADAG_GEO_BYSTPRO');?></label>'; } else { var secondOption = ''; }
			if(c3) { var thirdOption = '<br /><input type="radio" name="coptions" id="thirdOption" /><label for="thirdOption"><?php echo JText::_('ADAG_GEO_BYCITY');?></label>'; } else { var thirdOption = ''; }
			ADAG('#limitation-'+currentid+' td:first').append('<div id="country_container">' + firstOption + secondOption + thirdOption + '</div>');
			ADAG('#country_container').hide();
			ADAG('#firstOption').prop('checked','true').change(function(){
				ADAG('#region_container').remove();
				ADAG('#tbdy tr .city').remove();
			});
			ADAG('#secondOption').change(function(){
				ADAG('<table id="region_container">').insertAfter(ADAG(this).next('label'));
				ADAG('#city_container').remove();
				selim('region');
			});
			ADAG('#thirdOption').change(function(){
				ADAG('<table id="city_container">').insertAfter(ADAG(this).next('label'));
				ADAG('#region_container').remove();
				selim('city');
			});

			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="country" />';//<a href="#" onclick="javascript:deletelim(\''+myrow.id+'\');return false;" style="text-decoration: underline;" ><?php echo JText::_('ADAG_REMOVE');?></a>

			mycellfour.align = "left";
			mycellfour.width = "100";
			mycellfour.style.padding = "15px 0px";
			mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#limitation_select_"+currentid).fcbkcomplete({ filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false });
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo JText::_('ADAG_GEO_TYPE_COUN');?>';
					ADAG(".country .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val=="region") {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','region');
			document.getElementById('region_container').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			mycellthree.setAttribute('colspan','4');
			myrow.appendChild(mycellthree);
			myrow.style.backgroundColor = "#e6e6e6";
			if(currentid==1) { var display='none';} else { var display='';}
			var outputs = "<table>";
			var temp_aux = null;
			if(aux != null){
				temp_aux = aux.split('|');
			} else {
				//temp_aux = ADAG('.country .holder:first .bit-box:first').attr('rel');
				temp_aux = new Array();
				temp_aux[0] = ADAG('.country .holder:first .bit-box:first').attr('rel');
			}
			var has_selected = false;
			var getRegions = "";
			if(temp_aux != null){
				var getRegs = getRegionByCountryCode(temp_aux[0]);
				for(var j=0;j<=getRegs.length-1;j++){
					var selected_region = '';
					var splitEm = getRegs[j].split(",");
					for(var k=1;k<=temp_aux.length-1;k++){
						if(temp_aux[k].toString() == splitEm[0].toString()) {
							selected_region = ' class="selected" ';
						}
					}
					getRegions += "<option value=\""+splitEm[0]+"\""+selected_region+">"+splitEm[1]+"</option>";
				}
			}

			outputs+="<tr><td style='display: none;'><?php echo JText::_('ADAG_REGS');?></td><td><select class=\"upd8regs\" id=\"updateRegions-"+currentid+"\" name=\"limitation["+currentid+"][data][]\">"+getRegions+"</select></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='region' />";//</td></tr>
			var output = '<p />'+outputs;

			mycellthree.innerHTML = output;
			mycellthree.align = "left";
			mycellthree.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#updateRegions-"+currentid).fcbkcomplete({filter_selected: true, onselect: onSelectItem, firstselected: false});//, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false
				if(has_selected) {removeTyperRegion();}
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo JText::_('ADAG_GEO_TYPE_STAT');?>';
					ADAG(".region .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val=='city') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','city');
			document.getElementById('city_container').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			myrow.style.backgroundColor = "#e6e6e6";
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();
			var outputs = "<table width='100%'>";

		//	outputs+="<tr><td><?php echo JText::_('ADAG_CITY');?></td><td><input size=\"40\" type=\"text\" name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"city\" class='input20' onkeyup='ADAG(this).parent(\".city td:first\").css(\"border\",\"1px solid transparent\");' /></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='city' />";
			outputs+="<tr><td style='display: none;'><?php echo JText::_('ADAG_CITY');?></td><td><select class='upd8city' name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"city\" class='input20'></select></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='city' />";
			mycellthree.innerHTML = outputs;
			mycellthree.align = "left";
			mycellthree.setAttribute('colspan','3')
			mycellthree.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) + 1;
			updateColors();
			//alert(aux);
			if(aux != null) {
				//alert(aux);
				var temp = aux.split('|');
				for(var i=0;i<=temp.length-1;i++){
					ADAG('<option class="selected" selected="selected" value="'+temp[i]+'">'+temp[i]+'</option>').appendTo('.upd8city');
				}
			}
			window.setTimeout(function(){
				var the_country = ADAG(".country .holder:first .bit-box:first").attr('rel');
				//alert(the_country);
				if(typeof(the_country) != 'undefined') {
					ADAG("#limitation-"+currentid+" .upd8city").fcbkcomplete({ json_url: "<?php echo JUri::root().$configs->countryloc."/country-"; ?>" + the_country + ".txt", filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false}); //, maxitems: 1
					//alert('ADAG("#limitation-'+currentid+' .upd8city").fcbkcomplete({ json_url: "<?php echo JUri::root()."geoip/countries/country-"; ?>' + the_country + '.txt", filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false});');
				}
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo JText::_('ADAG_GEO_TYPE_CITY');?>';
					ADAG(".city .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val == "latitude") {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','latitude');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var outputs = "<table style=\"width: 50%;\" border=\"0\"><tr><td><input type=\"text\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][a]\" size=\"20\" value=\"0.0000\"/></td><td>&nbsp;>&nbsp;Latitude&nbsp;<&nbsp;</td><td><input type=\"text\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][b]\" size=\"20\" value=\"0.0000\"/></td></tr><tr><td><input type=\"text\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][c]\" size=\"20\" value=\"0.0000\"/></td><td>&nbsp;>&nbsp;Longitude&nbsp;<&nbsp;</td><td><input type=\"text\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][d]\" size=\"20\" value=\"0.0000\"/></td></tr></table>";
			var output = outputs;

			mycellthree.innerHTML = output;
			mycellthree.align = "left";
			mycellthree.style.verticalAlign = "top";
			mycellthree.style.padding = "15px 0 15px 5px";
			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="latitude" />';	//<a href="#" onclick="javascript:deletelim(\''+myrow.id+'\');return false;" style="text-decoration: underline;" ><?php echo JText::_('ADAG_REMOVE');?></a>
			mycellfour.align = "left";
			mycellfour.width = "100";
			mycellfour.style.padding = "15px 0px";
			mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
		} else if (val == 'dma') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','dma');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getDMA();
			if(aux != null){
				temp_aux = aux.split('|');
			}
			var current_selected = '';
			var outputs = "<select name=\"limitation["+currentid+"][data][]\" class=\"autocomplets\">";
			var mySplitResult = myString.split("|");
			for(var i=0;i<=mySplitResult.length-1;i++){
				var second = mySplitResult[i].split("*");
				current_selected = '';
				if(aux != null) {
					for(var j=0;j<=temp_aux.length-1;j++){
						if(temp_aux[j] == second[1]) {
							current_selected = ' class="selected" ';
						}
					}
				}
				outputs = outputs + "<option value=\""+second[1]+"\""+current_selected+">"+second[0]+"</option>";
			}
			outputs += "</select>";
			var output = outputs;

			mycellthree.innerHTML = output;
			//alert(newout);
			mycellthree.align = "left";
			mycellthree.style.verticalAlign = "top";
			mycellthree.style.padding = "15px 0 15px 5px";
			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="dma" />';//<a href="#" onclick="javascript:deletelim(\''+myrow.id+'\');return false;" style="text-decoration: underline;" ><?php echo JText::_('ADAG_REMOVE');?></a>
			mycellfour.align = "left";
			mycellfour.width = "100";
			mycellfour.style.padding = "15px 0px";
			mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#limitation-"+currentid+" .autocomplets").fcbkcomplete({ filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false});
			},1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo JText::_('ADAG_GEO_TYPE_DMA');?>';
					ADAG(".dma .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val == 'usarea') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','usarea');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();

			var outputs = "<table width='100%'>";
			outputs+="<tr><td style='display:none;'><?php echo JText::_('ADAG_AREACODE');?></td><td><select class='upd8usarea autocomplets' name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"usarea\" class='input20'></select></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='usarea' />";
			mycellthree.innerHTML = outputs;
			mycellthree.align = "left";
			mycellthree.setAttribute('colspan','3')
			mycellthree.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) + 1;
			updateColors();
			//alert(aux);
			if(aux != null) {
				//alert(aux);
				var temp = aux.split('|');
				for(var i=0;i<=temp.length-1;i++){
					ADAG('<option class="selected" selected="selected" value="'+temp[i]+'">'+temp[i]+'</option>').appendTo('.upd8usarea');
				}
			}

			window.setTimeout(function(){
				ADAG("#limitation-"+currentid+" .upd8usarea").fcbkcomplete({ json_url: "<?php echo JUri::root().$configs->codeloc."/areacode.txt"; ?>", filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false});//, maxitems: 1
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo JText::_('ADAG_GEO_TYPE_AREA');?>';
					ADAG(".usarea .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val == 'postalcode') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','postalcode');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();

			var outputs = "<table width='100%'>";
			//<select class='upd8postalcode autocomplets' name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"postalcode\" class='input20'></select>
			outputs+="<tr><td style=\"display: none;\"><?php echo JText::_('ADAG_POSTALCODE');?></td><td><input size=\"40\" type=\"text\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][]\" class=\"input20\" value=\"<?php echo JText::_('ADAG_ZIPNOTE');?>\" /></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='postalcode' />";
			if(aux == null) {
				window.setTimeout(function(){
					ADAG("#limitation-"+currentid+" .input20").focus(function() {
						ADAG(this).val('');
					}).blur(function(){
						if(ADAG(this).val() == '') {
							ADAG(this).val('<?php echo JText::_('Enter zip/postal code separated by comma');?>');
						}
					});
				},1);
			}
			mycellthree.innerHTML = outputs;
			mycellthree.align = "left";
			mycellthree.setAttribute('colspan','3')
			mycellthree.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) + 1;
			updateColors();
			/*if(aux != null) {
				ADAG('<option class="selected" selected="selected" value='+aux+'>'+aux+'</option>').appendTo('.upd8postalcode');
			}
			window.setTimeout(function(){
				ADAG("#limitation-"+currentid+" .upd8postalcode").fcbkcomplete({ json_url: "<?php echo JUri::root()."geoip/code/postalcode.txt"; ?>", filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false, maxitems: 1 });
			}, 1);*/

		}

		/*ADAG('#limitation option:gt(0)').attr('disabled','disabled');//[value='+val+']
		ADAG('#limitation option:first').prop('selected','selected');*/
		ADAG("#limitation option[value="+val+"]").prop('selected','selected');
		initAll();
	}

	// Function to return countries and their codes
	function getCountryCodes(){
		var codes = 'AF,Afghanistan|AX,Aland Islands|AL,Albania|DZ,Algeria|AS,American Samoa|AD,Andorra|AO,Angola|AI,Anguilla|AQ,Antarctica|AG,Antigua and Barbuda|AR,Argentina|AM,Armenia|AW,Aruba|AP,Asia/Pacific Region|AU,Australia|AT,Austria|AZ,Azerbaijan|BS,Bahamas|BH,Bahrain|BD,Bangladesh|BB,Barbados|BY,Belarus|BE,Belgium|BZ,Belize|BJ,Benin|BM,Bermuda|BT,Bhutan|BO,Bolivia|BA,Bosnia and Herzegovina|BW,Botswana|BV,Bouvet Island|BR,Brazil|IO,British Indian Ocean Territory|BN,Brunei Darussalam|BG,Bulgaria|BF,Burkina Faso|BI,Burundi|KH,Cambodia|CM,Cameroon|CA,Canada|CV,Cape Verde|KY,Cayman Islands|CF,Central African Republic|TD,Chad|CL,Chile|CN,China|CX,Christmas Island|CC,Cocos (Keeling) Islands|CO,Colombia|KM,Comoros|CD,Congo, The Democratic Republic of the|CG,Congo|CK,Cook Islands|CR,Costa Rica|CI,Cote d`Ivoire|HR,Croatia|CU,Cuba|CY,Cyprus|CZ,Czech Republic|DK,Denmark|DJ,Djibouti|DM,Dominica|DO,Dominican Republic|EC,Ecuador|EG,Egypt|SV,El Salvador|GQ,Equatorial Guinea|ER,Eritrea|EE,Estonia|ET,Ethiopia|EU,Europe|FK,Falkland Islands (Malvinas)|FO,Faroe Islands|FJ,Fiji|FI,Finland|FR,France|GF,French Guiana|PF,French Polynesia|TF,French Southern Territories|GA,Gabon|GM,Gambia|GE,Georgia|DE,Germany|GH,Ghana|GI,Gibraltar|GR,Greece|GL,Greenland|GD,Grenada|GP,Guadeloupe|GU,Guam|GT,Guatemala|GG,Guernsey|GN,Guinea|GW,Guinea-Bissau|GY,Guyana|HT,Haiti|HM,Heard Island and McDonald Islands|VA,Holy See (Vatican City State)|HN,Honduras|HK,Hong Kong|HU,Hungary|IS,Iceland|IN,India|ID,Indonesia|IR,Iran, Islamic Republic of|IQ,Iraq|IE,Ireland|IM,Isle of Man|IL,Israel|IT,Italy|JM,Jamaica|JP,Japan|JE,Jersey|JO,Jordan|KZ,Kazakhstan|KE,Kenya|KI,Kiribati|KP,Korea, Democratic People`s Republic of|KR,Korea, Republic of|KW,Kuwait|KG,Kyrgyzstan|LA,Lao People`s Democratic Republic|LV,Latvia|LB,Lebanon|LS,Lesotho|LR,Liberia|LY,Libyan Arab Jamahiriya|LI,Liechtenstein|LT,Lithuania|LU,Luxembourg|MO,Macao|MK,Macedonia|MG,Madagascar|MW,Malawi|MY,Malaysia|MV,Maldives|ML,Mali|MT,Malta|MH,Marshall Islands|MQ,Martinique|MR,Mauritania|MU,Mauritius|YT,Mayotte|MX,Mexico|FM,Micronesia, Federated States of|MD,Moldova, Republic of|MC,Monaco|MN,Mongolia|ME,Montenegro|MS,Montserrat|MA,Morocco|MZ,Mozambique|MM,Myanmar|NA,Namibia|NR,Nauru|NP,Nepal|AN,Netherlands Antilles|NL,Netherlands|NC,New Caledonia|NZ,New Zealand|NI,Nicaragua|NE,Niger|NG,Nigeria|NU,Niue|NF,Norfolk Island|MP,Northern Mariana Islands|NO,Norway|OM,Oman|PK,Pakistan|PW,Palau|PS,Palestinian Territory|PA,Panama|PG,Papua New Guinea|PY,Paraguay|PE,Peru|PH,Philippines|PN,Pitcairn|PL,Poland|PT,Portugal|PR,Puerto Rico|QA,Qatar|RE,Reunion|RO,Romania|RU,Russian Federation|RW,Rwanda|SH,Saint Helena|KN,Saint Kitts and Nevis|LC,Saint Lucia|PM,Saint Pierre and Miquelon|VC,Saint Vincent and the Grenadines|WS,Samoa|SM,San Marino|ST,Sao Tome and Principe|SA,Saudi Arabia|SN,Senegal|RS,Serbia|SC,Seychelles|SL,Sierra Leone|SG,Singapore|SK,Slovakia|SI,Slovenia|SB,Solomon Islands|SO,Somalia|ZA,South Africa|GS,South Georgia and the South Sandwich Islands|ES,Spain|LK,Sri Lanka|SD,Sudan|SR,Suriname|SJ,Svalbard and Jan Mayen|SZ,Swaziland|SE,Sweden|CH,Switzerland|SY,Syrian Arab Republic|TW,Taiwan|TJ,Tajikistan|TZ,Tanzania, United Republic of|TH,Thailand|TL,Timor-Leste|TG,Togo|TK,Tokelau|TO,Tonga|TT,Trinidad and Tobago|TN,Tunisia|TR,Turkey|TM,Turkmenistan|TC,Turks and Caicos Islands|TV,Tuvalu|UG,Uganda|UA,Ukraine|AE,United Arab Emirates|GB,United Kingdom|UM,United States Minor Outlying Islands|US,United States|UY,Uruguay|UZ,Uzbekistan|VU,Vanuatu|VE,Venezuela|VN,Vietnam|VG,Virgin Islands, British|VI,Virgin Islands, U.S.|WF,Wallis and Futuna|EH,Western Sahara|YE,Yemen|ZM,Zambia|ZW,Zimbabwe|A1,Anonymous Proxy|A2,Satellite Provider|O1,Other Country';
		return codes;
	}

	function getCountriesWithRegions(){
		var codes = "AF,Afghanistan|AL,Albania|DZ,Algeria|AD,Andorra|AO,Angola|AG,Antigua and Barbuda|AR,Argentina|AM,Armenia|AU,Australia|AT,Austria|AZ,Azerbaijan|BS,Bahamas|BH,Bahrain|BD,Bangladesh|BB,Barbados|BY,Belarus|BE,Belgium|BZ,Belize|BJ,Benin|BM,Bermuda|BT,Bhutan|BO,Bolivia|BA,Bosnia and Herzegovina|BW,Botswana|BR,Brazil|BN,Brunei Darussalam|BG,Bulgaria|BF,Burkina Faso|BI,Burundi|KH,Cambodia|CM,Cameroon|CA,Canada|CV,Cape Verde|KY,Cayman Islands|CF,Central African Republic|TD,Chad|CL,Chile|CN,China|CO,Colombia|KM,Comoros|CG,Congo|CR,Costa Rica|CI,Cote D'Ivoire|HR,Croatia|CU,Cuba|CY,Cyprus|CZ,Czech Republic|DK,Denmark|DJ,Djibouti|DM,Dominica|DO,Dominican Republic|EC,Ecuador|EG,Egypt|SV,El Salvador|GQ,Equatorial Guinea|EE,Estonia|ET,Ethiopia|FJ,Fiji|FI,Finland|FR,France|GA,Gabon|GM,Gambia|GE,Georgia|DE,Germany|GH,Ghana|GR,Greece|GL,Greenland|GD,Grenada|GT,Guatemala|GN,Guinea|GW,Guinea-Bissau|GY,Guyana|HT,Haiti|HN,Honduras|HU,Hungary|IS,Iceland|IN,India|ID,Indonesia|IR,Iran, Islamic Republic of|IQ,Iraq|IE,Ireland|IL,Israel|IT,Italy|JM,Jamaica|JP,Japan|JO,Jordan|KZ,Kazakhstan|KE,Kenya|KI,Kiribati|KP,Korea, Democratic People's Republic of|KR,Korea, Republic of|KW,Kuwait|KG,Kyrgyzstan|LA,Lao People's Democratic Republic|LV,Latvia|LB,Lebanon|LS,Lesotho|LR,Liberia|LY,Libyan Arab Jamahiriya|LI,Liechtenstein|LT,Lithuania|LU,Luxembourg|MO,Macau|MK,Macedonia|MG,Madagascar|MW,Malawi|MY,Malaysia|MV,Maldives|ML,Mali|MR,Mauritania|MU,Mauritius|MX,Mexico|FM,Micronesia, Federated States of|MD,Moldova, Republic of|MC,Monaco|MN,Mongolia|MS,Montserrat|MA,Morocco|MZ,Mozambique|MM,Myanmar|NA,Namibia|NR,Nauru|NP,Nepal|NL,Netherlands|NZ,New Zealand|NI,Nicaragua|NE,Niger|NG,Nigeria|NO,Norway|OM,Oman|PK,Pakistan|PA,Panama|PG,Papua New Guinea|PY,Paraguay|PE,Peru|PH,Philippines|PL,Poland|PT,Portugal|QA,Qatar|RO,Romania|RU,Russian Federation|RW,Rwanda|SH,Saint Helena|KN,Saint Kitts and Nevis|LC,Saint Lucia|VC,Saint Vincent and the Grenadines|WS,Samoa|SM,San Marino|ST,Sao Tome and Principe|SA,Saudi Arabia|SN,Senegal|SC,Seychelles|SL,Sierra Leone|SK,Slovakia|SB,Solomon Islands|SO,Somalia|ZA,South Africa|ES,Spain|LK,Sri Lanka|SD,Sudan|SR,Suriname|SZ,Swaziland|SE,Sweden|CH,Switzerland|SY,Syrian Arab Republic|TW,Taiwan|TZ,Tanzania, United Republic of|TH,Thailand|TG,Togo|TO,Tonga|TT,Trinidad and Tobago|TN,Tunisia|TR,Turkey|TM,Turkmenistan|UG,Uganda|UA,Ukraine|AE,United Arab Emirates|GB,United Kingdom|US,United States|UY,Uruguay|UZ,Uzbekistan|VU,Vanuatu|VE,Venezuela|VN,Vietnam|YE,Yemen|ZR,Zaire|ZM,Zambia|ZW,Zimbabwe";
		return codes;
	}

	function getRegionByCountryCode(code) {
		var regions = "AD,02,Canillo |AD,03,Encamp |AD,04,La Massana |AD,05,Ordino |AD,06,Sant Julia de Loria |AD,07,Andorra la Vella |AD,08,Escaldes-Engordany |AE,01,Abu Dhabi |AE,02,Ajman |AE,03,Dubai |AE,04,Fujairah |AE,05,Ras Al Khaimah |AE,06,Sharjah |AE,07,Umm Al Quwain |AF,01,Badakhshan |AF,02,Badghis |AF,03,Baghlan |AF,05,Bamian |AF,06,Farah |AF,07,Faryab |AF,08,Ghazni |AF,09,Ghowr |AF,10,Helmand |AF,11,Herat |AF,13,Kabol |AF,14,Kapisa |AF,15,Konar |AF,16,Laghman |AF,17,Lowgar |AF,18,Nangarhar |AF,19,Nimruz |AF,21,Paktia |AF,22,Parvan |AF,23,Kandahar |AF,24,Kondoz |AF,26,Takhar |AF,27,Vardak |AF,28,Zabol |AF,29,Paktika |AF,30,Balkh |AF,31,Jowzjan |AF,32,Samangan |AF,33,Sar-e Pol |AF,34,Konar |AF,35,Laghman |AF,36,Paktia |AF,37,Khowst |AF,38,Nurestan |AF,39,Oruzgan |AF,40,Parvan |AF,41,Daykondi |AF,42,Panjshir |AG,01,Barbuda |AG,03,Saint George |AG,04,Saint John |AG,05,Saint Mary |AG,06,Saint Paul |AG,07,Saint Peter |AG,08,Saint Philip |AL,40,Berat |AL,41,Diber |AL,42,Durres |AL,43,Elbasan |AL,44,Fier |AL,45,Gjirokaster |AL,46,Korce |AL,47,Kukes |AL,48,Lezhe |AL,49,Shkoder |AL,50,Tirane |AL,51,Vlore |AM,01,Aragatsotn |AM,02,Ararat |AM,03,Armavir |AM,04,Geghark'unik' |AM,05,Kotayk' |AM,06,Lorri |AM,07,Shirak |AM,08,Syunik' |AM,09,Tavush |AM,10,Vayots' Dzor |AM,11,Yerevan |AO,01,Benguela |AO,02,Bie |AO,03,Cabinda |AO,04,Cuando Cubango |AO,05,Cuanza Norte |AO,06,Cuanza Sul |AO,07,Cunene |AO,08,Huambo |AO,09,Huila |AO,10,Luanda |AO,12,Malanje |AO,13,Namibe |AO,14,Moxico |AO,15,Uige |AO,16,Zaire |AO,17,Lunda Norte |AO,18,Lunda Sul |AO,19,Bengo |AO,20,Luanda |AR,01,Buenos Aires |AR,02,Catamarca |AR,03,Chaco |AR,04,Chubut |AR,05,Cordoba |AR,06,Corrientes |AR,07,Distrito Federal |AR,08,Entre Rios |AR,09,Formosa |AR,10,Jujuy |AR,11,La Pampa |AR,12,La Rioja |AR,13,Mendoza |AR,14,Misiones |AR,15,Neuquen |AR,16,Rio Negro |AR,17,Salta |AR,18,San Juan |AR,19,San Luis |AR,20,Santa Cruz |AR,21,Santa Fe |AR,22,Santiago del Estero |AR,23,Tierra del Fuego |AR,24,Tucuman |AT,01,Burgenland |AT,02,Karnten |AT,03,Niederosterreich |AT,04,Oberosterreich |AT,05,Salzburg |AT,06,Steiermark |AT,07,Tirol |AT,08,Vorarlberg |AT,09,Wien |AU,01,Australian Capital Territory |AU,02,New South Wales |AU,03,Northern Territory |AU,04,Queensland |AU,05,South Australia |AU,06,Tasmania |AU,07,Victoria |AU,08,Western Australia |AZ,01,Abseron |AZ,02,Agcabadi |AZ,03,Agdam |AZ,04,Agdas |AZ,05,Agstafa |AZ,06,Agsu |AZ,07,Ali Bayramli |AZ,08,Astara |AZ,09,Baki |AZ,10,Balakan |AZ,11,Barda |AZ,12,Beylaqan |AZ,13,Bilasuvar |AZ,14,Cabrayil |AZ,15,Calilabad |AZ,16,Daskasan |AZ,17,Davaci |AZ,18,Fuzuli |AZ,19,Gadabay |AZ,20,Ganca |AZ,21,Goranboy |AZ,22,Goycay |AZ,23,Haciqabul |AZ,24,Imisli |AZ,25,Ismayilli |AZ,26,Kalbacar |AZ,27,Kurdamir |AZ,28,Lacin |AZ,29,Lankaran |AZ,30,Lankaran |AZ,31,Lerik |AZ,32,Masalli |AZ,33,Mingacevir |AZ,34,Naftalan |AZ,35,Naxcivan |AZ,36,Neftcala |AZ,37,Oguz |AZ,38,Qabala |AZ,39,Qax |AZ,40,Qazax |AZ,41,Qobustan |AZ,42,Quba |AZ,43,Qubadli |AZ,44,Qusar |AZ,45,Saatli |AZ,46,Sabirabad |AZ,47,Saki |AZ,48,Saki |AZ,49,Salyan |AZ,50,Samaxi |AZ,51,Samkir |AZ,52,Samux |AZ,53,Siyazan |AZ,54,Sumqayit |AZ,55,Susa |AZ,56,Susa |AZ,57,Tartar |AZ,58,Tovuz |AZ,59,Ucar |AZ,60,Xacmaz |AZ,61,Xankandi |AZ,62,Xanlar |AZ,63,Xizi |AZ,64,Xocali |AZ,65,Xocavand |AZ,66,Yardimli |AZ,67,Yevlax |AZ,68,Yevlax |AZ,69,Zangilan |AZ,70,Zaqatala |AZ,71,Zardab |BA,01,Federation of Bosnia and Herzegovina |BA,02,Republika Srpska |BB,01,Christ Church |BB,02,Saint Andrew |BB,03,Saint George |BB,04,Saint James |BB,05,Saint John |BB,06,Saint Joseph |BB,07,Saint Lucy |BB,08,Saint Michael |BB,09,Saint Peter |BB,10,Saint Philip |BB,11,Saint Thomas |BD,01,Barisal |BD,04,Bandarban |BD,05,Comilla |BD,12,Mymensingh |BD,13,Noakhali |BD,15,Patuakhali |BD,22,Bagerhat |BD,23,Bhola |BD,24,Bogra |BD,25,Barguna |BD,26,Brahmanbaria |BD,27,Chandpur |BD,28,Chapai Nawabganj |BD,29,Chattagram |BD,30,Chuadanga |BD,31,Cox's Bazar |BD,32,Dhaka |BD,33,Dinajpur |BD,34,Faridpur |BD,35,Feni |BD,36,Gaibandha |BD,37,Gazipur |BD,38,Gopalganj |BD,39,Habiganj |BD,40,Jaipurhat |BD,41,Jamalpur |BD,42,Jessore |BD,43,Jhalakati |BD,44,Jhenaidah |BD,45,Khagrachari |BD,46,Khulna |BD,47,Kishorganj |BD,48,Kurigram |BD,49,Kushtia |BD,50,Laksmipur |BD,51,Lalmonirhat |BD,52,Madaripur |BD,53,Magura |BD,54,Manikganj |BD,55,Meherpur |BD,56,Moulavibazar |BD,57,Munshiganj |BD,58,Naogaon |BD,59,Narail |BD,60,Narayanganj |BD,61,Narsingdi |BD,62,Nator |BD,63,Netrakona |BD,64,Nilphamari |BD,65,Pabna |BD,66,Panchagar |BD,67,Parbattya Chattagram |BD,68,Pirojpur |BD,69,Rajbari |BD,70,Rajshahi |BD,71,Rangpur |BD,72,Satkhira |BD,73,Shariyatpur |BD,74,Sherpur |BD,75,Sirajganj |BD,76,Sunamganj |BD,77,Sylhet |BD,78,Tangail |BD,79,Thakurgaon |BD,81,Dhaka |BD,82,Khulna |BD,83,Rajshahi |BD,84,Chittagong |BD,85,Barisal |BD,86,Sylhet |BE,01,Antwerpen |BE,02,Brabant |BE,03,Hainaut |BE,04,Liege |BE,05,Limburg |BE,06,Luxembourg |BE,07,Namur |BE,08,Oost-Vlaanderen |BE,09,West-Vlaanderen |BE,10,Brabant Wallon |BE,11,Brussels Hoofdstedelijk Gewest |BE,12,Vlaams-Brabant |BF,15,Bam |BF,19,Boulkiemde |BF,20,Ganzourgou |BF,21,Gnagna |BF,28,Kouritenga |BF,33,Oudalan |BF,34,Passore |BF,36,Sanguie |BF,40,Soum |BF,42,Tapoa |BF,44,Zoundweogo |BF,45,Bale |BF,46,Banwa |BF,47,Bazega |BF,48,Bougouriba |BF,49,Boulgou |BF,50,Gourma |BF,51,Houet |BF,52,Ioba |BF,53,Kadiogo |BF,54,Kenedougou |BF,55,Komoe |BF,56,Komondjari |BF,57,Kompienga |BF,58,Kossi |BF,59,Koulpelogo |BF,60,Kourweogo |BF,61,Leraba |BF,62,Loroum |BF,63,Mouhoun |BF,64,Namentenga |BF,65,Naouri |BF,66,Nayala |BF,67,Noumbiel |BF,68,Oubritenga |BF,69,Poni |BF,70,Sanmatenga |BF,71,Seno |BF,72,Sissili |BF,73,Sourou |BF,74,Tuy |BF,75,Yagha |BF,76,Yatenga |BF,77,Ziro |BF,78,Zondoma |BG,33,Mikhaylovgrad |BG,38,Blagoevgrad |BG,39,Burgas |BG,40,Dobrich |BG,41,Gabrovo |BG,42,Grad Sofiya |BG,43,Khaskovo |BG,44,Kurdzhali |BG,45,Kyustendil |BG,46,Lovech |BG,47,Montana |BG,48,Pazardzhik |BG,49,Pernik |BG,50,Pleven |BG,51,Plovdiv |BG,52,Razgrad |BG,53,Ruse |BG,54,Shumen |BG,55,Silistra |BG,56,Sliven |BG,57,Smolyan |BG,58,Sofiya |BG,59,Stara Zagora |BG,60,Turgovishte |BG,61,Varna |BG,62,Veliko Turnovo |BG,63,Vidin |BG,64,Vratsa |BG,65,Yambol |BH,01,Al Hadd |BH,02,Al Manamah |BH,03,Al Muharraq |BH,05,Jidd Hafs |BH,06,Sitrah |BH,07,Ar Rifa' wa al Mintaqah al Janubiyah |BH,08,Al Mintaqah al Gharbiyah |BH,09,Mintaqat Juzur Hawar |BH,10,Al Mintaqah ash Shamaliyah |BH,11,Al Mintaqah al Wusta |BH,12,Madinat |BH,13,Ar Rifa |BH,14,Madinat Hamad |BH,15,Al Muharraq |BH,16,Al Asimah |BH,17,Al Janubiyah |BH,18,Ash Shamaliyah |BH,19,Al Wusta |BI,02,Bujumbura |BI,09,Bubanza |BI,10,Bururi |BI,11,Cankuzo |BI,12,Cibitoke |BI,13,Gitega |BI,14,Karuzi |BI,15,Kayanza |BI,16,Kirundo |BI,17,Makamba |BI,18,Muyinga |BI,19,Ngozi |BI,20,Rutana |BI,21,Ruyigi |BI,22,Muramvya |BI,23,Mwaro |BJ,01,Atakora |BJ,02,Atlantique |BJ,03,Borgou |BJ,04,Mono |BJ,05,Oueme |BJ,06,Zou |BJ,07,Alibori |BJ,08,Atakora |BJ,09,Atlanyique |BJ,10,Borgou |BJ,11,Collines |BJ,12,Kouffo |BJ,13,Donga |BJ,14,Littoral |BJ,15,Mono |BJ,16,Oueme |BJ,17,Plateau |BJ,18,Zou |BM,01,Devonshire |BM,02,Hamilton |BM,03,Hamilton |BM,04,Paget |BM,05,Pembroke |BM,06,Saint George |BM,07,Saint George's |BM,08,Sandys |BM,09,Smiths |BM,10,Southampton |BM,11,Warwick |BN,07,Alibori |BN,08,Belait |BN,09,Brunei and Muara |BN,10,Temburong |BN,11,Collines |BN,12,Kouffo |BN,13,Donga |BN,14,Littoral |BN,15,Tutong |BN,16,Oueme |BN,17,Plateau |BN,18,Zou |BO,01,Chuquisaca |BO,02,Cochabamba |BO,03,El Beni |BO,04,La Paz |BO,05,Oruro |BO,06,Pando |BO,07,Potosi |BO,08,Santa Cruz |BO,09,Tarija |BR,01,Acre |BR,02,Alagoas |BR,03,Amapa |BR,04,Amazonas |BR,05,Bahia |BR,06,Ceara |BR,07,Distrito Federal |BR,08,Espirito Santo |BR,11,Mato Grosso do Sul |BR,13,Maranhao |BR,14,Mato Grosso |BR,15,Minas Gerais |BR,16,Para |BR,17,Paraiba |BR,18,Parana |BR,20,Piaui |BR,21,Rio de Janeiro |BR,22,Rio Grande do Norte |BR,23,Rio Grande do Sul |BR,24,Rondonia |BR,25,Roraima |BR,26,Santa Catarina |BR,27,Sao Paulo |BR,28,Sergipe |BR,29,Goias |BR,30,Pernambuco |BR,31,Tocantins |BS,05,Bimini |BS,06,Cat Island |BS,10,Exuma |BS,13,Inagua |BS,15,Long Island |BS,16,Mayaguana |BS,18,Ragged Island |BS,22,Harbour Island |BS,23,New Providence |BS,24,Acklins and Crooked Islands |BS,25,Freeport |BS,26,Fresh Creek |BS,27,Governor's Harbour |BS,28,Green Turtle Cay |BS,29,High Rock |BS,30,Kemps Bay |BS,31,Marsh Harbour |BS,32,Nichollstown and Berry Islands |BS,33,Rock Sound |BS,34,Sandy Point |BS,35,San Salvador and Rum Cay |BT,05,Bumthang |BT,06,Chhukha |BT,07,Chirang |BT,08,Daga |BT,09,Geylegphug |BT,10,Ha |BT,11,Lhuntshi |BT,12,Mongar |BT,13,Paro |BT,14,Pemagatsel |BT,15,Punakha |BT,16,Samchi |BT,17,Samdrup |BT,18,Shemgang |BT,19,Tashigang |BT,20,Thimphu |BT,21,Tongsa |BT,22,Wangdi Phodrang |BW,01,Central |BW,03,Ghanzi |BW,04,Kgalagadi |BW,05,Kgatleng |BW,06,Kweneng |BW,08,North-East |BW,09,South-East |BW,10,Southern |BW,11,North-West |BY,01,Brestskaya Voblasts' |BY,02,Homyel'skaya Voblasts' |BY,03,Hrodzyenskaya Voblasts' |BY,04,Minsk |BY,05,Minskaya Voblasts' |BY,06,Mahilyowskaya Voblasts' |BY,07,Vitsyebskaya Voblasts' |BZ,01,Belize |BZ,02,Cayo |BZ,03,Corozal |BZ,04,Orange Walk |BZ,05,Stann Creek |BZ,06,Toledo |CA,01,Alberta |CA,02,British Columbia |CA,03,Manitoba |CA,04,New Brunswick |CA,05,Newfoundland and Labrador |CA,07,Nova Scotia |CA,08,Ontario |CA,09,Prince Edward Island |CA,10,Quebec |CA,11,Saskatchewan |CA,12,Yukon Territory |CA,13,Northwest Territories |CA,14,Nunavut |CD,01,Bandundu |CD,02,Equateur |CD,04,Kasai-Oriental |CD,05,Katanga |CD,06,Kinshasa |CD,07,Kivu |CD,08,Bas-Congo |CD,09,Orientale |CD,10,Maniema |CD,11,Nord-Kivu |CD,12,Sud-Kivu |CD,13,Cuvette |CF,01,Bamingui-Bangoran |CF,02,Basse-Kotto |CF,03,Haute-Kotto |CF,04,Mambere-Kadei |CF,05,Haut-Mbomou |CF,06,Kemo |CF,07,Lobaye |CF,08,Mbomou |CF,09,Nana-Mambere |CF,11,Ouaka |CF,12,Ouham |CF,13,Ouham-Pende |CF,14,Cuvette-Ouest |CF,15,Nana-Grebizi |CF,16,Sangha-Mbaere |CF,17,Ombella-Mpoko |CF,18,Bangui |CG,01,Bouenza |CG,03,Cuvette |CG,04,Kouilou |CG,05,Lekoumou |CG,06,Likouala |CG,07,Niari |CG,08,Plateaux |CG,10,Sangha |CG,11,Pool |CG,12,Brazzaville |CH,01,Aargau |CH,02,Ausser-Rhoden |CH,03,Basel-Landschaft |CH,04,Basel-Stadt |CH,05,Bern |CH,06,Fribourg |CH,07,Geneve |CH,08,Glarus |CH,09,Graubunden |CH,10,Inner-Rhoden |CH,11,Luzern |CH,12,Neuchatel |CH,13,Nidwalden |CH,14,Obwalden |CH,15,Sankt Gallen |CH,16,Schaffhausen |CH,17,Schwyz |CH,18,Solothurn |CH,19,Thurgau |CH,20,Ticino |CH,21,Uri |CH,22,Valais |CH,23,Vaud |CH,24,Zug |CH,25,Zurich |CH,26,Jura |CI,05,Atacama |CI,06,Biobio |CI,51,Sassandra |CI,61,Abidjan |CI,74,Agneby |CI,75,Bafing |CI,76,Bas-Sassandra |CI,77,Denguele |CI,78,Dix-Huit Montagnes |CI,79,Fromager |CI,80,Haut-Sassandra |CI,81,Lacs |CI,82,Lagunes |CI,83,Marahoue |CI,84,Moyen-Cavally |CI,85,Moyen-Comoe |CI,86,N'zi-Comoe |CI,87,Savanes |CI,88,Sud-Bandama |CI,89,Sud-Comoe |CI,90,Vallee du Bandama |CI,91,Worodougou |CI,92,Zanzan |CL,01,Valparaiso |CL,02,Aisen del General Carlos Ibanez del Campo |CL,03,Antofagasta |CL,04,Araucania |CL,05,Atacama |CL,06,Bio-Bio |CL,07,Coquimbo |CL,08,Libertador General Bernardo O'Higgins |CL,09,Los Lagos |CL,10,Magallanes y de la Antartica Chilena |CL,11,Maule |CL,12,Region Metropolitana |CL,13,Tarapaca |CL,14,Los Lagos |CL,15,Tarapaca |CL,16,Arica y Parinacota |CL,17,Los Rios |CM,04,Est |CM,05,Littoral |CM,07,Nord-Ouest |CM,08,Ouest |CM,09,Sud-Ouest |CM,10,Adamaoua |CM,11,Centre |CM,12,Extreme-Nord |CM,13,Nord |CM,14,Sud |CN,01,Anhui |CN,02,Zhejiang |CN,03,Jiangxi |CN,04,Jiangsu |CN,05,Jilin |CN,06,Qinghai |CN,07,Fujian |CN,08,Heilongjiang |CN,09,Henan |CN,10,Hebei |CN,11,Hunan |CN,12,Hubei |CN,13,Xinjiang |CN,14,Xizang |CN,15,Gansu |CN,16,Guangxi |CN,18,Guizhou |CN,19,Liaoning |CN,20,Nei Mongol |CN,21,Ningxia |CN,22,Beijing |CN,23,Shanghai |CN,24,Shanxi |CN,25,Shandong |CN,26,Shaanxi |CN,28,Tianjin |CN,29,Yunnan |CN,30,Guangdong |CN,31,Hainan |CN,32,Sichuan |CN,33,Chongqing |CO,01,Amazonas |CO,02,Antioquia |CO,03,Arauca |CO,04,Atlantico |CO,05,Bolivar Department |CO,06,Boyaca Department |CO,07,Caldas Department |CO,08,Caqueta |CO,09,Cauca |CO,10,Cesar |CO,11,Choco |CO,12,Cordoba |CO,14,Guaviare |CO,15,Guainia |CO,16,Huila |CO,17,La Guajira |CO,18,Magdalena Department |CO,19,Meta |CO,20,Narino |CO,21,Norte de Santander |CO,22,Putumayo |CO,23,Quindio |CO,24,Risaralda |CO,25,San Andres y Providencia |CO,26,Santander |CO,27,Sucre |CO,28,Tolima |CO,29,Valle del Cauca |CO,30,Vaupes |CO,31,Vichada |CO,32,Casanare |CO,33,Cundinamarca |CO,34,Distrito Especial |CO,35,Bolivar |CO,36,Boyaca |CO,37,Caldas |CO,38,Magdalena |CR,01,Alajuela |CR,02,Cartago |CR,03,Guanacaste |CR,04,Heredia |CR,06,Limon |CR,07,Puntarenas |CR,08,San Jose |CU,01,Pinar del Rio |CU,02,Ciudad de la Habana |CU,03,Matanzas |CU,04,Isla de la Juventud |CU,05,Camaguey |CU,07,Ciego de Avila |CU,08,Cienfuegos |CU,09,Granma |CU,10,Guantanamo |CU,11,La Habana |CU,12,Holguin |CU,13,Las Tunas |CU,14,Sancti Spiritus |CU,15,Santiago de Cuba |CU,16,Villa Clara |CV,01,Boa Vista |CV,02,Brava |CV,04,Maio |CV,05,Paul |CV,07,Ribeira Grande |CV,08,Sal |CV,10,Sao Nicolau |CV,11,Sao Vicente |CV,13,Mosteiros |CV,14,Praia |CV,15,Santa Catarina |CV,16,Santa Cruz |CV,17,Sao Domingos |CV,18,Sao Filipe |CV,19,Sao Miguel |CV,20,Tarrafal |CY,01,Famagusta |CY,02,Kyrenia |CY,03,Larnaca |CY,04,Nicosia |CY,05,Limassol |CY,06,Paphos |CZ,03,Blansko |CZ,04,Breclav |CZ,20,Hradec Kralove |CZ,21,Jablonec nad Nisou |CZ,23,Jicin |CZ,24,Jihlava |CZ,30,Kolin |CZ,33,Liberec |CZ,36,Melnik |CZ,37,Mlada Boleslav |CZ,39,Nachod |CZ,41,Nymburk |CZ,45,Pardubice |CZ,52,Hlavni mesto Praha |CZ,61,Semily |CZ,70,Trutnov |CZ,78,Jihomoravsky kraj |CZ,79,Jihocesky kraj |CZ,80,Vysocina |CZ,81,Karlovarsky kraj |CZ,82,Kralovehradecky kraj |CZ,83,Liberecky kraj |CZ,84,Olomoucky kraj |CZ,85,Moravskoslezsky kraj |CZ,86,Pardubicky kraj |CZ,87,Plzensky kraj |CZ,88,Stredocesky kraj |CZ,89,Ustecky kraj |CZ,90,Zlinsky kraj |DE,01,Baden-Wurttemberg |DE,02,Bayern |DE,03,Bremen |DE,04,Hamburg |DE,05,Hessen |DE,06,Niedersachsen |DE,07,Nordrhein-Westfalen |DE,08,Rheinland-Pfalz |DE,09,Saarland |DE,10,Schleswig-Holstein |DE,11,Brandenburg |DE,12,Mecklenburg-Vorpommern |DE,13,Sachsen |DE,14,Sachsen-Anhalt |DE,15,Thuringen |DE,16,Berlin |DJ,01,Ali Sabieh |DJ,04,Obock |DJ,05,Tadjoura |DJ,06,Dikhil |DJ,07,Djibouti |DJ,08,Arta |DK,01,Arhus |DK,02,Bornholm |DK,03,Frederiksborg |DK,04,Fyn |DK,05,Kobenhavn |DK,06,Staden Kobenhavn |DK,07,Nordjylland |DK,08,Ribe |DK,09,Ringkobing |DK,10,Roskilde |DK,11,Sonderjylland |DK,12,Storstrom |DK,13,Vejle |DK,14,Vestsjalland |DK,15,Viborg |DK,17,Hovedstaden |DK,18,Midtjyllen |DK,19,Nordjylland |DK,20,Sjelland |DK,21,Syddanmark |DM,02,Saint Andrew |DM,03,Saint David |DM,04,Saint George |DM,05,Saint John |DM,06,Saint Joseph |DM,07,Saint Luke |DM,08,Saint Mark |DM,09,Saint Patrick |DM,10,Saint Paul |DM,11,Saint Peter |DO,01,Azua |DO,02,Baoruco |DO,03,Barahona |DO,04,Dajabon |DO,05,Distrito Nacional |DO,06,Duarte |DO,08,Espaillat |DO,09,Independencia |DO,10,La Altagracia |DO,11,Elias Pina |DO,12,La Romana |DO,14,Maria Trinidad Sanchez |DO,15,Monte Cristi |DO,16,Pedernales |DO,17,Peravia |DO,18,Puerto Plata |DO,19,Salcedo |DO,20,Samana |DO,21,Sanchez Ramirez |DO,23,San Juan |DO,24,San Pedro De Macoris |DO,25,Santiago |DO,26,Santiago Rodriguez |DO,27,Valverde |DO,28,El Seibo |DO,29,Hato Mayor |DO,30,La Vega |DO,31,Monsenor Nouel |DO,32,Monte Plata |DO,33,San Cristobal |DO,34,Distrito Nacional |DO,35,Peravia |DO,36,San Jose de Ocoa |DO,37,Santo Domingo |DZ,01,Alger |DZ,03,Batna |DZ,04,Constantine |DZ,06,Medea |DZ,07,Mostaganem |DZ,09,Oran |DZ,10,Saida |DZ,12,Setif |DZ,13,Tiaret |DZ,14,Tizi Ouzou |DZ,15,Tlemcen |DZ,18,Bejaia |DZ,19,Biskra |DZ,20,Blida |DZ,21,Bouira |DZ,22,Djelfa |DZ,23,Guelma |DZ,24,Jijel |DZ,25,Laghouat |DZ,26,Mascara |DZ,27,M'sila |DZ,29,Oum el Bouaghi |DZ,30,Sidi Bel Abbes |DZ,31,Skikda |DZ,33,Tebessa |DZ,34,Adrar |DZ,35,Ain Defla |DZ,36,Ain Temouchent |DZ,37,Annaba |DZ,38,Bechar |DZ,39,Bordj Bou Arreridj |DZ,40,Boumerdes |DZ,41,Chlef |DZ,42,El Bayadh |DZ,43,El Oued |DZ,44,El Tarf |DZ,45,Ghardaia |DZ,46,Illizi |DZ,47,Khenchela |DZ,48,Mila |DZ,49,Naama |DZ,50,Ouargla |DZ,51,Relizane |DZ,52,Souk Ahras |DZ,53,Tamanghasset |DZ,54,Tindouf |DZ,55,Tipaza |DZ,56,Tissemsilt |EC,01,Galapagos |EC,02,Azuay |EC,03,Bolivar |EC,04,Canar |EC,05,Carchi |EC,06,Chimborazo |EC,07,Cotopaxi |EC,08,El Oro |EC,09,Esmeraldas |EC,10,Guayas |EC,11,Imbabura |EC,12,Loja |EC,13,Los Rios |EC,14,Manabi |EC,15,Morona-Santiago |EC,17,Pastaza |EC,18,Pichincha |EC,19,Tungurahua |EC,20,Zamora-Chinchipe |EC,22,Sucumbios |EC,23,Napo |EC,24,Orellana |EE,01,Harjumaa |EE,02,Hiiumaa |EE,03,Ida-Virumaa |EE,04,Jarvamaa |EE,05,Jogevamaa |EE,06,Kohtla-Jarve |EE,07,Laanemaa |EE,08,Laane-Virumaa |EE,09,Narva |EE,10,Parnu |EE,11,Parnumaa |EE,12,Polvamaa |EE,13,Raplamaa |EE,14,Saaremaa |EE,15,Sillamae |EE,16,Tallinn |EE,17,Tartu |EE,18,Tartumaa |EE,19,Valgamaa |EE,20,Viljandimaa |EE,21,Vorumaa |EG,01,Ad Daqahliyah |EG,02,Al Bahr al Ahmar |EG,03,Al Buhayrah |EG,04,Al Fayyum |EG,05,Al Gharbiyah |EG,06,Al Iskandariyah |EG,07,Al Isma'iliyah |EG,08,Al Jizah |EG,09,Al Minufiyah |EG,10,Al Minya |EG,11,Al Qahirah |EG,12,Al Qalyubiyah |EG,13,Al Wadi al Jadid |EG,14,Ash Sharqiyah |EG,15,As Suways |EG,16,Aswan |EG,17,Asyut |EG,18,Bani Suwayf |EG,19,Bur Sa'id |EG,20,Dumyat |EG,21,Kafr ash Shaykh |EG,22,Matruh |EG,23,Qina |EG,24,Suhaj |EG,26,Janub Sina' |EG,27,Shamal Sina' |ER,01,Anseba |ER,02,Debub |ER,03,Debubawi K'eyih Bahri |ER,04,Gash Barka |ER,05,Ma'akel |ER,06,Semenawi K'eyih Bahri |ES,07,Islas Baleares |ES,27,La Rioja |ES,29,Madrid |ES,31,Murcia |ES,32,Navarra |ES,34,Asturias |ES,39,Cantabria |ES,51,Andalucia |ES,52,Aragon |ES,53,Canarias |ES,54,Castilla-La Mancha |ES,55,Castilla y Leon |ES,56,Catalonia |ES,57,Extremadura |ES,58,Galicia |ES,59,Pais Vasco |ES,60,Comunidad Valenciana |ET,02,Amhara |ET,07,Somali |ET,08,Gambella |ET,10,Addis Abeba |ET,11,Southern |ET,12,Tigray |ET,13,Benishangul |ET,14,Afar |ET,44,Adis Abeba |ET,45,Afar |ET,46,Amara |ET,47,Binshangul Gumuz |ET,48,Dire Dawa |ET,49,Gambela Hizboch |ET,50,Hareri Hizb |ET,51,Oromiya |ET,52,Sumale |ET,53,Tigray |ET,54,YeDebub Biheroch Bihereseboch na Hizboch |FI,01,Aland |FI,06,Lapland |FI,08,Oulu |FI,13,Southern Finland |FI,14,Eastern Finland |FI,15,Western Finland |FJ,01,Central |FJ,02,Eastern |FJ,03,Northern |FJ,04,Rotuma |FJ,05,Western |FM,01,Kosrae |FM,02,Pohnpei |FM,03,Chuuk |FM,04,Yap |FR,97,Aquitaine |FR,98,Auvergne |FR,99,Basse-Normandie |FR,A1,Bourgogne |FR,A2,Bretagne |FR,A3,Centre |FR,A4,Champagne-Ardenne |FR,A5,Corse |FR,A6,Franche-Comte |FR,A7,Haute-Normandie |FR,A8,Ile-de-France |FR,A9,Languedoc-Roussillon |FR,B1,Limousin |FR,B2,Lorraine |FR,B3,Midi-Pyrenees |FR,B4,Nord-Pas-de-Calais |FR,B5,Pays de la Loire |FR,B6,Picardie |FR,B7,Poitou-Charentes |FR,B8,Provence-Alpes-Cote d'Azur |FR,B9,Rhone-Alpes |FR,C1,Alsace |GA,01,Estuaire |GA,02,Haut-Ogooue |GA,03,Moyen-Ogooue |GA,04,Ngounie |GA,05,Nyanga |GA,06,Ogooue-Ivindo |GA,07,Ogooue-Lolo |GA,08,Ogooue-Maritime |GA,09,Woleu-Ntem |GB,01,Avon |GB,03,Berkshire |GB,07,Cleveland |GB,17,Greater London |GB,18,Greater Manchester |GB,20,Hereford and Worcester |GB,22,Humberside |GB,28,Merseyside |GB,37,South Yorkshire |GB,41,Tyne and Wear |GB,43,West Midlands |GB,45,West Yorkshire |GB,79,Central |GB,82,Grampian |GB,84,Lothian |GB,87,Strathclyde |GB,88,Tayside |GB,90,Clwyd |GB,91,Dyfed |GB,92,Gwent |GB,94,Mid Glamorgan |GB,96,South Glamorgan |GB,97,West Glamorgan |GB,A1,Barking and Dagenham |GB,A2,Barnet |GB,A3,Barnsley |GB,A4,Bath and North East Somerset |GB,A5,Bedfordshire |GB,A6,Bexley |GB,A7,Birmingham |GB,A8,Blackburn with Darwen |GB,A9,Blackpool |GB,B1,Bolton |GB,B2,Bournemouth |GB,B3,Bracknell Forest |GB,B4,Bradford |GB,B5,Brent |GB,B6,Brighton and Hove |GB,B7,Bristol, City of |GB,B8,Bromley |GB,B9,Buckinghamshire |GB,C1,Bury |GB,C2,Calderdale |GB,C3,Cambridgeshire |GB,C4,Camden |GB,C5,Cheshire |GB,C6,Cornwall |GB,C7,Coventry |GB,C8,Croydon |GB,C9,Cumbria |GB,D1,Darlington |GB,D2,Derby |GB,D3,Derbyshire |GB,D4,Devon |GB,D5,Doncaster |GB,D6,Dorset |GB,D7,Dudley |GB,D8,Durham |GB,D9,Ealing |GB,E1,East Riding of Yorkshire |GB,E2,East Sussex |GB,E3,Enfield |GB,E4,Essex |GB,E5,Gateshead |GB,E6,Gloucestershire |GB,E7,Greenwich |GB,E8,Hackney |GB,E9,Halton |GB,F1,Hammersmith and Fulham |GB,F2,Hampshire |GB,F3,Haringey |GB,F4,Harrow |GB,F5,Hartlepool |GB,F6,Havering |GB,F7,Herefordshire |GB,F8,Hertford |GB,F9,Hillingdon |GB,G1,Hounslow |GB,G2,Isle of Wight |GB,G3,Islington |GB,G4,Kensington and Chelsea |GB,G5,Kent |GB,G6,Kingston upon Hull, City of |GB,G7,Kingston upon Thames |GB,G8,Kirklees |GB,G9,Knowsley |GB,H1,Lambeth |GB,H2,Lancashire |GB,H3,Leeds |GB,H4,Leicester |GB,H5,Leicestershire |GB,H6,Lewisham |GB,H7,Lincolnshire |GB,H8,Liverpool |GB,H9,London, City of |GB,I1,Luton |GB,I2,Manchester |GB,I3,Medway |GB,I4,Merton |GB,I5,Middlesbrough |GB,I6,Milton Keynes |GB,I7,Newcastle upon Tyne |GB,I8,Newham |GB,I9,Norfolk |GB,J1,Northamptonshire |GB,J2,North East Lincolnshire |GB,J3,North Lincolnshire |GB,J4,North Somerset |GB,J5,North Tyneside |GB,J6,Northumberland |GB,J7,North Yorkshire |GB,J8,Nottingham |GB,J9,Nottinghamshire |GB,K1,Oldham |GB,K2,Oxfordshire |GB,K3,Peterborough |GB,K4,Plymouth |GB,K5,Poole |GB,K6,Portsmouth |GB,K7,Reading |GB,K8,Redbridge |GB,K9,Redcar and Cleveland |GB,L1,Richmond upon Thames |GB,L2,Rochdale |GB,L3,Rotherham |GB,L4,Rutland |GB,L5,Salford |GB,L6,Shropshire |GB,L7,Sandwell |GB,L8,Sefton |GB,L9,Sheffield |GB,M1,Slough |GB,M2,Solihull |GB,M3,Somerset |GB,M4,Southampton |GB,M5,Southend-on-Sea |GB,M6,South Gloucestershire |GB,M7,South Tyneside |GB,M8,Southwark |GB,M9,Staffordshire |GB,N1,St. Helens |GB,N2,Stockport |GB,N3,Stockton-on-Tees |GB,N4,Stoke-on-Trent |GB,N5,Suffolk |GB,N6,Sunderland |GB,N7,Surrey |GB,N8,Sutton |GB,N9,Swindon |GB,O1,Tameside |GB,O2,Telford and Wrekin |GB,O3,Thurrock |GB,O4,Torbay |GB,O5,Tower Hamlets |GB,O6,Trafford |GB,O7,Wakefield |GB,O8,Walsall |GB,O9,Waltham Forest |GB,P1,Wandsworth |GB,P2,Warrington |GB,P3,Warwickshire |GB,P4,West Berkshire |GB,P5,Westminster |GB,P6,West Sussex |GB,P7,Wigan |GB,P8,Wiltshire |GB,P9,Windsor and Maidenhead |GB,Q1,Wirral |GB,Q2,Wokingham |GB,Q3,Wolverhampton |GB,Q4,Worcestershire |GB,Q5,York |GB,Q6,Antrim |GB,Q7,Ards |GB,Q8,Armagh |GB,Q9,Ballymena |GB,R1,Ballymoney |GB,R2,Banbridge |GB,R3,Belfast |GB,R4,Carrickfergus |GB,R5,Castlereagh |GB,R6,Coleraine |GB,R7,Cookstown |GB,R8,Craigavon |GB,R9,Down |GB,S1,Dungannon |GB,S2,Fermanagh |GB,S3,Larne |GB,S4,Limavady |GB,S5,Lisburn |GB,S6,Derry |GB,S7,Magherafelt |GB,S8,Moyle |GB,S9,Newry and Mourne |GB,T1,Newtownabbey |GB,T2,North Down |GB,T3,Omagh |GB,T4,Strabane |GB,T5,Aberdeen City |GB,T6,Aberdeenshire |GB,T7,Angus |GB,T8,Argyll and Bute |GB,T9,Scottish Borders, The |GB,U1,Clackmannanshire |GB,U2,Dumfries and Galloway |GB,U3,Dundee City |GB,U4,East Ayrshire |GB,U5,East Dunbartonshire |GB,U6,East Lothian |GB,U7,East Renfrewshire |GB,U8,Edinburgh, City of |GB,U9,Falkirk |GB,V1,Fife |GB,V2,Glasgow City |GB,V3,Highland |GB,V4,Inverclyde |GB,V5,Midlothian |GB,V6,Moray |GB,V7,North Ayrshire |GB,V8,North Lanarkshire |GB,V9,Orkney |GB,W1,Perth and Kinross |GB,W2,Renfrewshire |GB,W3,Shetland Islands |GB,W4,South Ayrshire |GB,W5,South Lanarkshire |GB,W6,Stirling |GB,W7,West Dunbartonshire |GB,W8,Eilean Siar |GB,W9,West Lothian |GB,X1,Isle of Anglesey |GB,X2,Blaenau Gwent |GB,X3,Bridgend |GB,X4,Caerphilly |GB,X5,Cardiff |GB,X6,Ceredigion |GB,X7,Carmarthenshire |GB,X8,Conwy |GB,X9,Denbighshire |GB,Y1,Flintshire |GB,Y2,Gwynedd |GB,Y3,Merthyr Tydfil |GB,Y4,Monmouthshire |GB,Y5,Neath Port Talbot |GB,Y6,Newport |GB,Y7,Pembrokeshire |GB,Y8,Powys |GB,Y9,Rhondda Cynon Taff |GB,Z1,Swansea |GB,Z2,Torfaen |GB,Z3,Vale of Glamorgan, The |GB,Z4,Wrexham |GD,01,Saint Andrew |GD,02,Saint David |GD,03,Saint George |GD,04,Saint John |GD,05,Saint Mark |GD,06,Saint Patrick |GE,01,Abashis Raioni |GE,02,Abkhazia |GE,03,Adigenis Raioni |GE,04,Ajaria |GE,05,Akhalgoris Raioni |GE,06,Akhalk'alak'is Raioni |GE,07,Akhalts'ikhis Raioni |GE,08,Akhmetis Raioni |GE,09,Ambrolauris Raioni |GE,10,Aspindzis Raioni |GE,11,Baghdat'is Raioni |GE,12,Bolnisis Raioni |GE,13,Borjomis Raioni |GE,14,Chiat'ura |GE,15,Ch'khorotsqus Raioni |GE,16,Ch'okhatauris Raioni |GE,17,Dedop'listsqaros Raioni |GE,18,Dmanisis Raioni |GE,19,Dushet'is Raioni |GE,20,Gardabanis Raioni |GE,21,Gori |GE,22,Goris Raioni |GE,23,Gurjaanis Raioni |GE,24,Javis Raioni |GE,25,K'arelis Raioni |GE,26,Kaspis Raioni |GE,27,Kharagaulis Raioni |GE,28,Khashuris Raioni |GE,29,Khobis Raioni |GE,30,Khonis Raioni |GE,31,K'ut'aisi |GE,32,Lagodekhis Raioni |GE,33,Lanch'khut'is Raioni |GE,34,Lentekhis Raioni |GE,35,Marneulis Raioni |GE,36,Martvilis Raioni |GE,37,Mestiis Raioni |GE,38,Mts'khet'is Raioni |GE,39,Ninotsmindis Raioni |GE,40,Onis Raioni |GE,41,Ozurget'is Raioni |GE,42,P'ot'i |GE,43,Qazbegis Raioni |GE,44,Qvarlis Raioni |GE,45,Rust'avi |GE,46,Sach'kheris Raioni |GE,47,Sagarejos Raioni |GE,48,Samtrediis Raioni |GE,49,Senakis Raioni |GE,50,Sighnaghis Raioni |GE,51,T'bilisi |GE,52,T'elavis Raioni |GE,53,T'erjolis Raioni |GE,54,T'et'ritsqaros Raioni |GE,55,T'ianet'is Raioni |GE,56,Tqibuli |GE,57,Ts'ageris Raioni |GE,58,Tsalenjikhis Raioni |GE,59,Tsalkis Raioni |GE,60,Tsqaltubo |GE,61,Vanis Raioni |GE,62,Zestap'onis Raioni |GE,63,Zugdidi |GE,64,Zugdidis Raioni |GH,01,Greater Accra |GH,02,Ashanti |GH,03,Brong-Ahafo |GH,04,Central |GH,05,Eastern |GH,06,Northern |GH,08,Volta |GH,09,Western |GH,10,Upper East |GH,11,Upper West |GL,01,Nordgronland |GL,02,Ostgronland |GL,03,Vestgronland |GM,01,Banjul |GM,02,Lower River |GM,03,Central River |GM,04,Upper River |GM,05,Western |GM,07,North Bank |GN,01,Beyla |GN,02,Boffa |GN,03,Boke |GN,04,Conakry |GN,05,Dabola |GN,06,Dalaba |GN,07,Dinguiraye |GN,09,Faranah |GN,10,Forecariah |GN,11,Fria |GN,12,Gaoual |GN,13,Gueckedou |GN,15,Kerouane |GN,16,Kindia |GN,17,Kissidougou |GN,18,Koundara |GN,19,Kouroussa |GN,21,Macenta |GN,22,Mali |GN,23,Mamou |GN,25,Pita |GN,27,Telimele |GN,28,Tougue |GN,29,Yomou |GN,30,Coyah |GN,31,Dubreka |GN,32,Kankan |GN,33,Koubia |GN,34,Labe |GN,35,Lelouma |GN,36,Lola |GN,37,Mandiana |GN,38,Nzerekore |GN,39,Siguiri |GQ,03,Annobon |GQ,04,Bioko Norte |GQ,05,Bioko Sur |GQ,06,Centro Sur |GQ,07,Kie-Ntem |GQ,08,Litoral |GQ,09,Wele-Nzas |GR,01,Evros |GR,02,Rodhopi |GR,03,Xanthi |GR,04,Drama |GR,05,Serrai |GR,06,Kilkis |GR,07,Pella |GR,08,Florina |GR,09,Kastoria |GR,10,Grevena |GR,11,Kozani |GR,12,Imathia |GR,13,Thessaloniki |GR,14,Kavala |GR,15,Khalkidhiki |GR,16,Pieria |GR,17,Ioannina |GR,18,Thesprotia |GR,19,Preveza |GR,20,Arta |GR,21,Larisa |GR,22,Trikala |GR,23,Kardhitsa |GR,24,Magnisia |GR,25,Kerkira |GR,26,Levkas |GR,27,Kefallinia |GR,28,Zakinthos |GR,29,Fthiotis |GR,30,Evritania |GR,31,Aitolia kai Akarnania |GR,32,Fokis |GR,33,Voiotia |GR,34,Evvoia |GR,35,Attiki |GR,36,Argolis |GR,37,Korinthia |GR,38,Akhaia |GR,39,Ilia |GR,40,Messinia |GR,41,Arkadhia |GR,42,Lakonia |GR,43,Khania |GR,44,Rethimni |GR,45,Iraklion |GR,46,Lasithi |GR,47,Dhodhekanisos |GR,48,Samos |GR,49,Kikladhes |GR,50,Khios |GR,51,Lesvos |GT,01,Alta Verapaz |GT,02,Baja Verapaz |GT,03,Chimaltenango |GT,04,Chiquimula |GT,05,El Progreso |GT,06,Escuintla |GT,07,Guatemala |GT,08,Huehuetenango |GT,09,Izabal |GT,10,Jalapa |GT,11,Jutiapa |GT,12,Peten |GT,13,Quetzaltenango |GT,14,Quiche |GT,15,Retalhuleu |GT,16,Sacatepequez |GT,17,San Marcos |GT,18,Santa Rosa |GT,19,Solola |GT,20,Suchitepequez |GT,21,Totonicapan |GT,22,Zacapa |GW,01,Bafata |GW,02,Quinara |GW,04,Oio |GW,05,Bolama |GW,06,Cacheu |GW,07,Tombali |GW,10,Gabu |GW,11,Bissau |GW,12,Biombo |GY,10,Barima-Waini |GY,11,Cuyuni-Mazaruni |GY,12,Demerara-Mahaica |GY,13,East Berbice-Corentyne |GY,14,Essequibo Islands-West Demerara |GY,15,Mahaica-Berbice |GY,16,Pomeroon-Supenaam |GY,17,Potaro-Siparuni |GY,18,Upper Demerara-Berbice |GY,19,Upper Takutu-Upper Essequibo |HN,01,Atlantida |HN,02,Choluteca |HN,03,Colon |HN,04,Comayagua |HN,05,Copan |HN,06,Cortes |HN,07,El Paraiso |HN,08,Francisco Morazan |HN,09,Gracias a Dios |HN,10,Intibuca |HN,11,Islas de la Bahia |HN,12,La Paz |HN,13,Lempira |HN,14,Ocotepeque |HN,15,Olancho |HN,16,Santa Barbara |HN,17,Valle |HN,18,Yoro |HR,01,Bjelovarsko-Bilogorska |HR,02,Brodsko-Posavska |HR,03,Dubrovacko-Neretvanska |HR,04,Istarska |HR,05,Karlovacka |HR,06,Koprivnicko-Krizevacka |HR,07,Krapinsko-Zagorska |HR,08,Licko-Senjska |HR,09,Medimurska |HR,10,Osjecko-Baranjska |HR,11,Pozesko-Slavonska |HR,12,Primorsko-Goranska |HR,13,Sibensko-Kninska |HR,14,Sisacko-Moslavacka |HR,15,Splitsko-Dalmatinska |HR,16,Varazdinska |HR,17,Viroviticko-Podravska |HR,18,Vukovarsko-Srijemska |HR,19,Zadarska |HR,20,Zagrebacka |HR,21,Grad Zagreb |HT,03,Nord-Ouest |HT,06,Artibonite |HT,07,Centre |HT,09,Nord |HT,10,Nord-Est |HT,11,Ouest |HT,12,Sud |HT,13,Sud-Est |HT,14,Grand' Anse |HT,15,Nippes |HU,01,Bacs-Kiskun |HU,02,Baranya |HU,03,Bekes |HU,04,Borsod-Abauj-Zemplen |HU,05,Budapest |HU,06,Csongrad |HU,07,Debrecen |HU,08,Fejer |HU,09,Gyor-Moson-Sopron |HU,10,Hajdu-Bihar |HU,11,Heves |HU,12,Komarom-Esztergom |HU,13,Miskolc |HU,14,Nograd |HU,15,Pecs |HU,16,Pest |HU,17,Somogy |HU,18,Szabolcs-Szatmar-Bereg |HU,19,Szeged |HU,20,Jasz-Nagykun-Szolnok |HU,21,Tolna |HU,22,Vas |HU,23,Veszprem |HU,24,Zala |HU,25,Gyor |HU,26,Bekescsaba |HU,27,Dunaujvaros |HU,28,Eger |HU,29,Hodmezovasarhely |HU,30,Kaposvar |HU,31,Kecskemet |HU,32,Nagykanizsa |HU,33,Nyiregyhaza |HU,34,Sopron |HU,35,Szekesfehervar |HU,36,Szolnok |HU,37,Szombathely |HU,38,Tatabanya |HU,39,Veszprem |HU,40,Zalaegerszeg |HU,41,Salgotarjan |HU,42,Szekszard |ID,01,Aceh |ID,02,Bali |ID,03,Bengkulu |ID,04,Jakarta Raya |ID,05,Jambi |ID,06,Jawa Barat |ID,07,Jawa Tengah |ID,08,Jawa Timur |ID,09,Papua |ID,10,Yogyakarta |ID,11,Kalimantan Barat |ID,12,Kalimantan Selatan |ID,13,Kalimantan Tengah |ID,14,Kalimantan Timur |ID,15,Lampung |ID,16,Maluku |ID,17,Nusa Tenggara Barat |ID,18,Nusa Tenggara Timur |ID,19,Riau |ID,20,Sulawesi Selatan |ID,21,Sulawesi Tengah |ID,22,Sulawesi Tenggara |ID,23,Sulawesi Utara |ID,24,Sumatera Barat |ID,25,Sumatera Selatan |ID,26,Sumatera Utara |ID,28,Maluku |ID,29,Maluku Utara |ID,30,Jawa Barat |ID,31,Sulawesi Utara |ID,32,Sumatera Selatan |ID,33,Banten |ID,34,Gorontalo |ID,35,Kepulauan Bangka Belitung |ID,36,Papua |ID,37,Riau |ID,38,Sulawesi Selatan |ID,39,Irian Jaya Barat |ID,40,Kepulauan Riau |ID,41,Sulawesi Barat |IE,01,Carlow |IE,02,Cavan |IE,03,Clare |IE,04,Cork |IE,06,Donegal |IE,07,Dublin |IE,10,Galway |IE,11,Kerry |IE,12,Kildare |IE,13,Kilkenny |IE,14,Leitrim |IE,15,Laois |IE,16,Limerick |IE,18,Longford |IE,19,Louth |IE,20,Mayo |IE,21,Meath |IE,22,Monaghan |IE,23,Offaly |IE,24,Roscommon |IE,25,Sligo |IE,26,Tipperary |IE,27,Waterford |IE,29,Westmeath |IE,30,Wexford |IE,31,Wicklow |IL,01,HaDarom |IL,02,HaMerkaz |IL,03,HaZafon |IL,04,Hefa |IL,05,Tel Aviv |IL,06,Yerushalayim |IN,01,Andaman and Nicobar Islands |IN,02,Andhra Pradesh |IN,03,Assam |IN,05,Chandigarh |IN,06,Dadra and Nagar Haveli |IN,07,Delhi |IN,09,Gujarat |IN,10,Haryana |IN,11,Himachal Pradesh |IN,12,Jammu and Kashmir |IN,13,Kerala |IN,14,Lakshadweep |IN,16,Maharashtra |IN,17,Manipur |IN,18,Meghalaya |IN,19,Karnataka |IN,20,Nagaland |IN,21,Orissa |IN,22,Puducherry |IN,23,Punjab |IN,24,Rajasthan |IN,25,Tamil Nadu |IN,26,Tripura |IN,28,West Bengal |IN,29,Sikkim |IN,30,Arunachal Pradesh |IN,31,Mizoram |IN,32,Daman and Diu |IN,33,Goa |IN,34,Bihar |IN,35,Madhya Pradesh |IN,36,Uttar Pradesh |IN,37,Chhattisgarh |IN,38,Jharkhand |IN,39,Uttarakhand |IQ,01,Al Anbar |IQ,02,Al Basrah |IQ,03,Al Muthanna |IQ,04,Al Qadisiyah |IQ,05,As Sulaymaniyah |IQ,06,Babil |IQ,07,Baghdad |IQ,08,Dahuk |IQ,09,Dhi Qar |IQ,10,Diyala |IQ,11,Arbil |IQ,12,Karbala' |IQ,13,At Ta'mim |IQ,14,Maysan |IQ,15,Ninawa |IQ,16,Wasit |IQ,17,An Najaf |IQ,18,Salah ad Din |IR,01,Azarbayjan-e Bakhtari |IR,02,Azarbayjan-e Khavari |IR,03,Chahar Mahall va Bakhtiari |IR,04,Sistan va Baluchestan |IR,05,Kohkiluyeh va Buyer Ahmadi |IR,07,Fars |IR,08,Gilan |IR,09,Hamadan |IR,10,Ilam |IR,11,Hormozgan |IR,12,Kerman |IR,13,Bakhtaran |IR,15,Khuzestan |IR,16,Kordestan |IR,17,Mazandaran |IR,18,Semnan Province |IR,19,Markazi |IR,21,Zanjan |IR,22,Bushehr |IR,23,Lorestan |IR,24,Markazi |IR,25,Semnan |IR,26,Tehran |IR,27,Zanjan |IR,28,Esfahan |IR,29,Kerman |IR,30,Khorasan |IR,31,Yazd |IR,32,Ardabil |IR,33,East Azarbaijan |IR,34,Markazi |IR,35,Mazandaran |IR,36,Zanjan |IR,37,Golestan |IR,38,Qazvin |IR,39,Qom |IR,40,Yazd |IR,41,Khorasan-e Janubi |IR,42,Khorasan-e Razavi |IR,43,Khorasan-e Shemali |IS,03,Arnessysla |IS,05,Austur-Hunavatnssysla |IS,06,Austur-Skaftafellssysla |IS,07,Borgarfjardarsysla |IS,09,Eyjafjardarsysla |IS,10,Gullbringusysla |IS,15,Kjosarsysla |IS,17,Myrasysla |IS,20,Nordur-Mulasysla |IS,21,Nordur-Tingeyjarsysla |IS,23,Rangarvallasysla |IS,28,Skagafjardarsysla |IS,29,Snafellsnes- og Hnappadalssysla |IS,30,Strandasysla |IS,31,Sudur-Mulasysla |IS,32,Sudur-Tingeyjarsysla |IS,34,Vestur-Bardastrandarsysla |IS,35,Vestur-Hunavatnssysla |IS,36,Vestur-Isafjardarsysla |IS,37,Vestur-Skaftafellssysla |IS,40,Norourland Eystra |IS,41,Norourland Vestra |IS,42,Suourland |IS,43,Suournes |IS,44,Vestfiroir |IS,45,Vesturland |IT,01,Abruzzi |IT,02,Basilicata |IT,03,Calabria |IT,04,Campania |IT,05,Emilia-Romagna |IT,06,Friuli-Venezia Giulia |IT,07,Lazio |IT,08,Liguria |IT,09,Lombardia |IT,10,Marche |IT,11,Molise |IT,12,Piemonte |IT,13,Puglia |IT,14,Sardegna |IT,15,Sicilia |IT,16,Toscana |IT,17,Trentino-Alto Adige |IT,18,Umbria |IT,19,Valle d'Aosta |IT,20,Veneto |JM,01,Clarendon |JM,02,Hanover |JM,04,Manchester |JM,07,Portland |JM,08,Saint Andrew |JM,09,Saint Ann |JM,10,Saint Catherine |JM,11,Saint Elizabeth |JM,12,Saint James |JM,13,Saint Mary |JM,14,Saint Thomas |JM,15,Trelawny |JM,16,Westmoreland |JM,17,Kingston |JO,02,Al Balqa' |JO,07,Ma |JO,09,Al Karak |JO,10,Al Mafraq |JO,11,Amman Governorate |JO,12,At Tafilah |JO,13,Az Zarqa |JO,14,Irbid |JO,16,Amman |JP,01,Aichi |JP,02,Akita |JP,03,Aomori |JP,04,Chiba |JP,05,Ehime |JP,06,Fukui |JP,07,Fukuoka |JP,08,Fukushima |JP,09,Gifu |JP,10,Gumma |JP,11,Hiroshima |JP,12,Hokkaido |JP,13,Hyogo |JP,14,Ibaraki |JP,15,Ishikawa |JP,16,Iwate |JP,17,Kagawa |JP,18,Kagoshima |JP,19,Kanagawa |JP,20,Kochi |JP,21,Kumamoto |JP,22,Kyoto |JP,23,Mie |JP,24,Miyagi |JP,25,Miyazaki |JP,26,Nagano |JP,27,Nagasaki |JP,28,Nara |JP,29,Niigata |JP,30,Oita |JP,31,Okayama |JP,32,Osaka |JP,33,Saga |JP,34,Saitama |JP,35,Shiga |JP,36,Shimane |JP,37,Shizuoka |JP,38,Tochigi |JP,39,Tokushima |JP,40,Tokyo |JP,41,Tottori |JP,42,Toyama |JP,43,Wakayama |JP,44,Yamagata |JP,45,Yamaguchi |JP,46,Yamanashi |JP,47,Okinawa |KE,01,Central |KE,02,Coast |KE,03,Eastern |KE,05,Nairobi Area |KE,06,North-Eastern |KE,07,Nyanza |KE,08,Rift Valley |KE,09,Western |KG,01,Bishkek |KG,02,Chuy |KG,03,Jalal-Abad |KG,04,Naryn |KG,05,Osh |KG,06,Talas |KG,07,Ysyk-Kol |KG,08,Osh |KG,09,Batken |KH,01,Batdambang |KH,02,Kampong Cham |KH,03,Kampong Chhnang |KH,04,Kampong Speu |KH,05,Kampong Thum |KH,06,Kampot |KH,07,Kandal |KH,08,Koh Kong |KH,09,Kracheh |KH,10,Mondulkiri |KH,11,Phnum Penh |KH,12,Pursat |KH,13,Preah Vihear |KH,14,Prey Veng |KH,15,Ratanakiri Kiri |KH,16,Siem Reap |KH,17,Stung Treng |KH,18,Svay Rieng |KH,19,Takeo |KH,25,Banteay Meanchey |KH,29,Batdambang |KH,30,Pailin |KI,01,Gilbert Islands |KI,02,Line Islands |KI,03,Phoenix Islands |KM,01,Anjouan |KM,02,Grande Comore |KM,03,Moheli |KN,01,Christ Church Nichola Town |KN,02,Saint Anne Sandy Point |KN,03,Saint George Basseterre |KN,04,Saint George Gingerland |KN,05,Saint James Windward |KN,06,Saint John Capisterre |KN,07,Saint John Figtree |KN,08,Saint Mary Cayon |KN,09,Saint Paul Capisterre |KN,10,Saint Paul Charlestown |KN,11,Saint Peter Basseterre |KN,12,Saint Thomas Lowland |KN,13,Saint Thomas Middle Island |KN,15,Trinity Palmetto Point |KP,01,Chagang-do |KP,03,Hamgyong-namdo |KP,06,Hwanghae-namdo |KP,07,Hwanghae-bukto |KP,08,Kaesong-si |KP,09,Kangwon-do |KP,11,P'yongan-bukto |KP,12,P'yongyang-si |KP,13,Yanggang-do |KP,14,Namp'o-si |KP,15,P'yongan-namdo |KP,17,Hamgyong-bukto |KP,18,Najin Sonbong-si |KR,01,Cheju-do |KR,03,Cholla-bukto |KR,05,Ch'ungch'ong-bukto |KR,06,Kangwon-do |KR,10,Pusan-jikhalsi |KR,11,Seoul-t'ukpyolsi |KR,12,Inch'on-jikhalsi |KR,13,Kyonggi-do |KR,14,Kyongsang-bukto |KR,15,Taegu-jikhalsi |KR,16,Cholla-namdo |KR,17,Ch'ungch'ong-namdo |KR,18,Kwangju-jikhalsi |KR,19,Taejon-jikhalsi |KR,20,Kyongsang-namdo |KR,21,Ulsan-gwangyoksi |KW,01,Al Ahmadi |KW,02,Al Kuwayt |KW,05,Al Jahra |KW,07,Al Farwaniyah |KW,08,Hawalli |KW,09,Mubarak al Kabir |KY,01,Creek |KY,02,Eastern |KY,03,Midland |KY,04,South Town |KY,05,Spot Bay |KY,06,Stake Bay |KY,07,West End |KY,08,Western |KZ,01,Almaty |KZ,02,Almaty City |KZ,03,Aqmola |KZ,04,Aqtobe |KZ,05,Astana |KZ,06,Atyrau |KZ,07,West Kazakhstan |KZ,08,Bayqonyr |KZ,09,Mangghystau |KZ,10,South Kazakhstan |KZ,11,Pavlodar |KZ,12,Qaraghandy |KZ,13,Qostanay |KZ,14,Qyzylorda |KZ,15,East Kazakhstan |KZ,16,North Kazakhstan |KZ,17,Zhambyl |LA,01,Attapu |LA,02,Champasak |LA,03,Houaphan |LA,04,Khammouan |LA,05,Louang Namtha |LA,07,Oudomxai |LA,08,Phongsali |LA,09,Saravan |LA,10,Savannakhet |LA,11,Vientiane |LA,13,Xaignabouri |LA,14,Xiangkhoang |LA,17,Louangphrabang |LB,01,Beqaa |LB,02,Al Janub |LB,03,Liban-Nord |LB,04,Beyrouth |LB,05,Mont-Liban |LB,06,Liban-Sud |LB,07,Nabatiye |LB,08,Beqaa |LB,09,Liban-Nord |LB,10,Aakk,r |LB,11,Baalbek-Hermel |LC,01,Anse-la-Raye |LC,02,Dauphin |LC,03,Castries |LC,04,Choiseul |LC,05,Dennery |LC,06,Gros-Islet |LC,07,Laborie |LC,08,Micoud |LC,09,Soufriere |LC,10,Vieux-Fort |LC,11,Praslin |LI,01,Balzers |LI,02,Eschen |LI,03,Gamprin |LI,04,Mauren |LI,05,Planken |LI,06,Ruggell |LI,07,Schaan |LI,08,Schellenberg |LI,09,Triesen |LI,10,Triesenberg |LI,11,Vaduz |LI,21,Gbarpolu |LI,22,River Gee |LK,01,Amparai |LK,02,Anuradhapura |LK,03,Badulla |LK,04,Batticaloa |LK,06,Galle |LK,07,Hambantota |LK,09,Kalutara |LK,10,Kandy |LK,11,Kegalla |LK,12,Kurunegala |LK,14,Matale |LK,15,Matara |LK,16,Moneragala |LK,17,Nuwara Eliya |LK,18,Polonnaruwa |LK,19,Puttalam |LK,20,Ratnapura |LK,21,Trincomalee |LK,23,Colombo |LK,24,Gampaha |LK,25,Jaffna |LK,26,Mannar |LK,27,Mullaittivu |LK,28,Vavuniya |LK,29,Central |LK,30,North Central |LK,31,Northern |LK,32,North Western |LK,33,Sabaragamuwa |LK,34,Southern |LK,35,Uva |LK,36,Western |LR,01,Bong |LR,04,Grand Cape Mount |LR,05,Lofa |LR,06,Maryland |LR,07,Monrovia |LR,09,Nimba |LR,10,Sino |LR,11,Grand Bassa |LR,12,Grand Cape Mount |LR,13,Maryland |LR,14,Montserrado |LR,17,Margibi |LR,18,River Cess |LR,19,Grand Gedeh |LR,20,Lofa |LR,21,Gbarpolu |LR,22,River Gee |LS,10,Berea |LS,11,Butha-Buthe |LS,12,Leribe |LS,13,Mafeteng |LS,14,Maseru |LS,15,Mohales Hoek |LS,16,Mokhotlong |LS,17,Qachas Nek |LS,18,Quthing |LS,19,Thaba-Tseka |LT,56,Alytaus Apskritis |LT,57,Kauno Apskritis |LT,58,Klaipedos Apskritis |LT,59,Marijampoles Apskritis |LT,60,Panevezio Apskritis |LT,61,Siauliu Apskritis |LT,62,Taurages Apskritis |LT,63,Telsiu Apskritis |LT,64,Utenos Apskritis |LT,65,Vilniaus Apskritis |LU,01,Diekirch |LU,02,Grevenmacher |LU,03,Luxembourg |LV,01,Aizkraukles |LV,02,Aluksnes |LV,03,Balvu |LV,04,Bauskas |LV,05,Cesu |LV,06,Daugavpils |LV,07,Daugavpils |LV,08,Dobeles |LV,09,Gulbenes |LV,10,Jekabpils |LV,11,Jelgava |LV,12,Jelgavas |LV,13,Jurmala |LV,14,Kraslavas |LV,15,Kuldigas |LV,16,Liepaja |LV,17,Liepajas |LV,18,Limbazu |LV,19,Ludzas |LV,20,Madonas |LV,21,Ogres |LV,22,Preilu |LV,23,Rezekne |LV,24,Rezeknes |LV,25,Riga |LV,26,Rigas |LV,27,Saldus |LV,28,Talsu |LV,29,Tukuma |LV,30,Valkas |LV,31,Valmieras |LV,32,Ventspils |LV,33,Ventspils |LY,03,Al Aziziyah |LY,05,Al Jufrah |LY,08,Al Kufrah |LY,13,Ash Shati' |LY,30,Murzuq |LY,34,Sabha |LY,41,Tarhunah |LY,42,Tubruq |LY,45,Zlitan |LY,47,Ajdabiya |LY,48,Al Fatih |LY,49,Al Jabal al Akhdar |LY,50,Al Khums |LY,51,An Nuqat al Khams |LY,52,Awbari |LY,53,Az Zawiyah |LY,54,Banghazi |LY,55,Darnah |LY,56,Ghadamis |LY,57,Gharyan |LY,58,Misratah |LY,59,Sawfajjin |LY,60,Surt |LY,61,Tarabulus |LY,62,Yafran |MA,01,Agadir |MA,02,Al Hoceima |MA,03,Azilal |MA,04,Ben Slimane |MA,05,Beni Mellal |MA,06,Boulemane |MA,07,Casablanca |MA,08,Chaouen |MA,09,El Jadida |MA,10,El Kelaa des Srarhna |MA,11,Er Rachidia |MA,12,Essaouira |MA,13,Fes |MA,14,Figuig |MA,15,Kenitra |MA,16,Khemisset |MA,17,Khenifra |MA,18,Khouribga |MA,19,Marrakech |MA,20,Meknes |MA,21,Nador |MA,22,Ouarzazate |MA,23,Oujda |MA,24,Rabat-Sale |MA,25,Safi |MA,26,Settat |MA,27,Tanger |MA,29,Tata |MA,30,Taza |MA,32,Tiznit |MA,33,Guelmim |MA,34,Ifrane |MA,35,Laayoune |MA,36,Tan-Tan |MA,37,Taounate |MA,38,Sidi Kacem |MA,39,Taroudannt |MA,40,Tetouan |MA,41,Larache |MA,45,Grand Casablanca |MA,46,Fes-Boulemane |MA,47,Marrakech-Tensift-Al Haouz |MA,48,Meknes-Tafilalet |MA,49,Rabat-Sale-Zemmour-Zaer |MA,50,Chaouia-Ouardigha |MA,51,Doukkala-Abda |MA,52,Gharb-Chrarda-Beni Hssen |MA,53,Guelmim-Es Smara |MA,54,Oriental |MA,55,Souss-Massa-Dr,a |MA,56,Tadla-Azilal |MA,57,Tanger-Tetouan |MA,58,Taza-Al Hoceima-Taounate |MA,59,La,youne-Boujdour-Sakia El Hamra |MC,01,La Condamine |MC,02,Monaco |MC,03,Monte-Carlo |MD,46,Balti |MD,47,Cahul |MD,48,Chisinau |MD,49,Stinga Nistrului |MD,50,Edinet |MD,51,Gagauzia |MD,52,Lapusna |MD,53,Orhei |MD,54,Soroca |MD,55,Tighina |MD,56,Ungheni |MD,58,Stinga Nistrului |MD,59,Anenii Noi |MD,60,Balti |MD,61,Basarabeasca |MD,62,Bender |MD,63,Briceni |MD,64,Cahul |MD,65,Cantemir |MD,66,Calarasi |MD,67,Causeni |MD,68,Cimislia |MD,69,Criuleni |MD,70,Donduseni |MD,71,Drochia |MD,72,Dubasari |MD,73,Edinet |MD,74,Falesti |MD,75,Floresti |MD,76,Glodeni |MD,77,Hincesti |MD,78,Ialoveni |MD,79,Leova |MD,80,Nisporeni |MD,81,Ocnita |MD,83,Rezina |MD,84,Riscani |MD,85,Singerei |MD,86,Soldanesti |MD,87,Soroca |MD,88,Stefan-Voda |MD,89,Straseni |MD,90,Taraclia |MD,91,Telenesti |MD,92,Ungheni |MG,01,Antsiranana |MG,02,Fianarantsoa |MG,03,Mahajanga |MG,04,Toamasina |MG,05,Antananarivo |MG,06,Toliara |MK,01,Aracinovo |MK,02,Bac |MK,03,Belcista |MK,04,Berovo |MK,05,Bistrica |MK,06,Bitola |MK,07,Blatec |MK,08,Bogdanci |MK,09,Bogomila |MK,10,Bogovinje |MK,11,Bosilovo |MK,12,Brvenica |MK,13,Cair |MK,14,Capari |MK,15,Caska |MK,16,Cegrane |MK,17,Centar |MK,18,Centar Zupa |MK,19,Cesinovo |MK,20,Cucer-Sandevo |MK,21,Debar |MK,22,Delcevo |MK,23,Delogozdi |MK,24,Demir Hisar |MK,25,Demir Kapija |MK,26,Dobrusevo |MK,27,Dolna Banjica |MK,28,Dolneni |MK,29,Dorce Petrov |MK,30,Drugovo |MK,31,Dzepciste |MK,32,Gazi Baba |MK,33,Gevgelija |MK,34,Gostivar |MK,35,Gradsko |MK,36,Ilinden |MK,37,Izvor |MK,38,Jegunovce |MK,39,Kamenjane |MK,40,Karbinci |MK,41,Karpos |MK,42,Kavadarci |MK,43,Kicevo |MK,44,Kisela Voda |MK,45,Klecevce |MK,46,Kocani |MK,47,Konce |MK,48,Kondovo |MK,49,Konopiste |MK,50,Kosel |MK,51,Kratovo |MK,52,Kriva Palanka |MK,53,Krivogastani |MK,54,Krusevo |MK,55,Kuklis |MK,56,Kukurecani |MK,57,Kumanovo |MK,58,Labunista |MK,59,Lipkovo |MK,60,Lozovo |MK,61,Lukovo |MK,62,Makedonska Kamenica |MK,63,Makedonski Brod |MK,64,Mavrovi Anovi |MK,65,Meseista |MK,66,Miravci |MK,67,Mogila |MK,68,Murtino |MK,69,Negotino |MK,70,Negotino-Polosko |MK,71,Novaci |MK,72,Novo Selo |MK,73,Oblesevo |MK,74,Ohrid |MK,75,Orasac |MK,76,Orizari |MK,77,Oslomej |MK,78,Pehcevo |MK,79,Petrovec |MK,80,Plasnica |MK,81,Podares |MK,82,Prilep |MK,83,Probistip |MK,84,Radovis |MK,85,Rankovce |MK,86,Resen |MK,87,Rosoman |MK,88,Rostusa |MK,89,Samokov |MK,90,Saraj |MK,91,Sipkovica |MK,92,Sopiste |MK,93,Sopotnica |MK,94,Srbinovo |MK,95,Staravina |MK,96,Star Dojran |MK,97,Staro Nagoricane |MK,98,Stip |MK,99,Struga |MK,A1,Strumica |MK,A2,Studenicani |MK,A3,Suto Orizari |MK,A4,Sveti Nikole |MK,A5,Tearce |MK,A6,Tetovo |MK,A7,Topolcani |MK,A8,Valandovo |MK,A9,Vasilevo |MK,B1,Veles |MK,B2,Velesta |MK,B3,Vevcani |MK,B4,Vinica |MK,B5,Vitoliste |MK,B6,Vranestica |MK,B7,Vrapciste |MK,B8,Vratnica |MK,B9,Vrutok |MK,C1,Zajas |MK,C2,Zelenikovo |MK,C3,Zelino |MK,C4,Zitose |MK,C5,Zletovo |MK,C6,Zrnovci |ML,01,Bamako |ML,03,Kayes |ML,04,Mopti |ML,05,Segou |ML,06,Sikasso |ML,07,Koulikoro |ML,08,Tombouctou |ML,09,Gao |ML,10,Kidal |MM,01,Rakhine State |MM,02,Chin State |MM,03,Irrawaddy |MM,04,Kachin State |MM,05,Karan State |MM,06,Kayah State |MM,07,Magwe |MM,08,Mandalay |MM,09,Pegu |MM,10,Sagaing |MM,11,Shan State |MM,12,Tenasserim |MM,13,Mon State |MM,14,Rangoon |MM,17,Yangon |MN,01,Arhangay |MN,02,Bayanhongor |MN,03,Bayan-Olgiy |MN,05,Darhan |MN,06,Dornod |MN,07,Dornogovi |MN,08,Dundgovi |MN,09,Dzavhan |MN,10,Govi-Altay |MN,11,Hentiy |MN,12,Hovd |MN,13,Hovsgol |MN,14,Omnogovi |MN,15,Ovorhangay |MN,16,Selenge |MN,17,Suhbaatar |MN,18,Tov |MN,19,Uvs |MN,20,Ulaanbaatar |MN,21,Bulgan |MN,22,Erdenet |MN,23,Darhan-Uul |MN,24,Govisumber |MN,25,Orhon |MO,01,Ilhas |MO,02,Macau |MR,01,Hodh Ech Chargui |MR,02,Hodh El Gharbi |MR,03,Assaba |MR,04,Gorgol |MR,05,Brakna |MR,06,Trarza |MR,07,Adrar |MR,08,Dakhlet Nouadhibou |MR,09,Tagant |MR,10,Guidimaka |MR,11,Tiris Zemmour |MR,12,Inchiri |MS,01,Saint Anthony |MS,02,Saint Georges |MS,03,Saint Peter |MU,12,Black River |MU,13,Flacq |MU,14,Grand Port |MU,15,Moka |MU,16,Pamplemousses |MU,17,Plaines Wilhems |MU,18,Port Louis |MU,19,Riviere du Rempart |MU,20,Savanne |MU,21,Agalega Islands |MU,22,Cargados Carajos |MU,23,Rodrigues |MV,01,Seenu |MV,02,Aliff |MV,03,Laviyani |MV,04,Waavu |MV,05,Laamu |MV,07,Haa Aliff |MV,08,Thaa |MV,12,Meemu |MV,13,Raa |MV,14,Faafu |MV,17,Daalu |MV,20,Baa |MV,23,Haa Daalu |MV,24,Shaviyani |MV,25,Noonu |MV,26,Kaafu |MV,27,Gaafu Aliff |MV,28,Gaafu Daalu |MV,29,Naviyani |MV,40,Male |MW,02,Chikwawa |MW,03,Chiradzulu |MW,04,Chitipa |MW,05,Thyolo |MW,06,Dedza |MW,07,Dowa |MW,08,Karonga |MW,09,Kasungu |MW,11,Lilongwe |MW,12,Mangochi |MW,13,Mchinji |MW,15,Mzimba |MW,16,Ntcheu |MW,17,Nkhata Bay |MW,18,Nkhotakota |MW,19,Nsanje |MW,20,Ntchisi |MW,21,Rumphi |MW,22,Salima |MW,23,Zomba |MW,24,Blantyre |MW,25,Mwanza |MW,26,Balaka |MW,27,Likoma |MW,28,Machinga |MW,29,Mulanje |MW,30,Phalombe |MX,01,Aguascalientes |MX,02,Baja California |MX,03,Baja California Sur |MX,04,Campeche |MX,05,Chiapas |MX,06,Chihuahua |MX,07,Coahuila de Zaragoza |MX,08,Colima |MX,09,Distrito Federal |MX,10,Durango |MX,11,Guanajuato |MX,12,Guerrero |MX,13,Hidalgo |MX,14,Jalisco |MX,15,Mexico |MX,16,Michoacan de Ocampo |MX,17,Morelos |MX,18,Nayarit |MX,19,Nuevo Leon |MX,20,Oaxaca |MX,21,Puebla |MX,22,Queretaro de Arteaga |MX,23,Quintana Roo |MX,24,San Luis Potosi |MX,25,Sinaloa |MX,26,Sonora |MX,27,Tabasco |MX,28,Tamaulipas |MX,29,Tlaxcala |MX,30,Veracruz-Llave |MX,31,Yucatan |MX,32,Zacatecas |MY,01,Johor |MY,02,Kedah |MY,03,Kelantan |MY,04,Melaka |MY,05,Negeri Sembilan |MY,06,Pahang |MY,07,Perak |MY,08,Perlis |MY,09,Pulau Pinang |MY,11,Sarawak |MY,12,Selangor |MY,13,Terengganu |MY,14,Kuala Lumpur |MY,15,Labuan |MY,16,Sabah |MY,17,Putrajaya |MZ,01,Cabo Delgado |MZ,02,Gaza |MZ,03,Inhambane |MZ,04,Maputo |MZ,05,Sofala |MZ,06,Nampula |MZ,07,Niassa |MZ,08,Tete |MZ,09,Zambezia |MZ,10,Manica |MZ,11,Maputo |NA,01,Bethanien |NA,02,Caprivi Oos |NA,03,Boesmanland |NA,04,Gobabis |NA,05,Grootfontein |NA,06,Kaokoland |NA,07,Karibib |NA,08,Keetmanshoop |NA,09,Luderitz |NA,10,Maltahohe |NA,11,Okahandja |NA,12,Omaruru |NA,13,Otjiwarongo |NA,14,Outjo |NA,15,Owambo |NA,16,Rehoboth |NA,17,Swakopmund |NA,18,Tsumeb |NA,20,Karasburg |NA,21,Windhoek |NA,22,Damaraland |NA,23,Hereroland Oos |NA,24,Hereroland Wes |NA,25,Kavango |NA,26,Mariental |NA,27,Namaland |NA,28,Caprivi |NA,29,Erongo |NA,30,Hardap |NA,31,Karas |NA,32,Kunene |NA,33,Ohangwena |NA,34,Okavango |NA,35,Omaheke |NA,36,Omusati |NA,37,Oshana |NA,38,Oshikoto |NA,39,Otjozondjupa |NE,01,Agadez |NE,02,Diffa |NE,03,Dosso |NE,04,Maradi |NE,05,Niamey |NE,06,Tahoua |NE,07,Zinder |NE,08,Niamey |NG,05,Lagos |NG,10,Rivers |NG,11,Federal Capital Territory |NG,12,Gongola |NG,16,Ogun |NG,17,Ondo |NG,18,Oyo |NG,21,Akwa Ibom |NG,22,Cross River |NG,23,Kaduna |NG,24,Katsina |NG,25,Anambra |NG,26,Benue |NG,27,Borno |NG,28,Imo |NG,29,Kano |NG,30,Kwara |NG,31,Niger |NG,32,Oyo |NG,35,Adamawa |NG,36,Delta |NG,37,Edo |NG,39,Jigawa |NG,40,Kebbi |NG,41,Kogi |NG,42,Osun |NG,43,Taraba |NG,44,Yobe |NG,45,Abia |NG,46,Bauchi |NG,47,Enugu |NG,48,Ondo |NG,49,Plateau |NG,50,Rivers |NG,51,Sokoto |NG,52,Bayelsa |NG,53,Ebonyi |NG,54,Ekiti |NG,55,Gombe |NG,56,Nassarawa |NG,57,Zamfara |NI,01,Boaco |NI,02,Carazo |NI,03,Chinandega |NI,04,Chontales |NI,05,Esteli |NI,06,Granada |NI,07,Jinotega |NI,08,Leon |NI,09,Madriz |NI,10,Managua |NI,11,Masaya |NI,12,Matagalpa |NI,13,Nueva Segovia |NI,14,Rio San Juan |NI,15,Rivas |NI,16,Zelaya |NI,17,Autonoma Atlantico Norte |NI,18,Region Autonoma Atlantico Sur |NL,01,Drenthe |NL,02,Friesland |NL,03,Gelderland |NL,04,Groningen |NL,05,Limburg |NL,06,Noord-Brabant |NL,07,Noord-Holland |NL,08,Overijssel |NL,09,Utrecht |NL,10,Zeeland |NL,11,Zuid-Holland |NL,12,Dronten |NL,13,Zuidelijke IJsselmeerpolders |NL,14,Lelystad |NL,15,Overijssel |NL,16,Flevoland |NO,01,Akershus |NO,02,Aust-Agder |NO,04,Buskerud |NO,05,Finnmark |NO,06,Hedmark |NO,07,Hordaland |NO,08,More og Romsdal |NO,09,Nordland |NO,10,Nord-Trondelag |NO,11,Oppland |NO,12,Oslo |NO,13,Ostfold |NO,14,Rogaland |NO,15,Sogn og Fjordane |NO,16,Sor-Trondelag |NO,17,Telemark |NO,18,Troms |NO,19,Vest-Agder |NO,20,Vestfold |NP,01,Bagmati |NP,02,Bheri |NP,03,Dhawalagiri |NP,04,Gandaki |NP,05,Janakpur |NP,06,Karnali |NP,07,Kosi |NP,08,Lumbini |NP,09,Mahakali |NP,10,Mechi |NP,11,Narayani |NP,12,Rapti |NP,13,Sagarmatha |NP,14,Seti |NR,01,Aiwo |NR,02,Anabar |NR,03,Anetan |NR,04,Anibare |NR,05,Baiti |NR,06,Boe |NR,07,Buada |NR,08,Denigomodu |NR,09,Ewa |NR,10,Ijuw |NR,11,Meneng |NR,12,Nibok |NR,13,Uaboe |NR,14,Yaren |NZ,10,Chatham Islands |NZ,E7,Auckland |NZ,E8,Bay of Plenty |NZ,E9,Canterbury |NZ,F1,Gisborne |NZ,F2,Hawke's Bay |NZ,F3,Manawatu-Wanganui |NZ,F4,Marlborough |NZ,F5,Nelson |NZ,F6,Northland |NZ,F7,Otago |NZ,F8,Southland |NZ,F9,Taranaki |NZ,G1,Waikato |NZ,G2,Wellington |NZ,G3,West Coast |NZ,85,Waikato |OM,01,Ad Dakhiliyah |OM,02,Al Batinah |OM,03,Al Wusta |OM,04,Ash Sharqiyah |OM,05,Az Zahirah |OM,06,Masqat |OM,07,Musandam |OM,08,Zufar |PA,01,Bocas del Toro |PA,02,Chiriqui |PA,03,Cocle |PA,04,Colon |PA,05,Darien |PA,06,Herrera |PA,07,Los Santos |PA,08,Panama |PA,09,San Blas |PA,10,Veraguas |PE,01,Amazonas |PE,02,Ancash |PE,03,Apurimac |PE,04,Arequipa |PE,05,Ayacucho |PE,06,Cajamarca |PE,07,Callao |PE,08,Cusco |PE,09,Huancavelica |PE,10,Huanuco |PE,11,Ica |PE,12,Junin |PE,13,La Libertad |PE,14,Lambayeque |PE,15,Lima |PE,16,Loreto |PE,17,Madre de Dios |PE,18,Moquegua |PE,19,Pasco |PE,20,Piura |PE,21,Puno |PE,22,San Martin |PE,23,Tacna |PE,24,Tumbes |PE,25,Ucayali |PG,01,Central |PG,02,Gulf |PG,03,Milne Bay |PG,04,Northern |PG,05,Southern Highlands |PG,06,Western |PG,07,North Solomons |PG,08,Chimbu |PG,09,Eastern Highlands |PG,10,East New Britain |PG,11,East Sepik |PG,12,Madang |PG,13,Manus |PG,14,Morobe |PG,15,New Ireland |PG,16,Western Highlands |PG,17,West New Britain |PG,18,Sandaun |PG,19,Enga |PG,20,National Capital |PH,01,Abra |PH,02,Agusan del Norte |PH,03,Agusan del Sur |PH,04,Aklan |PH,05,Albay |PH,06,Antique |PH,07,Bataan |PH,08,Batanes |PH,09,Batangas |PH,10,Benguet |PH,11,Bohol |PH,12,Bukidnon |PH,13,Bulacan |PH,14,Cagayan |PH,15,Camarines Norte |PH,16,Camarines Sur |PH,17,Camiguin |PH,18,Capiz |PH,19,Catanduanes |PH,20,Cavite |PH,21,Cebu |PH,22,Basilan |PH,23,Eastern Samar |PH,24,Davao |PH,25,Davao del Sur |PH,26,Davao Oriental |PH,27,Ifugao |PH,28,Ilocos Norte |PH,29,Ilocos Sur |PH,30,Iloilo |PH,31,Isabela |PH,32,Kalinga-Apayao |PH,33,Laguna |PH,34,Lanao del Norte |PH,35,Lanao del Sur |PH,36,La Union |PH,37,Leyte |PH,38,Marinduque |PH,39,Masbate |PH,40,Mindoro Occidental |PH,41,Mindoro Oriental |PH,42,Misamis Occidental |PH,43,Misamis Oriental |PH,44,Mountain |PH,45,Negros Occidental |PH,46,Negros Oriental |PH,47,Nueva Ecija |PH,48,Nueva Vizcaya |PH,49,Palawan |PH,50,Pampanga |PH,51,Pangasinan | |PH,53,Rizal |PH,54,Romblon |PH,55,Samar |PH,56,Maguindanao |PH,57,North Cotabato |PH,58,Sorsogon |PH,59,Southern Leyte |PH,60,Sulu |PH,61,Surigao del Norte |PH,62,Surigao del Sur |PH,63,Tarlac |PH,64,Zambales |PH,65,Zamboanga del Norte |PH,66,Zamboanga del Sur |PH,67,Northern Samar |PH,68,Quirino |PH,69,Siquijor |PH,70,South Cotabato |PH,71,Sultan Kudarat |PH,72,Tawitawi |PH,A1,Angeles |PH,A2,Bacolod |PH,A3,Bago |PH,A4,Baguio |PH,A5,Bais |PH,A6,Basilan City |PH,A7,Batangas City |PH,A8,Butuan |PH,A9,Cabanatuan |PH,B1,Cadiz |PH,B2,Cagayan de Oro |PH,B3,Calbayog |PH,B4,Caloocan |PH,B5,Canlaon |PH,B6,Cavite City |PH,B7,Cebu City |PH,B8,Cotabato |PH,B9,Dagupan |PH,C1,Danao |PH,C2,Dapitan |PH,C3,Davao City |PH,C4,Dipolog |PH,C5,Dumaguete |PH,C6,General Santos |PH,C7,Gingoog |PH,C8,Iligan |PH,C9,Iloilo City |PH,D1,Iriga |PH,D2,La Carlota |PH,D3,Laoag |PH,D4,Lapu-Lapu |PH,D5,Legaspi |PH,D6,Lipa |PH,D7,Lucena |PH,D8,Mandaue |PH,D9,Manila |PH,E1,Marawi |PH,E2,Naga |PH,E3,Olongapo |PH,E4,Ormoc |PH,E5,Oroquieta |PH,E6,Ozamis |PH,E7,Pagadian |PH,E8,Palayan |PH,E9,Pasay |PH,F1,Puerto Princesa |PH,F2,Quezon City |PH,F3,Roxas |PH,F4,San Carlos |PH,F5,San Carlos |PH,F6,San Jose |PH,F7,San Pablo |PH,F8,Silay |PH,F9,Surigao |PH,G1,Tacloban |PH,G2,Tagaytay |PH,G3,Tagbilaran |PH,G4,Tangub |PH,G5,Toledo |PH,G6,Trece Martires |PH,G7,Zamboanga |PH,G8,Aurora |PH,H2,Quezon |PH,H3,Negros Occidental |PK,01,Federally Administered Tribal Areas |PK,02,Balochistan |PK,03,North-West Frontier |PK,04,Punjab |PK,05,Sindh |PK,06,Azad Kashmir |PK,07,Northern Areas |PK,08,Islamabad |PL,23,Biala Podlaska |PL,24,Bialystok |PL,25,Bielsko |PL,26,Bydgoszcz |PL,27,Chelm |PL,28,Ciechanow |PL,29,Czestochowa |PL,30,Elblag |PL,31,Gdansk |PL,32,Gorzow |PL,33,Jelenia Gora |PL,34,Kalisz |PL,35,Katowice |PL,36,Kielce |PL,37,Konin |PL,38,Koszalin |PL,39,Krakow |PL,40,Krosno |PL,41,Legnica |PL,42,Leszno |PL,43,Lodz |PL,44,Lomza |PL,45,Lublin |PL,46,Nowy Sacz |PL,47,Olsztyn |PL,48,Opole |PL,49,Ostroleka |PL,50,Pila |PL,51,Piotrkow |PL,52,Plock |PL,53,Poznan |PL,54,Przemysl |PL,55,Radom |PL,56,Rzeszow |PL,57,Siedlce |PL,58,Sieradz |PL,59,Skierniewice |PL,60,Slupsk |PL,61,Suwalki |PL,62,Szczecin |PL,63,Tarnobrzeg |PL,64,Tarnow |PL,65,Torun |PL,66,Walbrzych |PL,67,Warszawa |PL,68,Wloclawek |PL,69,Wroclaw |PL,70,Zamosc |PL,71,Zielona Gora |PL,72,Dolnoslaskie |PL,73,Kujawsko-Pomorskie |PL,74,Lodzkie |PL,75,Lubelskie |PL,76,Lubuskie |PL,77,Malopolskie |PL,78,Mazowieckie |PL,79,Opolskie |PL,80,Podkarpackie |PL,81,Podlaskie |PL,82,Pomorskie |PL,83,Slaskie |PL,84,Swietokrzyskie |PL,85,Warminsko-Mazurskie |PL,86,Wielkopolskie |PL,87,Zachodniopomorskie |PS,GZ,Gaza |PS,WE,West Bank |PT,02,Aveiro |PT,03,Beja |PT,04,Braga |PT,05,Braganca |PT,06,Castelo Branco |PT,07,Coimbra |PT,08,Evora |PT,09,Faro |PT,10,Madeira |PT,11,Guarda |PT,13,Leiria |PT,14,Lisboa |PT,16,Portalegre |PT,17,Porto |PT,18,Santarem |PT,19,Setubal |PT,20,Viana do Castelo |PT,21,Vila Real |PT,22,Viseu |PT,23,Azores |PY,01,Alto Parana |PY,02,Amambay |PY,03,Boqueron |PY,04,Caaguazu |PY,05,Caazapa |PY,06,Central |PY,07,Concepcion |PY,08,Cordillera |PY,10,Guaira |PY,11,Itapua |PY,12,Misiones |PY,13,Neembucu |PY,15,Paraguari |PY,16,Presidente Hayes |PY,17,San Pedro |PY,19,Canindeyu |PY,20,Chaco |PY,21,Nueva Asuncion |PY,23,Alto Paraguay |QA,01,Ad Dawhah |QA,02,Al Ghuwariyah |QA,03,Al Jumaliyah |QA,04,Al Khawr |QA,05,Al Wakrah Municipality |QA,06,Ar Rayyan |QA,08,Madinat ach Shamal |QA,09,Umm Salal |QA,10,Al Wakrah |QA,11,Jariyan al Batnah |QA,12,Umm Sa'id |RO,01,Alba |RO,02,Arad |RO,03,Arges |RO,04,Bacau |RO,05,Bihor |RO,06,Bistrita-Nasaud |RO,07,Botosani |RO,08,Braila |RO,09,Brasov |RO,10,Bucuresti |RO,11,Buzau |RO,12,Caras-Severin |RO,13,Cluj |RO,14,Constanta |RO,15,Covasna |RO,16,Dambovita |RO,17,Dolj |RO,18,Galati |RO,19,Gorj |RO,20,Harghita |RO,21,Hunedoara |RO,22,Ialomita |RO,23,Iasi |RO,25,Maramures |RO,26,Mehedinti |RO,27,Mures |RO,28,Neamt |RO,29,Olt |RO,30,Prahova |RO,31,Salaj |RO,32,Satu Mare |RO,33,Sibiu |RO,34,Suceava |RO,35,Teleorman |RO,36,Timis |RO,37,Tulcea |RO,38,Vaslui |RO,39,Valcea |RO,40,Vrancea |RO,41,Calarasi |RO,42,Giurgiu |RO,43,Ilfov |RS,01,Kosovo |RS,02,Vojvodina |RU,01,Adygeya, Republic of |RU,02,Aginsky Buryatsky AO |RU,03,Gorno-Altay |RU,04,Altaisky krai |RU,05,Amur |RU,06,Arkhangel'sk |RU,07,Astrakhan' |RU,08,Bashkortostan |RU,09,Belgorod |RU,10,Bryansk |RU,11,Buryat |RU,12,Chechnya |RU,13,Chelyabinsk |RU,14,Chita |RU,15,Chukot |RU,16,Chuvashia |RU,17,Dagestan |RU,18,Evenk |RU,19,Ingush |RU,20,Irkutsk |RU,21,Ivanovo |RU,22,Kabardin-Balkar |RU,23,Kaliningrad |RU,24,Kalmyk |RU,25,Kaluga |RU,26,Kamchatka |RU,27,Karachay-Cherkess |RU,28,Karelia |RU,29,Kemerovo |RU,30,Khabarovsk |RU,31,Khakass |RU,32,Khanty-Mansiy |RU,33,Kirov |RU,34,Komi |RU,35,Komi-Permyak |RU,36,Koryak |RU,37,Kostroma |RU,38,Krasnodar |RU,39,Krasnoyarsk |RU,40,Kurgan |RU,41,Kursk |RU,42,Leningrad |RU,43,Lipetsk |RU,44,Magadan |RU,45,Mariy-El |RU,46,Mordovia |RU,47,Moskva |RU,48,Moscow City |RU,49,Murmansk |RU,50,Nenets |RU,51,Nizhegorod |RU,52,Novgorod |RU,53,Novosibirsk |RU,54,Omsk |RU,55,Orenburg |RU,56,Orel |RU,57,Penza |RU,58,Perm' |RU,59,Primor'ye |RU,60,Pskov |RU,61,Rostov |RU,62,Ryazan' |RU,63,Sakha |RU,64,Sakhalin |RU,65,Samara |RU,66,Saint Petersburg City |RU,67,Saratov |RU,68,North Ossetia |RU,69,Smolensk |RU,70,Stavropol' |RU,71,Sverdlovsk |RU,72,Tambovskaya oblast |RU,73,Tatarstan |RU,74,Taymyr |RU,75,Tomsk |RU,76,Tula |RU,77,Tver' |RU,78,Tyumen' |RU,79,Tuva |RU,80,Udmurt |RU,81,Ul'yanovsk |RU,82,Ust-Orda Buryat |RU,83,Vladimir |RU,84,Volgograd |RU,85,Vologda |RU,86,Voronezh |RU,87,Yamal-Nenets |RU,88,Yaroslavl' |RU,89,Yevrey |RU,90,Permskiy Kray |RU,91,Krasnoyarskiy Kray |RU,CI,Chechnya Republic |RW,01,Butare |RW,06,Gitarama |RW,07,Kibungo |RW,09,Kigali |RW,11,Est |RW,12,Kigali |RW,13,Nord |RW,14,Ouest |RW,15,Sud |SA,02,Al Bahah |SA,03,Al Jawf |SA,05,Al Madinah |SA,06,Ash Sharqiyah |SA,08,Al Qasim |SA,09,Al Qurayyat |SA,10,Ar Riyad |SA,13,Ha'il |SA,14,Makkah |SA,15,Al Hudud ash Shamaliyah |SA,16,Najran |SA,17,Jizan |SA,19,Tabuk |SA,20,Al Jawf |SB,03,Malaita |SB,06,Guadalcanal |SB,07,Isabel |SB,08,Makira |SB,09,Temotu |SB,10,Central |SB,11,Western |SB,12,Choiseul |SB,13,Rennell and Bellona |SC,01,Anse aux Pins |SC,02,Anse Boileau |SC,03,Anse Etoile |SC,04,Anse Louis |SC,05,Anse Royale |SC,06,Baie Lazare |SC,07,Baie Sainte Anne |SC,08,Beau Vallon |SC,09,Bel Air |SC,10,Bel Ombre |SC,11,Cascade |SC,12,Glacis |SC,13,Grand' Anse |SC,14,Grand' Anse |SC,15,La Digue |SC,16,La Riviere Anglaise |SC,17,Mont Buxton |SC,18,Mont Fleuri |SC,19,Plaisance |SC,20,Pointe La Rue |SC,21,Port Glaud |SC,22,Saint Louis |SC,23,Takamaka |SD,27,Al Wusta |SD,28,Al Istiwa'iyah |SD,29,Al Khartum |SD,30,Ash Shamaliyah |SD,31,Ash Sharqiyah |SD,32,Bahr al Ghazal |SD,33,Darfur |SD,34,Kurdufan |SD,35,Upper Nile |SD,40,Al Wahadah State |SD,44,Central Equatoria State |SE,01,Alvsborgs Lan |SE,02,Blekinge Lan |SE,03,Gavleborgs Lan |SE,04,Goteborgs och Bohus Lan |SE,05,Gotlands Lan |SE,06,Hallands Lan |SE,07,Jamtlands Lan |SE,08,Jonkopings Lan |SE,09,Kalmar Lan |SE,10,Dalarnas Lan |SE,11,Kristianstads Lan |SE,12,Kronobergs Lan |SE,13,Malmohus Lan |SE,14,Norrbottens Lan |SE,15,Orebro Lan |SE,16,Ostergotlands Lan |SE,17,Skaraborgs Lan |SE,18,Sodermanlands Lan |SE,21,Uppsala Lan |SE,22,Varmlands Lan |SE,23,Vasterbottens Lan |SE,24,Vasternorrlands Lan |SE,25,Vastmanlands Lan |SE,26,Stockholms Lan |SE,27,Skane Lan |SE,28,Vastra Gotaland |SH,01,Ascension |SH,02,Saint Helena |SH,03,Tristan da Cunha |SI,01,Ajdovscina |SI,02,Beltinci |SI,03,Bled |SI,04,Bohinj |SI,05,Borovnica |SI,06,Bovec |SI,07,Brda |SI,08,Brezice |SI,09,Brezovica |SI,11,Celje |SI,12,Cerklje na Gorenjskem |SI,13,Cerknica |SI,14,Cerkno |SI,15,Crensovci |SI,16,Crna na Koroskem |SI,17,Crnomelj |SI,19,Divaca |SI,20,Dobrepolje |SI,22,Dol pri Ljubljani |SI,24,Dornava |SI,25,Dravograd |SI,26,Duplek |SI,27,Gorenja Vas-Poljane |SI,28,Gorisnica |SI,29,Gornja Radgona |SI,30,Gornji Grad |SI,31,Gornji Petrovci |SI,32,Grosuplje |SI,34,Hrastnik |SI,35,Hrpelje-Kozina |SI,36,Idrija |SI,37,Ig |SI,38,Ilirska Bistrica |SI,39,Ivancna Gorica |SI,40,Izola-Isola |SI,42,Jursinci |SI,44,Kanal |SI,45,Kidricevo |SI,46,Kobarid |SI,47,Kobilje |SI,49,Komen |SI,50,Koper-Capodistria |SI,51,Kozje |SI,52,Kranj |SI,53,Kranjska Gora |SI,54,Krsko |SI,55,Kungota |SI,57,Lasko |SI,61,Ljubljana |SI,62,Ljubno |SI,64,Logatec |SI,66,Loski Potok |SI,68,Lukovica |SI,71,Medvode |SI,72,Menges |SI,73,Metlika |SI,74,Mezica |SI,76,Mislinja |SI,77,Moravce |SI,78,Moravske Toplice |SI,79,Mozirje |SI,80,Murska Sobota |SI,81,Muta |SI,82,Naklo |SI,83,Nazarje |SI,84,Nova Gorica |SI,86,Odranci |SI,87,Ormoz |SI,88,Osilnica |SI,89,Pesnica |SI,91,Pivka |SI,92,Podcetrtek |SI,94,Postojna |SI,97,Puconci |SI,98,Racam |SI,99,Radece |SI,A1,Radenci |SI,A2,Radlje ob Dravi |SI,A3,Radovljica |SI,A6,Rogasovci |SI,A7,Rogaska Slatina |SI,A8,Rogatec |SI,B1,Semic |SI,B2,Sencur |SI,B3,Sentilj |SI,B4,Sentjernej |SI,B6,Sevnica |SI,B7,Sezana |SI,B8,Skocjan |SI,B9,Skofja Loka |SI,C1,Skofljica |SI,C2,Slovenj Gradec |SI,C4,Slovenske Konjice |SI,C5,Smarje pri Jelsah |SI,C6,Smartno ob Paki |SI,C7,Sostanj |SI,C8,Starse |SI,C9,Store |SI,D1,Sveti Jurij |SI,D2,Tolmin |SI,D3,Trbovlje |SI,D4,Trebnje |SI,D5,Trzic |SI,D6,Turnisce |SI,D7,Velenje |SI,D8,Velike Lasce |SI,E1,Vipava |SI,E2,Vitanje |SI,E3,Vodice |SI,E5,Vrhnika |SI,E6,Vuzenica |SI,E7,Zagorje ob Savi |SI,E9,Zavrc |SI,F1,Zelezniki |SI,F2,Ziri |SI,F3,Zrece |SI,G4,Dobrova-Horjul-Polhov Gradec |SI,G7,Domzale |SI,H4,Jesenice |SI,H6,Kamnik |SI,H7,Kocevje |SI,I2,Kuzma |SI,I3,Lenart |SI,I5,Litija |SI,I6,Ljutomer |SI,I7,Loska Dolina |SI,I9,Luce |SI,J1,Majsperk |SI,J2,Maribor |SI,J5,Miren-Kostanjevica |SI,J7,Novo Mesto |SI,J9,Piran |SI,K5,Preddvor |SI,K7,Ptuj |SI,L1,Ribnica |SI,L3,Ruse |SI,L7,Sentjur pri Celju |SI,L8,Slovenska Bistrica |SI,N2,Videm |SI,N3,Vojnik |SI,N5,Zalec |SK,01,Banska Bystrica |SK,02,Bratislava |SK,03,Kosice |SK,04,Nitra |SK,05,Presov |SK,06,Trencin |SK,07,Trnava |SK,08,Zilina |SL,01,Eastern |SL,02,Northern |SL,03,Southern |SL,04,Western Area |SM,01,Acquaviva |SM,02,Chiesanuova |SM,03,Domagnano |SM,04,Faetano |SM,05,Fiorentino |SM,06,Borgo Maggiore |SM,07,San Marino |SM,08,Monte Giardino |SM,09,Serravalle |SN,01,Dakar |SN,03,Diourbel |SN,04,Saint-Louis |SN,05,Tambacounda |SN,07,Thies |SN,08,Louga |SN,09,Fatick |SN,10,Kaolack |SN,11,Kolda |SN,12,Ziguinchor |SN,13,Louga |SN,14,Saint-Louis |SN,15,Matam |SO,01,Bakool |SO,02,Banaadir |SO,03,Bari |SO,04,Bay |SO,05,Galguduud |SO,06,Gedo |SO,07,Hiiraan |SO,08,Jubbada Dhexe |SO,09,Jubbada Hoose |SO,10,Mudug |SO,11,Nugaal |SO,12,Sanaag |SO,13,Shabeellaha Dhexe |SO,14,Shabeellaha Hoose |SO,16,Woqooyi Galbeed |SO,18,Nugaal |SO,19,Togdheer |SO,20,Woqooyi Galbeed |SO,21,Awdal |SO,22,Sool |SR,10,Brokopondo |SR,11,Commewijne |SR,12,Coronie |SR,13,Marowijne |SR,14,Nickerie |SR,15,Para |SR,16,Paramaribo |SR,17,Saramacca |SR,18,Sipaliwini |SR,19,Wanica |ST,01,Principe |ST,02,Sao Tome |SV,01,Ahuachapan |SV,02,Cabanas |SV,03,Chalatenango |SV,04,Cuscatlan |SV,05,La Libertad |SV,06,La Paz |SV,07,La Union |SV,08,Morazan |SV,09,San Miguel |SV,10,San Salvador |SV,11,Santa Ana |SV,12,San Vicente |SV,13,Sonsonate |SV,14,Usulutan |SY,01,Al Hasakah |SY,02,Al Ladhiqiyah |SY,03,Al Qunaytirah |SY,04,Ar Raqqah |SY,05,As Suwayda' |SY,06,Dar |SY,07,Dayr az Zawr |SY,08,Rif Dimashq |SY,09,Halab |SY,10,Hamah |SY,11,Hims |SY,12,Idlib |SY,13,Dimashq |SY,14,Tartus |SZ,01,Hhohho |SZ,02,Lubombo |SZ,03,Manzini |SZ,04,Shiselweni |SZ,05,Praslin |TD,01,Batha |TD,02,Biltine |TD,03,Borkou-Ennedi-Tibesti |TD,04,Chari-Baguirmi |TD,05,Guera |TD,06,Kanem |TD,07,Lac |TD,08,Logone Occidental |TD,09,Logone Oriental |TD,10,Mayo-Kebbi |TD,11,Moyen-Chari |TD,12,Ouaddai |TD,13,Salamat |TD,14,Tandjile |TG,09,Lama-Kara |TG,18,Tsevie |TG,22,Centrale |TG,23,Kara |TG,24,Maritime |TG,25,Plateaux |TG,26,Savanes |TH,01,Mae Hong Son |TH,02,Chiang Mai |TH,03,Chiang Rai |TH,04,Nan |TH,05,Lamphun |TH,06,Lampang |TH,07,Phrae |TH,08,Tak |TH,09,Sukhothai |TH,10,Uttaradit |TH,11,Kamphaeng Phet |TH,12,Phitsanulok |TH,13,Phichit |TH,14,Phetchabun |TH,15,Uthai Thani |TH,16,Nakhon Sawan |TH,17,Nong Khai |TH,18,Loei |TH,20,Sakon Nakhon |TH,21,Nakhon Phanom |TH,22,Khon Kaen |TH,23,Kalasin |TH,24,Maha Sarakham |TH,25,Roi Et |TH,26,Chaiyaphum |TH,27,Nakhon Ratchasima |TH,28,Buriram |TH,29,Surin |TH,30,Sisaket |TH,31,Narathiwat |TH,32,Chai Nat |TH,33,Sing Buri |TH,34,Lop Buri |TH,35,Ang Thong |TH,36,Phra Nakhon Si Ayutthaya |TH,37,Saraburi |TH,38,Nonthaburi |TH,39,Pathum Thani |TH,40,Krung Thep |TH,41,Phayao |TH,42,Samut Prakan |TH,43,Nakhon Nayok |TH,44,Chachoengsao |TH,45,Prachin Buri |TH,46,Chon Buri |TH,47,Rayong |TH,48,Chanthaburi |TH,49,Trat |TH,50,Kanchanaburi |TH,51,Suphan Buri |TH,52,Ratchaburi |TH,53,Nakhon Pathom |TH,54,Samut Songkhram |TH,55,Samut Sakhon |TH,56,Phetchaburi |TH,57,Prachuap Khiri Khan |TH,58,Chumphon |TH,59,Ranong |TH,60,Surat Thani |TH,61,Phangnga |TH,62,Phuket |TH,63,Krabi |TH,64,Nakhon Si Thammarat |TH,65,Trang |TH,66,Phatthalung |TH,67,Satun |TH,68,Songkhla |TH,69,Pattani |TH,70,Yala |TH,71,Ubon Ratchathani |TH,72,Yasothon |TH,73,Nakhon Phanom |TH,75,Ubon Ratchathani |TH,76,Udon Thani |TH,77,Amnat Charoen |TH,78,Mukdahan |TH,79,Nong Bua Lamphu |TH,80,Sa Kaeo |TJ,01,Kuhistoni Badakhshon |TJ,02,Khatlon |TJ,03,Sughd |TM,01,Ahal |TM,02,Balkan |TM,03,Dashoguz |TM,04,Lebap |TM,05,Mary |TN,02,Kasserine |TN,03,Kairouan |TN,06,Jendouba |TN,14,El Kef |TN,15,Al Mahdia |TN,16,Al Munastir |TN,17,Bajah |TN,18,Bizerte |TN,19,Nabeul |TN,22,Siliana |TN,23,Sousse |TN,26,Ariana |TN,27,Ben Arous |TN,28,Madanin |TN,29,Gabes |TN,30,Gafsa |TN,31,Kebili |TN,32,Sfax |TN,33,Sidi Bou Zid |TN,34,Tataouine |TN,35,Tozeur |TN,36,Tunis |TN,37,Zaghouan |TO,01,Ha |TO,02,Tongatapu |TO,03,Vava |TR,02,Adiyaman |TR,03,Afyonkarahisar |TR,04,Agri |TR,05,Amasya |TR,07,Antalya |TR,08,Artvin |TR,09,Aydin |TR,10,Balikesir |TR,11,Bilecik |TR,12,Bingol |TR,13,Bitlis |TR,14,Bolu |TR,15,Burdur |TR,16,Bursa |TR,17,Canakkale |TR,19,Corum |TR,20,Denizli |TR,21,Diyarbakir |TR,22,Edirne |TR,23,Elazig |TR,24,Erzincan |TR,25,Erzurum |TR,26,Eskisehir |TR,28,Giresun |TR,31,Hatay |TR,32,Mersin |TR,33,Isparta |TR,34,Istanbul |TR,35,Izmir |TR,37,Kastamonu |TR,38,Kayseri |TR,39,Kirklareli |TR,40,Kirsehir |TR,41,Kocaeli |TR,43,Kutahya |TR,44,Malatya |TR,45,Manisa |TR,46,Kahramanmaras |TR,48,Mugla |TR,49,Mus |TR,50,Nevsehir |TR,52,Ordu |TR,53,Rize |TR,54,Sakarya |TR,55,Samsun |TR,57,Sinop |TR,58,Sivas |TR,59,Tekirdag |TR,60,Tokat |TR,61,Trabzon |TR,62,Tunceli |TR,63,Sanliurfa |TR,64,Usak |TR,65,Van |TR,66,Yozgat |TR,68,Ankara |TR,69,Gumushane |TR,70,Hakkari |TR,71,Konya |TR,72,Mardin |TR,73,Nigde |TR,74,Siirt |TR,75,Aksaray |TR,76,Batman |TR,77,Bayburt |TR,78,Karaman |TR,79,Kirikkale |TR,80,Sirnak |TR,81,Adana |TR,82,Cankiri |TR,83,Gaziantep |TR,84,Kars |TR,85,Zonguldak |TR,86,Ardahan |TR,87,Bartin |TR,88,Igdir |TR,89,Karabuk |TR,90,Kilis |TR,91,Osmaniye |TR,92,Yalova |TR,93,Duzce |TT,01,Arima |TT,02,Caroni |TT,03,Mayaro |TT,04,Nariva |TT,05,Port-of-Spain |TT,06,Saint Andrew |TT,07,Saint David |TT,08,Saint George |TT,09,Saint Patrick |TT,10,San Fernando |TT,11,Tobago |TT,12,Victoria |TW,01,Fu-chien |TW,02,Kao-hsiung |TW,03,T'ai-pei |TW,04,T'ai-wan |TZ,02,Pwani |TZ,03,Dodoma |TZ,04,Iringa |TZ,05,Kigoma |TZ,06,Kilimanjaro |TZ,07,Lindi |TZ,08,Mara |TZ,09,Mbeya |TZ,10,Morogoro |TZ,11,Mtwara |TZ,12,Mwanza |TZ,13,Pemba North |TZ,14,Ruvuma |TZ,15,Shinyanga |TZ,16,Singida |TZ,17,Tabora |TZ,18,Tanga |TZ,19,Kagera |TZ,20,Pemba South |TZ,21,Zanzibar Central |TZ,22,Zanzibar North |TZ,23,Dar es Salaam |TZ,24,Rukwa |TZ,25,Zanzibar Urban |TZ,26,Arusha |TZ,27,Manyara |UA,01,Cherkas'ka Oblast' |UA,02,Chernihivs'ka Oblast' |UA,03,Chernivets'ka Oblast' |UA,04,Dnipropetrovs'ka Oblast' |UA,05,Donets'ka Oblast' |UA,06,Ivano-Frankivs'ka Oblast' |UA,07,Kharkivs'ka Oblast' |UA,08,Khersons'ka Oblast' |UA,09,Khmel'nyts'ka Oblast' |UA,10,Kirovohrads'ka Oblast' |UA,11,Krym |UA,12,Kyyiv |UA,13,Kyyivs'ka Oblast' |UA,14,Luhans'ka Oblast' |UA,15,L'vivs'ka Oblast' |UA,16,Mykolayivs'ka Oblast' |UA,17,Odes'ka Oblast' |UA,18,Poltavs'ka Oblast' |UA,19,Rivnens'ka Oblast' |UA,20,Sevastopol' |UA,21,Sums'ka Oblast' |UA,22,Ternopil's'ka Oblast' |UA,23,Vinnyts'ka Oblast' |UA,24,Volyns'ka Oblast' |UA,25,Zakarpats'ka Oblast' |UA,26,Zaporiz'ka Oblast' |UA,27,Zhytomyrs'ka Oblast' |UG,05,Busoga |UG,08,Karamoja |UG,12,South Buganda |UG,18,Central |UG,20,Eastern |UG,21,Nile |UG,22,North Buganda |UG,23,Northern |UG,24,Southern |UG,25,Western |UG,33,Jinja |UG,36,Kalangala |UG,37,Kampala |UG,42,Kiboga |UG,52,Mbarara |UG,56,Mubende |UG,65,Adjumani |UG,66,Bugiri |UG,67,Busia |UG,69,Katakwi |UG,71,Masaka |UG,73,Nakasongola |UG,74,Sembabule |UG,77,Arua |UG,78,Iganga |UG,79,Kabarole |UG,80,Kaberamaido |UG,81,Kamwenge |UG,82,Kanungu |UG,83,Kayunga |UG,84,Kitgum |UG,85,Kyenjojo |UG,86,Mayuge |UG,87,Mbale |UG,88,Moroto |UG,89,Mpigi |UG,90,Mukono |UG,91,Nakapiripirit |UG,92,Pader |UG,93,Rukungiri |UG,94,Sironko |UG,95,Soroti |UG,96,Wakiso |UG,97,Yumbe |US,AL,Alabama |US,AK,Alaska |US,AZ,Arizona |US,AR,Arkansas |US,CA,California |US,CO,Colorado |US,CT,Connecticut |US,DE,Delaware |US,DC,District of Columbia |US,FL,Florida |US,GA,Georgia |US,HI,Hawaii |US,ID,Idaho |US,IL,Illinois |US,IN,Indiana |US,IA,Iowa |US,KS,Kansas |US,KY,Kentucky |US,LA,Louisiana |US,ME,Maine |US,MD,Maryland |US,MA,Massachusetts |US,MI,Michigan |US,MN,Minnesota |US,,Mississippi |US,MS,Missouri |US,MO,Montana |US,NE,Nebraska |US,NV,Nevada |US,NH,New Hampshire |US,NJ,New Jersey |US,NM,New Mexico |US,NY,New York |US,NC,North Carolina |US,ND,North Dakota |US,OH,Ohio |US,OK,Oklahoma |US,OR,Oregon |US,PA,Pennsylvania |US,RI,Rhode Island |US,SC,South Carolina |US,SD,South Dakota |US,TN,Tennessee |US,TX,Texas |US,UT,Utah |US,VT,Vermont |US,VA,Virginia |US,WA,Washington |US,WV,West Virginia |US,WI,Wisconsin |US,WY,Wyoming |UY,01,Artigas |UY,02,Canelones |UY,03,Cerro Largo |UY,04,Colonia |UY,05,Durazno |UY,06,Flores |UY,07,Florida |UY,08,Lavalleja |UY,09,Maldonado |UY,10,Montevideo |UY,11,Paysandu |UY,12,Rio Negro |UY,13,Rivera |UY,14,Rocha |UY,15,Salto |UY,16,San Jose |UY,17,Soriano |UY,18,Tacuarembo |UY,19,Treinta y Tres |UZ,01,Andijon |UZ,02,Bukhoro |UZ,03,Farghona |UZ,04,Jizzakh |UZ,05,Khorazm |UZ,06,Namangan |UZ,07,Nawoiy |UZ,08,Qashqadaryo |UZ,09,Qoraqalpoghiston |UZ,10,Samarqand |UZ,11,Sirdaryo |UZ,12,Surkhondaryo |UZ,13,Toshkent |UZ,14,Toshkent |VC,01,Charlotte |VC,02,Saint Andrew |VC,03,Saint David |VC,04,Saint George |VC,05,Saint Patrick |VC,06,Grenadines |VE,01,Amazonas |VE,02,Anzoategui |VE,03,Apure |VE,04,Aragua |VE,05,Barinas |VE,06,Bolivar |VE,07,Carabobo |VE,08,Cojedes |VE,09,Delta Amacuro |VE,11,Falcon |VE,12,Guarico |VE,13,Lara |VE,14,Merida |VE,15,Miranda |VE,16,Monagas |VE,17,Nueva Esparta |VE,18,Portuguesa |VE,19,Sucre |VE,20,Tachira |VE,21,Trujillo |VE,22,Yaracuy |VE,23,Zulia |VE,24,Dependencias Federales |VE,25,Distrito Federal |VE,26,Vargas |VN,01,An Giang |VN,02,Bac Thai |VN,03,Ben Tre |VN,04,Binh Tri Thien |VN,05,Cao Bang |VN,06,Cuu Long |VN,07,Dac Lac |VN,09,Dong Thap |VN,11,Ha Bac |VN,12,Hai Hung |VN,13,Hai Phong |VN,14,Ha Nam Ninh |VN,15,Ha Noi |VN,16,Ha Son Binh |VN,17,Ha Tuyen |VN,19,Hoang Lien Son |VN,20,Ho Chi Minh |VN,21,Kien Giang |VN,22,Lai Chau |VN,23,Lam Dong |VN,24,Long An |VN,25,Minh Hai |VN,26,Nghe Tinh |VN,27,Nghia Binh |VN,28,Phu Khanh |VN,29,Quang Nam-Da Nang |VN,30,Quang Ninh |VN,31,Song Be |VN,32,Son La |VN,33,Tay Ninh |VN,34,Thanh Hoa |VN,35,Thai Binh |VN,36,Thuan Hai |VN,37,Tien Giang |VN,38,Vinh Phu |VN,39,Lang Son |VN,40,Dong Nai |VN,43,An Giang |VN,44,Dac Lac |VN,45,Dong Nai |VN,46,Dong Thap |VN,47,Kien Giang |VN,48,Minh Hai |VN,49,Song Be |VN,50,Vinh Phu |VN,51,Ha Noi |VN,52,Ho Chi Minh |VN,53,Ba Ria-Vung Tau |VN,54,Binh Dinh |VN,55,Binh Thuan |VN,56,Can Tho |VN,57,Gia Lai |VN,58,Ha Giang |VN,59,Ha Tay |VN,60,Ha Tinh |VN,61,Hoa Binh |VN,62,Khanh Hoa |VN,63,Kon Tum |VN,64,Quang Tri |VN,65,Nam Ha |VN,66,Nghe An |VN,67,Ninh Binh |VN,68,Ninh Thuan |VN,69,Phu Yen |VN,70,Quang Binh |VN,71,Quang Ngai |VN,72,Quang Tri |VN,73,Soc Trang |VN,74,Thua Thien |VN,75,Tra Vinh |VN,76,Tuyen Quang |VN,77,Vinh Long |VN,78,Da Nang |VN,79,Hai Duong |VN,80,Ha Nam |VN,81,Hung Yen |VN,82,Nam Dinh |VN,83,Phu Tho |VN,84,Quang Nam |VN,85,Thai Nguyen |VN,86,Vinh Puc Province |VN,87,Can Tho |VN,88,Dak Lak |VN,89,Lai Chau |VN,90,Lao Cai |VN,91,Dak Nong |VN,92,Dien Bien |VN,93,Hau Giang |VU,05,Ambrym |VU,06,Aoba |VU,07,Torba |VU,08,Efate |VU,09,Epi |VU,10,Malakula |VU,11,Paama |VU,12,Pentecote |VU,13,Sanma |VU,14,Shepherd |VU,15,Tafea |VU,16,Malampa |VU,17,Penama |VU,18,Shefa |WS,02,Aiga-i-le-Tai |WS,03,Atua |WS,04,Fa |WS,05,Gaga |WS,06,Va |WS,07,Gagaifomauga |WS,08,Palauli |WS,09,Satupa |WS,10,Tuamasaga |WS,11,Vaisigano |YE,01,Abyan |YE,02,Adan |YE,03,Al Mahrah |YE,04,Hadramawt |YE,05,Shabwah |YE,06,Al Ghaydah |YE,08,Al Hudaydah |YE,10,Al Mahwit |YE,11,Dhamar |YE,14,Ma'rib |YE,15,Sa |YE,16,San |YE,20,Al Bayda' |YE,21,Al Jawf |YE,22,Hajjah |YE,23,Ibb |YE,24,Lahij |YE,25,Ta |ZA,01,North-Western Province |ZA,02,KwaZulu-Natal |ZA,03,Free State |ZA,05,Eastern Cape |ZA,06,Gauteng |ZA,07,Mpumalanga |ZA,08,Northern Cape |ZA,09,Limpopo |ZA,10,North-West |ZA,11,Western Cape |ZM,01,Western |ZM,02,Central |ZM,03,Eastern |ZM,04,Luapula |ZM,05,Northern |ZM,06,North-Western |ZM,07,Southern |ZM,08,Copperbelt |ZM,09,Lusaka |ZW,01,Manicaland |ZW,02,Midlands |ZW,03,Mashonaland Central |ZW,04,Mashonaland East |ZW,05,Mashonaland West |ZW,06,Matabeleland North |ZW,07,Matabeleland South |ZW,08,Masvingo |ZW,09,Bulawayo |ZW,10,Harare";
		var result = new Array();
		var all = regions.split("|");
		var count=0;
		for	(var i=0;i<all.length;i++) {
			var one = all[i].split(",");
			if(one[0]==code) {
				result[count]=one[1].trim()+","+one[2].trim();
				count++;
			}
		}
		return result;
	}

	// Function to return DMAs
	function getDMA() {
		var dmas='Abilene-Sweetwater, TX*662|Albany, GA*525|Albany-Schenectady-Troy, NY*532|Albuquerque, NM*790|Alexandria, LA*644|Alpena, MI*583|Amarillo, TX*634|Anchorage, AK*743|Anniston, AL*646|Atlanta, GA*524|Augusta, GA*520|Austin, TX*635|Bakersfield, CA*800|Baltimore, MD*512|Bangor, ME*537|Baton Rouge, LA*716|Beaumont-Port Author, TX*692|Bend, OR*821|Billings, MT*756|Biloxi-Gulfport, MS*746|Binghamton, NY*502|Birmingham, AL*630|Bluefield-Beckley-Oak Hill, WV*559|Boise, ID*757|Boston, MA*506|Bowling Green, KY*736|Buffalo, NY*514|Burlington, VT*523|Butte-Bozeman, MT*754|Casper-Riverton, WY*767|Cedar Rapids-Waterloo, IA*637|Champaign-Springfield-Decatur, IL*648|Charleston, SC*519|Charleston-Huntington, WV*564|Charlotte, NC*517|Charlottesville, VA*584|Chattanooga, TN*575|Cheyenne, WY*759|Chicago, IL*602|Chico-Redding, CA*868|Cincinnati, OH*515|Clarksburg-Weston, WV*598|Cleveland, OH*510|Colorado Springs, CO*752|Columbia, SC*546|Columbia-Jefferson City, MO*604|Columbus, GA*522|Columbus, OH*535|Columbus-Tupelo-West Point, MS*673|Corpus Christi, TX*600|Dallas-Fort Worth, TX*623|Davenport-Rock Island-Moline, IL*682|Dayton, OH*542|Denver, CO*751|Des Moines, IA*679|Detroit, MI*505|Dothan, AL*606|Duluth, MN*676|El Paso, TX*765|Elmira, NY*565|Erie, PA*516|Eugene, OR*801|Eureka, CA*802|Evansville, IN*649|Fairbanks, AK*745|Fargo-Valley City, ND*724|Flint, MI*513|Florence-Myrtle Beach, SC*570|Fresno, CA*866|Ft Myers, FL*571|Ft Smith-Fay-Springfield, AR*670|Ft Wayne, IN*509|Gainesville, FL*592|Glendive, MT*798|Grand Junction, CO*773|Grand Rapids, MI*563|Great Falls, MT*755|Green Bay-Appleton, WI*658|Greensboro, NC*518|Greenville-New Bern-Washington, NC*545|Greenville-Spartenburg, SC*567|Greenwood-Greenville, MS*647|Harlingen, TX*636|Harrisburg-Lancaster-Lebanon-York, PA*566|Harrisonburg, VA*569|Hartford, CT*533|Hattiesburg-Laurel, MS*710|Helena, MT*766|Honolulu, HI*744|Houston, TX*618|Huntsville, AL*691|Idaho Falls-Pocatello, ID*758|Indianapolis, IN*527|Jackson, MS*718|Jackson, TN*639|Jacksonville, FL*561|Johnstown-Altoona, PA*574|Jonesboro, AR*734|Joplin-Pittsburg, MO*603|Juneau, AK*747|Kansas City, MO*616|Knoxville, TN*557|La Crosse-Eau Claire, WI*702|Lafayette, IN*582|Lafayette, LA*642|Lake Charles, LA*643|Lansing, MI*551|Laredo, TX*749|Las Vegas, NV*839|Lexington, KY*541|Lima, OH*558|Lincoln-Hastings, NE*722|Little Rock-Pine Bluff, AR*693|Los Angeles, CA*803|Louisville, KY*529|Lubbock, TX*651|Macon, GA*503|Madison, WI*669|Mankato, MN*737|Marquette, MI*553|Medford-Klamath Falls, OR*813|Memphis, TN*640|Meridian, MS*711|Miami, FL*528|Milwaukee, WI*617|Minneapolis-St Paul, MN*613|Minot-Bismarck-Dickinson, ND*687|Missoula, MT*762|Mobile, AL*686|Monroe, LA*628|Monterey-Salinas, CA*828|Montgomery, AL*698|Nashville, TN*659|New Orleans, LA*622|New York, NY*501|Norfolk-Portsmouth, VA*544|North Platte, NE*740|Odessa-Midland, TX*633|Oklahoma City, OK*650|Omaha, NE*652|Orlando, FL*534|Ottumwa-Kirksville, IA*631|Paducah, KY*632|Palm Springs, CA*804|Panama City, FL*656|Parkersburg, WV*597|Peoria-Bloomington, IL*675|Philadelphia, PA*504|Phoenix, AZ*753|Pittsburgh, PA*508|Portland, OR*820|Portland-Auburn, ME*500|Presque Isle, ME*552|Providence, RI*521|Quincy, IL*717|Raleigh-Durham, NC*560|Rapid City, SD*764|Reno, NV*811|Richmond-Petersburg, VA*556|Roanoke-Lynchburg, VA*573|Rochester, NY*538|Rochester-Mason City-Austin, MN*611|Rockford, IL*610|Sacramento, CA*862|Salisbury, MD*576|Salt Lake City, UT*770|San Angelo, TX*661|San Antonio, TX*641|San Diego, CA*825|San Francisco, CA*807|Santa Barbara, CA*855|Savannah, GA*507|Seattle-Tacoma, WA*819|Sherman, TX*657|Shreveport, LA*612|Sioux City, IA*624|Sioux Falls, SD*725|South Bend, IN*588|Spokane, WA*881|Springfield, MO*619|Springfield-Holyoke, MA*543|St Joseph, MO*638|St Louis, MO*609|Syracuse, NY*555|Tallahassee, FL*530|Tampa, FL*539|Terre Haute, IN*581|Toledo, OH*547|Topeka, KS*605|Traverse City-Cadillac, MI*540|Tri-Cities, TN*531|Tucson, AZ*789|Tulsa, OK*671|Tuscaloosa, AL*196|Twin Falls, ID*760|Tyler-Longview, TX*709|Utica-Rome, NY*526|Victoria, TX*626|Waco-Temple-Bryan, TX*625|Washington, DC*511|Watertown, NY*549|Wausau-Rhinelander, WI*705|West Palm Beach, FL*548|Wheeling, WV*554|Wichita Falls TX & Lawton OK*627|Wichita, KS*678|Wilkes Barre-Scranton, PA*577|Wilmington, NC*550|Yakima-Pasco, WA*810|Youngstown-Warren, OH*536|Yuma, AZ*771|Zanesville, OH*596';
		return dmas;
	}

	// Function to update regions on change for country
	function updateRegions(id,currentid,code) {
		var getR = getRegionByCountryCode(code);
		var output = "";
		for(var j=0;j<=getR.length-1;j++){
			var splitEm2 = getR[j].split(",");
			output += "<option value=\""+splitEm2[0]+"\">"+splitEm2[1]+"</option>";
		}
		document.getElementById(id).innerHTML = output;
		ADAG('#'+id).siblings().remove().end().fcbkcomplete({ filter_selected: true });
		initAll();
	}

	function onSelectItem(param){
		//alert(param.attr('id'));
		param.css('border','0px none');
		sanitizeLi(param);
		window.setTimeout(function(){
			if(param.hasClass('city')||param.hasClass('postalcode')) { //||param.hasClass('usarea')
				if(!param.hasClass('city')) param.find('.maininput').hide();
			} else if(param.hasClass('region')){
				param.find('td:first').css('border','1px solid transparent');
				//param.find('.maininput').hide();
				/*var currentid = param.attr('id').split('-');
				var val = param.find('.selected').val();
				var id = "updateRegions-"+currentid[1];
				updateRegions(id.toString(),currentid[1],val);*/
			} else if(param.hasClass('country')){
				ADAG('#firstOption').prop('checked','true');
				if(param.find('.bit-box').length <= 1) {
					ADAG('#country_container').show();
				} else {
					ADAG('#country_container').hide();
					ADAG('#region_container').remove();
					ADAG('#city_container').remove();
				}
			}
		}, 1);
	}

	function onRemoveItem(param){
		sanitizeLi(param);
		if(param.hasClass('city')||param.hasClass('region')||param.hasClass('postalcode')) {//||param.hasClass('usarea')
			window.setTimeout(function(){
				param.find('.maininput').show();
			}, 1);
		} else if(param.hasClass('region')){
			param.find('.upd8regs').siblings().remove();
			param.find('.upd8regs').html('').fcbkcomplete();
		} else if(param.hasClass('country')){
			//alert(param.find('.bit-box').length);
			if(param.find('.holder:first .bit-box').length <= 2) {
				if(param.find('.holder:first .bit-box').length != 1){
					ADAG('#country_container').show();
				} else {
					ADAG('#country_container').hide();
					ADAG('#region_container').remove();
					ADAG('#city_container').remove();
				}
			} else {
				ADAG('#country_container').hide();
				ADAG('#region_container').remove();
				ADAG('#city_container').remove();
			}
		}
	}

	function initSanitizeLi(){
		ADAG('#opts tr').each(function(){
			if(!ADAG(this).hasClass('country')){
				ADAG(this).find('.holder .bit-input :not(:last)').remove();
			} else {
				ADAG(this).find('.holder:first').find('.bit-input :not(:last)').remove();
			}
		});
	}

	function sanitizeLi(param){
		if(!param.hasClass('country')){
			ADAG(this).find('.holder .bit-input :not(:last)').remove();
		} else {
			ADAG(this).find('.holder:first').find('.bit-input :not(:last)').remove();
		}
	}

	function removeTyper(){
	/*	ADAG('#opts tr').each(function(){
			if(ADAG(this).hasClass('city') && (ADAG(this).find('.selected').length != 0)) {
				ADAG(this).find('.maininput').hide();
			}
		});
		*/
	}

	function removeTyperRegion(){
	}

	function sanitizeAndSubmit(pressbutton){
		ADAG('.autocomplets .selected').each(function(){
			ADAG('<input type="hidden" name="'+ADAG(this).parents('.autocomplets').attr('name')+'" value="'+ADAG(this).val()+'" />').insertAfter(ADAG(this).parents('.autocomplets'));
		 });
		ADAG('.upd8regs .selected').each(function(){
			//alert(ADAG(this).val());
			ADAG('<input type="hidden" name="'+ADAG(this).parents('.upd8regs').attr('name')+'" value="'+ADAG(this).val()+'" />').insertAfter(ADAG(this).parents('.upd8regs'));
		 });
		ADAG('.autocomplets').remove();
		ADAG('.upd8regs').remove();
		submitform(pressbutton);
	}

	function fpopulate(){
		ADAG('#tbdy').text('');
		var value = ADAG('#populate_geo').val();
		if(value == 0){
			ADAG('#limitation option').removeProp('disabled');
			ADAG('#geo_container').hide();
		} else {
			ADAG.ajax({
					cache: false,
					type: "GET",
					url: "index.php?option=com_adagency&controller=adagencyAds&task=getChannel&bid=" + value + "&format=raw",
					dataType: "script"
			});
		}
	}

	function fpopulate2(){
		ADAG('#existing_container').text('');
		var value = ADAG('#limitation_existing').val();

		var url_address = "";

		if(typeof(document.adminForm.getElementById("administrator")) != "undefined"){
			url_address  = "<?php echo JUri::root()."index.php?option=com_adagency&controller=adagencyAds&task=getChannelInfo&cid="; ?>";
			url_address += value+"&format=raw";
		}
		else{
			url_address = "index.php?option=com_adagency&controller=adagencyAds&task=getChannelInfo&cid=" + value + "&format=raw";
		}

		if(value != 0){
			ADAG.ajax({
				cache: false,
				type: "GET",
				url: url_address,
				dataType: "html",
				success: function(data){
					ADAG('#existing_container').html("<table style='margin-top: 15px;' cellpadding='2' cellspacing='2'><tr><td valign='top' align='left' style='padding-top: 5px;'><strong><?php echo JText::_('ADAG_CHAN_DETAILS');?>:</strong></td><td valign='top' align='left'>"+data+"</td></tr>");
					ADAG('#geo_type2').prop('checked','checked');
				}
			});
		}
	}
</script>
