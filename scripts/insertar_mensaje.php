<?php
	sleep(1);
	include("clases/class.mysql.php");
	include("clases/class.mensajes.php");
	$mensaje = new mensajes();
	$mensaje->me_texto = $_POST["me_texto"];
	echo $mensaje->insertarMensaje();
?>
