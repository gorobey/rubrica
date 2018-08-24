<?php
	sleep(1);
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelo = new paralelos();
	$paralelo->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$paralelo->id_curso = $_POST["id_curso"];
	$paralelo->pa_nombre = $_POST["pa_nombre"];
	echo $paralelo->insertarParalelo();
?>
