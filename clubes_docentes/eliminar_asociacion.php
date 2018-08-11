<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	$club = new clubes();
	$club->code = $_POST["id_club_docente"];
	echo $club->eliminarClubDocente();
?>
