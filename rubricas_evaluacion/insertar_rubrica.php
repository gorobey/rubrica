<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubrica_evaluacion = new rubricas_evaluacion();
	$rubrica_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$rubrica_evaluacion->id_tipo_asignatura = $_POST["id_tipo_asignatura"];
	$rubrica_evaluacion->id_tipo_rubrica = $_POST["id_tipo_rubrica"];
	$rubrica_evaluacion->ru_nombre = $_POST["ru_nombre"];
	$rubrica_evaluacion->ru_abreviatura = $_POST["ru_abreviatura"];
	echo $rubrica_evaluacion->insertarRubricaEvaluacion();
?>
