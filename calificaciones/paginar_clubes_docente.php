<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.clubes.php");
	$club = new clubes();
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["num_pagina"];
	$total_registros = $_POST["total_registros"];
	echo $club->paginarClubesDocente($cantidad_registros,$numero_pagina,$total_registros);
?>
