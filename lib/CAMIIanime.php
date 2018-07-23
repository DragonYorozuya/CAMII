<?php
require_once dirname(__DIR__).'/lib/BancoDeDados.php';
/**
 * 
 * @author Gustavo Lucena
 * @version 1.0
 */
class CAMIIanime{
   private $bd;
   public $dataAnime;
   
   public function myList($user, $json=false){
       $this->bd = new BancoDeDados();
      // $sql = "SELECT * FROM MINHALISTA LEFT JOIN ANIME ON MINANIME=ANICOD WHERE MINCLIENTE=?";
//       $sql = "SELECT *,COUNT(ASSEP) AS EP FROM MINHALISTA LEFT JOIN ANIME ON MINANIME=ANICOD LEFT JOIN ANIMEASSISTIDO ON MINANIME=AASANIME AND MINCLIENTE=AASUSER WHERE MINCLIENTE=? GROUP BY MINANIME";
       $sql = "SELECT MINCLIENTE,ANICOD,MINSITUACAO,ANINOME,ANIEPI,ANITIPO,ANIIMG,ANISTATUS,ANIINICIO,ANIFINAL,ANIADAP,ANICI,ANIESTUDIO,COUNT(ASSEP) AS EP FROM MINHALISTA LEFT JOIN ANIME ON MINANIME=ANICOD LEFT JOIN ANIMEASSISTIDO ON MINANIME=AASANIME AND MINCLIENTE=AASUSER WHERE MINCLIENTE=? GROUP BY MINANIME ORDER BY ANINOME ASC";
       if($this->bd->querySelect($sql,[$user])){
           if($json == true){
                return json_encode($this->bd->ResultadosASSOCAll(),true);
           }
           return $this->bd->ResultadosASSOCAll();
       }
   }
   
   public function searchAnime($search,$json=false){
       $this->bd = new BancoDeDados();
       
       $sql = "SELECT * FROM ANIME LEFT JOIN MINHALISTA ON ANICOD=MINANIME WHERE ANINOME LIKE ?";
       
       if($this->bd->querySelect($sql,['%'.$search.'%'])){
       
           if($json == true){
               return json_encode($this->bd->ResultadosASSOCAll(),true);
           }
           return $this->bd->ResultadosASSOCAll();
       }
   }
   
   public function addAnimeLista($anime){
       $this->bd = new BancoDeDados();
       
       $sql = "INSERT INTO MINHALISTA(MINCLIENTE,MINANIME,MINSITUACAO,MININICIO,MINFINAL) VALUES (1,?,?,NOW(),?)";
       
       if($this->bd->query($sql,[$anime,1,'NULL'])){
           return true;
       }
       return false;
   }
   
   public function add1ep($anime,$ep) {
       $this->bd = new BancoDeDados();
       if ($this->epCadastradoVerifica($anime, $ep)) {
           $sql = "INSERT INTO ANIMEASSISTIDO(AASUSER, AASANIME, ASSEP, ASSDATA) VALUES (?,?,?,NOW())";
           if ($this->bd->query($sql,[1,$anime,$ep])) {
               return true;
           }
           return 2;
       }
       return false;    
   }
   
   public function add1epStatusCompleto($anime,$ep) {
       $this->bd = new BancoDeDados();
       if ($this->epCadastradoVerifica($anime, $ep)) {
           $sql = "INSERT INTO ANIMEASSISTIDO(AASUSER, AASANIME, ASSEP, ASSDATA) VALUES (?,?,?,NOW())";
           if ($this->bd->query($sql,[1,$anime,$ep])) {
               $sql1 = "UPDATE MINHALISTA SET MINSITUACAO=?,MINFINAL=NOW() WHERE MINCLIENTE=? AND MINANIME=?";
               if ($this->bd->query($sql1,[2,1,$anime])) {
                   return true;
               }
           }
           return 2;
       }
       return false;
   }
   
   public function animeEpAssistido($anime) {
       $this->bd = new BancoDeDados();
       $sql = "SELECT * FROM ANIMEASSISTIDO WHERE  AASUSER=? AND AASANIME=?";
       if ($this->bd->querySelect($sql,[1,$anime])) {
           return $this->bd->ResultadosASSOCAll();
       }
       return FALSE;
   }
   
   public function get1AnimeMyList($anime){
       $this->bd = new BancoDeDados();
      // $sql = "SELECT * FROM MINHALISTA LEFT JOIN ANIME ON ANICOD=MINANIME WHERE MINCLIENTE=1 AND MINANIME=?";
       $sql = "SELECT *,COUNT(ASSEP) AS EP FROM MINHALISTA LEFT JOIN ANIME ON ANICOD=MINANIME LEFT JOIN ANIMEASSISTIDO ON MINCLIENTE=AASUSER AND ANICOD=AASANIME WHERE MINCLIENTE=1 AND MINANIME=? GROUP BY MINANIME";
       
       if ($this->bd->querySelect($sql,[$anime])) {
           $this->dataAnime = $this->bd->ResultadosASSOC();
           return true;
       }
       return false;
   }
   
   public function getEpAnimeList($anime){
       $this->bd = new BancoDeDados();
       $sql = "SELECT ASSEP AS EP,ASSDATA AS DATA FROM ANIMEASSISTIDO WHERE AASUSER=? AND AASANIME=?";
       if ($this->bd->querySelect($sql,[1,$anime])) {
           $this->dataAnime = $this->bd->ResultadosASSOCAll();
           return true;
       }
       return false;
   }
   
   public function getStatAnimeLista(){
       $this->bd = new BancoDeDados();
       //$sql = "SELECT * FROM ANIMEASSISTIDO WHERE ASSDATA BETWEEN '2017-01-01' AND '2017-12-31' AND AASUSER=1";
       $sql = "SELECT COUNT(*) AS ANIME,YEAR(ASSDATA) as DATA FROM ANIMEASSISTIDO WHERE AASUSER=? GROUP BY DATA ORDER BY DATA DESC";
       //DATE_FORMAT( ASSDATA, '%e %b %Y')
       if ($this->bd->querySelect($sql,[1])) {
           $this->dataAnime['ANO'] =  $this->bd->ResultadosASSOCAll();
           
           $sql1 = "SELECT COUNT(AASUSER) AS ANIME, extract(year from ASSDATA) AS ANO, extract(month from ASSDATA) as MES FROM ANIMEASSISTIDO WHERE AASUSER=? GROUP BY ANO,MES ORDER BY ANO,MES";
           if ($this->bd->querySelect($sql1,[1])) {
               $this->dataAnime['MES'] =  $this->bd->ResultadosASSOCAll();
               
               $sql2 = "SELECT COUNT(*) AS ANIMECOMP,YEAR(MINFINAL) AS ANOCOMP FROM MINHALISTA WHERE MINCLIENTE=? AND MINSITUACAO=2 GROUP BY ANOCOMP";
               if ($this->bd->querySelect($sql2,[1])) {
                   $this->dataAnime['COMPLETO'] = $this->bd->ResultadosASSOCAll();
               }
           }
           $this->dataAnime['META']["DIA"] = date('z')+1;
           $this->dataAnime['META']["B"] = date('L');
           return TRUE;
       }
       return false; 
   }
   
   public function animeUpdateLista($sit,$dI,$dF,$anime){
       $this->bd = new BancoDeDados();
       $sql = "UPDATE MINHALISTA SET MINSITUACAO=?,MININICIO=?,MINFINAL=? WHERE MINCLIENTE=1 AND MINANIME=?";

       if ($this->bd->query($sql,[$sit,$dI,$dF,$anime])) {
           return TRUE;
       }
       return FALSE;
   }
   public function updateAnimeLsCompleto($sit,$dI,$dF,$anime){
       $this->bd = new BancoDeDados();
       $sql = "UPDATE MINHALISTA SET MINSITUACAO=?,MININICIO=?,MINFINAL=? WHERE MINCLIENTE=1 AND MINANIME=?";
       
       $sql1= "SELECT ASSEP,ANIEPI FROM ANIME LEFT JOIN ANIMEASSISTIDO ON ANICOD=AASANIME WHERE ANICOD= ? OR (AASUSER=? AND AASANIME=?)";
       
       if ($this->bd->query($sql1,[$anime,1,$anime])) {
           $eps = $this->bd->ResultadosASSOCAll();
           //var_dump($eps);
           $arr = "";
           for($i=1;$i<=$eps[0]['ANIEPI'];$i++)
               $arr[$i]= $i;
           foreach($eps as $ep )
               unset($arr[$ep['ASSEP']]);
           //var_dump($arr);
           $v = "'";
           if(trim($dF) == ""){
               $dF = "NOW()";
               $v = "";
           }    
           $sql3 = "INSERT INTO ANIMEASSISTIDO(AASUSER, AASANIME, ASSEP, ASSDATA) VALUES ";
           foreach ($arr as $ep){
               $sql3 .= "(1,".$anime.",".$ep.",".$v.$dF.$v."),";
           }
           //echo rtrim($sql3, ",");
           if ($this->bd->query(rtrim($sql3, ","))) {
               
           }
       }
       if ($this->bd->query($sql,[$sit,$dI,$dF,$anime])) {
           return TRUE;
       }
       return FALSE;
   }
   
   public function epiUpdateList($anime,$ep,$data){
       $this->bd = new BancoDeDados();
       $sql = "UPDATE ANIMEASSISTIDO SET ASSDATA=? WHERE AASUSER=1 AND AASANIME =? AND ASSEP=?";
       
       if ($this->bd->query($sql,[$data,$anime,$ep])) {
           return TRUE;
       }
       return FALSE;
   }
   
   // 
   
   public function deletAnimeList($anime) {
       $this->bd = new BancoDeDados();
       
       $sql = "DELETE FROM MINHALISTA WHERE MINCLIENTE = ? AND MINANIME = ?";
       if ($this->bd->query($sql,[1,$anime])) {
           $sql1 = "DELETE FROM ANIMEASSISTIDO WHERE AASUSER = 1 AND AASANIME = 225";
           if ($this->bd->query($sql1,[1,$anime])) {
               return true;
           }
       }
       return false;
   }
   
   /**
    * Verifica se um episodio ja foi cadastrado
    * @param int $anime
    * @param int $ep
    * @return boolean
    */
   private function epCadastradoVerifica($anime, $ep) {
       $this->bd = new BancoDeDados();
       $sql = "SELECT * FROM ANIMEASSISTIDO WHERE  AASUSER=? AND AASANIME=? AND ASSEP=?";
       if ($this->bd->querySelect($sql,[1,$anime,$ep]) && $this->bd->recuperaQtdeDeLinhaRetornadas() == 0) {
          return true;
       }
       return false;
   }
   
   public function animeMal($search) {
       $search = str_replace(" ", "%20", $search);
       $ch = @curl_init();
       //@curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/anime/34332');
       @curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/search/prefix.json?type=all&keyword='.$search.'&v=1');
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
           header("Content-type:application/json");
           echo $html;
       }
   }
    
   
    
}


?>