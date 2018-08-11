<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aportes_evaluacion = new aportes_evaluacion();
	$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aportes_evaluacion->id_curso = $_POST["id_curso"];
	$aportes_evaluacion->ap_nombre = $_POST["ap_nombre"];
	$aportes_evaluacion->ap_fecha_apertura = $_POST["fecha_apertura"];
	$aportes_evaluacion->ap_fecha_cierre = $_POST["fecha_cierre"];
	echo $aportes_evaluacion->actualizarFechasAporteEvaluacion();
?>
