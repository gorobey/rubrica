<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelo = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	echo $paralelo->obtenerNomCurso($id_paralelo);
?>
