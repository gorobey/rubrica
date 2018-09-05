<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horas_clase.php");
	$hora_clase = new horas_clase();
	$hora_clase->code = $_POST["id_hora_clase"];
	echo $hora_clase->obtenerHoraClase();
?>
