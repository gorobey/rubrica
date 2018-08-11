<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.foros.php");
	$foro = new foros();
	$foro->code = $_POST["id_foro"];
	$foro->fo_titulo = $_POST["fo_titulo"];
	$foro->fo_descripcion = $_POST["fo_descripcion"];
	echo $foro->actualizarForo();
?>
