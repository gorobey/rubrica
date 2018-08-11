<?php
	sleep(1);
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	$estudiante = new estudiantes();
	$txt_apellidos = $_POST["txt_apellidos"];
	$txt_nombres = $_POST["txt_nombres"];
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	if (!$estudiante->existeEstudiante($txt_apellidos, $txt_nombres, $id_periodo_lectivo))
		//echo "No existe el estudiante solicitado...";
		echo json_encode(array('error' => true));
	else {
		$id_estudiante = $estudiante->obtenerIdEstudiante($txt_apellidos, $txt_nombres, $id_periodo_lectivo);
		echo json_encode(array('id_estudiante' => $id_estudiante));
	}
?>