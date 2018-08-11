<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.respuestas.php");
	session_start();
	$respuesta = new respuestas();
	$respuesta->id_tema = $_POST['id_tema'];
	$respuesta->re_texto = $_POST["re_texto"];
	$respuesta->re_autor = $_SESSION["id_usuario"];
	echo $respuesta->insertarRespuesta();
?>
