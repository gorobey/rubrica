<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.foros.php");
	session_start();
	$foro = new foros();
	$foro->id_usuario = $_SESSION['id_usuario'];
	$foro->fo_titulo = $_POST["fo_titulo"];
	$foro->fo_descripcion = $_POST["fo_descripcion"];
	echo $foro->insertarForo();
?>
