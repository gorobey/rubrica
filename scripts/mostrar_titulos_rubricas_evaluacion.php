<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubrica_evaluacion = new rubricas_evaluacion();
	$alineacion = $_POST["alineacion"];
	$rubrica_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	echo $rubrica_evaluacion->mostrarTitulosRubricas($alineacion);
?>
