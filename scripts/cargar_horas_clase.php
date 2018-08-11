<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	session_start();
	$select = new selects();
	$select->id_dia_semana = $_POST["id_dia_semana"];
	$select->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $select->cargarHorasClase();
?>