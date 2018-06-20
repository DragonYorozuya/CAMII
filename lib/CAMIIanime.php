<?php
require_once dirname(__DIR__).'/lib/BancoDeDados.php';
/**
 * 
 * @author Gustavo Lucena
 * @version 1.0
 */
class CAMIIanime{
   private $bd;
   
   public function myList($user, $json=false){
       $this->bd = new BancoDeDados();
       
//       $sql = "SELECT * FROM ANIME";
       $sql = "SELECT * FROM MINHALISTA LEFT JOIN ANIME ON MINANIME=ANICOD WHERE MINCLIENTE=?";
       
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
       
       $sql = "INSERT INTO MINHALISTA(MINCLIENTE, MINANIME, MINEP, MINSITUACAO) VALUES (1,?,?,?)";
       
       if($this->bd->query($sql,[$anime,0,1])){
           return true;
       }
       return false;
   }
   
   public function add1ep($anime) {
       $this->bd = new BancoDeDados();
       
       $sql = "UPDATE MINHALISTA SET MINEP=MINEP+1 WHERE MINCLIENTE = 1 AND MINANIME = ?";
       
       if ($this->bd->query($sql,[$anime])) {
           return true;
       }
       
   }
   
   public function animeMal($search) {
       $ch = @curl_init();
       //@curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/anime/34332');
       @curl_setopt($ch, CURLOPT_URL,'https://myanimelist.net/search/prefix.json?type=anime&keyword='.$search.'&v=1');
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