<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.foros.php");
	$foro = new foros();
	$foro->code = $_POST["id_foro"];
	echo $foro->obtenerDatosForo();
?>
