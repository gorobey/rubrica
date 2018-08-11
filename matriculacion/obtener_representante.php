<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	session_start();
	$estudiante = new estudiantes();
	$estudiante->code = $_POST["id_estudiante"];
	echo $estudiante->obtenerRepresentante();
?>
