<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->code = $_POST["id_rubrica_evaluacion"];
	$rubricas_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$rubricas_evaluacion->ru_nombre = $_POST["ru_nombre"];
	$rubricas_evaluacion->ru_abreviatura = $_POST["ru_abreviatura"];
	$rubricas_evaluacion->id_tipo_rubrica = $_POST["tipo_rubrica"];
	echo $rubricas_evaluacion->actualizarRubricaEvaluacion();
?>
