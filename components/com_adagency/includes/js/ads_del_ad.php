<script type="text/javascript" language="JavaScript">
		function Change(i) {
			if (confirm('<?php echo JText::_("ADAG_SURE_DELETE_AD");?>')){
				document.adminForm.sid.value = i;
				document.adminForm.task.value = "remove";
				document.adminForm.submit();
				return true; 
			}
			else { 
				return;
			}
		}	
</script>