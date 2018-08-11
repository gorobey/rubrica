<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tutores.php");
	session_start();
	$tutor = new tutores();
	$tutor->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $tutor->listarParalelosTutores();
?>
