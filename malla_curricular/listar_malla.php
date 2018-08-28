<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.mallas.php");
	$malla = new mallas();
	$malla->id_paralelo = $_GET["id_paralelo"];
	echo $malla->listarMalla();
?>
