<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menus = new menus();
	$menus->id_perfil = $_GET["id_perfil"];
	$menus->mnu_nivel = $_GET["nivel"];
	echo $menus->cargarMenusNivel();
?>
