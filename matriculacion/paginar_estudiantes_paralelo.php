<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	$total_registros = $_POST["total_registros"];
	$id_paralelo = $_POST["id_paralelo"];
	echo $estudiantes->paginarEstudiantes($cantidad_registros,$numero_pagina,$total_registros,$id_paralelo);
?>
