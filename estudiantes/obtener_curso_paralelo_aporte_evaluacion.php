<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	$estudiante = new estudiantes();
	$id_estudiante = $_POST["id_estudiante"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	echo $estudiante->obtenerCursoParaleloEstudianteAporteEvaluacion($id_estudiante, $id_aporte_evaluacion);
?>