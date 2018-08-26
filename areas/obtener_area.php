<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.areas.php");
	$area = new areas();
	$area->code = $_POST["id_area"];
	echo $area->obtenerDatosArea();
?>
