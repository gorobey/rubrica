<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	$inspector = new inspectores();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["num_pagina"];
	$total_registros = $_POST["total_registros"];
	echo $inspector->paginarParalelosInspector($cantidad_registros,$numero_pagina,$total_registros);
?>
