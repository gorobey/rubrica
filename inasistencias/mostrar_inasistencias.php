<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inasistencias.php");
	$inasistencia = new inasistencias();
	$alineacion = $_POST["alineacion"];
	echo $inasistencia->mostrarInasistencias($alineacion);
?>
