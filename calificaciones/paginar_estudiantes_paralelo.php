<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	$total_registros = $_POST["total_registros"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];
	echo $estudiantes->paginarEstudiantesParalelo($cantidad_registros,$numero_pagina,$total_registros,$id_paralelo,$id_asignatura);
?>
