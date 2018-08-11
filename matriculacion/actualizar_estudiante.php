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
	$estudiantes->es_cedula = $_POST["es_cedula"];
	$estudiantes->es_genero = $_POST["es_genero"];
	$estudiantes->es_email = $_POST["es_email"];
        $estudiantes->es_direccion = $_POST["es_direccion"];
        $estudiantes->es_sector = $_POST["es_sector"];
        $estudiantes->es_telefono = $_POST["es_telefono"];
	echo $estudiantes->actualizarEstudiante();
?>
