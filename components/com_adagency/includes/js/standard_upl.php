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

function UploadImage(){
	if(document.getElementById("imgdiv")){
    	document.getElementById("imgdiv").style.display="none";
	}
	document.getElementById("imgwait").style.display="block";
    
    var fileControl = document.adminForm.image_file;
    var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
    if (thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG")
        { alert('<?php echo 'The image must be gif, png, jpg, jpeg!';?>');
          return false;
        }
    if (fileControl.value) {
        //alert('here');
        document.adminForm.task.value = 'upload';
        return true;
        //submitbutton('upload');
    }
    return false;
}