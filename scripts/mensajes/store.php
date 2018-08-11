<?php
	require_once("../clases/class.mysql.php");
	require_once("../clases/class.mensajes.php");
	$mensaje = new mensajes();
    $mensaje->me_texto = $_POST["texto"];
    $mensaje->id_usuario = $_POST["id_usuario"];
    $mensaje->id_perfil = $_POST["id_perfil"];
    echo $mensaje->insertarMensaje();
?>