<?php
	sleep(1);
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	$estudiante = new estudiantes();
	$id_estudiante = $_POST["id_estudiante"];
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	echo $estudiante->obtenerAportesEstudianteId($id_estudiante, $id_periodo_lectivo, $id_aporte_evaluacion);
?>