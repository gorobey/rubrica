<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.criterios_evaluacion.php");
	session_start();
	$criterios_evaluacion = new criterios_evaluacion();
	$id_estudiante = $_POST["id_estudiante"];
	$id_asignatura = $_POST["id_asignatura"];
	$id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$id_usuario = $_SESSION["id_usuario"];
	$id_paralelo = $_POST["id_paralelo"];
	echo $criterios_evaluacion->obtenerRubricaPersonalizada($id_estudiante,$id_asignatura,$id_rubrica_evaluacion,$id_usuario,$id_paralelo);
?>
