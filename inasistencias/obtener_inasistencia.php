<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inasistencias.php");
	$inasistencia = new inasistencias();
	$inasistencia->code = $_POST["id_inasistencia"];
	echo $inasistencia->obtenerInasistencia();
?>
