<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$id_estudiante = $_POST["id_estudiante"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];
	$id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	if ($rubricas_evaluacion->existeRubricaEstudiante($id_estudiante,$id_paralelo,$id_asignatura,$id_rubrica_evaluacion))
		echo json_encode(array('existe' => true));
	else
		echo json_encode(array('existe' => false));
?>
