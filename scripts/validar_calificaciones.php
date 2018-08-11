<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	$periodos = new periodos_evaluacion();
	$id_periodo_evaluacion = $_POST["id_periodo"];
	echo $periodos->listarCalificacionesErroneas($id_periodo_evaluacion);
?>
