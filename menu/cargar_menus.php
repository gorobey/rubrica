<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menus = new menus();
	$menus->code = $_GET["code"];
	echo $menus->cargarMenus();
?>
