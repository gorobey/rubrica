<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horas_clase.php");
	$hora_clase = new horas_clase();
	$hora_clase->code = $_POST["id_hora_clase"];
	$hora_clase->id_dia_semana = $_POST["id_dia_semana"];
	$hora_clase->hc_nombre = $_POST["hc_nombre"];
	$hora_clase->hc_hora_inicio = $_POST["hc_hora_inicio"];
	$hora_clase->hc_hora_fin = $_POST["hc_hora_fin"];
	echo $hora_clase->actualizarHoraClase();
?>
