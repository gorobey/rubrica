<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.modalidades.php");
	$modalidad = new modalidades();
	$modalidad->code = $_POST["id_modalidad"];
	echo $modalidad->obtenerModalidad();
?>
