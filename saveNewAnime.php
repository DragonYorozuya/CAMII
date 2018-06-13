<?php
require_once './lib/CAMIInewAnime.php';



$CAMII = new CAMIInewAnime();
var_dump( $CAMII->getAnime(true) );

//$CAMII->saveAnime( $CAMII->getAnime() );

?>
