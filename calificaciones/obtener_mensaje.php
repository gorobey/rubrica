<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.mensajes.php");
	$mensaje = new mensajes();
	$mensaje->code = $_POST["id_mensaje"];
	echo $mensaje->obtenerMensaje();
?>
