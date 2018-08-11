<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	session_start();
	$periodos_evaluacion = new periodos_evaluacion();
	$periodos_evaluacion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $periodos_evaluacion->cargar_periodos_evaluacion();
?>
