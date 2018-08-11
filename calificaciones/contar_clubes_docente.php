<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	session_start();
	$club = new clubes();
	$club->id_usuario = $_SESSION["id_usuario"];
	$club->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $club->contarClubesDocente();
?>
