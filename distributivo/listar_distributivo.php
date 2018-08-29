<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.distributivos.php");
	$distributivo = new distributivos();
	$distributivo->id_usuario = $_GET["id_usuario"];
	echo $distributivo->listarDistributivo();
?>
