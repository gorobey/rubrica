<?php
	//sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	session_start();
	$usuario = new usuarios();
	$id_usuario = $_SESSION['id_usuario'];
	echo $usuario->listarCalificacionesErroneasDocente($id_usuario);
?>
