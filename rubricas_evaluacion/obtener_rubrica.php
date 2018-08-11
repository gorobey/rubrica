<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->code = $_POST["id_rubrica"];
	echo $rubricas_evaluacion->obtenerRubricaEvaluacion();
?>
