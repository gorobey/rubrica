<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	$periodos_evaluacion = new periodos_evaluacion();
	$periodos_evaluacion->code = $_POST["id_periodo_evaluacion"];
	echo $periodos_evaluacion->obtenerTipoPeriodo();
?>
