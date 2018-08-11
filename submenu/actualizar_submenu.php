<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menus = new menus();
	$menus->code = $_POST["id_menu"];
	$menus->mnu_texto = $_POST["mnu_texto"];
	$menus->mnu_enlace = $_POST["mnu_enlace"];
	$menus->mnu_orden = $_POST["mnu_orden"];
	echo $menus->actualizarSubmenu();
?>
