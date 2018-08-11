<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inasistencias.php");
	session_start();
	$inasistencia = new inasistencias();
	$inasistencia->in_nombre = $_POST["in_nombre"];
	$inasistencia->in_abreviatura = $_POST["in_abreviatura"];
	echo $inasistencia->insertarInasistencia();
?>
