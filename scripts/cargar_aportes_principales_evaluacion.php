<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$selects = new selects();
	$selects->id_periodo_evaluacion = $_GET["id_periodo_evaluacion"];
	echo $selects->cargarAportesPrincipalesEvaluacion();
?>