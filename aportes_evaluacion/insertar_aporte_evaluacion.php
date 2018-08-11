<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	session_start();
	$aporte_evaluacion = new aportes_evaluacion();
	$aporte_evaluacion->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$aporte_evaluacion->ap_nombre = $_POST["ap_nombre"];
	$aporte_evaluacion->ap_abreviatura = $_POST["ap_abreviatura"];
	$aporte_evaluacion->ap_tipo = $_POST["ap_tipo"];
	echo $aporte_evaluacion->insertarAporteEvaluacion();
?>
