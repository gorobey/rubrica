<?php
	include_once("scripts/clases/class.mysql.php");
	include_once("scripts/clases/class.funciones.php");
	$funciones = new funciones();
	echo "Test de la funci&oacute;n calcular_promedio_aporte:<br>";
	echo $funciones->calcular_promedio_aporte(1, 182, 1, 1)."<br>";
	echo "Test de la funci&oacute;n calcular_promedio_quimestre:<br>";
	echo $funciones->calcular_promedio_quimestre(1, 182, 1, 1)."<br>";
	echo "Test de la funci&oacute;n calcular_promedio_anual:<br>";
	echo $funciones->calcular_promedio_anual(1, 182, 1, 1)."<br>";
?>