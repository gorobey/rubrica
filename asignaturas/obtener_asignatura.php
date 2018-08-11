<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$asignaturas->code = $_POST["id_asignatura"];
	echo $asignaturas->obtenerDatosAsignatura();
?>
