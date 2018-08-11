<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_estudiante = $_POST["id_estudiante"];
	$rubricas_evaluacion->id_club = $_POST["id_club"];
	$rubricas_evaluacion->id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$rubricas_evaluacion->rc_calificacion = $_POST["rc_calificacion"];
	$rubricas_evaluacion->rc_fec_entrega = date("Y-m-d");
	if (!$rubricas_evaluacion->existeRubricaEstudianteClub())
		echo $rubricas_evaluacion->insertarRubricaEstudianteClub();
	else
		echo $rubricas_evaluacion->actualizarRubricaEstudianteClub();
?>
