<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aportes_evaluacion = new aportes_evaluacion();
	$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aportes_evaluacion->id_curso = $_POST["id_curso"];
	$estado = $_POST["estado"];
	echo $aportes_evaluacion->cerrarAporteEvaluacion($estado);
?>
