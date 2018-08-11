<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	$club = new clubes();
	echo $club->listar_clubes();
?>
