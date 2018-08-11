<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.especialidades.php");
	$especialidades = new especialidades();
	$especialidades->id_tipo_educacion = $_POST["id_tipo_educacion"];
	$especialidades->es_nombre = $_POST["es_nombre"];
	$especialidades->es_figura = $_POST["es_figura"];
	echo $especialidades->insertarEspecialidad();
?>
