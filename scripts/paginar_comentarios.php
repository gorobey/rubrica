<?php
	include("clases/class.mysql.php");
	include("clases/class.comentarios.php");
	$comentario = new comentarios();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["num_pagina"];
	$total_registros = $_POST["total_registros"];
	echo $comentario->paginarComentarios($cantidad_registros, $numero_pagina, $total_registros);
?>
