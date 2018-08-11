<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.institucion.php");
	$institucion = new institucion();
	$institucion->in_nombre = $_POST["in_nombre"];
	$institucion->in_direccion = $_POST["in_direccion"];
	$institucion->in_telefono1 = $_POST["in_telefono1"];
	$institucion->in_nom_rector = $_POST["in_nom_rector"];
	$institucion->in_nom_secretario = $_POST["in_nom_secretario"];
	echo $institucion->actualizarDatosInstitucion();
?>
