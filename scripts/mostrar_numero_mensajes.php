<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.mensajes.php");
	$mensaje = new mensajes();
	echo $mensaje->obtenerNumeroMensajes();
?>