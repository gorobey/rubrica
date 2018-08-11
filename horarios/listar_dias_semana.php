<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	session_start();
	$dia_semana = new dias_semana();
	$dia_semana->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $dia_semana->listar_dias_semana();
?>
