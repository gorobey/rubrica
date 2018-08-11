<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$id_asignatura = $_POST["id_asignatura"];
	echo $paralelos->listarEstudiantesComportamientoPorDocente($id_paralelo,$id_periodo_evaluacion,$id_asignatura);
?>
