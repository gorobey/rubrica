<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.inspectores.php");
	session_start();
	$inspector = new inspectores();
	$inspector->id_usuario = $_SESSION["id_usuario"];
	$inspector->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	echo $inspector->listarParalelosPorInspector($cantidad_registros,$numero_pagina);
?>