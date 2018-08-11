<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.temas.php");
	session_start();
	$tema = new temas();
	$tema->id_foro = $_POST['id_foro'];
	$tema->te_titulo = $_POST["te_titulo"];
	$tema->te_descripcion = $_POST["te_descripcion"];
	echo $tema->insertarTema();
?>
