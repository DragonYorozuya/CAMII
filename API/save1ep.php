<?php
if (isset($_GET['anime'])) {
    require_once '../lib/CAMIIanime.php';
    
    $camii = new CAMIIanime();
    
    if ($camii->add1ep($_GET['anime'])){
        header("Content-type:application/json");
        echo '{"sit":1}';
        return;
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
    return;  
}


?>