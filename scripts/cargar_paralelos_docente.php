<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	session_start();
	$selects = new selects();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_usuario = $_SESSION["id_usuario"];
	echo $selects->cargarParalelosDocente($id_periodo_lectivo, $id_usuario);
?>