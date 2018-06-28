<?php
require_once '../lib/CAMIIanime.php';

$camii = new CAMIIanime();
$a = $camii->animeEpAssistido(2);

echo json_encode($a);

?>