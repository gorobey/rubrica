<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$paralelos->code = $_POST["id_paralelo"];
	$paralelos->id_curso = $_POST["id_curso"];
	$paralelos->pa_nombre = $_POST["pa_nombre"];
	echo $paralelos->actualizarParalelo();
?>
