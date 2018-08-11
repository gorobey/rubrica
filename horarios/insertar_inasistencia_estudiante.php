<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asistencias.php");
	$asistencia = new asistencias();
        $asistencia->id_estudiante = $_POST["id_estudiante"];
	$asistencia->id_asignatura = $_POST["id_asignatura"];
	$asistencia->id_paralelo = $_POST["id_paralelo"];
        $asistencia->id_dia_semana = $_POST["id_dia_semana"];
	$asistencia->id_hora_clase = $_POST["id_hora_clase"];
        $asistencia->id_inasistencia = $_POST["id_inasistencia"];
        $asistencia->ae_fecha = $_POST["ae_fecha"];
	echo $asistencia->insertarInasistenciaEstudiante();
?>
