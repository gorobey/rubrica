<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.temas.php");
	$tema = new temas();
	$tema->code = $_POST["id_tema"];
	echo $tema->obtenerTituloTema();
?>
