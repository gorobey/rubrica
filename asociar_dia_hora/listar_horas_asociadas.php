<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$dia_semana->id_dia_semana = $_GET["id_dia_semana"];
	echo $dia_semana->listarHorasAsociadas();
?>
