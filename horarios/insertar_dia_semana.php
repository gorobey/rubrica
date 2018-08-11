<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	session_start();
	$dia_semana = new dias_semana();
	$dia_semana->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$dia_semana->ds_nombre = $_POST["ds_nombre"];
	$dia_semana->ds_ordinal = $_POST["ds_ordinal"];
	echo $dia_semana->insertarDiaSemana();
?>
