<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$paralelos->id_paralelo = $_POST["id_paralelo"];
	$paralelos->id_asignatura = $_POST["id_asignatura"];
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$paralelos->id_usuario = $_SESSION["id_usuario"];
	//$cantidad_registros = $_POST["cantidad_registros"];
	//$numero_pagina = $_POST["numero_pagina"];
	//echo $paralelos->listarCalificacionesParalelo($id_periodo_evaluacion, $cantidad_registros, $numero_pagina, 1);
	echo $paralelos->listarCalificacionesParalelo($id_periodo_evaluacion, 1);
?>
