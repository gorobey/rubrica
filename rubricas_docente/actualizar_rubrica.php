<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	session_start();
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->code = $_POST["id_rubrica_personalizada"];
	$rubricas_evaluacion->id_rubrica_evaluacion = $_POST["id_rubrica_evaluacion"];
	$rubricas_evaluacion->id_usuario = $_SESSION["id_usuario"];
	$rubricas_evaluacion->rp_tema = $_POST["rp_tema"];
	$rubricas_evaluacion->rp_fec_envio = $_POST["rp_fec_envio"];
	$rubricas_evaluacion->rp_fec_evaluacion = $_POST["rp_fec_evaluacion"];
	echo $rubricas_evaluacion->actualizarRubricaPersonalizada();
?>
