<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuarios = new usuarios();
	$usuarios->code = $_POST["id_usuario"];
	echo $usuarios->eliminarUsuario();
?>
