<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	session_start();
	$club = new clubes();
	echo $club->equivalencia($_POST["promedio_aporte"],$_SESSION["id_periodo_lectivo"]);
?>
