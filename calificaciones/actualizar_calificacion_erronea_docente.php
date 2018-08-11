<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubrica_evaluacion = new rubricas_evaluacion();
	$rubrica_evaluacion->code = $_POST["id_rubrica_estudiante"];
	$rubrica_evaluacion->re_calificacion = $_POST["re_calificacion"];
	echo $rubrica_evaluacion->actualizarCalificacionErronea();
?>
