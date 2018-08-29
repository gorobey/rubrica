<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.distributivos.php");
	$distributivo = new distributivos();
	$distributivo->code = $_POST["id_distributivo"];
	echo $distributivo->eliminarDistributivo();
?>
