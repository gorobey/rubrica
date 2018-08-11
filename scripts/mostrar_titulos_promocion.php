<?php
	include("clases/class.mysql.php");
	include("clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$id_paralelo = $_POST["id_paralelo"];
	$alineacion = $_POST["alineacion"];
	echo $asignaturas->mostrarTitulosPromocion($id_paralelo, $alineacion);
?>
