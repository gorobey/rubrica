<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tutores.php");
	session_start();
	$tutor = new tutores();
	$id_usuario = $_SESSION["id_usuario"];
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $tutor->obtenerIdParaleloTutor($id_usuario, $id_periodo_lectivo);
?>
