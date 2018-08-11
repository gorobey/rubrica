<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["num_pagina"];
	$total_registros = $_POST["total_registros"];
	echo $asignaturas->paginarAsignaturasDocente($cantidad_registros,$numero_pagina,$total_registros);
?>
