<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	$periodos_evaluacion = new periodos_evaluacion();
	$alineacion = $_POST["alineacion"];
	$periodos_evaluacion->pe_principal = $_POST["pe_principal"];
	$periodos_evaluacion->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $periodos_evaluacion->mostrarTitulosPeriodos($alineacion);
?>
