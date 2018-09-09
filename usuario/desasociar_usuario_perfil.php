<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuario = new usuarios();
	$usuario->id_usuario = $_POST["id_usuario"];
	$usuario->id_perfil = $_POST["id_perfil"];
	echo $usuario->eliminarUsuarioPerfil();
?>
