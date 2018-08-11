<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$paralelos->code = $_POST["id_paralelo"];
	echo $paralelos->obtenerParalelo();
?>
