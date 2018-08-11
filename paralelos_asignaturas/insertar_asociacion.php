<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$paralelos->id_paralelo = $_POST["id_paralelo"];
	$paralelos->id_asignatura = $_POST["id_asignatura"];
	$paralelos->id_docente = $_POST["id_docente"];
	$paralelos->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $paralelos->asociarAsignatura();
?>
