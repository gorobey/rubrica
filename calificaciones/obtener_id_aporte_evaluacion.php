<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	$periodos_evaluacion = new periodos_evaluacion();
	$periodos_evaluacion->id_curso = $_POST["id_curso"];
	$periodos_evaluacion->pe_principal = $_POST["pe_principal"];
	echo $periodos_evaluacion->obtenerIdAporteEvaluacionSupRemGracia();
?>
