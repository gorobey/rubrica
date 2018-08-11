<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	session_start();
	$estudiante = new estudiantes();
	$estudiante->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$estudiante->id_paralelo = $_POST["id_paralelo"];
	$estudiante->es_apellidos = $_POST["es_apellidos"];
	$estudiante->es_nombres = $_POST["es_nombres"];
	$estudiante->es_cedula = $_POST["es_cedula"];
	$estudiante->es_genero = $_POST["es_genero"];
	$estudiante->es_email = $_POST["es_email"];
        $estudiante->es_direccion = $_POST["es_direccion"];
        $estudiante->es_sector = $_POST["es_sector"];
        $estudiante->es_telefono = $_POST["es_telefono"];
	if ($estudiante->existeNombreEstudiante($estudiante->es_apellidos, $estudiante->es_nombres, $estudiante->id_periodo_lectivo)) {
		//echo "Ya existe el Estudiante en la base de datos...";
		$estudiante->code = $estudiante->obtenerIdEstudiante($estudiante->es_apellidos, $estudiante->es_nombres, $estudiante->id_periodo_lectivo);
		echo $estudiante->actualizarEstudiante();
	} else
		echo $estudiante->insertarEstudiante();
?>
