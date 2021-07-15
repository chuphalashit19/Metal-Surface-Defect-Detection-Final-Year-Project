<?php
    $folder_path = "Output";
    $files = glob($folder_path.'/*'); 
       
    foreach($files as $file) {
        if(is_file($file)) 
            unlink($file); 
    }
    set_time_limit(1000);
    $command = "python steel-defect-detect-final.py 2>&1";
    # $command = "python Calci.py 2>&1";
    $pid = popen( $command,"r");
    while( !feof( $pid ) )
    {
        echo fread($pid, 256);
        flush();
        ob_flush();
        usleep(100000);
    }
    pclose($pid);
?>
<html>
    <meta http-equiv="refresh" content="1; URL='http://localhost/Project/Output/'" />    
</html>