<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aportes_evaluacion = new aportes_evaluacion();
	$aportes_evaluacion->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$aportes_evaluacion->id_club = $_POST["id_club"];
	echo $aportes_evaluacion->listar_aportes_evaluacion_clubes();
?>
