<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horas_clase.php");
	session_start();
	$hora_clase = new horas_clase();
	$hora_clase->id_dia_semana = $_POST["id_dia_semana"];
	echo $hora_clase->listar_horas_clase();
?>
