<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_estudiante = $_POST["id_estudiante"];
	$rubricas_evaluacion->id_paralelo = $_POST["id_paralelo"];
	$rubricas_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$rubricas_evaluacion->id_rubrica_personalizada = $_POST["id_rubrica_personalizada"];
	$rubricas_evaluacion->re_calificacion = $_POST["re_calificacion"];
	$rubricas_evaluacion->re_fec_entrega = date("Y-m-d");
	if (!$rubricas_evaluacion->existeRubricaEstudiante())
		echo $rubricas_evaluacion->insertarRubricaEstudiante();
	else
		echo $rubricas_evaluacion->actualizarRubricaEstudiante();
?>
