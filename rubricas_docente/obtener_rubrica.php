<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	session_start();
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->code = $_POST["id_rubrica_evaluacion"];
	$rubricas_evaluacion->id_usuario = $_SESSION["id_usuario"];
	$rubricas_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$rubricas_evaluacion->id_paralelo = $_POST["id_paralelo"];
	echo $rubricas_evaluacion->obtenerRubricaPersonalizada();
?>
