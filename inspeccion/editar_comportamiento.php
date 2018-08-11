<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_indice_evaluacion = $_POST["id_indice_evaluacion"];
	$rubricas_evaluacion->nombre_campo = $_POST["nombre_campo"];
	$rubricas_evaluacion->calificacion = $_POST["calificacion"];
	$rubricas_evaluacion->total = $_POST["total"];
	$rubricas_evaluacion->promedio = $_POST["promedio"];
	$rubricas_evaluacion->equivalencia = $_POST["equivalencia"];
	echo $rubricas_evaluacion->editarComportamiento();
?>
