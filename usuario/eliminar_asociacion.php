<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuarios = new usuarios();
	$usuarios->id_perfil = $_POST["id_perfil"];
	$usuarios->id_usuario = $_POST["id_usuario"];
	echo $usuarios->eliminarUsuarioPerfil();
?>
