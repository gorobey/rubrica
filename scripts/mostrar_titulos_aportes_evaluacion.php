<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aporte_evaluacion = new aportes_evaluacion();
	$alineacion = $_POST["alineacion"];
	$aporte_evaluacion->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	echo $aporte_evaluacion->mostrarTitulosAportes($alineacion);
?>
