<?php
if (isset($_GET['add']) && trim($_GET['add']) != ""){
    require_once 'lib/CAMIIanime.php';

    $CAMII = new CAMIIanime();
    
    if($CAMII->addAnimeLista($_GET['add'])){
        header("Content-type:application/json");
        echo '{"sit":1}';
        return;
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
}
?>