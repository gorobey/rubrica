<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$rubricas_evaluacion->id_tipo_asignatura = $_POST["id_tipo_asignatura"];
	echo $rubricas_evaluacion->cargarRubricasEvaluacion();
?>
