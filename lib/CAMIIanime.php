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
       $sql = "SELECT MINCLIENTE,ANICOD,MINSITUACAO,ANINOME,ANIEPI,ANITIPO,ANIIMG,ANISTATUS,ANIINICIO,ANIFINAL,ANIADAP,ANICI,ANIESTUDIO,COUNT(ASSEP) AS EP FROM MINHALISTA LEFT JOIN ANIME ON MINANIME=ANICOD LEFT JOIN ANIMEASSISTIDO ON MINANIME=AASANIME AND MINCLIENTE=AASUSER WHERE MINCLIENTE=? GROUP BY MINANIME";
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
   
   public function animeUpdateLista($sit,$dI,$dF,$anime){
       $this->bd = new BancoDeDados();
       $sql = "UPDATE MINHALISTA SET MINSITUACAO=?,MININICIO=?,MINFINAL=? WHERE MINCLIENTE=1 AND MINANIME=?";

       if ($this->bd->query($sql,[$sit,$dI,$dF,$anime])) {
           return true;
       }
       return FALSE;
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