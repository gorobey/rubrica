<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_lectivos.php");
	$periodos_lectivos = new periodos_lectivos();
	echo $periodos_lectivos->listarPeriodosLectivos();
?>
