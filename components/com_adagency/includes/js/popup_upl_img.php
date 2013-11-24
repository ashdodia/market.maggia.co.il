<script  language="javascript" type="text/javascript">
function UploadImage() {
    var fileControl = document.adminForm.image_file;
    var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
            if (thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG")
                { alert('<?php echo "Only jpg, gif and png allowed!";?>');
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
</script>
