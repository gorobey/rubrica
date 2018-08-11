<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_asignatura.php");
	$tipos_asignatura = new tipos_asignatura();
	$tipos_asignatura->code = $_POST["id_tipo_asignatura"];
	$tipos_asignatura->ta_descripcion = $_POST["ta_descripcion"];
	echo $tipos_asignatura->actualizarTipoAsignatura();
?>
