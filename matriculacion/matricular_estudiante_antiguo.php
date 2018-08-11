<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$estudiantes->code = $_POST["id_estudiante"];
	$estudiantes->id_paralelo = $_POST["id_paralelo"];
	$estudiantes->id_periodo_lectivo = $_POST["id_periodo_lectivo"] - 1;
	if($estudiantes->apruebaPeriodoLectivo()) {
		echo "APROBADO";
	} else {
		echo "NO APROBADO";
	}
?>
