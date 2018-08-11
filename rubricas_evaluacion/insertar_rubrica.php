<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$rubricas_evaluacion->id_tipo_rubrica = $_POST["id_tipo_rubrica"];
	$rubricas_evaluacion->ru_nombre = $_POST["ru_nombre"];
	$rubricas_evaluacion->ru_abreviatura = $_POST["ru_abreviatura"];
	echo $rubricas_evaluacion->insertarRubricaEvaluacion();
?>
