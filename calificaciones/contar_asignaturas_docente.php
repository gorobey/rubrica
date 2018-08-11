<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	session_start();
	$asignaturas = new asignaturas();
	$asignaturas->id_usuario = $_SESSION["id_usuario"];
	$asignaturas->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $asignaturas->contarAsignaturasDocente();
?>
