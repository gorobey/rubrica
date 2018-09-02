<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.distributivos.php");
	session_start();
	$distributivo = new distributivos();
	$distributivo->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$distributivo->id_usuario = $_GET["id_usuario"];
	echo $distributivo->listarDistributivo();
?>
