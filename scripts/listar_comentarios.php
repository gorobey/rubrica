<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.comentarios.php");
	$comentarios = new comentarios();
	echo $comentarios->listarComentarios();
?>