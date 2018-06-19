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
    
   
    
}


?>