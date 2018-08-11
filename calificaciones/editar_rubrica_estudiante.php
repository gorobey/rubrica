<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	session_start();
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_estudiante = $_POST["id_estudiante"];
	$rubricas_evaluacion->id_paralelo = $_POST["id_paralelo"];
	$rubricas_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$rubricas_evaluacion->id_rubrica_personalizada = $_POST["id_rubrica_personalizada"];
	$rubricas_evaluacion->re_calificacion = $_POST["re_calificacion"];
	$rubricas_evaluacion->re_fec_entrega = $_POST["re_fec_entrega"];
	$accion = $_POST["accion"];
	$id_rubrica_estudiante = $_POST["id_rubrica_estudiante"];
	echo $rubricas_evaluacion->editarRubricaEstudiante($_POST["cadena_puntajes"],$_POST["cadena_ids_personalizado"],$_POST["cadena_ids_estudiante"],$accion,$id_rubrica_estudiante);
?>
