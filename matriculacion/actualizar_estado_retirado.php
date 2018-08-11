<?php
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$estudiantes->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$estudiantes->code = $_POST["id_estudiante"];
	$estado_retirado = $_POST["es_retirado"];
	echo $estudiantes->actualizarEstadoRetirado($estado_retirado);
?>
