<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menus = new menus();
	$menus->id_perfil = $_POST["id_perfil"];
	$menus->mnu_texto = $_POST["mnu_texto"];
	$menus->mnu_enlace = $_POST["mnu_enlace"];
	$menus->mnu_nivel = $_POST["mnu_nivel"];
	$menus->mnu_padre = $_POST["mnu_padre"];
	echo $menus->insertarSubMenu();
?>
