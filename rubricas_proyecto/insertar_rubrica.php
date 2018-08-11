<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubrica_evaluacion = new rubricas_evaluacion();
	$rubrica_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$rubrica_evaluacion->rc_nombre = $_POST["rc_nombre"];
	$rubrica_evaluacion->rc_abreviatura = $_POST["rc_abreviatura"];
	echo $rubrica_evaluacion->insertarRubricaProyecto();
?>
