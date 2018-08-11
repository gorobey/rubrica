<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.perfiles.php");
	$perfil = new perfiles();
	echo $perfil->cargarPerfiles();
?>
