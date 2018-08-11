<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inasistencias.php");
	session_start();
	$inasistencia = new inasistencias();
	echo $inasistencia->listar_inasistencias();
?>
