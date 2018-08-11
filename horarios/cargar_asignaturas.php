<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.combos.php");
	$asignaturas = new selects();
	$id_paralelo = $_POST["id_paralelo"];
	$id_usuario = $_POST["id_usuario"];
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $asignaturas->cargarAsignaturasParalelo($id_periodo_lectivo,$id_usuario,$id_paralelo);
?>