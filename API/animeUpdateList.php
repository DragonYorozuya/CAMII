<?php
if (isset($_GET['anime']) && isset($_GET['sit']) && isset($_GET['dI']) && isset($_GET['dF'])) {
    require_once '../lib/CAMIIanime.php';
    $camii = new CAMIIanime();
    
    if($camii->animeUpdateLista($_GET['sit'], $_GET['dI'], $_GET['dF'],$_GET['anime'])){
        header("Content-type:application/json");
        echo '{"sit":1}';
        return;
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
    return;
}
?>