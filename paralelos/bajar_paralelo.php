<?php
    sleep(1);
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelo = new paralelos();
    $paralelo->code = $_POST["id_paralelo"];
    $paralelo->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $paralelo->bajarParalelo();
?>
