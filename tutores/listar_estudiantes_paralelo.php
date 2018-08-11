<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	session_start();
	$paralelos = new paralelos();
	$paralelos->id_paralelo = $_POST["id_paralelo"];
	$paralelos->id_asignatura = $_POST["id_asignatura"];
	$paralelos->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$paralelos->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$paralelos->id_usuario = $_SESSION["id_usuario"];
	$aportes_evaluacion = new aportes_evaluacion();
	$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
	if($aportes_evaluacion->obtenerTipoAporte()==1)	
		echo $paralelos->listarCalificacionesAsignatura();
	else if($aportes_evaluacion->obtenerTipoAporte()==2)
		echo $paralelos->listarCalificacionesParalelo($_POST["id_periodo_evaluacion"], 1);
?>
