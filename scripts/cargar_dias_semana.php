<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$selects = new selects();
	session_start();
	$selects->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $selects->cargarDiasSemana();
?>