<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	session_start();
	$selects = new selects();
	$selects->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $selects->cargarPeriodosEvaluacionPrincipales();
?>