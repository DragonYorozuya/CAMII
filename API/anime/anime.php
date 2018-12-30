<?php
if (isset($_GET['cod']) && trim($_GET['cod'])!="") { 
    require_once '../../lib/CAMIIanime.php';
    $camii = new CAMIIanime();
    
    if($camii->cAnime($_GET['cod'])){
        header("Content-type:application/json");
        echo json_encode($camii->cData,true) ;
        return;
    }
}
header("Content-type:application/json");
echo '{"sit":0}';
return;

?>