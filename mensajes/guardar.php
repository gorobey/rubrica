<?php
	require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.mensajes.php");
    $mensaje = new mensajes();
    $mensaje->id_usuario = $_POST["id_usuario"];
    $mensaje->id_perfil = $_POST["id_perfil"];
    $mensaje->me_texto = $_POST["txt_mensaje"];
	echo $mensaje->insertarMensaje();
?>