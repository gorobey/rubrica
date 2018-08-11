<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuario = new usuarios();
	$usuario->id_perfil = $_POST["id_perfil"];
	$usuario->id_usuario = $_POST["id_usuario"];
	echo $usuario->asociarUsuarioPerfil();
?>
