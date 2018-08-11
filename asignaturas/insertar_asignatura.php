<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$asignaturas->id_tipo_asignatura = $_POST["id_tipo_asignatura"];
	$asignaturas->as_nombre = $_POST["as_nombre"];
	$asignaturas->as_abreviatura = $_POST["as_abreviatura"];
	$asignaturas->as_carga_horaria = $_POST["as_carga_horaria"];
	echo $asignaturas->insertarAsignatura();
?>
