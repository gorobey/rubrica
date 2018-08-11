<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.modalidades.php");
	$modalidad = new modalidades();
	$modalidad->code = $_POST["id_modalidad"];
	$modalidad->mo_nombre = $_POST["mo_nombre"];
	echo $modalidad->actualizarModalidad();
?>
