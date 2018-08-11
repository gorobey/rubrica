<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.estudiantes.php");
	require_once("clases/class.periodos_lectivos.php");
	$estudiantes = new estudiantes();
	$txt_apellidos = $_POST["txt_apellidos"];
	$txt_nombres = $_POST["txt_nombres"];
	$periodo_lectivo = new periodos_lectivos();
	$id_periodo_lectivo = $periodo_lectivo->obtenerIdPeriodoLectivoActual();
	if ($estudiantes->existeEstudiante($txt_apellidos, $txt_nombres, $id_periodo_lectivo)) {
		$id_estudiante = $estudiantes->obtenerIdEstudiante($txt_apellidos, $txt_nombres, $id_periodo_lectivo);
		echo json_encode(array('id_estudiante' => $id_estudiante));
	} else {
		echo json_encode(array('error' => true));
	}
?>