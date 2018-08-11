<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inasistencias.php");
	$inasistencia = new inasistencias();
	$inasistencia->code = $_POST["id_inasistencia"];
	$inasistencia->in_nombre = $_POST["in_nombre"];
	$inasistencia->in_abreviatura = $_POST["in_abreviatura"];
	echo $inasistencia->actualizarInasistencia();
?>
