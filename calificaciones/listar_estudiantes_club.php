<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	session_start();
	$club = new clubes();
	$club->id_club = $_POST["id_club"];
	$club->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$club->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$club->id_usuario = $_SESSION["id_usuario"];
	$club->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$aporte_evaluacion = new aportes_evaluacion();
	$aporte_evaluacion->code = $_POST["id_aporte_evaluacion"];
	if($aporte_evaluacion->obtenerTipoAporte()==1)	
		echo $club->listarEstudiantesClub();
	else if($aporte_evaluacion->obtenerTipoAporte()==2)
		echo $club->listarCalificacionesClub($_POST["id_periodo_evaluacion"], 2);
?>
