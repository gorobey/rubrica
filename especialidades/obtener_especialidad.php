<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.especialidades.php");
	$especialidades = new especialidades();
	$especialidades->code = $_POST["id_especialidad"];
	echo $especialidades->obtenerEspecialidad();
?>
