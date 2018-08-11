<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	session_start();
	$estudiantes = new estudiantes();
	$estudiantes->id_paralelo = $_POST["id_paralelo"];
	$estudiantes->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $estudiantes->contarEstudiantesParalelo();
?>
