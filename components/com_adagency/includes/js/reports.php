<script type="text/javascript" language="javascript">
function changetype() {
	var form=document.adminForm;
	if (form['type'].value=="Click Detail") document.getElementById("breackdown").style.visibility="hidden";
	else document.getElementById("breackdown").style.visibility="visible";
	return;
}

Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
		// do field validation
	if ((pressbutton == 'creat')) {
/*	message1='<?php echo JText::_('REPDATEPAST');?>';
	message2='<?php echo JText::_('REPDATEBEFORE');?>';
	startyear=form['start_year'].value;
	startmonth=form['start_month'].value;
	startday=form['start_day'].value;
	endyear=form['stop_year'].value;
	endmonth=form['stop_month'].value;
	endday=form['stop_day'].value;
	day=<?php echo date('d')?>;
	month=<?php echo date('m')?>;
	year=<?php echo date('Y')?>;
		if (startyear > year) { alert(message1); return;}
		else if ((startyear == year) && (startmonth > month)) { alert(message1); return;}
		else if ((startyear == year) && (startmonth == month) && (startday > day)) { alert(message1); return;}
		else if (startyear > endyear) { alert(message2); return;}
		else if ((startyear == endyear)&&(parseInt(startmonth) > parseInt(endmonth)))
		{
			alert(message2);
			return;
		}
		else if ((startyear == endyear) && (startmonth == endmonth) && (startday > endday)) { alert(message2); return;}
	else */
		if((document.getElementById('start_date').value != document.getElementById('start_date2').value) || (document.getElementById('end_date').value != document.getElementById('end_date2').value)) {
			document.getElementById('adag_datepicker').value = 1;
		}
		submitform( pressbutton );
	}
}
function adagsetdate(x) {
	if(document.getElementById('tfa_adag').value>0){
		var tfa = document.getElementById('tfa_adag').value;
		var current = new Date();
		var past = new Date();
		var endDate = new Array();
		var startDate = new Array();

		if(x == 3) { current.setDate(current.getDate()-1);}

		endDate['month'] = current.getMonth() + 1;
		endDate['day'] = current.getUTCDate();
		endDate['year'] = current.getUTCFullYear();
		if (endDate['day']<10) { endDate['day'] = "0" + endDate['day']; }
		if (endDate['month']<10) { endDate['month'] = "0" + endDate['month']; }

		if(x == 3) { past.setDate(past.getDate()-1); }
		else if(x == 4) { past.setDate(past.getDate()-7); }
		else if(x == 5) { past.setMonth(past.getMonth()-1); }
		else if(x == 6) { past.setFullYear(past.getUTCFullYear()-1); }
		else if(x == 7) { past.setFullYear(past.getUTCFullYear()-10); }

		startDate['month'] = past.getMonth() + 1;
		startDate['day'] = past.getUTCDate();
		startDate['year'] = past.getUTCFullYear();
		if (startDate['day']<10) { startDate['day'] = "0" + startDate['day']; }
		if (startDate['month']<10) { startDate['month'] = "0" + startDate['month']; }


		if((tfa == 1)||(tfa == 7)) {
			var fullEndDate = endDate['day'] + "-" + endDate['month'] + "-" + endDate['year'];
			var fullStartDate = startDate['day'] + "-" + startDate['month'] + "-" + startDate['year'];
		} else if((tfa == 2)||(tfa == 8)) {
			var fullEndDate = endDate['day'] + "/" + endDate['month'] + "/" + endDate['year'];
			var fullStartDate = startDate['day'] + "/" + startDate['month'] + "/" + startDate['year'];
		} else if((tfa == 3)||(tfa == 9)) {
			var fullEndDate = endDate['month'] + "-" + endDate['day'] + "-" + endDate['year'];
			var fullStartDate = startDate['month'] + "-" + startDate['day'] + "-" + startDate['year'];
		} else if((tfa == 4)||(tfa == 10)) {
			var fullEndDate = endDate['month'] + "/" + endDate['day'] + "/" + endDate['year'];
			var fullStartDate = startDate['month'] + "/" + startDate['day'] + "/" + startDate['year'];
		} else if((tfa == 5)||(tfa == 11)) {
			var fullEndDate = endDate['year'] + "-" + endDate['month'] + "-" + endDate['day'];
			var fullStartDate = startDate['year'] + "-" + startDate['month'] + "-" + startDate['day'];
		} else if((tfa == 6)||(tfa == 12)) {
			var fullEndDate = endDate['year'] + "/" + endDate['month'] + "/" + endDate['day'];
			var fullStartDate = startDate['year'] + "/" + startDate['month'] + "/" + startDate['day'];
		}
	}
	//var fullDate = startDate['month'] + "/" + startDate['day'] + "/" + startDate['year'];
	document.getElementById('end_date').value = fullEndDate;
	document.getElementById('end_date2').value = fullEndDate;
	document.getElementById('start_date').value = fullStartDate;
	document.getElementById('start_date2').value = fullStartDate;
}
	<?php if(!isset($_POST['tfa'])) { ?>
		window.onload = function(){
			adagsetdate(5);
		}
	<?php
	} else {
	?>
	window.onload = function(){
		document.getElementById('end_date2').value = document.getElementById('end_date').value;
		document.getElementById('start_date2').value = document.getElementById('start_date').value;
	}
	<?php }	?>
</script>
