<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.combos.php");
	session_start();
	$selects = new selects();
	$selects->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $selects->cargarPeriodosEvaluacion();
?>