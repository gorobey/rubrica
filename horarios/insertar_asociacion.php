<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$horario->id_paralelo = $_POST["id_paralelo"];
	$horario->id_asignatura = $_POST["id_asignatura"];
	$horario->id_hora_clase = $_POST["id_hora_clase"];
        $horario->id_dia_semana = $_POST["id_dia_semana"];
	echo $horario->asociarAsignaturaHoraClase();
?>
