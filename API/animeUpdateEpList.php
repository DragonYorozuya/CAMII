<?php
if (isset($_GET['anime']) && isset($_GET['ep']) && isset($_GET['d'])) {
    require_once '../lib/CAMIIanime.php';
    $camii = new CAMIIanime();
    
    if($camii->epiUpdateList($_GET['anime'],$_GET['ep'], $_GET['d'])){
        header("Content-type:application/json");
        echo '{"sit":1}';
        return;
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
    return;
}

?>