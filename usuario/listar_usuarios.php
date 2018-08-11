<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.usuarios.php");
	$usuarios = new usuarios();
	$usuarios->id_perfil = $_GET["id_perfil"];
	echo $usuarios->listarUsuarios();
?>
