<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignaturas = new asignaturas();
	$asignaturas->code = $_POST["id_paralelo"];
	$alineacion = (isset($_POST["alineacion"])) ? $_POST["alineacion"] : "center";
	echo $asignaturas->mostrarTitulosAsignaturas(2,$alineacion);
?>
