<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	//consultar si ya existe asociada una asignatura...
	if ($horario->existeAsignaturaHoraClase($_POST["id_paralelo"], $_POST["id_hora_clase"])) {
		echo json_encode(array('error' => true));
	} else {
		echo json_encode(array('error' => false));
	}
?>
