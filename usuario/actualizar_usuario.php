<?php
	sleep(1);
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuarios = new usuarios();
	$usuarios->code = $_POST["id_usuario"];
	$usuarios->id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
	$usuarios->id_perfil = $_POST["id_perfil"];
	$usuarios->us_titulo = $_POST["us_titulo"];
	$usuarios->us_apellidos = $_POST["us_apellidos"];
	$usuarios->us_nombres = $_POST["us_nombres"];
	$usuarios->us_fullname = $_POST["us_fullname"];
	$usuarios->us_login = $_POST["us_login"];
	$usuarios->us_password = $_POST["us_password"];
	echo $usuarios->actualizarUsuario();
?>
