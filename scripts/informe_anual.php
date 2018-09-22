<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	session_start();
	$asignaturas = new asignaturas();
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $asignaturas->listarEscalaCalificacionesAnual($id_periodo_lectivo, $id_paralelo, $id_asignatura);
?>
