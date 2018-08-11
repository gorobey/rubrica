<?php
	sleep(1);
	session_start();
	include("clases/class.mysql.php");
	include("clases/class.usuarios.php");
	$usuarios = new usuarios();
	$usuarios->code = $_SESSION["id_usuario"];
	$usuarios->clave = $_POST["password1"];
	echo $usuarios->actualizarClave();
?>
