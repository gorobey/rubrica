<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	$inspector = new inspectores();
	$inspector->code = $_POST["id_paralelo_inspector"];
	echo $inspector->eliminarParaleloInspector();
?>
