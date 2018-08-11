<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];
	echo $asignaturas->obtenerIdParaleloAsignatura($id_paralelo, $id_asignatura);
?>
