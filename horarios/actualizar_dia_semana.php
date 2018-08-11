<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$dia_semana->code = $_POST["id_dia_semana"];
	$dia_semana->ds_nombre = $_POST["ds_nombre"];
	$dia_semana->ds_ordinal = $_POST["ds_ordinal"];
	echo $dia_semana->actualizarDiaSemana();
?>
