<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$dia_semana->code = $_POST["id_dia_semana"];
	echo $dia_semana->eliminarDiaSemana();
?>
