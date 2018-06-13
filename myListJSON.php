<?php
require_once 'lib/CAMIIanime.php';

$anime = new CAMIIanime();

header("Content-type:application/json");
echo $anime->myList(1);
?>