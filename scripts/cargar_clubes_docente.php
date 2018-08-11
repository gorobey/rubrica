<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.clubes.php");
	session_start();
	$club = new clubes();
	$club->id_usuario = $_SESSION["id_usuario"];
	$club->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$cantidad_registros = $_POST["cantidad_registros"];
	$numero_pagina = $_POST["numero_pagina"];
	echo $club->listarClubesDocente($cantidad_registros,$numero_pagina);
?>