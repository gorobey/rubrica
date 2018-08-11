<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.perfiles.php");
	$perfil = new perfiles();
	$perfil->code = $_POST["id"];
	echo $perfil->eliminarPerfil();
?>
