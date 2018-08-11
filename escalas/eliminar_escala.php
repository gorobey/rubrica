<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.escalas.php");
	$escala = new escalas();
	$escala->code = $_POST["id"];
	echo $escala->eliminarEscala();
?>
