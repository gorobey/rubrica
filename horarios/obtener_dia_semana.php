<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dias_semana = new dias_semana();
	$dias_semana->code = $_POST["id_dia_semana"];
	echo $dias_semana->obtenerDiaSemana();
?>
