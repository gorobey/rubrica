<?php
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$estudiantes->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$estudiantes->id_paralelo = $_POST["id_paralelo"];
	echo $estudiantes->listarEstudiantesPromocion();
?>
