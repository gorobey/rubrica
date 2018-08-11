<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.combos.php");
	$selects = new selects();
	echo $selects->cargarAsignaturasAsociadas($_GET["id_paralelo"]);
?>