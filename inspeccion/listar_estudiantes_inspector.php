<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	echo $paralelos->listarEstudiantesCompInspector($id_paralelo,$id_periodo_evaluacion);
?>
