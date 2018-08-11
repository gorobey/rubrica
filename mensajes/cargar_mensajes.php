<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.mensajes.php");
	$mensaje = new mensajes();
	echo $mensaje->obtenerMensajes();
?>