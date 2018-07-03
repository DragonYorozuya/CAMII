<?php
if (isset($_GET['anime']) && isset($_GET['ep'])) {
    require_once '../lib/CAMIIanime.php';
    
    $camii = new CAMIIanime();
    
   if ($camii->add1epStatusCompleto($_GET['anime'],$_GET['ep'])){
        header("Content-type:application/json");
        echo '{"sit":1}';
        return;
    }
    header("Content-type:application/json");
    echo '{"sit":0}';
    return;  
}

// SELECT *,COUNT(ASSEP) AS EP FROM `MINHALISTA` INNER JOIN ANIMEASSISTIDO ON MINANIME=AASANIME  AND MINCLIENTE=AASUSER GROUP BY MINANIME
?>