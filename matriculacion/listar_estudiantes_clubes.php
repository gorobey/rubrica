<?php
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	$club = new clubes();
	$club->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$club->id_club = $_POST["id_club"];
	echo $club->listarEstudiantes();
?>
