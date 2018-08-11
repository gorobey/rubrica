<?php
	include("clases/class.mysql.php");
	include("clases/class.usuarios.php");
	$usuarios = new usuarios();
	$usuarios->code = $_GET["code"];
	echo $usuarios->cargarUsuarios();
?>
