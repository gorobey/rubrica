<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tutores.php");
	session_start();
	$tutor = new tutores();
	$tutor->id_paralelo = $_POST["id_paralelo"];
	$tutor->id_usuario = $_POST["id_usuario"];
	$tutor->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $tutor->asociarParaleloTutor();
?>
