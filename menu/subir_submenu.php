<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menu = new menus();
	$menu->code = $_POST["id_menu"];
	$menu->mnu_padre = $_POST["mnu_padre"];
	echo $menu->subirSubmenu();
?>
