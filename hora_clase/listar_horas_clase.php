<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.horas_clase.php");
    session_start();
	$hora_clase = new horas_clase();
	$hora_clase->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $hora_clase->listar_horas_clase();
?>
