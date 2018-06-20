<?php
require_once './lib/BancoDeDados.php';
class CAMIInewAnime{
    /**
     * 
     * @var BancoDeDados
     */
    private $bd;
    
    /**
     * 
     * @param getAnime() $an
     */
    public function saveAnime($an){
        $this->bd = new BancoDeDados();
        $sql = "INSERT INTO ANIME(ANICOD, ANINOME, ANIEPI, ANITIPO, ANIIMG, ANISTATUS, ANIINICIO, ANIFINAL, ANIADAP, ANICI, ANIESTUDIO) VALUES";
        $sql .= "(?,?,?,?,?,?,?,?,?,?,?)";
       
        //$sql .= "($an[cod],'$an[nome]',$an[episodio],$an[tipo],'$an[img]',$an[status],'$an[inicio]','$an[fim]',$an[adaptacao],$an[ce],$an[estudio])";
        
        if($this->bd->query($sql,[$an['cod'],$an['nome'],$an['episodio'],$an['tipo'],$an['img'],$an['status'],$an['inicio'],$an['fim'],$an['adaptacao'],$an['ce'],$an['estudio']])){
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param boolean $save  True: save BD | False: not save BD
     * @return number|string|mixed $save=true retorna resultado BD| $save=false retorna array anime | 2: anime ja cadastrado
     */
    public function getAnime($cod, $save=false){
        if($this->animeExiste($cod)){
        
            $ch = @curl_init();
            //@curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/anime/34332');
            @curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/anime/'.$cod);
            //@curl_setopt ( $ch, CURLOPT_HEADER, TRUE );
            //@curl_setopt ( $ch, CURLOPT_NOBODY, TRUE );
            @curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
            @curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt  ($ch, CURLOPT_FILETIME, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $html = curl_exec($ch);// acessar URL
            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);// Pegar o c�digo de resposta
            curl_close($ch);
            
            
            
            if ($response_code == '404') {
                echo 'Página não existente';
            } else {
                $pos = strpos ( $html, '<h1 class="h1"><span itemprop="name">' );
                if ($pos === false) {
                    
                }else{
                    $htmlReduzido = substr($html,$pos);
                    
                    $pos2 = strpos ( $htmlReduzido, 'More reviews</a></span>Reviews</h2>' );
                    
                    $htmlReduzido = substr($htmlReduzido,0,$pos2);
                    
                    $an = $this->getDadosanime($htmlReduzido,$cod);
                    
                    if($save != false){
                        if($this->saveAnime($an)){
                            return true;
                        };
                       return false;
                    }
                    return $an;
                }
            }
        }
        return 2; // erro se anime ja existir
    }
    
    public function animeExiste($cod) {
        $this->bd = new BancoDeDados();
        
        $sql = "SELECT * FROM ANIME WHERE ANICOD =?";
        
        if ($this->bd->querySelect($sql,[$cod]) && $this->bd->recuperaQtdeDeLinhaRetornadas() == 0) {
            return TRUE ;
        }
        return false;
    }
    
    
    public function getDadosanime($html,$cod){
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
            
             if (! copy ( $img[0], './img/anime/'.$img[1].".jpg")) {
                echo "erro na porra da imagem $img[1]";
             // return false;
             }
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
        
        //##### adapta��o
        if(preg_match ( '/Source:<\/span>[\s][ ]*([a-z-A-Z-0-9]*)/', $html, $adaptacao)){
            //var_dump($adaptacao);
            $adapArray = array('Manga'=>1,'Light'=>2,'Original'=>3,'Visual'=>4,
                'Card'=>5,'Novel'=>6,'Web'=>7,'Other'=>8,'4-koma'=>9,'Digital'=>10,
                'Game'=>11,'Unknown'=>12,'Picture'=>13,'Book'=>14,'Radio'=>15,'Music'=>16);
            
            $anime["adaptacao"] = $adapArray[$adaptacao[1]];
        }
        
        //##### Classifica��o etaria
        if(preg_match('/Rating:<\/span>[ |\s]*([a-z-A-Z+1-9]*)/',$html, $CE)){
            //var_dump($CE);
            //1:PG-13 2: none  3: R - 17+ (violence & profanity) ex18+  4:R+  5:Rx hentai 6: PG - Children 7: G All Ages
            $ceArray = array('PG-13'=>1,'None'=>2,'R+'=>4,'Rx'=>5,'R'=>3,'PG'=>6,'G'=>7);
            
            $anime["ce"] = $ceArray[$CE[1]];
        }
        
        //##### estudio
        //if (preg_match ( '/Studios:<\/span>([a-zA-Z0-9]|[\s])*[a-zA-z0-9:-@ -�-]*<\/div>/', $html, $estudio )) {
        if (preg_match ( '/Studios:<\/span>[\s][ ]*<a href="\/anime\/producer\/([0-9]*)/', $html, $estudio )) {
            //var_dump($estudio);
            
            $anime["estudio"] =$estudio[1];
        }else{
            $anime["estudio"] = 'NULL';
        }
        //##### estudio FIM
        
        
        //##### exibi��o
        if(preg_match('/Aired:<\/span>[\s][ ]*([a-z-A-Z-0-9- -,-\?]*)/', $html, $exb)){
            //var_dump($exb);
            if($exb[1] == "Not available"){
                $anime["inicio"] = 'null';
            }
            
            $exb[1]= str_replace('Jan', '1', $exb[1]);
            $exb[1]= str_replace('Feb', '2', $exb[1]);
            $exb[1]= str_replace('Mar', '3', $exb[1]);
            $exb[1]= str_replace('Apr', '4', $exb[1]);
            $exb[1]= str_replace('May', '5', $exb[1]);
            $exb[1]= str_replace('Jun', '6', $exb[1]);
            $exb[1]= str_replace('Jul', '7', $exb[1]);
            $exb[1]= str_replace('Aug', '8', $exb[1]);
            $exb[1]= str_replace('Sep', '9', $exb[1]);
            $exb[1]= str_replace('Oct', '10', $exb[1]);
            $exb[1]= str_replace('Nov', '11', $exb[1]);
            $exb[1]= str_replace('Dec', '12', $exb[1]);
            $exb[1]= str_replace(',', '', $exb[1]);
            
            $data = explode ('to',$exb[1]);
            
            if(isset($data[0])){
                if (trim ( $data[0] ) == '?'){
                    $anime["inicio"] = 'null';
                }else{
                    $iniExb = explode ( ' ', trim($data[0]));
                    if (isset($iniExb[2]) && isset($iniExb[0]) && isset($iniExb[1])){
                        $anime["inicio"] = $iniExb[2].'-'.$iniExb[0].'-'.$iniExb[1];
                    }
                }
            }else{
                $anime["inicio"] = 'null';
            }
            if(isset($data[1])){
                if(trim($data[1]) == '?'){
                    $anime["fim"] = 'null';
                }else{
                    $fimExb = explode ( ' ', trim ( $data [1] ) );
                    if (isset ( $fimExb [2] ) && isset ( $fimExb [0] ) && isset ( $fimExb [1] )) {
                        $anime["fim"] = $fimExb[2].'-'.$fimExb[0].'-'.$fimExb[1];
                    }
                }
            }else{
                $anime["fim"] = 'null';
            }
        }
        //var_dump($anime);
        return $anime;
    }
    
    
    
}
?>