<?php
if (isset($_GET['cod'])) {
    require_once '../lib/CAMIInewAnime.php';
    $CAMII = new CAMIInewAnime();
    
    if ($CAMII->animeExiste($_GET['cod'])) {
        if ($CAMII->getAnime($_GET['cod'],true)) {
            header("Content-type:application/json");
            echo '{"sit":1}';
            return;
        }   
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
    return;
}
//$CAMII->saveAnime( $CAMII->getAnime() );
?>
