<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.periodos_lectivos.php");
	$periodo_lectivo = new periodos_lectivos();
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $periodo_lectivo->crear_enlaces_quimestres($id_periodo_lectivo);
?>