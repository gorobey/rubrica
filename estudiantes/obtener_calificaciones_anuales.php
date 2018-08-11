<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$txt_apellidos = $_POST["txt_apellidos"];
	$txt_nombres = $_POST["txt_nombres"];
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	if (!$estudiantes->existeEstudiante($txt_apellidos, $txt_nombres, $id_periodo_lectivo))
		echo "No existe el estudiante solicitado...";
	else
		echo $estudiantes->obtenerCalificacionesAnuales($txt_apellidos, $txt_nombres, $id_periodo_lectivo);
?>