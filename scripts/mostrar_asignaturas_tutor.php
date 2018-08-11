<?php
	include("clases/class.mysql.php");
	include("clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$asignaturas->code = $_POST["id_paralelo"];
	$alineacion = $_POST["alineacion"];
	echo $asignaturas->mostrarTitulosAsignaturasTutor($alineacion);
?>
