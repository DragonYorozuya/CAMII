<?php
require_once dirname(__DIR__).'/lib/BancoDeDados.php';
/**
 * 
 * @author Gustavo Lucena
 * @version 1.0
 */
class CAMIIanime{
   private $bd;
   
   public function myList($json=false){
       $this->bd = new BancoDeDados();
       
       $sql = "SELECT * FROM ANIME";
       
       if($this->bd->querySelect($sql)){
       
           if($json == true){
                return json_encode($this->bd->ResultadosASSOCAll(),true);
           }
           return $this->bd->ResultadosASSOCAll();
       }
   }
   
   public function searchAnime($search,$json=false){
       $this->bd = new BancoDeDados();
       
       $sql = "SELECT * FROM ANIME WHERE ANINOME LIKE ?";
       
       if($this->bd->querySelect($sql,['%'.$search.'%'])){
       
           if($json == true){
               return json_encode($this->bd->ResultadosASSOCAll(),true);
           }
           return $this->bd->ResultadosASSOCAll();
       }
   }
   
   public function addAnimeLista(){
       $this->bd = new BancoDeDados();
       
       $sql = "INSERT INTO MINHALISTA(MINCLIENTE, MINANIME, MINEP, MINSITUACAO) VALUES (1,?,?,?)";
       
       if($this->bd->query($sql,[1,0,1])){
           return true;
       }
   }
    
   
    
}


?>