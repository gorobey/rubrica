<?php
	sleep(1);
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuario = new usuarios();
	$usuario->code = $_POST["id_usuario"];
	$usuario->id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
	$usuario->id_perfil = $_POST["id_perfil"];
	$usuario->us_titulo = $_POST["us_titulo"];
	$usuario->us_apellidos = $_POST["us_apellidos"];
	$usuario->us_nombres = $_POST["us_nombres"];
	$usuario->us_fullname = $usuario->us_apellidos . " " . $usuario->us_nombres;
	$usuario->us_login = $_POST["us_login"];
	$usuario->us_password = $_POST["us_password"];
	$usuario->us_activo = $_POST["us_activo"];
	echo $usuario->actualizarUsuario();
?>
