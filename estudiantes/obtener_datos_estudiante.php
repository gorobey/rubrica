<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	$estudiante = new estudiantes();
	$estudiante->code = $_POST["id_estudiante"];
	$estudiante->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $estudiante->obtenerEstudiante();
?>