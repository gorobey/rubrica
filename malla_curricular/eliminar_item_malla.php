<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.mallas.php");
	$malla = new mallas();
	$malla->code = $_POST["id_malla_curricular"];
	echo $malla->eliminarMalla();
?>
