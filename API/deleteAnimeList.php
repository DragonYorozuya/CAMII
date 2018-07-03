<?php
if (isset($_GET['anime'])) {
    require_once '../lib/CAMIIanime.php';
    $camii = new CAMIIanime();
    
    if ($camii->deletAnimeList(225)) {
        header("Content-type:application/json");
        echo '{"sit":1}';
        return;
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
    return;
}
