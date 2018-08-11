<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	session_start();
	$aporte = new aportes_evaluacion();
	$aporte->id_curso = $_POST["id_curso"];
	$aporte->code = $_POST["id_aporte_evaluacion"];
	echo $aporte->asociarAporteCurso();
?>
