<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menu = new menus();
	$menu->id_perfil = $_POST["id_perfil"];
	$menu->mnu_texto = $_POST["mnu_texto"];
	$menu->mnu_enlace = $_POST["mnu_enlace"];
	$menu->mnu_publicado = $_POST["mnu_publicado"];
	$menu->mnu_padre = 0;
	$menu->mnu_nivel = 1;
	echo $menu->insertarMenu();
?>
