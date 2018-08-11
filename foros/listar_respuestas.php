<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.respuestas.php");
	session_start();
	$respuesta = new respuestas();
	$respuesta->id_tema = $_POST["id_tema"];
	$respuesta->id_usuario = $_SESSION["id_usuario"];
	echo $respuesta->listarRespuestas();
?>
