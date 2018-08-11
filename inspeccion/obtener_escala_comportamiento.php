<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	$co_calificacion = $_POST["co_calificacion"];
	$inspector = new inspectores();
	echo $inspector->obtenerEscalaComportamiento($co_calificacion);
?>
