<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	session_start();
	$club = new clubes();
	$club->id_club = $_POST["id_club"];
	$club->es_nombres = $_POST["es_nombres"];
	$club->es_apellidos = $_POST["es_apellidos"];
	$club->id_estudiante = $_POST["id_estudiante"];
	$club->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $club->insertarEstudianteSeleccionado();
?>
