<?php
	include("clases/class.mysql.php");
	include("clases/class.comentarios.php");
	session_start();
	$comentario = new comentarios();
	echo $comentario->contarComentarios();
?>
