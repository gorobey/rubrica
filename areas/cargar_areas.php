<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.areas.php");
	$area = new areas();
	echo $area->cargarAreas();
?>
