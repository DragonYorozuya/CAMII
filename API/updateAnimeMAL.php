<?php
if (isset($_GET["n"])) {
    

    require_once "../lib/BancoDeDados.php";

//     $anime = 37210;
    $anime = $_GET["n"];
    //var_dump($arq[0]);
    
    if(is_numeric($anime)){
        $bd = new BancoDeDados;
        $sql = "SELECT * FROM ANIME WHERE ANICOD=? limit 1";
        if($bd->querySelect($sql,[$anime])){
            if(!$bd->recuperaQtdeDeLinhaRetornadas()){
                //#### RESETAR PARA 0
                echo "Não existe o Anime";
                return;
            }else{
                $resul = $bd->ResultadosASSOC();
//                 var_dump($resul);
                
                $AnMAL = acessaMAL($resul['ANICOD']);
//                 var_dump($AnMAL);
                
                if($AnMAL == false){
                    echo "Anime Não Exite No MAL";
                    return;
                }
                
              
                if(($sql = gerarUpdate($resul, $AnMAL)) != false){
//                     echo $resul['ANICOD'];
//                     var_dump($sql);
                    
                    if($bd->query($sql)){
//                         echo "SUCESSO Update";
                        header("Content-type:application/json");
                        echo '{"sit":1}';
                        return;
                        return true;
                    }
                    header("Content-type:application/json");
                    echo '{"sit":0}';
//                     return;
//                     echo "ERRO Update";
                    return false;;
                }
            }
        }
    }
}

function gerarUpdate($anBD, $AnMAL){
    //var_dump($anBD);
    //var_dump($AnMAL);
    if($anBD['ANICOD'] == $AnMAL['cod']){
        $set = 0;
        $nome = "";
        $ep = "";
        $tipo = "";
        $status = "";
        $inicio = "";
        $fim = "";
        $adaptacao = "";
        $ce = "";
        $estudio = "";
        $img = "";
        
        
        //NOME
        if(trim($anBD['ANINOME']) != trim($AnMAL['nome'])){
            if(strlen($AnMAL['nome']) > 0){
                $nome = "ANINOME='".$AnMAL['nome']."',";
                $set++;
            }
        }
        
        //EPISODIO
        if(trim($anBD['ANIEPI']) != trim($AnMAL['episodio'])){
            if(strlen($AnMAL['episodio']) > 0){
                $ep = "ANIEPI=".$AnMAL['episodio'].",";
            }else{
                $ep = "ANIEPI=NULL,";
            }
            $set++;
        }
        
        //TIPO
        if(trim($anBD['ANITIPO']) != trim($AnMAL['tipo'])){
            if(strlen($AnMAL['tipo']) > 0){
                $tipo = "ANITIPO=".$AnMAL['tipo'].",";
            }else{
                $tipo = "ANITIPO=NULL,";
            }
            $set++;
        }
        
        //STATUS
        if(trim($anBD['ANISTATUS']) != trim($AnMAL['status'])){
            if(strlen($AnMAL['status']) > 0){
                $status = "ANISTATUS=".$AnMAL['status'].",";
            }else{
                $status = "ANISTATUS=NULL,";
            }
            $set++;
        }
        
        //INICIO
        if(trim($anBD['ANIINICIO']) != trim($AnMAL['inicio'])){
            if(strlen($AnMAL['inicio']) > 0){
                $inicio = "ANIINICIO='".$AnMAL['inicio']."',";
            }else{
                $inicio = "ANIINICIO='0000-00-00',";
            }
            $set++;
        }
        
        //FIM
        if(trim($anBD['ANIFINAL']) != trim($AnMAL['fim'])){
            if(strlen($AnMAL['fim']) > 0){
                $fim = "ANIFINAL='".$AnMAL['fim']."',";
            }else{
                $fim = "ANIFINAL='0000-00-00',";
            }
            $set++;
        }
        
        //ADAPTAÇÃO
        if(trim($anBD['ANIADAP']) != trim($AnMAL['adaptacao'])){
            if(strlen($AnMAL['adaptacao']) > 0){
                $adaptacao = "ANIADAP=".$AnMAL['adaptacao'].",";
            }else{
                $adaptacao = "ANIADAP=NULL,";
            }
            $set++;
        }
        
        //CLASSIFICAÇÃO INDICATIVA
        if(trim($anBD['ANICI']) != trim($AnMAL['ce'])){
            if(strlen($AnMAL['ce']) > 0){
                $ce = "ANICI=".$AnMAL['ce'].",";
            }else{
                $ce = "ANICI=NULL,";
            }
            $set++;
        }
        
        //ESTUDIO
        if(trim($anBD['ANIESTUDIO']) != trim($AnMAL['estudio'])){
            if(strlen($AnMAL['ce']) > 0){
                $estudio = "ANIESTUDIO=".$AnMAL['estudio'].",";
            }else{
                $estudio = "ANIESTUDIO=NULL,";
            }
            $set++;
        }
        
        //img
        if(trim($anBD['ANIIMG']) != trim($AnMAL['img']) && strlen($AnMAL['img']) > 0){
            if (! copy ( $AnMAL['imgUrl'], '../img/anime/'.$AnMAL['img'].".jpg")) {
                echo "erro na porra da imagem $img[1]";
                // return false;
            }else{
//                 if (! copy ( './anime/imgUp/'.$AnMAL['img'].".jpg", '../../arq/img/anime/'.$AnMAL['img'].".jpg")) {
//                     echo "erro na COPIA para pasta da imagem ".$AnMAL['img'];
//                     // return false;
//                 }
                if(file_exists('../img/anime/'.$anBD['ANIIMG'].".jpg"))
//                     if ( copy( '../../arq/img/anime/'.$anBD['ANIIMG'].".jpg", './anime/imgVelha/'.$anBD['ANIIMG'].".jpg"))
                        unlink('../img/anime/'.$anBD['ANIIMG'].".jpg");
                        
                        $img = "ANIIMG='".$AnMAL['img']."',";
                        $set++;
            }
        }
        
        
        if($set != 0){
            return $sql = "UPDATE ANIME SET ". rtrim($nome.$ep.$tipo.$status.$inicio.$fim.$adaptacao.$ce.$estudio.$img  ,',')." WHERE ANICOD=".$anBD['ANICOD'];
        }
    }
    return false;
}

function acessaMAL($n){
    $ch = @curl_init();
    //@curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/anime/34332');
    @curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/anime/'.$n);
    //@curl_setopt ( $ch, CURLOPT_HEADER, TRUE );
    //@curl_setopt ( $ch, CURLOPT_NOBODY, TRUE );
    @curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
    @curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt  ($ch, CURLOPT_FILETIME, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);// acessar URL
    $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);// Pegar o código de resposta
    curl_close($ch);
    
    
    if ($response_code == '404') {
        echo 'Página não existente';
        return false;
    } else {
        //echo $html;
        $pos = strpos ( $html, '<h1 class="h1"><span itemprop="name">' );
        if ($pos === false) {
            echo "A string '' não foi encontrada na string ''";
            return $html;
        } else {
            $htmlReduzido = substr($html,$pos);
            $pos2 = strpos ( $htmlReduzido, 'More reviews</a></span>Reviews</h2>' );
            $htmlReduzido = substr($htmlReduzido,0,$pos2);
            
            $htmlReduzido = str_replace('http://cdn.myanimelist.net/images/spacer.gif" data-src="',"",$htmlReduzido);
            $htmlReduzido = str_replace('/images/spacer.gif" data-src="',"",$htmlReduzido);
            //echo $htmlReduzido;

            return dados($htmlReduzido,$n);;
//             return $htmlReduzido;
        }
    }
}

function dados($html,$cod){
    $anime["cod"] = $cod;
    //#### NOME
    if(preg_match ('/<h1 class=\"h1\"><span itemprop=\"name\">(.*)<\/span>/', $html, $tit)){
        //var_dump($tit[1]);
        
        $tit[1] = str_replace('"','\\"', $tit[1]);
        $tit[1] = str_replace("'","\\'", $tit[1]);
        
        $anime["nome"] = $tit[1];
        //$tit[1];
    }
    
    //#### IMAGE
    if(preg_match ('/http[ s]:\/\/myanimelist.cdn-dena.com\/images\/anime\/[0-9]*[\/]{1}([0-9]*)[\.][a-z-A-Z]{3,4}/', $html, $img)){
        //var_dump($img);
        $anime["img"] = $img[1];
        $anime["imgUrl"] = $img[0];
        //$img[0];
        //$img[1];
    }else{
        $anime["img"] = '';
    }
    
    //### TIPO
    if(preg_match ('/(Music)<\/div>|<a href="[http|https]*:\/\/myanimelist.net\/topanime.php\?type=([a-z-A-Z- -]*)/', $html, $tipo)){
        //var_dump($tipo);
        $tipoArray = array("tv"=>1,"ona"=>2,"ova"=>3,"movie"=>4,"special"=>5,"unknown"=>6,"Music"=>7,"music"=>7);
        if($tipo[1] == ""){
            if (array_key_exists($tipo[2], $tipoArray)){
                $anime["tipo"] = $tipoArray[$tipo[2]];
            }else{
                echo "eviar erro";
            }
        }else{
            if (array_key_exists($tipo[1], $tipoArray)){
                $anime["tipo"] = $tipoArray[$tipo[1]];
            }else{
                echo "eviar erro Music";
            }
        }
    }else{
        $anime["tipo"] = 'NULL';
    }
    
    //##### Episodio ####
    if(preg_match ('/Episodes:<\/span>[\s][ ]*([a-z-A-Z-0-9]*)/', $html, $ep)){
        //var_dump($ep);
        if($ep[1] != "" && $ep[1] != "Unknown")
            $anime["episodio"] = $ep[1];
            else
                $anime["episodio"] = 'NULL';
    }
    //##### Episodio FIM
    
    //##### Status ####
    if(preg_match ('/Status:<\/span>[\s][ ]*([a-z-A-Z-0-9]*)/', $html, $status)){
        //var_dump($status);
        // status. 1:Currently Airing 2:Finished Airing 3:Not yet aired
        $statusArray = array("Currently"=>1,"Finished"=>2,"Not"=>3);
        $anime["status"] = $statusArray[$status[1]];
    }
    //##### Status Fim
    
    //##### adaptação
    if(preg_match ( '/Source:<\/span>[\s][ ]*([a-z-A-Z-0-9]*)/', $html, $adaptacao)){
        //var_dump($adaptacao);
        $adapArray = array('Manga'=>1,'Light'=>2,'Original'=>3,'Visual'=>4,
            'Card'=>5,'Novel'=>6,'Web'=>7,'Other'=>8,'4-koma'=>9,'Digital'=>10,
            'Game'=>11,'Unknown'=>12,'Picture'=>13,'Book'=>14,'Radio'=>15,'Music'=>16);
        
        $anime["adaptacao"] = $adapArray[$adaptacao[1]];
    }
    
    //##### Classificação etaria
    if(preg_match('/Rating:<\/span>[ |\s]*([a-z-A-Z+1-9]*)/',$html, $CE)){
        //var_dump($CE);
        //1:PG-13 2: none  3: R - 17+ (violence & profanity) ex18+  4:R+  5:Rx hentai 6: PG - Children 7: G All Ages
        $ceArray = array('PG-13'=>1,'None'=>2,'R+'=>4,'Rx'=>5,'R'=>3,'PG'=>6,'G'=>7);
        
        $anime["ce"] = $ceArray[$CE[1]];
    }
    
    //##### estudio
    //if (preg_match ( '/Studios:<\/span>([a-zA-Z0-9]|[\s])*[a-zA-z0-9:-@ -º-]*<\/div>/', $html, $estudio )) {
    if (preg_match ( '/Studios:<\/span>[\s][ ]*<a href="\/anime\/producer\/([0-9]*)/', $html, $estudio )) {
        //var_dump($estudio);
        $anime["estudio"] =$estudio[1];
    }else{
        $anime["estudio"] = 'NULL';
    }
    //##### estudio FIM
    
    
    //##### exibição
    if(preg_match('/Aired:<\/span>[\s][ ]*([a-z-A-Z-0-9- -,-\?]*)/', $html, $exb)){
        //var_dump($exb);
        if($exb[1] == "Not available"){
            $anime["inicio"] = 'NULL';
        }
        
        $exb[1]= str_replace('Jan', '01', $exb[1]);
        $exb[1]= str_replace('Feb', '02', $exb[1]);
        $exb[1]= str_replace('Mar', '03', $exb[1]);
        $exb[1]= str_replace('Apr', '04', $exb[1]);
        $exb[1]= str_replace('May', '05', $exb[1]);
        $exb[1]= str_replace('Jun', '06', $exb[1]);
        $exb[1]= str_replace('Jul', '07', $exb[1]);
        $exb[1]= str_replace('Aug', '08', $exb[1]);
        $exb[1]= str_replace('Sep', '09', $exb[1]);
        $exb[1]= str_replace('Oct', '10', $exb[1]);
        $exb[1]= str_replace('Nov', '11', $exb[1]);
        $exb[1]= str_replace('Dec', '12', $exb[1]);
        $exb[1]= str_replace(',', '', $exb[1]);
        
        $data = explode ('to',$exb[1]);
        
        if(isset($data[0])){
            if (trim ( $data[0] ) == '?'){
                $anime["inicio"] = '0000-00-00';
            }else{
                $iniExb = explode ( ' ', trim($data[0]));
                if (isset($iniExb[2]) && isset($iniExb[0]) && isset($iniExb[1])){
                    $anime["inicio"] = $iniExb[2].'-'.$iniExb[0].'-'.$iniExb[1];
                }
            }
        }else{
            $anime["inicio"] = '0000-00-00';
        }
        if(isset($data[1])){
            if(trim($data[1]) == '?'){
                $anime["fim"] = '0000-00-00';
            }else{
                $fimExb = explode ( ' ', trim ( $data [1] ) );
                if (isset ( $fimExb [2] ) && isset ( $fimExb [0] ) && isset ( $fimExb [1] )) {
                    $anime["fim"] = $fimExb[2].'-'.$fimExb[0].'-'.$fimExb[1];
                }
            }
        }else{
            $anime["fim"] = '0000-00-00';
        }
    }
    //var_dump($anime);
    return $anime;
}
?>