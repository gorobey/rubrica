<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	include("../scripts/clases/class.criterios_evaluacion.php");
	session_start();
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$rubricas_evaluacion->id_usuario = $_SESSION["id_usuario"];
	$rubricas_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$rubricas_evaluacion->id_paralelo = $_POST["id_paralelo"];
	$rubricas_evaluacion->rp_tema = $_POST["rp_tema"];
	$rubricas_evaluacion->rp_fec_envio = $_POST["rp_fec_envio"];
	$rubricas_evaluacion->rp_fec_evaluacion = $_POST["rp_fec_evaluacion"];
	$criterios_evaluacion = new criterios_evaluacion();
	$criterios_evaluacion->id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$criterios_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$criterios_evaluacion->id_usuario = $_SESSION["id_usuario"];
	$criterios_evaluacion->id_paralelo = $_POST["id_paralelo"];
	if ($rubricas_evaluacion->insertarRubricaPersonalizada())
		echo $criterios_evaluacion->personalizarCriteriosDocente();
	else
		echo "No se pudo insertar la r&uacute;brica personalizada...";
?>
