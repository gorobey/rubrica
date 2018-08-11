<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_lectivos.php");
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	$periodos_lectivos = new periodos_lectivos();
	echo $periodos_lectivos->cerrarPeriodoLectivo($id_periodo_lectivo);
?>
