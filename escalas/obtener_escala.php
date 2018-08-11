<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.escalas.php");
	$escala = new escalas();
	echo $escala->obtenerDatosEscala($_POST["id"]);
?>
