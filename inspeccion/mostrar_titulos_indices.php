<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	$inspector = new inspectores();
	$alineacion = $_POST["alineacion"];
	echo $inspector->mostrarTitulosIndices($alineacion);
?>
