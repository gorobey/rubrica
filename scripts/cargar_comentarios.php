<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.comentarios.php");
	$comentario = new comentarios();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	echo $comentario->listarComentarios($cantidad_registros, $numero_pagina);
?>