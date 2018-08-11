<?php
$conexion=mysql_connect("localhost","colegion_1","AQSWDE123") 
  or die("Problemas en la conexion");
mysql_select_db("colegion_1",$conexion) 
  or die("Problemas en la seleccion de la base de datos");
$registros=mysql_query("select id_escala_calificaciones,ec_nota_minima,ec_nota_maxima,ec_equivalencia from sw_escala_calificaciones where id_periodo_lectivo = 2",$conexion) 
  or die("Problemas en el select".mysql_error());
while ($reg=mysql_fetch_array($registros))
{
  $vec[]=$reg;
}

require('funciones/JSON.php');
$json=new Services_JSON();
$cad=$json->encode($vec);
echo $cad;
?>
