<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menu = new menus();
	$menu->code = $_GET["id"];
	echo $menu->listarSubmenus();
?>
