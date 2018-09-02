<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.distributivos.php");
	session_start();
	$distributivo = new distributivos();
	$distributivo->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    $distributivo->id_usuario = $_POST["id_usuario"];
	$distributivo->id_paralelo = $_POST["id_paralelo"];
	$distributivo->id_asignatura = $_POST["id_asignatura"];
	echo $distributivo->insertarDistributivo();
?>
