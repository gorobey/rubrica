<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	session_start();
	$inspeccion = new inspectores();
	$inspeccion->id_usuario = $_SESSION["id_usuario"];
	$inspeccion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $inspeccion->contarParalelosInspector();
?>
