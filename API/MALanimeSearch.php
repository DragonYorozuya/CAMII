<?php
if (isset($_GET['search']) && trim($_GET['search']) != "") {

    require_once '../lib/CAMIIanime.php';
    
    $camii = new CAMIIanime();
    header("Content-type:application/json");
    echo $camii->animeMal(trim($_GET['search']));
    return ;
}
header("Content-type:application/json");

?>