<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aportes_evaluacion = new aportes_evaluacion();
	$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aportes_evaluacion->id_asignatura = $_POST["id_asignatura"];
	echo $aportes_evaluacion->mostrarLeyendasRubricas("center");
?>
