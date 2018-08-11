<?php
	require_once("../clases/class.mysql.php");
	require_once("../clases/class.perfiles.php");
	$perfil = new perfiles();
	$id_perfil = $_POST["id"];
    $reg_perfil = $perfil->obtenerPerfil($id_perfil);
    echo "<p><strong>Nombre:</strong> $reg_perfil->pe_nombre</p>";
?>