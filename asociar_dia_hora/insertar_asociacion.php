<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$dia_semana->id_dia_semana = $_POST["id_dia_semana"];
	$dia_semana->id_hora_clase = $_POST["id_hora_clase"];
	echo $dia_semana->insertarHoraDia();
?>
