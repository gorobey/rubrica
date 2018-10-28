<?php
$week = date("W");
/* for($i=0; $i<7; $i++){
    echo date('d/n/Y', strtotime('01/01 +' . ($week - 1) . ' weeks first day +' . ($i - 1) . ' day')) . '<br />';
} */
$i = 5;
echo date('d/n/Y', strtotime('01/01 +' . ($week - 1) . ' weeks first day +' . ($i - 2) . ' day')) . '<br />';

date_default_timezone_set("America/Guayaquil");

$fecha1 = strtotime("2018-09-03");
$fecha2 = strtotime(date("Y-m-d"));
//$fecha2 = strtotime("2019-07-5");
$cont_dias = 0; 
$feriados = array('2018-10-08','2018-11-01','2018-11-02','2018-12-06','2018-12-24',
                '2018-12-25','2018-12-26','2018-12-27','2018-12-28','2018-12-31',
                '2019-01-01','2019-02-18','2019-02-19','2019-02-20','2019-02-21',
                '2019-02-22','2019-04-19','2019-05-03','2019-05-24');
for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
    if(date('w',$fecha1)!=0 && date('w',$fecha1)!=6 && !in_array(date('Y-m-d',$fecha1),$feriados)){
        $cont_dias++; 
    }
}

echo "<br><br>$cont_dias<br>";

/* function truncar($numero, $digitos)
{
    $truncar = pow(10,$digitos);
    return intval($numero * $truncar) / $truncar;
}

echo truncar(9.05555,2); */