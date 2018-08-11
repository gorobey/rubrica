<?php
	sleep(1);
	include("clases/class.mysql.php");
	include("clases/class.mensajes.php");
	$mensaje = new mensajes();
	$mensaje->code = $_POST["id_mensaje"];
	$mensaje->me_texto = $_POST["me_texto"];
	echo $mensaje->actualizarMensaje();
?>
