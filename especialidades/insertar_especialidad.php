<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.especialidades.php");
	$especialidad = new especialidades();
	$especialidad->id_tipo_educacion = $_POST["id_tipo_educacion"];
	$especialidad->es_nombre = $_POST["es_nombre"];
	$especialidad->es_figura = $_POST["es_figura"];
	$especialidad->es_abreviatura = $_POST["es_abreviatura"];
	echo $especialidad->insertarEspecialidad();
?>
