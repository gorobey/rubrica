<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$dia_semana->id_hora_dia = $_POST["id_hora_dia"];
	echo $dia_semana->eliminarHoraDia();
?>
