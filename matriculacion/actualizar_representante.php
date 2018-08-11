<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiante = new estudiantes();
	$estudiante->code = $_POST["id_estudiante"];
	$estudiante->id_representante = $_POST["id_representante"];
	$estudiante->re_apellidos = $_POST["re_apellidos"];
    $estudiante->re_nombres = $_POST["re_nombres"];
    $estudiante->re_nombre_completo = $_POST["re_nombre_completo"];
	$estudiante->re_cedula = $_POST["re_cedula"];
	$estudiante->re_email = $_POST["re_email"];
    $estudiante->re_direccion = $_POST["re_direccion"];
    $estudiante->re_sector = $_POST["re_sector"];
    $estudiante->re_telefono = $_POST["re_telefono"];
	$estudiante->re_observacion = $_POST["re_observacion"];
	$estudiante->re_parentesco = $_POST["re_parentesco"];
	if($estudiante->existeRepresentante($estudiante->code)){
		echo $estudiante->actualizarRepresentante(); 
	} else {
		echo $estudiante->insertarRepresentante();
	}
?>
