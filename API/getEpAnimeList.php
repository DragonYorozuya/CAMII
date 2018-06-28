<?php
if (isset($_GET['anime'])) {
    require_once '../lib/CAMIIanime.php';
    $camii = new CAMIIanime();
    
    if ($camii->getEpAnimeList($_GET['anime'])) {
        header("Content-type:application/json");
        echo json_encode($camii->dataAnime);
        return true;
    }
}
?>