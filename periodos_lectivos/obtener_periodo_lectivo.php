<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_lectivos.php");
	$periodo_lectivo = new periodos_lectivos();
	echo $periodo_lectivo->obtenerDatosPeriodoLectivo($_POST["id"]);
?>
