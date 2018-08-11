<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.criterios_evaluacion.php");
	session_start();
	$criterios_evaluacion = new criterios_evaluacion();
	$criterios_evaluacion->id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$criterios_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$criterios_evaluacion->id_paralelo = $_POST["id_paralelo"];
	$criterios_evaluacion->id_usuario = $_SESSION['id_usuario'];
	echo $criterios_evaluacion->listarCriteriosDocente();
?>
