<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_estudiante = $_POST["id_estudiante"];
	$rubricas_evaluacion->id_paralelo = $_POST["id_paralelo"];
	$rubricas_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$rubricas_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	echo $rubricas_evaluacion->eliminarRubricaEstudianteComportamiento();
?>
