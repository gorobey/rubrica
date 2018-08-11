<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$paralelos->id_curso = $_POST["id_curso"];
	$paralelos->id_asignatura = $_POST["id_asignatura"];
	$paralelos->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $paralelos->asociarAsignaturaCurso();
?>
