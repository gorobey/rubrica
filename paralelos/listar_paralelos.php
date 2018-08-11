<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$paralelos->code = $_GET["id_curso"];
	echo $paralelos->listarParalelos();
?>
