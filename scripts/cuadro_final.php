<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	echo $paralelos->listarCuadroFinal($_SESSION["id_periodo_lectivo"],$id_paralelo);
?>
