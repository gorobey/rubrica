<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.menus.php");
	$menus = new menus();
	$menus->code = $_GET["code"];
	$menus->id_perfil = $_GET["id_perfil"];
	$menus->mnu_nivel = $_GET["mnu_nivel"];
	echo $menus->listarSubmenus();
?>
