<?php
	require_once("../clases/class.mysql.php");
	require_once("../clases/class.mensajes.php");
	$mensaje = new mensajes();
	$mensaje->code = $_POST["id"];
	$reg_mensaje = $mensaje->obtenerMensaje();
	echo "<p><strong>Usuario:</strong> " . $reg_mensaje->us_titulo . " " . $reg_mensaje->us_apellidos . " " . $reg_mensaje->us_nombres . "</p>";
    echo "<p><strong>Perfil:</strong> " . $reg_mensaje->pe_nombre . "</p>";
    echo "<p><strong>Texto:</strong> " . $reg_mensaje->me_texto . "</p>";
    echo "<p><strong>Fecha:</strong> " . $reg_mensaje->me_fecha . "</p>";
?>