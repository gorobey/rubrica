<?php
	include("clases/class.mysql.php");
	include("clases/class.mensajes.php");
	$mensaje = new mensajes();
	$mensaje->code = $_POST["id_mensaje"];
	echo $mensaje->obtenerMensaje();
?>
