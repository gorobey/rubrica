<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$horario->code = $_POST["id_horario"];
	echo $horario->eliminarAsignaturaHoraClase();
?>
