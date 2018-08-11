<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.tutores.php");
	$tutor = new tutores();
	echo $tutor->obtenerNombreParalelo($_POST["id_paralelo"]);
?>
