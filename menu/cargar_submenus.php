<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$submenus = new menus();
	$submenus->code = $_GET["code"];
	echo $submenus->cargarSubmenus();
?>
