<?php
	//sleep(1);
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$paralelos->id_paralelo = $_POST["id_paralelo"];
	$id_periodo_evaluacion = $_POST["id_periodo"];
	$paralelos->id_usuario = $_SESSION["id_usuario"];
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	echo $paralelos->listarCalificacionesConsolidado($id_periodo_evaluacion, $cantidad_registros, $numero_pagina);
?>
