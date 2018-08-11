<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.mensajes.php");
	session_start();
	$mensaje = new mensajes();
	$mensaje->id_usuario = $_SESSION["id_usuario"];
	echo $mensaje->listarMensajes();
?>