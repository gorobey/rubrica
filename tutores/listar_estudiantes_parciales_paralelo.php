<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	echo $paralelos->listarEstudiantesComportamientoParciales($id_paralelo,$id_aporte_evaluacion);
?>
