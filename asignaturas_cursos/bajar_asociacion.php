<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$paralelos->code = $_POST["id_asignatura_curso"];
	$paralelos->id_curso = $_POST["id_curso"];
	echo $paralelos->bajarAsignaturaCurso();
?>
