<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.criterios_evaluacion.php");
	$criterios_evaluacion = new criterios_evaluacion();
	$criterios_evaluacion->id_rubrica_evaluacion = $_GET["id_rubrica_evaluacion"];
	echo $criterios_evaluacion->listarCriteriosEvaluacion();
?>
