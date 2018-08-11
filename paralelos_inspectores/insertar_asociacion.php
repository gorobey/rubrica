<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	session_start();
	$inspector = new inspectores();
	$inspector->id_paralelo = $_POST["id_paralelo"];
	$inspector->id_usuario = $_POST["id_usuario"];
	$inspector->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $inspector->asociarParaleloInspector();
?>
