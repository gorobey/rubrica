<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.temas.php");
	session_start();
	$tema = new temas();
	$tema->id_foro = $_POST["id_foro"];
	$tema->id_usuario = $_SESSION["id_usuario"];
	echo $tema->listarTemas();
?>
