<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.escalas.php");
	session_start();
	$escalas = new escalas();
	$escalas->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $escalas->obtenerEscalasCalificacionesClub();
?>
