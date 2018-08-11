<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$selects = new selects();
	$selects->id_aporte_evaluacion = $_GET["id_aporte_evaluacion"];
	echo $selects->cargarRubricasEvaluacion();
?>