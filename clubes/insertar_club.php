<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	$club = new clubes();
	$club->cl_nombre = $_POST["cl_nombre"];
	$club->cl_abreviatura = $_POST["cl_abreviatura"];
	echo $club->insertarClub();
?>
