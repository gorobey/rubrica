<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.criterios_evaluacion.php");
	$criterios_evaluacion = new criterios_evaluacion();
	$criterios_evaluacion->code = $_POST["id_criterio"];
	echo $criterios_evaluacion->obtenerCriterioEvaluacion();
?>
