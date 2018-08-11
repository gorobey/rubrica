<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.temas.php");
	$tema = new temas();
	$tema->code = $_POST["id_tema"];
	$tema->te_titulo = $_POST["te_titulo"];
	$te_descripcion = $_POST["te_descripcion"];
	$tema->te_descripcion = str_replace("\r","<br>",$te_descripcion);
	echo $tema->actualizarTema();
?>
