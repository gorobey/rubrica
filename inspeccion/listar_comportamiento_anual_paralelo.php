<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	echo $paralelos->listarEstudiantesComportamientoAnual($id_periodo_lectivo, $id_paralelo);
?>
