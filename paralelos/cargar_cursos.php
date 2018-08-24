<?php
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.combos.php");
	$combo = new selects();
	$combo->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $combo->cargarCursos();
?>
