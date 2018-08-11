<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_lectivos.php");
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	$anio_inicial = $_POST["anio_inicial"];
	$anio_final = $_POST["anio_final"];
	$periodos_lectivos = new periodos_lectivos();
	echo $periodos_lectivos->actualizarPeriodoLectivo($id_periodo_lectivo, $anio_inicial, $anio_final);
?>
