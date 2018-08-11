<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.perfiles.php");
	$perfil = new perfiles();
	$perfil->pe_nombre = $_POST["perfil"];
	echo $perfil->insertarPerfil();
?>
