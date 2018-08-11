<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	session_start();
	$club = new clubes();
	$club->id_club = $_POST["id_club"];
	$club->id_usuario = $_POST["id_usuario"];
	$club->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $club->asociarClubDocente();
?>
