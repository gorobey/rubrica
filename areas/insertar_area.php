<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.areas.php");
	$area = new areas();
	$area->ar_nombre = $_POST["ar_nombre"];
	echo $area->insertarArea();
?>
