<?php

if (isset($_GET['search']) && trim($_GET['search'])!=""){

    require_once 'lib/CAMIIanime.php';
    
    $anime = new CAMIIanime();
    
    header("Content-type:application/json");
    echo $anime->searchAnime($_GET['search'],1);
}
?>