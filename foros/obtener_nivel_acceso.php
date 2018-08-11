<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	session_start();
	$usuario = new usuarios();
	$usuario->code = $_SESSION["id_usuario"];
	echo $usuario->obtenerNivelAcceso();
?>
