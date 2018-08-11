<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubrica_evaluacion = new rubricas_evaluacion();
	$rubrica_evaluacion->code = $_POST["id_rubrica_estudiante"];
	echo $rubrica_evaluacion->obtenerRubricaEstudiante();
?>
