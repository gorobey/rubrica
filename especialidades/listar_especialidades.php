<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.especialidades.php");
	$especialidades = new especialidades();
	$especialidades->code = $_GET["id_tipo_educacion"];
	echo $especialidades->listarEspecialidades();
?>
