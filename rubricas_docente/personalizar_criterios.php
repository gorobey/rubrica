<?php

	include("../scripts/clases/class.mysql.php");

	include("../scripts/clases/class.criterios_evaluacion.php");

	session_start();

	$criterios_evaluacion = new criterios_evaluacion();

	$criterios_evaluacion->id_rubrica_evaluacion = $_GET["id_rubrica_evaluacion"];

	$criterios_evaluacion->id_usuario = $_SESSION['id_usuario'];

	echo $criterios_evaluacion->personalizarCriteriosDocente();

?>

