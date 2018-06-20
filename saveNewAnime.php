<?php
if (isset($_GET['cod'])) {
    require_once './lib/CAMIInewAnime.php';
    $CAMII = new CAMIInewAnime();
    var_dump( $CAMII->getAnime($_GET['cod'],true) );
    return;
}
//$CAMII->saveAnime( $CAMII->getAnime() );
?>
