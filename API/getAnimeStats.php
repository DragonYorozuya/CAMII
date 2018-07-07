<?php
// if (isset($_GET['anime'])) {
    require_once '../lib/CAMIIanime.php';
    $camii = new CAMIIanime(); 
    if ($camii->getStatAnimeLista()) {
        header("Content-type:application/json");
        echo json_encode($camii->dataAnime);
        return true;
    }
// }
?>