<?php

use Ifsnop\Mysqldump\Mysqldump;

require_once dirname(__FILE__) .'/Mysqldump.php';
require_once dirname(__FILE__).'/CAMIIemail.php';

$dumpSettingsDefault = array(
    'include-tables' => array('MINHALISTA','ANIMEASSISTIDO'),
    'compress' => Mysqldump::GZIP
);
$arq = 'CAMII.sql.gz';

$d = new Mysqldump('mysql:host=sql205.ezyro.com;dbname=ezyro_22162217_CAMII', 'ezyro_22162217', '6kc860mvh41',$dumpSettingsDefault);
//$d = new Mysqldump('mysql:host=localhost;dbname=CAMII', 'root', '',$dumpSettingsDefault);
$d->start($arq );

$camii = new CAMIIemail();

$camii->destinatario("dragon", "dragond103@gmail.com","BACKUP");
$camii->textoDoEmail("Enviando o Backup do dia ".date("d-m-Y"));
$camii->geraAnexo(dirname(__FILE__).'/'.$arq, $arq,'application/x-gzip');

var_dump($camii->enviarEmail());
