<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	session_start();
	$estudiantes = new estudiantes();
	$estudiantes->code = $_POST["id_estudiante"];
	$estudiantes->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$estudiantes->id_paralelo = $_POST["id_paralelo"];
	$estudiantes->es_apellidos = $_POST["es_apellidos"];
	$estudiantes->es_nombres = $_POST["es_nombres"];
	echo $estudiantes->insertarEstudianteSeleccionado();
?>
