<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	session_start();
	$periodos_evaluacion = new periodos_evaluacion();
	$periodos_evaluacion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$periodos_evaluacion->pe_nombre = $_POST["pe_nombre"];
	$periodos_evaluacion->pe_abreviatura = $_POST["pe_abreviatura"];
	$periodos_evaluacion->pe_tipo = $_POST["pe_tipo"];
	echo $periodos_evaluacion->insertarPeriodoEvaluacion();
?>
