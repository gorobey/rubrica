<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignatura = new asignaturas();
	$asignatura->id_area = $_POST["id_area"];
	$asignatura->id_tipo_asignatura = $_POST["id_tipo_asignatura"];
	$asignatura->as_nombre = $_POST["as_nombre"];
	$asignatura->as_abreviatura = $_POST["as_abreviatura"];
	echo $asignatura->insertarAsignatura();
?>
