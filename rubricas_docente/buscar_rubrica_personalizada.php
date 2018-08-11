<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	session_start();
	$rubricas_evaluacion = new rubricas_evaluacion();
	$id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$id_asignatura = $_POST["id_asignatura"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_usuario = $_SESSION['id_usuario'];
	if ($rubricas_evaluacion->existeRubricaPersonalizada($id_rubrica_evaluacion, $id_usuario, $id_asignatura, $id_paralelo))
		echo json_encode(array('existe' => true));
	else
		echo json_encode(array('existe' => false));
?>
