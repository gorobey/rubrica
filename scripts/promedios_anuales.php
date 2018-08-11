<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	echo $paralelos->listarCalificacionesAnuales($_SESSION["id_periodo_lectivo"],$id_paralelo, $cantidad_registros, $numero_pagina);
?>
