<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.criterios_evaluacion.php");
	$criterios_evaluacion = new criterios_evaluacion();
	$criterios_evaluacion->id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$criterios_evaluacion->cr_descripcion = $_POST["cr_descripcion"];
	$criterios_evaluacion->cr_ponderacion = $_POST["cr_ponderacion"];
	$criterios_evaluacion->id_asignatura = $_POST["id_asignatura"];
	if ($criterios_evaluacion->existeCriterioPersonalizado($criterios_evaluacion->id_rubrica_evaluacion, $criterios_evaluacion->cr_descripcion))
		echo "Ya existe el criterio de evaluaci&oacute;n en la base de datos...";
	else
		echo $criterios_evaluacion->insertarCriterioPersonalizado();
?>
