<?php
	sleep(1);
	include("clases/class.mysql.php");
	include("clases/class.comentarios.php");
	$comentarios = new comentarios();
	$comentarios->co_id_usuario = $_POST["co_id_usuario"];
	$comentarios->co_tipo = $_POST["co_tipo"];
	$comentarios->co_perfil = $_POST["co_perfil"];
	$comentarios->co_nombre = $_POST["co_nombre"];
	$comentarios->co_texto = $_POST["co_texto"];
	echo $comentarios->insertarComentario();
?>
