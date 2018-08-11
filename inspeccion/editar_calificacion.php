<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	$inspector = new inspectores();
	$inspector->id_estudiante = $_POST["id_estudiante"];
	$inspector->id_paralelo = $_POST["id_paralelo"];
        $inspector->id_escala_comportamiento = $_POST["id_escala_comportamiento"];
	$inspector->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$inspector->co_calificacion = $_POST["co_calificacion"];
	if (!$inspector->existeCalifComportamiento())
		echo $inspector->insertarCalifComportamiento();
	else
		echo $inspector->actualizarCalifComportamiento();
?>
