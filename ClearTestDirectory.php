<?php
    $folder_path = "Dataset/test_images";
    $files = glob($folder_path.'/*'); 
    foreach($files as $file) {
        if(is_file($file)) 
            unlink($file); 
    }
    header( "Refresh:1; url=upload.html");
?>