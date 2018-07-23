<?php
$stop_date = new DateTime('2018-01-07');
$c = 0;
$c1 = 0;
$sql = "INSERT INTO ANIMEASSISTIDO(AASUSER, AASANIME, ASSEP, ASSDATA) VALUES ";
for($i=820;$i<=843;$i++){
    
    if($c==1){
    //echo 'date before day adding: ' . $stop_date->format('Y-m-d');
        $stop_date->modify('+8 day');
       // echo 'date after adding 1 day: ' . $stop_date->format('Y-m-d');
        //$c=0;
    }else if($c1 ==2){
        //$stop_date->modify('+7 day');
        // echo 'date after adding 1 day: ' . $stop_date->format('Y-m-d');
      //  $c1=0;
    }else{
        $c++;
        //$c1++;
    }
   
    $sql.= "(1,21,$i,'".$stop_date->format('Y-m-d')."'), ";
    echo $c1++;
}
echo $sql;


