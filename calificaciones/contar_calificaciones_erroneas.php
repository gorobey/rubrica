<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	$periodos_evaluacion = new periodos_evaluacion();
	$id_periodo_evaluacion = $_POST["id_periodo"];
	echo $periodos_evaluacion->contarCalificacionesErroneas($id_periodo_evaluacion);
?>
