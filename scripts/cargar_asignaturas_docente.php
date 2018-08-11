<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.asignaturas.php");
	session_start();
	$asignaturas = new asignaturas();
	$asignaturas->id_usuario = $_SESSION["id_usuario"];
	$asignaturas->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	echo $asignaturas->listarAsignaturasDocente($cantidad_registros,$numero_pagina);
?>