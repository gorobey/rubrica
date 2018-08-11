<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.foros.php");
	session_start();
	$foro = new foros();
	$foro->id_usuario = $_SESSION["id_usuario"];
	echo $foro->listarForos();
?>
