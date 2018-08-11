<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.inspectores.php");
	$inspector = new inspectores();
	echo $inspector->listarParalelosInspectores();
?>
