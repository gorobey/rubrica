<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuarios = new usuarios();
	echo $usuarios->obtenerUsuarios($_POST["valor"]);
?>
