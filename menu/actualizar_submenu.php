<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menu = new menus();
	$menu->code = $_POST["id"];
	$menu->mnu_texto = $_POST["texto"];
	$menu->mnu_enlace = $_POST["enlace"];
	$menu->mnu_publicado = $_POST["publicado"];
	echo $menu->actualizarMenu();
?>
