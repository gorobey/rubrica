<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aporte_evaluacion = new aportes_evaluacion();
	$aporte_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aporte_evaluacion->id_curso = $_POST["id_curso"];
	echo $aporte_evaluacion->actualizarEstadoRubrica();
?>
