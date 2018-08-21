<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.especialidades.php");
	$especialidades = new especialidades();
	$especialidades->id_tipo_educacion = $_POST["id_tipo_educacion"];
	echo $especialidades->cargarEspecialidades();
?>
