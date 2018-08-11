<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_lectivos.php");
	$anio_inicial = $_POST["anio_inicial"];
	$anio_final = $_POST["anio_final"];
	$periodos_lectivos = new periodos_lectivos();
	echo $periodos_lectivos->insertarPeriodoLectivo($anio_inicial, $anio_final);
?>
