<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menus = new menus();
	$menus->code = $_POST["id"];
	echo $menus->eliminarMenu();
?>
